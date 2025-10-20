

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
    :local search ") -"

    :local startPos 0;

    :set $startPos [find $queueName ") -"];
    # :put ($startPos." outside");
    :local positionCounter 0;

    :while ([:find $queueName $search] > 0) do={
        :set $startPos [:find $queueName $search];
        # :put $queueName;
        :set $queueName [:pick $queueName ($startPos+4) [:len $queueName]]
        # :put $queueName;
        :set $positionCounter ($positionCounter+$startPos)
        # :put $positionCounter
    }
    :local accNo $queueName;

    
    :if ([:len $accNo] > 0 [:len $accNo] <= 8) do={
        # :local accNo [:pick $queueName ($accStart+4) [:len $queueName]]
        #:put ($accNo."\n")
        :local bytes [/queue simple get $queue bytes]
        :local recieveStart [:find $bytes "/"]
        :local upload [:pick $bytes 0 ($recieveStart)]
        :local download [:pick $bytes ($recieveStart+1) [:len $bytes]]
        
        :local rates [/queue simple get $queue rate]
        :set staticJson ($staticJson."{\"type\":\"static\", \"account\": \"$accNo\", \"download\":$download, \"upload\":$upload, \"rate\":\"$rates\"},")
    }
}