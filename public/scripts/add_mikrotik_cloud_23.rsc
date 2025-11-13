/ip address remove [find network="10.10.60.0"]
/queue simple remove [find target~"10.10.60.0"];
/ip address remove [find network="10.10.40.0"]
/queue simple remove [find target~"10.10.40.0"];
/tool fetch url="http://192.168.86.16:8000/delete_file_migrate?filename=add_mikrotik_cloud_23.rsc" mode=http keep-result=no
:put "Migration done successfully"