
<!DOCTYPE html>
<html class="loading" lang="en" data-textdirection="ltr">
<!-- BEGIN: Head-->

<head>
    <meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, user-scalable=0, minimal-ui">
    <meta name="description" content="Chameleon Admin is a modern Bootstrap 4 webapp &amp; admin dashboard html template with a large number of components, elegant design, clean and organized code.">
    <meta name="keywords" content="admin template, Chameleon admin template, dashboard template, gradient admin template, responsive admin template, webapp, eCommerce dashboard, analytic dashboard">
    <meta name="author" content="ThemeSelect">
    <title>Error 404 - Hypbits Billing System</title>
    <link rel="apple-touch-icon" href="/theme-assets/images/logo2.jpeg">
    <link rel="shortcut icon" href="/theme-assets/images/logo2.jpeg">
    <link href="https://fonts.googleapis.com/css?family=Muli:300,300i,400,400i,600,600i,700,700i%7CComfortaa:300,400,700" rel="stylesheet">

    {{-- CSS COMPONENT --}}
    <x-css></x-css>
    <!-- END: Custom CSS-->

</head>
<!-- END: Head-->
@php
    $motivations = 
    [
        "🌟 Every great achievement begins with the decision to try.",
        "💪 You’re stronger than you think and braver than you feel.",
        "⏳ Progress is progress, no matter how small.",
        "🚀 Don’t wait for opportunity. Create it.",
        "🔥 Push yourself because no one else is going to do it for you.",
        "🌈 Difficult roads often lead to beautiful destinations.",
        "🧠 Your only limit is your mindset.",
        "🛠️ Dream big. Start small. Act now.",
        "📈 Every setback is a setup for a comeback.",
        "⛰️ Success doesn’t come from what you do occasionally. It comes from what you do consistently.",
        "🌱 Believe in yourself even when no one else does.",
        "🦋 You don’t have to be perfect to be amazing.",
        "🕊️ Let your courage be bigger than your fear.",
        "🛤️ Keep going. Your future self will thank you.",
        "🌞 Rise up, start fresh, and see the bright opportunity in each new day.",
        "🧗 Fall seven times, stand up eight.",
        "🎯 Stay focused. Stay fighting. Stay strong.",
        "📚 Learn from yesterday, live for today, hope for tomorrow.",
        "💡 The best way to get things done is to begin.",
        "🌻 Don’t quit. Sometimes the last key opens the lock."
    ]

    
@endphp
<!-- BEGIN: Body-->

<body class="vertical-layout vertical-menu 1-column  bg-gradient-directional-danger blank-page blank-page" data-open="click" data-menu="vertical-menu" data-color="bg-gradient-x-purple-blue" data-col="1-column">
    <!-- BEGIN: Content-->
    <div class="app-content content">
        <div class="content-wrapper">
            <div class="content-wrapper-before"></div>
            <div class="content-header row">
            </div>
            <div class="content-body">
                <section class="flexbox-container bg-hexagons-danger">
                    <div class="col-12 d-flex align-items-center justify-content-center">
                        <div class="col-lg-4 col-md-6 col-10 p-0">
                            <div class="card-header bg-transparent border-0">
                                <h2 class="error-code text-center mb-2 white">404</h2>
                                <h3 class="text-center white">Ooops! Link is missing you.</h3>
                            </div>
                            <div class="card-content">
                                <div class="row py-2 text-center">
                                    <div class="col-12">
                                        <a href="{{session("auth") == "admin" ? '/Dashboard' : '/ClientDashboard'}}" class="btn btn-white danger box-shadow-4"><i class="ft-home"></i> Back to Home</a>
                                    </div>

                                </div>
                            </div>
                            <div class="card-footer bg-transparent">
                                <div class="row">
                                    <p class="text-muted text-center col-12 py-1 white">© <span class="year"></span> <a href="https://hypbits.com/" class="white text-bold-700">Hypbits Enterprises </a> : {{$motivations[rand(0,19)]}} </p>

                                    <div class="col-12 text-center">
                                        <a href="#" class="font-large-1 white p-2 "><span class="ft-facebook"></span></a>
                                        <a href="#" class="font-large-1 white "><span class="ft-twitter"></span></a>
                                        <a href="#" class="font-large-1 white p-2"><span class="ft-instagram"></span></a>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </section>

            </div>
        </div>
    </div>
    <!-- END: Content-->


    <!-- BEGIN: Vendor JS-->
    <script src="app-assets/vendors/js/vendors.min.js" type="text/javascript"></script>
    <!-- BEGIN Vendor JS-->

    <!-- BEGIN: Page Vendor JS-->
    <script src="app-assets/vendors/js/forms/validation/jqBootstrapValidation.js" type="text/javascript"></script>
    <!-- END: Page Vendor JS-->

    <!-- BEGIN: Theme JS-->
    <script src="app-assets/js/core/app-menu.js" type="text/javascript"></script>
    <script src="app-assets/js/core/app.js" type="text/javascript"></script>
    <!-- END: Theme JS-->

    <!-- BEGIN: Page JS-->
    <script src="app-assets/js/scripts/forms/form-login-register.js" type="text/javascript"></script>
    <!-- END: Page JS-->

</body>
<!-- END: Body-->

</html>