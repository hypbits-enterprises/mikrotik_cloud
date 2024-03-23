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

<style>
    .bg-login-image {
        background: url("/theme-assets/images/backgrounds/01.jpg");
        background-position: center;
        background-size: cover;
    }
</style>
<body>

    <!-- create the icons that customers will go to -->
    <div class="container">
        <!-- Outer Row -->
        <div class="row col-md-8 mx-auto justify-content-center align-content-center">
            <div class="col-xl-10 col-lg-12 col-md-9">
                <div class="card o-hidden border-0 shadow-lg my-5">
                    <div class="card-body p-0">
                        <!-- Nested Row within Card Body -->
                        <div class="row">
                            <div class="col-lg-12">
                                <div class="p-5">
                                    <div class="text-center">
                                        <div class="d-flex flex-column align-content-center container text-center">
                                            <a href="/Login" style="width: 30%;margin: auto">
                                                <img class="w-100" src="/theme-assets/images/logo2.jpeg" alt="" srcset="">
                                            </a>
                                        </div>
                                        <h1 class="h4 text-gray-900 my-2">Login!</h1>
                                        @php
                                            Session::forget('Usernames');
                                        @endphp
                                    </div>
                                    <form class="user" action="{{url()->route("process_login")}}" method="POST">
                                        @csrf
                                        <div class="form-group">
                                            @if(session('error'))
                                                <p class="text-danger text-bolder">{{session('error')}}</p>
                                            @endif
                                        </div>
                                        <div class="form-group">
                                            <select name="send_code" id="send_code" class="form-control" required>
                                                <option value="" hidden >How to recieve code!</option>
                                                <option value="SMS">Send SMS</option>
                                                <option selected value="EMAILS">Send Email</option>
                                            </select>
                                        </div>
                                        <div class="form-group">
                                            <select name="authority" id="authority" class="form-control d-none" required>
                                                <option value="" hidden >Select an option to proceed!</option>
                                                <option selected value="admin">Administrator</option>
                                                <option value="client">Clients</option>
                                            </select>
                                        </div>
                                        <div class="form-group">
                                            <input type="text" name="emails" class="form-control form-control-user text-center"
                                                id="exampleInputEmail" aria-describedby="emailHelp"
                                                placeholder="Enter Username . . ." required>
                                        </div>
                                        <div class="form-group">
                                            <input type="password" name="password" class="form-control form-control-user  text-center"
                                                id="exampleInputPassword" placeholder="Password . . ." required>
                                        </div>
                                        <button  type="submit" id="login-btn" class="btn btn-primary btn-user btn-block">
                                            Login
                                        </button>
                                        <p class="text-left text-xxs text-bolder pt-2" id="errHandler"></p>
                                    </form>
                                    <div class="text-center">
                                        {{-- <a href="/" class="secondary">Return to Home Page...</a> --}}
                                    </div>
                                    <hr>
                                    <div class="text-center">
                                        <h6>&COPY; Copyright HypBits {{date("Y");}}</h6>
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
<script>
    var authority = document.getElementById("authority");
    var exampleInputEmail = document.getElementById("exampleInputEmail");
    var exampleInputPassword = document.getElementById("exampleInputPassword");
    var login_btn = document.getElementById("login-btn");
    login_btn.onclick = function () {
        var err = 0;
        err+=checkBlank("authority");
        err+=checkBlank("exampleInputEmail");
        err+=checkBlank("exampleInputPassword");
        if (err == 0) {
            setTimeout(() => {
                login_btn.disabled = true;
            }, 100);
        }
    }
    function checkBlank(id) {
        var elemts = document.getElementById(id);
        if (elemts.value.trim().length > 0) {
            return 0;
        }
        return 1;
    }
</script>
</body>

</html>