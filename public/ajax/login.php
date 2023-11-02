<?php
session_start();
/**ALlow CORS with this file */
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Credentials: true ");
header("Access-Control-Allow-Methods: OPTIONS, GET, POST");
header("Access-Control-Allow-Headers: Content-Type, Depth, User-Agent, X-File-Size, X-Requested-With, If-Modified-Since, X-File-Name, Cache-Control");

// include the connection

if ($_SERVER['REQUEST_METHOD'] == "POST") {
    //get the transaction for today
    if (isset($_POST['transactions'])) {

    }
}