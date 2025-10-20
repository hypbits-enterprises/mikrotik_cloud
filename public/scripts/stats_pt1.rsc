#START BY CHECKING THE GLOBAL CONFIGS
:global routerId;
:global userAccount;
:global domain
:if ([:typeof $routerId] = "nothing" || [:typeof $userAccount] = "nothing" || [:typeof $domain] = "nothing") do={
    error "No configuration set";
}

#COLLECT THE TRAFFIC USAGE ON PPPOE AND QUEUES
:put "Start from here"
# Store the current logging state
:local loggingState [/system logging find where topics~"info"]

# Disable interface logging to avoid log flooding
/system logging disable [find topics~"info"]

#get the pppoe stats
:local pppoeStats [/interface find where name~"pppoe-" disabled=no]

#:put [:pick $pppoeStats 1];

:local pppoeJson "[";
:foreach interface in=[$pppoeStats] do={
    #:put "Interface name is $[/interface get $interface name]\n";
    :local pppoeName ""
    :local name [/interface get $interface name]
    :local startAcc ([:find $name "-"]+1)
    :local endAcc [:find $name ">"]
    :set name [:pick $name $startAcc $endAcc]

    :local recieved [/interface get $interface rx-byte];
    :local transfered [/interface get $interface tx-byte];
    :local stat [/interface monitor-traffic $interface once as-value];
    :local upload [:pick $stat 7]
    :local download [:pick $stat 11]
    :set pppoeJson ($pppoeJson."{\"type\":\"pppoe\", \"account\": \"$name\", \"download\":$transfered, \"upload\":$recieved,\"upload_speed\":\"$upload\", \"download_speed\": \"$download\"},")
}