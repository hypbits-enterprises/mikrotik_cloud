# ============================================================
# CHR-VPN  -  local stand-in for the Hypbits SSTP server (GNS3)
# Matches mikrotik_cloud dev DB: settings.sstp_server
#   ip_address=192.168.86.21  api port=1982  user=admin
# Paste into the CHR terminal (RouterOS v7, also works on v6.45+).
# Safe to re-run: every object is removed by name/comment first.
# ============================================================

## 0. Identity + static LAN address (replaces the DHCP client on ether1)
/system identity set name="CHR-VPN"
/ip route remove [find where comment="HYPBITS-LAB default"]
/ip route add dst-address=0.0.0.0/0 gateway=192.168.86.1 comment="HYPBITS-LAB default"
/ip dhcp-client remove [find where interface=ether1]; /ip address remove [find where comment="HYPBITS-LAB LAN"]; /ip address add address=192.168.86.21/24 interface=ether1 comment="HYPBITS-LAB LAN"
/ip dns set servers=8.8.8.8,1.1.1.1

## 1. API user the app logs in with (settings.sstp_server username/password)
/ip service set api disabled=no port=1982
/ip service set winbox disabled=no
/ip service set api-ssl disabled=yes
/user set [find where name="admin"] group=full password="<sstp_server.password>"

## 2. Certificates for the SSTP server (RouterOS 7.23 needs a CA to sign with)
/interface sstp-server server set certificate=none
/certificate remove [find where name="hypbits-sstp"]
/certificate remove [find where name="hypbits-lab-ca"]
/certificate add name="hypbits-lab-ca" common-name="hypbits-lab-ca" key-size=2048 days-valid=3650 key-usage=key-cert-sign,crl-sign
/certificate sign "hypbits-lab-ca"
/certificate add name="hypbits-sstp" common-name=192.168.86.21 subject-alt-name="IP:192.168.86.21" key-size=2048 days-valid=3650 key-usage=digital-signature,key-encipherment,tls-server
/certificate sign "hypbits-sstp" ca="hypbits-lab-ca"
/certificate set [find where name="hypbits-sstp"] trusted=yes

## 3. Address pool + PPP profile the app assigns to new routers (SSTP_PROFILE)
##    Clients' generated script expects the server side to be 192.168.254.1
/ip pool remove [find where name="hypbits-sstp-pool"]
/ip pool add name="hypbits-sstp-pool" ranges=192.168.254.10-192.168.254.250
/ppp profile remove [find where name="SSTP_PROFILE"]
/ppp profile add name="SSTP_PROFILE" local-address=192.168.254.1 remote-address=hypbits-sstp-pool use-encryption=yes only-one=yes change-tcp-mss=yes comment="Used by mikrotik_cloud save_cloud_router()"

## 4. SSTP server on 443 (client script uses connect-to=<ip> with the default port)
/interface sstp-server server set enabled=yes port=443 certificate="hypbits-sstp" default-profile="SSTP_PROFILE" authentication=mschap2,mschap1,chap,pap verify-client-certificate=no

## 5. SSTP secrets for routers already in the mikrotik_cloud dev DB (remote_routers)
/ppp secret remove [find where name="<sstp_username>"]
/ppp secret add name="<sstp_username>" password="<sstp_password>" service=sstp profile="SSTP_PROFILE" comment="Router Name (<router_name>) - router_id <router_id>"

## 6. Let the dev machine (192.168.86.16 / LAN) reach routers on their tunnel IPs.
##    Source-NAT to 192.168.254.1 so client routers reply through the tunnel
##    (they only have routes for 192.168.252-254.0/24 via the VPN).
/ip firewall nat remove [find where comment="HYPBITS-LAB lan-to-tunnels"]
/ip firewall nat add chain=srcnat src-address=!192.168.254.0/24 dst-address=192.168.254.0/24 action=src-nat to-addresses=192.168.254.1 comment="HYPBITS-LAB lan-to-tunnels"

:put "CHR-VPN ready. SSTP on 192.168.86.21:443, API on port 1982."
/beep
