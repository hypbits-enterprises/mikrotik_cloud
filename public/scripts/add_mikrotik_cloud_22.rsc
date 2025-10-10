/ip address remove [find network="10.10.80.0"]
/queue simple remove [find target~"10.10.80.0"];
/ppp secret remove [find name="HYP016"]
/ppp secret remove [find name="HYP015"]
/ip address remove [find network="10.10.70.0"]
/queue simple remove [find target~"10.10.70.0"];
/tool fetch url="http://192.168.86.16:8000/delete_file_migrate?filename=add_mikrotik_cloud_22.rsc" mode=http keep-result=no
:put "Migration done successfully"