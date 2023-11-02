<?php require_once('api_mt_include2.php'); ?>
<?php
$ipRouteros = "10.10.10.1";  // tu RouterOS.
$Username = "mukiritoapi";
$Pass = "Kamasutra2020";
$api_puerto = "8728";

$API = new routeros_api();
$API->debug = false;
if ($API->connect($ipRouteros, $Username, $Pass)) {
       $comment = "Hillary disabled on 12th june 2020";
       $src_address = "10.10.30.0/24";
       $action = "drop";
       $chain = "forward";
       $disabled = "true";
       $API->comm("/ip/firewall/filter/add", array("chain" => "$chain", "src-address" => "$src_address", "action" => "$action","comment"=>"$comment","disabled" => "$disabled"));
       
       

       
       
       //  $API->write("/ip/firewall/filter/print");
       // $results = $API->read();
       // foreach ($results as $key => $value) {
       //        // key and value
       //        foreach ($value as $keys => $values) {
       //               echo $keys." - ".$values." ,, ";
       //        }
       //        echo " <br>";
       // }
       // $API->write("/ip/firewall/filter/remove",false);
       // $API->write("=.id=B");
       // $results = $API->read();
       // var_dump($results);
       //    write and read comands
       // lets add an ip address
       // $API->comm("/ip/address/disable", array("address" => "10.10.22.1/24", "network" => "10.10.22.0", "interface" => "ether2","comment"=>"Hillary Test Address"));
       // $API->comm("/ip/address/disable", array("find address = ['10.10.22.1/24']"));
       // $API->write('/ip/address/disable', false);         //this is the basic command; false says the there are more lines to the command.
       // $API->write('=numbers=3');
       // $dis_address = "10.10.10.1/24";
       // $API->write('/ip/address/print');
       // $ips = $API->read();
       // // loop throug netwrok addresses
       // $networks = $ips[0];
       // foreach($ips as $keys => $values){
       //        $count = 0;
       //        $found = 0;
       //        foreach ($values as $key => $value) {
       //               $ids = 0;
       //               if ($key == "row.id") {
       //                      $ids = substr($value,1);
       //               }
       //               if ($key == "address") {
       //                      // check if the address is the same as the one given
       //                      if ($dis_address == $value) {
       //                             // disable the address
       //                             $API->comm("/ip/address/disable", array("$ids"));
       //                             $found = 1;
       //                             echo "Disabled ".$dis_address." counter ".$ids;
       //                      }
       //               }
       //               $count++;
       //        }
       //        if ($found == 1) {
       //               break;
       //        }
       //        echo "<br>";
       // }
} else {
       echo "<font color='#ff0000'>La conexion ha fallado. Verifique si el Api esta activo.</font>";
}
$API->disconnect();
?>