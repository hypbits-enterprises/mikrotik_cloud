<?php
    $dbname = 'my_isp';
    $hostname = 'localhost';
    $dbusername = 'root';
    $dbpassword = '';
    // $dbpassword = '2000hILARY';

    $conn = new mysqli($hostname,$dbusername,$dbpassword,$dbname);
    // Check connection
    if (mysqli_connect_errno()) {
        die("Failed to connect to MySQL: " . mysqli_connect_error());
        exit();
    } 
?>