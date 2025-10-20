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