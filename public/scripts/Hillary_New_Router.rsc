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
if ([/ip address find network="10.10.50.0"] = "") do={
   /ip address add address="10.10.50.1/24" network="10.10.50.0" disabled="yes" interface="bridge" comment="Shakur Abdumalik 2 (Mombasa - ) - SF001"
}
:if ([/queue simple find target~"10.10.50.0"] = "") do={
   /queue simple add name="Shakur Abdumalik 2 (Mombasa - ) - SF001" target="10.10.50.0/24" max-limit="26M/30M" comment="Shakur Abdumalik 2 (Mombasa - ) - SF001"
}
