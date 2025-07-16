# Exported on: Wed 16th Jul 2025 08:40:39 PM
# THIS EXPORT ONLY CONTAINS THE CLIENT`S DATA
# NO OTHER CONFIGURATION INCLUDED.
# Router name : 'Test Router'.


#IP ADDRESSES
/ip address 
 add address="192.254.5.1/24" interface=ether2 network=192.254.5.0 comment="Test two (Kisimani  - ) - HYP001"
add address="172.16.2.1/24" interface=ether5 network=172.16.2.0 comment="HILLARY NGIGE (KISAUNI - ) - HYP002"
add address="100.10.1.1/24" interface=ether4 network=100.10.1.0 comment="Gloria Muwanguzi (Uganda - ) - HYP004"

#SIMPLE QUEUES
/queue simple 
 add name="Test two (Kisimani  - ) - HYP001" target="192.254.5.0/24" max-limit="5M/5M"
add name="HILLARY NGIGE (KISAUNI - ) - HYP002" target="172.16.2.0/24" max-limit="5M/4M"
add name="Gloria Muwanguzi (Uganda - ) - HYP004" target="100.10.1.0/24" max-limit="2M/2M"

#ADD PPPOE PROFILES (MODIFY THESE PROFILES TO YOUR PREFERENCE AFTER THEY HAVE BEEN ADDED)
/ppp profile
add name="default-encryption" comment="OPEN TO MODIFICATION"

#PPPOE
/ppp secret 
add name="HYP007" service="pppoe" password="HYP007" profile="default-encryption"  comment="JAMES (Kisumu - ) - HYP007"
