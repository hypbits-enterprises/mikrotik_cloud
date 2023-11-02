<!DOCTYPE html>
<html class="loading" lang="en" data-textdirection="ltr">

<head>
    <meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, user-scalable=0, minimal-ui">
    <meta name="description" content="My ISP is the number one kenyan webserver software that helps you manage and monitor your webserver.">
    <meta name="keywords" content="admin template, Client template, dashboard template, gradient admin template, responsive client template, webapp, eCommerce dashboard, analytic dashboard">
    <meta name="author" content="ThemeSelect">
    <title>Hypbits - Login</title>
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
                        return "";
                    }
                }
            }
        }
        return "disabled";
    }
    function isJson($string) {
        return ((is_string($string) &&
                (is_object(json_decode($string)) ||
                is_array(json_decode($string))))) ? true : false;
    }
@endphp

<style>
    .bg-login-image {
        background: url("/theme-assets/images/backgrounds/01.jpg");
        background-position: center;
        background-size: cover;
    }
</style>
<body>

    <!-- create the icons that customers will go to -->
    <div class="container align-content-center ">
        <!-- Outer Row -->
        <div class="row col-md-8 justify-content-center align-content-center mx-auto">
            <div class="col-xl-10 col-lg-12 col-md-9">
                <div class="card o-hidden border-0 shadow-lg my-5">
                    <div class="card-body p-0">
                        <!-- Nested Row within Card Body -->
                        <div class="row">
                            {{-- <div class="col-lg-6 d-none d-lg-block bg-secondary bg-login-image">
                            </div> --}}
                            <div class="col-lg-12">
                                <div class="p-5">
                                    <div class="text-center">
                                        <h1 class="h4 text-gray-900 mb-4">Verify Your Account!</h1>
                                    </div>
                                    <div class="text-center">
                                        <p class="text-success">
                                            @if(session('contacts'))
                                                <span>We have sent you a verification code to {{session('contacts')}}</span>
                                            @endif.</p>
                                    </div>
                                    <form class="user" action="/verifycode" method="POST">
                                        @csrf
                                        <div class="form-group">
                                            @if(session('error'))
                                                <p class="text-danger text-bolder">{{session('error')}}</p>
                                            @endif
                                            
                                        </div>
                                        <div class="form-group">
                                            <input type="number" name="verification_code" class="form-control form-control-user text-center"
                                                placeholder="Enter verification code . . ." required>
                                        </div>
                                        <button type="submit" id="login-btn" class="btn btn-primary btn-user btn-block">
                                            <span id="spinners" class="invisible spinner-grow text-success spinner-grow-sm"></span>
                                            Submit
                                        </button>
                                        <p class="text-left text-xxs text-bolder pt-2" id="errHandler"></p>
                                    </form>
                                    <div class="text-center">
                                        <a href="/Login" class="secondary">Return to Login Page...</a>
                                    </div>
                                    <hr>
                                    <div class="text-center">
                                        <h6>&COPY; Copyright {{date("Y");}}</h6>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <!-- ////////////////////////// -->
</body>
</html>