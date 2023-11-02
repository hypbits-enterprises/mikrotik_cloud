<!DOCTYPE html>
<html class="loading" lang="en" data-textdirection="ltr">

<head>
    <meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, user-scalable=0, minimal-ui">
    <meta name="description" content="My ISP is the number one kenyan webserver software that helps you manage and monitor your webserver.">
    <meta name="keywords" content="admin template, Client template, dashboard template, gradient admin template, responsive client template, webapp, eCommerce dashboard, analytic dashboard">
    <meta name="author" content="ThemeSelect">
    <title>Hypbits</title>
    <link rel="apple-touch-icon" href="/theme-assets/images/logo2.jpeg">
    <link rel="shortcut icon" href="/theme-assets/images/logo2.jpeg">
    <link href="https://fonts.googleapis.com/css?family=Muli:300,300i,400,400i,600,600i,700,700i%7CComfortaa:300,400,700" rel="stylesheet">
    <link href="https://maxcdn.icons8.com/fonts/line-awesome/1.1/css/line-awesome.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <!-- BEGIN VENDOR CSS-->
    <link rel="stylesheet" type="text/css" href="/theme-assets/css/vendors.css">
    <link rel="stylesheet" type="text/css" href="/theme-assets/vendors/css/charts/chartist.css">
    <!-- END VENDOR CSS-->
    <!-- BEGIN CHAMELEON  CSS-->
    <link rel="stylesheet" type="text/css" href="/theme-assets/css/app-lite.css">
    <!-- END CHAMELEON  CSS-->
    <!-- BEGIN Page Level CSS-->
    <link rel="stylesheet" type="text/css" href="/theme-assets/css/core/menu/menu-types/vertical-menu.css">
    <link rel="stylesheet" type="text/css" href="/theme-assets/css/core/colors/palette-gradient.css">
    <link rel="stylesheet" type="text/css" href="/theme-assets/css/pages/dashboard-ecommerce.css">
    <!-- END Page Level CSS-->
    <!-- BEGIN Custom CSS-->
    <!-- END Custom CSS-->
</head>

<body>
    @php
        date_default_timezone_set('Africa/Nairobi');
        $privilleged = session("priviledges");
        $priviledges = ($privilleged);
        function showOption($priviledges,$name){
            if (isJson($priviledges)) {
                $priviledges = json_decode($priviledges);
                for ($index=0; $index < count($priviledges); $index++) { 
                    if ($priviledges[$index]->option == $name) {
                        if ($priviledges[$index]->view) {
                            return "";
                        }
                    }
                }
            }
            return "hide";
        }
        function readOnly($priviledges,$name){
            if (isJson($priviledges)){
                $priviledges = json_decode($priviledges);
                for ($index=0; $index < count($priviledges); $index++) { 
                    if ($priviledges[$index]->option == $name) {
                        if ($priviledges[$index]->readonly) {
                            return "disabled";
                        }
                    }
                }
            }
            return "";
        }
        // get the readonly value
        $readonly = readOnly($priviledges,"Expenses");

        function isJson($string) {
            return ((is_string($string) &&
                    (is_object(json_decode($string)) ||
                    is_array(json_decode($string))))) ? true : false;
        }
    @endphp
    @php
        $myfile = fopen("logs/log.txt", "a") or die("Unable to open file!");
        $date = date("dS M Y (H:i:sa)");
        $txt = "{$date}: New Client accessed the main page using ip addr: ".$_SERVER['REMOTE_ADDR']."\n";
        fwrite($myfile, $txt);
        fclose($myfile);
    @endphp
    <!-- create the icons that customers will go to -->
    <div class="">
        <nav class="navbar navbar-light bg-light">
            <span class="nav-item mr-auto"  style="width: 200px"><a class="navbar-brand " href="#"><img class="brand-logo w-100 mb-1 "
                        alt="Chameleon admin logo" src="/theme-assets/images/logo.jpeg" />
                </a></span>
        </nav>
        <div class="container my-2">
            <h4 class="text-bold">Select your company profile to proceed</h4>
            <div class="row shadow-lg my-2">
                <div class="d-flex flex-column align-content-center pull-up container bg-secondary col-md-3 text-center">
                    <a href="/Login" style="width: 70%;margin: auto">
                        <img class="w-100" src="/theme-assets/images/logo2.jpeg" alt="" srcset="">
                    </a>
                </div>
                <div class="container bg-white col-md-9 p-1">
                    <h4>Hypbits Enterprises limited:</h4>
                    <p>We supply internet across Mombasa town and its surrounding. Give us a call for internet and other computers services on +254717748569 </p>
                    <div class="card-footer">
                        <a href="/Login" class="text-sm text-primary">Proceed to your Dashboard</a>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <!-- ////////////////////////// -->

    <!-- BEGIN VENDOR JS-->
    <script src="theme-assets/vendors/js/vendors.min.js" type="text/javascript"></script>
    <!-- BEGIN VENDOR JS-->
    <!-- BEGIN PAGE VENDOR JS-->
    
    <!-- END PAGE VENDOR JS-->
    <!-- BEGIN CHAMELEON  JS-->
    <script src="theme-assets/js/core/app-menu-lite.js" type="text/javascript"></script>
    <script src="theme-assets/js/core/app-lite.js" type="text/javascript"></script>
    <!-- END CHAMELEON  JS-->
    <!-- BEGIN PAGE LEVEL JS-->
    
    <!-- END PAGE LEVEL JS-->
</body>

</html>