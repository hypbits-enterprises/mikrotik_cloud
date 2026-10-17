# GNS3 lab — virtual MikroTik routers for hotspot testing

All router-write testing for the hotspot module happens here, never on live ISP routers.
Built and verified on 2026-10-17.

## 1. Topology

```
Windows PC "ladybird" (Ethernet, home LAN 192.168.86.0/24, gateway = home MikroTik 192.168.86.1)
 └─ VMware Workstation
     ├─ Linux dev VM (mikrotik_cloud, manager, crontab)   ens33 bridged → 192.168.86.16
     └─ GNS3 VM                                            eth0 bridged  → 192.168.86.15
         └─ GNS3 project "Mikrotik CHR Hypbits"
              Cloud (server: GNS3 VM, interface eth0)
                └─ Ethernet switch
                     ├─ CHR-VPN  ether1  192.168.86.21 (static)   console telnet 192.168.86.15:5000
                     └─ CHR-ISP  ether1  DHCP (192.168.86.20)     console telnet 192.168.86.15:5003
                                 ether2  reserved for the hotspot LAN (Stage 4+)
                                 SSTP → CHR-VPN, tunnel IP 192.168.254.x
```

| Router | Role | In the dev DB |
|---|---|---|
| CHR-VPN | Stand-in for the Hypbits SSTP server | `mikrotik_cloud.settings` `sstp_server` → `ip_address` `192.168.86.21`, API port `1982`, user `admin` |
| CHR-ISP | The ISP's cloud router, hotspot test router | `remote_routers.router_id = 23` "Hillary New Router" (org LADYBIRD SYSTEMS, `mikrotik_cloud`) |

Request path from the app to a router:
```
Laravel (.16) → home MikroTik (.1) → CHR-VPN (.21) → SSTP tunnel → CHR-ISP (192.168.254.x)
```

## 2. GNS3 setup

1. **GNS3 VM network:** in VMware, the GNS3 VM's adapter is **Bridged** to the PC's Ethernet card
   (VMnet0 bridged to the exact NIC, not "Automatic"). Inside the GNS3 VM it is `eth0` and gets a
   home-LAN address by DHCP.
2. **CHR template** (Edit → Preferences → QEMU VMs → New, *Run on the GNS3 VM*):
   - Image: `chr-7.23.8.img` (the unzipped **raw disk image**, not `.zip`/`.vmdk`), as hda.
   - Boot priority **HDD**, RAM 256 MB, 1 vCPU, console **telnet**.
   - Disk interface `virtio`; Network: 4 adapters, type `virtio-net-pci`,
     port name format `ether{port1}` (GNS3 port names then match RouterOS).
   - *Use as a linked base VM* ticked (each node gets its own disk).
   - Symptom of a missing disk image: console shows only `iPXE … Press Ctrl-B` lines.
3. **Cloud and switch** both on the **GNS3 VM** server; Cloud interface `eth0` only.
4. **Windows console app:** GNS3 Preferences → General → Console applications → PuTTY /
   Solar-PuTTY, or telnet directly to `192.168.86.15:<port>` (hover a node for its port).
   The console ports can also be read from the Linux VM, e.g. with a small socket script.

## 3. Home MikroTik (192.168.86.1)

```
/ip dhcp-server lease add address=192.168.86.21 mac-address=0C:EB:F5:5D:00:00 comment="GNS3 CHR-VPN"
/ip route add dst-address=192.168.254.0/24 gateway=192.168.86.21 comment="GNS3 lab VPN tunnels"
```
The route is what lets the app reach routers on their tunnel IPs (step 2 of
`Router_Cloud::checkActive()` connects to the tunnel IP directly). The Linux VM learns it through
an ICMP redirect — no change on the VM is needed.

## 4. CHR-VPN configuration

Script: `docs/hotspot/lab/chr-vpn.rsc` (one command per line, safe to re-run). Replace the
placeholders before pasting:
- `<sstp_server.password>` — the `password` in the org's `settings.sstp_server` JSON;
- one `/ppp secret` pair per row in `remote_routers` (`sstp_username`, `sstp_password`,
  `router_name`, `router_id`). Routers added later through the app get their secret created on
  CHR-VPN automatically by `save_cloud_router()`.

What it sets: static `192.168.86.21/24` + default route; API on 1982; `admin` password; a local
CA `hypbits-lab-ca` signing the SSTP certificate `hypbits-sstp` (CN `192.168.86.21` — RouterOS
7.23 will not self-sign a TLS-server certificate without a CA); pool `192.168.254.10-250`;
profile `SSTP_PROFILE` (local `192.168.254.1`); SSTP server on 443; a src-NAT rule so routers
reply to the LAN through the tunnel.

Client routers do not verify the server certificate (the generated config does not set
`verify-server-certificate`), so the certificate name only needs to be tidy, not trusted.

## 5. CHR-ISP

1. First boot: log in as `admin` (empty password) and set a password. `ether1` gets DHCP.
2. In the app (logged in as LADYBIRD SYSTEMS) open router 23's profile, copy the router
   configuration (it uses `connect-to=192.168.86.21`) and paste it into the CHR-ISP console.
3. Check: on CHR-VPN `/ppp active print` shows the router's SSTP username with a
   `192.168.254.x` address; the router profile page loads RouterOS details.

## 6. Dev DB changes made for the lab

- `mikrotik_cloud.settings` (`keyword = 'sstp_server'`): `ip_address` changed from
  `192.168.86.10` to `192.168.86.21`. Username, password and port unchanged.
- `.env`: `HOTSPOT_ROUTER_WRITE_ALLOWLIST=mikrotik_cloud:23` (checked from Stage 4 on).

## 7. Keeping a clean state

After both routers are configured, take a GNS3 snapshot (*Edit → Manage snapshots*) named e.g.
`stage-00-clean`. Restore it before a stage that changes hotspot config on CHR-ISP, so each stage
starts from a known router state.

## 8. Troubleshooting

| Symptom | Check |
|---|---|
| CHR console blank / iPXE only | Template has no hda image or boot priority isn't HDD. |
| No DHCP lease for a CHR | Cloud must be on the GNS3 VM's bridged `eth0`; link must be on `ether1`; `/interface print` shows `R` on ether1. |
| Profile page: "SSTP server cannot be reached" | `settings.sstp_server.ip_address` = CHR-VPN address; API on 1982 (`/ip service print`). |
| Profile page: "Cannot connect to router" | `/ppp active print` on CHR-VPN; home-MikroTik route to `192.168.254.0/24`; `ip route get 192.168.254.x` on the Linux VM shows `via 192.168.86.21`. |
| SSTP client stuck connecting | `/interface sstp-server server print` (enabled, certificate set), `/log print where topics~"sstp"`, port 443 reachable from CHR-ISP. |
