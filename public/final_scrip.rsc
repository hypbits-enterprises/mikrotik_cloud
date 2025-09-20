# Mikrotik script (RouterOS v6.x)
:local apiUrl "http://192.168.86.16:8000/router_clients/mikrotik_cloud/22"

:log info ("[API-SYNC] starting fetch from " . $apiUrl)

/tool fetch url=$apiUrl mode=https keep-result=yes dst-path=client_list.txt

# read file contents (string)
:local content [/file get client_list.txt contents]

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
        :local gateway [:pick $obj ($gwStart + 11) [:len $obj]]
        :put "Gateway = $gateway";

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

        :local f1 [/ip address find where address~$regex]
        #:local f1 [/ip address find where address~($network . "/")]
        :if ([:len $f1] > 0) do={
            :put "Enabled ip address $gateway $f1";
            /ip address set $f1 disabled=no
            :log info ("[API-SYNC] enabled ip address " . $gateway)
        } else={
            :log warning ("[API-SYNC] active_static not found: or active " . $network)
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
        :local gateway [:pick $obj ($gwStart + 11) [:len $obj]]
        #:put "Gateway = $gateway";

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

        :local f1 [/ip address find where address~$regex]
        #:local f1 [/ip address find where address~($network . "/")]
        :if ([:len $f1] > 0) do={
            :put "Enabled ip address $gateway $f1";
            /ip address set $f1 disabled=yes
            :log info ("[API-SYNC] enabled ip address " . $gateway)
        } else={
            :log warning ("[API-SYNC] active_static not found: or active " . $network)
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

        :local f1 [/ppp secret find where name=$secret]
        :if ([:len $f1] > 0) do={
            :put "Enabled secret $secret $f1";
            /ppp secret set $f1 disabled=no
            :log info ("[API-SYNC] enabled secret " . $secret)
            
            :local f2 [/ppp active find where name=$secret]
            :if ([:len $f2] > 0) do={
                :put "Disable active secret : $secret";
                /ppp active remove $f2
                :log info ("Removed its active connection");
            }
        } else={
            :log warning ("[API-SYNC] secret not found: or active " . $secret)
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

        :local f1 [/ppp secret find where name=$secret]
        :if ([:len $f1] > 0) do={
            :put "Enabled secret $secret $f1";
            /ppp secret set $f1 disabled=yes
            :log info ("[API-SYNC] enabled secret " . $secret)
            
            :local f2 [/ppp active find where name=$secret]
            :if ([:len $f2] > 0) do={
                :put "Disable active secret : $secret";
                /ppp active remove $f2
                :log info ("Removed its active connection");
            }
        } else={
            :log warning ("[API-SYNC] secret not found: or active " . $secret)
        }
    }
}
