:put "....Hypbits Enterprises Ltd..."
:put "....Preparing your configuration...."
:put "....Downloading...."
:local environments [/system/script/environment find];
:foreach i in=[$environments] do={
    /system/script/environment remove $i
}

# :local domain "http://192.168.86.16:8000"
:local domain "https://billing.hypbits.com"

:local apiUrl "$domain/scripts/check_config_pt1.rsc"
/tool fetch url=$apiUrl mode=https keep-result=yes dst-path=checkconfigpt1.rsc
:put "....Downloading Config I PART I...."
:delay 2;

#----------------------ADD THE CHECK CONFIG STATS--------------------------------
# PART 1
:local checkconfig [/system script find where name="checkconfig"];
:if ([:len $checkconfig] > 0) do={
    /system script set $checkconfig source=[/file get checkconfigpt1.rsc contents]
} else={
    /system script add name="checkconfig" source=[/file get checkconfigpt1.rsc contents]
}
/file remove checkconfigpt1.rsc

# ADD PART 2
:set $apiUrl "$domain/scripts/check_config_pt2.rsc"
/tool fetch url=$apiUrl mode=https keep-result=yes dst-path=checkconfigpt2.rsc
:put "....Downloading Config I PART II...."
:delay 2;
:local checkconfig [/system script find where name="checkconfig"];
:if ([:len $checkconfig] > 0) do={
    /system script set $checkconfig source=([/system script get $checkconfig source]."\n".[/file get checkconfigpt2.rsc contents])
    /file remove checkconfigpt2.rsc
}

# FINAL SCRIPT 5 PARTS
:local apiUrl2 "$domain/scripts/final_script_pt1.rsc"
/tool fetch url=$apiUrl2 mode=https keep-result=yes dst-path=final_script_pt1.rsc
:put "....Downloading Config II PART 1...."
:delay 2


#--------------------ADD THE MAIN ACTIVATE AND DEACTIVATE SCRIPT-----------------------
# PART 1
:local hbsScript [/system script find where name="hbsScript"];
:if ([:len $hbsScript] > 0) do={
    /system script set $hbsScript source=[/file get final_script_pt1.rsc contents]
} else={
    /system script add name="hbsScript" source=[/file get final_script_pt1.rsc contents]
}
/file remove final_script_pt1.rsc

# PART 2
:set apiUrl2 "$domain/scripts/final_script_pt2.rsc"
/tool fetch url=$apiUrl2 mode=https keep-result=yes dst-path=final_script_pt2.rsc
:put "....Downloading Config II PART II...."
:delay 2
:set hbsScript [/system script find where name="hbsScript"];
:if ([:len $hbsScript] > 0) do={
    /system script set $hbsScript source=([/system script get $hbsScript source]."\n".[/file get final_script_pt2.rsc contents])
    /file remove final_script_pt2.rsc
}

# PART 3
:set apiUrl2 "$domain/scripts/final_script_pt3.rsc"
/tool fetch url=$apiUrl2 mode=https keep-result=yes dst-path=final_script_pt3.rsc
:put "....Downloading Config II PART III...."
:delay 2
:set hbsScript [/system script find where name="hbsScript"];
:if ([:len $hbsScript] > 0) do={
    /system script set $hbsScript source=([/system script get $hbsScript source]."\n".[/file get final_script_pt3.rsc contents])
    /file remove final_script_pt3.rsc
}

# PART 4
:set apiUrl2 "$domain/scripts/final_script_pt4.rsc"
/tool fetch url=$apiUrl2 mode=https keep-result=yes dst-path=final_script_pt4.rsc
:put "....Downloading Config II PART IV...."
:delay 2
:set hbsScript [/system script find where name="hbsScript"];
:if ([:len $hbsScript] > 0) do={
    /system script set $hbsScript source=([/system script get $hbsScript source]."\n".[/file get final_script_pt4.rsc contents])
    /file remove final_script_pt4.rsc
}

# PART 5
:set apiUrl2 "$domain/scripts/final_script_pt5.rsc"
/tool fetch url=$apiUrl2 mode=https keep-result=yes dst-path=final_script_pt5.rsc
:put "....Downloading Config II PART IV...."
:delay 2
:set hbsScript [/system script find where name="hbsScript"];
:if ([:len $hbsScript] > 0) do={
    /system script set $hbsScript source=([/system script get $hbsScript source]."\n".[/file get final_script_pt5.rsc contents])
    /file remove final_script_pt5.rsc
}

# ---------------------------------DOWNLOAD STATS----------------------------
:local apiUrl3 "$domain/scripts/stats_pt1.rsc"
/tool fetch url=$apiUrl3 mode=https keep-result=yes dst-path=stats_pt1.rsc
:put "....Downloading Config III PART 1...."
:delay 2

#add the script if not exists
:put "Final script downloaded";
:local stats [/system script find where name="stats"];
:if ([:len $stats] > 0) do={
    /system script set $stats source=[/file get stats_pt1.rsc contents]
} else={
    /system script add name="stats" source=[/file get stats_pt1.rsc contents]
}
/file remove stats_pt1.rsc

:set apiUrl3 "$domain/scripts/stats_pt2.rsc"
/tool fetch url=$apiUrl3 mode=https keep-result=yes dst-path=stats_pt2.rsc
:put "....Downloading Config III PART II...."
:delay 2
:set stats [/system script find where name="stats"];
:if ([:len $stats] > 0) do={
    /system script set $stats source=([/system script get $stats source]."\n".[/file get stats_pt2.rsc contents])
    /file remove stats_pt2.rsc
}

:set apiUrl3 "$domain/scripts/stats_pt3.rsc"
/tool fetch url=$apiUrl3 mode=https keep-result=yes dst-path=stats_pt3.rsc
:put "....Downloading Config III PART III...."
:delay 2
:set stats [/system script find where name="stats"];
:if ([:len $stats] > 0) do={
    /system script set $stats source=([/system script get $stats source]."\n".[/file get stats_pt3.rsc contents])
    /file remove stats_pt3.rsc
}


# ------------------------------- SET SCHEDULER ----------------------------------------


# set scheduler to run final script after 1 minute
/system scheduler
:local schd [/system scheduler find where name="rfs"]
:if ([:len $schd] > 0) do={
    /system scheduler set $schd interval=5m disabled="no" start-time=00:00:00 on-event="/system script run hbsScript"
} else={
    /system scheduler
    add name="rfs" start-time=00:00:00 disabled="no" interval=5m on-event="/system script run hbsScript"
}

# set scheduler for stats
/system scheduler
:local schd2 [/system scheduler find where name="stats"]
:if ([:len $schd2] > 0) do={ 
    /system scheduler set $schd2 interval=1m disabled="no" start-time=00:00:00 on-event="/system script run stats"
} else={
    /system scheduler add name="stats" disabled="no" start-time=00:00:00 interval=1m on-event="/system script run stats"
}

#download the script for statistics
/file remove import_config.rsc
/system script remove [/system script find name="run_import_script"]
:put "....Thank you for choosing Hypbits Enterprises Ltd...."