# Mikrotik script (RouterOS v6.x)
:local apiUrl "http://192.168.86.16:8000/router_clients/mikrotik_cloud/22"

:log info ("[API-SYNC] starting fetch from " . $apiUrl)

/tool fetch url=$apiUrl mode=https keep-result=yes dst-path=client_list.txt

# read file contents (string)
:local content [/file get client_list.txt contents]

#ACTIVE STATIC CLIENTS
:local start [:find $content "\"active_pppoe\":["]
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
    }
}