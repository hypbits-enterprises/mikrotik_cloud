
#INACTIVE STATIC CLIENTS
:local start [:find $content "\"inactive_static\":["]
:local end [:find $content "]" $start]
:local subStr [:pick $content ($start + 19) $end]

if ([:len $subStr] > 0) do= {
    :local objects [:toarray ($subStr)]
    :set objects [:toarray [:pick $subStr 0 [:len $subStr]]]
    :put $objects;
    
    :foreach obj in=[:toarray $subStr] do={
        #NETWORK
        :local netStart [:find $obj "\"network\":\""]
        :local netEnd [:find $obj "\",\"gateway\"" $netStart]
        :local network [:pick $obj ($netStart + 11) $netEnd]
        #:put "Network = $network";

        #GATEWAY
        :local gwStart [:find $obj "\"gateway\":\""]
        :local gwEnd [:find $obj "\",\"account\":\""]
        :local gateway [:pick $obj ($gwStart + 11) $gwEnd]
        :put "Gateway = $gateway";

        #ACCOUNT
        :local accStart [:find $obj "\"account\":"]
        :local accEnd [:find $obj "\"speed\":"]
        :local account [:pick $obj ($accStart+11) ($accEnd-2)]
        :put "Account = $account"
        
        #SPEED
        :local speedStart [:find $obj "\"speed\":\""]
        :local speed [:pick $obj ($speedStart+9) ([:len $obj]-1)]
        :local speedStart [:find $speed "\\"]
        :set $speed ([:pick $speed 0 $speedStart].[:pick $speed ($speedStart+1) [:len $speed]])
        :put "Speed : $speed";

        #check if the queue is more than 1
        :local queues [/queue/simple/find where name~"$account\$"]
        :put $queues
        :local selectedQueue
        :foreach queue in=$queues do={
            #find the queue thats active
            :put $queue
            :local queueData [/queue/simple/get $queue]
            :local uploadSpeed ($queueData->"bytes")
            :set $uploadSpeed [:pick $uploadSpeed 0 [:find $uploadSpeed "/"]]
            #:put $uploadSpeed;
            #:put $queueData
            :if (($queueData->"max-limit") = $speed $uploadSpeed > 0) do={
                :set $selectedQueue $queue
                #:put "We are here!"
            }
        }
        :if ([:typeof $selectedQueue] = "nothing") do={
            #just use the first that matches the speed
            :foreach queue in=$queues do={
                :local queueData [/queue/simple/get $queue]
                :if (($queueData->"max-limit") = $speed) do={
                    :set $selectedQueue $queue
                    #:put "We are here!"
                }
            }
        }
        :foreach queue in=$queues do={
            :local queueData [/queue/simple/get $queue]
            :if (!(($queueData->".id") = $selectedQueue)) do={
                :put ("Deleted ".($queueData->".id"))
                [/queue/simple/remove ($queueData->".id")]
                #:put "We are here!"
            }
        }
        :local regex ([:pick $gateway 0 [:find $gateway "\\"]]."".[:pick $gateway ([:find $gateway "\\"]+1) [:len $gateway]]);
        :put $regex;
        :local f1 [/ip address find where address~$regex disabled=no]
        :if ([:len $f1] > 0) do={
            :put "Disabled ip address $gateway $f1";
            /ip address set $f1 disabled=yes
            #:log info ("[API-SYNC] enabled ip address " . $gateway)
        } else={
            #:log warning ("[API-SYNC] active_static not found: or active " . $network)
        }
    }
}