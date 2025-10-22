if ([/ip address find network="10.10.10.0"] = "") do={
   /ip address add address="10.10.10.1/24" network="10.10.10.0" disabled="no" interface="bridge1" comment="Hillary (Kiembeni - ) - HYP011"
}
:if ([/queue simple find target~"10.10.10.0"] = "") do={
   /queue simple add name="Hillary (Kiembeni - ) - HYP011" target="10.10.10.0/24" max-limit="10M/10M" comment="Hillary (Kiembeni - ) - HYP011"
}
:if ([/ppp secret find name="HYP003"] = "") do={
   /ppp secret add name="HYP003" password="HYP003" disabled="yes" service="pppoe" profile="bridge1" comment="James Karani (Mtwapa - ) - HYP020"
}
