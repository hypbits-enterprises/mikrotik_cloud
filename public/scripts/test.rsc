:put "....Hypbits Enterprises Ltd...."
:put "....Preparing your configuration...."
:local domain "https://test_billing.hypbits.com"
# Function to download + import scripts safely
:local installScript do={
    :local fname $1; :local url $2;
    :put ("Downloading " . $fname . ".rsc ...");
    /tool fetch url=$url mode=https dst-path=($fname . ".rsc");
    :delay 2
    #:put ([/file find name="$fname.rsc"])    
    #error ($fname.".rsc")    
    :if ([:len [/file find name=($fname . ".rsc")]] > 0) do={        
        :local fId [/file find name=($fname . ".rsc")]        
        :local size [/file get $fId size];        
        :if ($size > 0) do={
            :put ("Creating or updating script: " . $fname);
            :local existing [/system script find where name=$fname];            
            :put [/file get $fId contents]
            :if ([:len $existing] > 0) do={
                /system script set $existing source=[/file get $fId contents];            
            } else={
                /system script add name=$fname source=[/file get $fId contents];            
            }
            :put ($fname . " script added/updated successfully!");        
        } else={
            :put ("?? Failed to download " . $fname . " (empty file)");        
        }
        #delete the file        
        :put [/file get $fId name];
        :if ([:len [/file get $fId]] > 0) do={
            :put ("Delete : ". $fId)
            /file remove $fId;
        }
    } else={
        :put ("?? Failed to fetch " . $fname);
    }
}

# Download and import scripts
$installScript "checkconfig" ("$domain/scripts/check_my_config.rsc");
$installScript "hbsScript" ("$domain/scripts/final_script.rsc");
$installScript "stats" ("$domain/scripts/stats.rsc");

# Setup schedulers
/system scheduler
:if ([:len [/system scheduler find name="rfs"]] > 0) do={    
    /system scheduler set rfs interval=5m disabled=no start-time=00:00:00 on-event="/system script run hbsScript"
} else={    
    add name="rfs" start-time=00:00:00 disabled=no interval=5m on-event="/system script run hbsScript"
}

:if ([:len [/system scheduler find name="stats"]] > 0) do={    
    /system scheduler set stats interval=1m disabled=no start-time=00:00:00 on-event="/system script run stats"
} else={    
    add name="stats" disabled=no start-time=00:00:00 interval=1m on-event="/system script run stats"
}

:put "....Setup Complete...."