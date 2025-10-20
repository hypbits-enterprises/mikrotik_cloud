:put "Get my global variables"

# Declare globals
:global domain
:global routerId
:global userAccount

:local id [/ip address find where interface="SYSTEM_SSTP_TWO"];

:if ([:len $id] > 0) do={
    :local fullIpAddress [/ip address get $id address];
    :put $fullIpAddress;
    
    #extract the ip address
    :local subnetPos [:find $fullIpAddress "/"]

    :local ipAddress [:pick $fullIpAddress 0 $subnetPos]
    :put $ipAddress
    
    :local apiUrl "https://test_billing.hypbits.com/my_global_config?ip_address=$ipAddress";
    :put $apiUrl;
    
    #check if the file is present
    :if ([:len [/file find name="global_config.txt"]] = 0) do={
        /file print file="global_config.txt"
    }
    
    /tool fetch url=$apiUrl mode=https keep-result=yes dst-path=global_config.txt
    :local globalConfig [/file get global_config.txt contents]
    
    :put $globalConfig;
    :local successStart [:find $globalConfig ":"];
    :local successStop [:find $globalConfig ","];
    :local successStatus [:pick $globalConfig ($successStart+1) $successStop];
    :put $successStatus;
