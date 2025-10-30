:local version [/system resource get version];
:local subStart [:find $version "."]
:local subEnd [:find [:pick $version ($subStart+1) [:len $version]] "."]
:if ([:typeof $subEnd] = "nil") do={
    :set $subEnd [:len $version]
}
:local mainVersion [:pick $version 0 $subStart]
:local subVersion [:pick $version ($subStart+1) ($subEnd+1+$subStart)]
:if ($mainVersion < 7) do={
    error "Version not supported: Version 7.18 and above required!"
    #return logging
}
if ($mainVersion >= 7 $subVersion < 18) do={
    error "Version not supported: Version 7.18 and above required!"
    #return logging
}

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
    
    :local apiUrl "https://billing.hypbits.com/my_global_config?ip_address=$ipAddress";
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
