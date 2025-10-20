
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
:local serverAddr "ftp://hbsftpuser:2000Hilary@159.65.81.51/$filePath";
/tool fetch upload=yes url=$serverAddr src-path=$filePath
:delay 1
#delete the file
:local f1 [/file find name=$filePath]
:if ([:len $f1] > 0) do={
    /file remove $f1;
}

#run the request to process its usage
/tool fetch url="$domain/upload_client_stats?account=$userAccount&router_id=$routerId" mode=https keep-result=yes dst-path=upload_response.txt
:delay 1
:local file [/file find name="upload_response.txt"]
:if ([:len $file] > 0) do={
    :put [/file get $file contents]
    /file remove $file;
}

# Restore logging state silently
:foreach logEntry in=$loggingState do={
    /system logging set $logEntry disabled=no
}
:put "Files uploaded successfully!"