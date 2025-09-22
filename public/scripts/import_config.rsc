:put "....Hypbits Enterprises Ltd..."
:put "....Preparing your configuration...."
:put "....Downloading...."


:local apiUrl "http://192.168.86.16:8000/scripts/check_my_config.rsc"
/tool fetch url=$apiUrl mode=http keep-result=yes dst-path=checkconfig.rsc
:put "....Downloading Config I...."
:delay 2;

#add the script if not exists
:local checkconfig [/system script find where name="checkconfig"];
:if ([:len $checkconfig] > 0) do={
    /system script set $checkconfig source=[/file get checkconfig.rsc contents]
} else={
    /system script add name="checkconfig" source=[/file get checkconfig.rsc contents]
}

:local apiUrl2 "http://192.168.86.16:8000/scripts/final_script.rsc"
/tool fetch url=$apiUrl2 mode=http keep-result=yes dst-path=hbsScript.rsc
:put "....Downloading Config II...."
:delay 2

# set the script source
:local scriptSource [/file get hbsScript.rsc contents]

#add the script if not exists
:put "Final script downloaded";
:if ([:len $hbsScript] > 0) do={
    /system script set $hbsScript source=$scriptSource
} else={
    /system script add name="hbsScript" source=$scriptSource
}


# set scheduler to run final script after 1 minute
/system scheduler
:local schd [/system scheduler find where name="rfs"]
:if ([:len $schd] > 0) do={
    /system scheduler set $schd interval=1m start-time=startup on-event="/system script run hbsScript"
} else={
    /system scheduler
    add name="rfs" start-time=00:00:00 interval=1m on-event="/system script run hbsScript"
}

#import the router config to setup it up for the first time

:put "....Thank you for choosing Hypbits Enterprises Ltd...."