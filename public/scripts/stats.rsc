#START BY CHECKING THE GLOBAL CONFIGS
:global routerId;
:global userAccount;
:global domain
:if ([:typeof $routerId] = "nothing" || [:typeof $userAccount] = "nothing" || [:typeof $domain] = "nothing") do={
    error "No configuration set";
}

#COLLECT THE TRAFFIC USAGE ON PPPOE AND QUEUES
:put "Start from here"
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
    :set pppoeJson ($pppoeJson."{\"type\":\"pppoe\", \"account\": \"$name\", \"download\":$recieved, \"upload\":$transfered,\"upload_speed\":\"$upload\", \"download_speed\": \"$download\"},")
}

:if ([:len $pppoeStats] > 0) do={
    :set pppoeJson [:pick $pppoeJson 0 ([:len $pppoeJson]-1)]
}

:set pppoeJson ($pppoeJson."]")
#:put $pppoeJson;

#QUEUE USAGE
:local staticQueues [/queue simple find where !(name~"<pppoe") disabled=no]
:local staticJson "["
:foreach queue in=[$staticQueues] do={
    #:put [/queue simple get $queue]
    :local queueName [/queue simple get $queue name]
    :local accStart [:find $queueName ") -"]
    :if ([:len $accStart] > 0) do={
        :local accNo [:pick $queueName ($accStart+4) [:len $queueName]]
        #:put ($accNo."\n")
        :local bytes [/queue simple get $queue bytes]
        :local recieveStart [:find $bytes "/"]
        :local recieved [:pick $bytes 0 ($recieveStart)]
        :local transfered [:pick $bytes ($recieveStart+1) [:len $bytes]]
        
        :local rates [/queue simple get $queue rate]
        :set staticJson ($staticJson."{\"type\":\"static\", \"account\": \"$accNo\", \"download\":$recieved, \"upload\":$transfered, \"rate\":\"$rates\"},")
    }
}

:if ([:len $staticQueues] > 0) do={
    :set staticJson [:pick $staticJson 0 ([:len $staticJson]-1)]
}
:set staticJson ($staticJson."]")
#:put $staticJson;

:local jsonData "{\"static\":$staticJson, \"pppoe\":$pppoeJson}"
:put $jsonData;

:local filePath "$userAccount-$routerId-stats.json"
if ([/file find where name=$filePath]) do={
    /file remove [find name=$filePath]; # remove old file if exists
}
#add the data to the file
/file add name=$filePath contents=$jsonData
:put "File created successfully!"

#upload the json file to the server for processing
:local serverAddr "ftp://mikrotik:mikrotik@192.168.86.16/$filePath";
/tool fetch upload=yes url=$serverAddr src-path=$filePath
:delay 1
#delete the file
:local f1 [/file find name=$filePath]
:if ([:len $f1] > 0) do={
    /file remove $f1;
}

#run the request to process its usage
/tool fetch url="$domain/upload_client_stats?account=$userAccount&router_id=$routerId" mode=http keep-result=yes dst-path=upload_response.txt
:delay 1
:local file [/file find name="upload_response.txt"]
:if ([:len $file] > 0) do={
    :put [/file get $file contents]
    /file remove $file;
}

#:put "Files uploaded successfully!"