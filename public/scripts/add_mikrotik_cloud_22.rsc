if ([/ip address find network="10.10.60.0"] = "") do={
   /ip address add address="10.10.60.1/24" network="10.10.60.0" disabled="yes" interface="bridge" comment="James Gichuru (Kitale - ) - HYP001"
}
:if ([/queue simple find target~"10.10.60.0"] = "") do={
   /queue simple add name="James Gichuru (Kitale - ) - HYP001" target="10.10.60.0/24" max-limit="50K/50M" comment="James Gichuru (Kitale - ) - HYP001"
}
if ([/ip address find network="10.10.40.0"] = "") do={
   /ip address add address="10.10.40.1/24" network="10.10.40.0" disabled="yes" interface="bridge" comment="Shakur Abdumalik (Thika. Kiganjo - ) - HYP002"
}
:if ([/queue simple find target~"10.10.40.0"] = "") do={
   /queue simple add name="Shakur Abdumalik (Thika. Kiganjo - ) - HYP002" target="10.10.40.0/24" max-limit="50M/15M" comment="Shakur Abdumalik (Thika. Kiganjo - ) - HYP002"
}
/file remove [find name="add_mikrotik_cloud_22.rsc"]
:put "File deleted successfully"
/tool fetch url="http://192.168.86.16:8000/delete_file_migrate?filename=add_mikrotik_cloud_22.rsc" mode=http keep-result=no
