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

# Declare globals
:global domain
:global routerId
:global userAccount

# Store the current logging state
:local loggingState [/system logging find where topics~"info"]

# Disable interface logging to avoid log flooding
/system logging disable [find topics~"info"]

# Check if "domain" exists
:if ([:typeof $domain] = "nothing" || [:typeof $routerId] = "nothing" || [:typeof $userAccount] = "nothing") do={
    :local targetScript "checkconfig"

    :if ([:len [/system script find where name=$targetScript]] > 0) do={
        #:log info ("[SCRIPT] Running " . $targetScript)
        /system script run $targetScript
    }
    :delay 0.5
    :if ([:typeof $domain] = "nothing" || [:typeof $routerId] = "nothing" || [:typeof $userAccount] = "nothing") do={
        :put "No value";
        # Enable logging back
        /system logging enable [find topics~"info"]
        :error ""
    }
}

# Mikrotik script (RouterOS v6.x)
:local apiUrl "$domain/router_clients/$userAccount/$routerId"

#:local apiUrl "https://test_billing.hypbits.com/router_clients/mikrotik_cloud/22"

#:log info ("[API-SYNC] starting fetch from " . $apiUrl)

#create the file if its not present
:local f [/file find name="client_list.txt"]
:if ([:len $f] = 0) do={
    :put "Create the file"
    /file print file="client_list.txt"
}
:delay 1;
/tool fetch url=$apiUrl mode=https keep-result=yes dst-path=client_list.txt

# read file contents (string)
:local content [/file get client_list.txt contents]
:put $content;
#ACTIVE STATIC CLIENTS
:local start [:find $content "\"active_static\":["]
:local end [:find $content "]" $start]
:local subStr [:pick $content ($start + 17) $end]

if ([:len $subStr] > 0) do= {
    :local objects [:toarray ($subStr)]
    :set objects [:toarray [:pick $subStr 0 [:len $subStr]]]
    :put $objects;
    
    :foreach obj in=[:toarray $subStr] do={
        #NETWORK
        :local netStart [:find $obj "\"network\":\""]
        :local netEnd [:find $obj "\",\"gateway\"" $netStart]
        :local network [:pick $obj ($netStart + 11) $netEnd]
        :put "Network = $network";

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

        # find first dot
        :local firstDot [:find $network "."]
        # find second dot (start searching after the first one)
        :local secondDot [:find $network "." ($firstDot + 1)]
        # find third dot (start searching after the second one)
        :local thirdDot [:find $network "." ($secondDot + 1)]

        # take prefix up to the last dot (10.10.70.)
        :local prefix [:pick $network 0 ($thirdDot + 1)]

        :local regex ("^" . $prefix . "[0-9]+/24")
        :put $regex;

        :local f1 [/ip address find where address~$regex disabled=yes]
        :if ([:len $f1] > 0) do={
            :put "Enabled ip address $gateway $f1";
            /ip address set $f1 disabled=no
            #:log info ("[API-SYNC] enabled ip address " . $gateway)
        } else={
            #:log warning ("[API-SYNC] active_static not found: or active " . $network)
        }
    }
}

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

        # find first dot
        :local firstDot [:find $network "."]
        # find second dot (start searching after the first one)
        :local secondDot [:find $network "." ($firstDot + 1)]
        # find third dot (start searching after the second one)
        :local thirdDot [:find $network "." ($secondDot + 1)]

        # take prefix up to the last dot (10.10.70.)
        :local prefix [:pick $network 0 ($thirdDot + 1)]
        
        :local regex ("^" . $prefix . "[0-9]+/24")
        :put $regex;

        :local f1 [/ip address find where address~$regex disabled=no]
        #:local f1 [/ip address find where address~($network . "/")]
        :if ([:len $f1] > 0) do={
            :put "Enabled ip address $gateway $f1";
            /ip address set $f1 disabled=yes
            #:log info ("[API-SYNC] enabled ip address " . $gateway)
        } else={
            #:log warning ("[API-SYNC] active_static not found: or active " . $network)
        }
    }
}

#ACTIVE PPPOE CLIENTS
:local start [:find $content "\"active_pppoe\":["]
:local end [:find $content "]" $start]
:local subStr [:pick $content ($start + 16) $end]

#:put $subStr;

if ([:len $subStr] > 0) do= {
    :local objects [:toarray ($subStr)]
    :set objects [:toarray [:pick $subStr 0 [:len $subStr]]]
    #:put $objects;
    
    :foreach obj in=[:toarray $subStr] do={
        :put $obj;
        #NETWORK
        :local secretStart [:find $obj "\"secret\":\""]
        :local secret [:pick $obj ($secretStart + 10) ([:len $obj]-1)]
        :put "Secret = $secret";

        :local f1 [/ppp secret find where name=$secret disabled=yes]
        :if ([:len $f1] > 0) do={
            :put "Enabled secret $secret $f1";
            /ppp secret set $f1 disabled=no
            #:log info ("[API-SYNC] enabled secret " . $secret)
            
            :local f2 [/ppp active find where name=$secret]
            :if ([:len $f2] > 0) do={
                :put "Disable active secret : $secret";
                /ppp active remove $f2
                #:log info ("Removed its active connection");
            }
        } else={
            #:log warning ("[API-SYNC] secret not found: or active " . $secret)
        }
    }
}


#INACTIVE PPPOE CLIENTS
:local start [:find $content "\"inactive_pppoe\":["]
:local end [:find $content "]}" $start]
:local subStr [:pick $content ($start + 18) $end]

#:put $subStr;

if ([:len $subStr] > 0) do= {
    :local objects [:toarray ($subStr)]
    :set objects [:toarray [:pick $subStr 0 [:len $subStr]]]
    #:put $objects;
    
    :foreach obj in=[:toarray $subStr] do={
        :put $obj;
        #NETWORK
        :local secretStart [:find $obj "\"secret\":\""]
        :local secret [:pick $obj ($secretStart + 10) ([:len $obj]-1)]
        :put "Secret = $secret";

        :local f1 [/ppp secret find where name=$secret disabled=no]
        :if ([:len $f1] > 0) do={
            :put "Enabled secret $secret $f1";
            /ppp secret set $f1 disabled=yes
            #:log info ("[API-SYNC] enabled secret " . $secret)
            
            :local f2 [/ppp active find where name=$secret]
            :if ([:len $f2] > 0) do={
                :put "Disable active secret : $secret";
                /ppp active remove $f2
                #:log info ("Removed its active connection");
            }
        } else={
            #:log warning ("[API-SYNC] secret not found: or active " . $secret)
        }
    }
}

# Clean up temporary file
:local fileId [/file find name="client_list.txt"]
:if ([:len $fileId] > 0) do={
    /file remove $fileId
}

# Restore logging state silently
:foreach logEntry in=$loggingState do={
    /system logging set $logEntry disabled=no
}
:put "Script completed successfully."