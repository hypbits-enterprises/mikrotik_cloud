

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