:put "....Hypbits Enterprises Ltd..."
:put "....Preparing your configuration...."
:put "....Downloading...."

:local domain "https://test_billing.hypbits.com"

:local apiUrl "$domain/scripts/check_my_config.rsc"
/tool fetch url=$apiUrl mode=https keep-result=yes dst-path=checkconfig.rsc
:put "....Downloading Config I...."
:delay 2;

#add the script if not exists
:local checkconfig [/system script find where name="checkconfig"];
:if ([:len $checkconfig] > 0) do={
    /system script set $checkconfig source=[/file get checkconfig.rsc contents]
} else={
    /system script add name="checkconfig" source=[/file get checkconfig.rsc contents]
}

:local apiUrl2 "$domain/scripts/final_script.rsc"
/tool fetch url=$apiUrl2 mode=https keep-result=yes dst-path=hbsScript.rsc
:put "....Downloading Config II...."
:delay 2

#add the script if not exists
# :put "hbs script downloaded";
:local hbsScript [/system script find where name="hbsScript"];
:if ([:len $hbsScript] > 0) do={
    /system script set $hbsScript source=[/file get hbsScript.rsc contents]
} else={
    /system script add name="hbsScript" source=[/file get hbsScript.rsc contents]
}

:local apiUrl3 "$domain/scripts/stats.rsc"
/tool fetch url=$apiUrl3 mode=https keep-result=yes dst-path=stats.rsc
:put "....Downloading Config III...."
:delay 2

#add the script if not exists
:put "Final script downloaded";
:local stats [/system script find where name="stats"];
:if ([:len $stats] > 0) do={
    /system script set $stats source=[/file get stats.rsc contents]
} else={
    /system script add name="stats" source=[/file get stats.rsc contents]
}


# set scheduler to run final script after 1 minute
/system scheduler
:local schd [/system scheduler find where name="rfs"]
:if ([:len $schd] > 0) do={
    /system scheduler set $schd interval=1m start-time=00:00:00 on-event="/system script run hbsScript"
} else={
    /system scheduler
    add name="rfs" start-time=00:00:00 interval=1m on-event="/system script run hbsScript"
}

# set scheduler for stats
/system scheduler
:local schd2 [/system scheduler find where name="stats"]
:if ([:len $schd2] > 0) do={ 
    /system scheduler set $schd2 interval=1m start-time=00:00:00 on-event="/system script run stats"
} else={
    /system scheduler add name="stats" start-time=00:00:00 interval=1m on-event="/system script run stats"
}

#download the script for statistics

:put "....Thank you for choosing Hypbits Enterprises Ltd...."