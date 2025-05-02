<!-- fixed-top-->
<nav class="header-navbar navbar-expand-md navbar  navbar-with-menu navbar-without-dd-arrow fixed-top navbar-semi-light">
    <div class="navbar-wrapper">
        <div class="navbar-container content ">
            <div class="collapse navbar-collapse show" id="navbar-mobile">
                <ul class="nav navbar-nav mr-auto float-left">
                    <li class="nav-item d-block d-md-none"><a class="nav-link nav-menu-main menu-toggle hidden-xs"
                            href="#"><i class="ft-menu"></i></a></li>
                    <li class="nav-item dropdown navbar-search">
                        <span class="text-light">Hello, {{ session('Usernames') }}</span>
                    </li>
                </ul>
                @if (!Session::has('Usernames'))
                    @php
                        header('Location: ' . URL::to('/Login'), true, 302);
                        exit();
                    @endphp
                @endif
                @php
                    $validated_users = 0;
                    if(Session::has("unvalidated_users")){
                        $validated_users = session("unvalidated_users");
                    }
                @endphp
                <ul class="nav navbar-nav float-right">
                    <li class="dropdown dropdown-notification nav-item"><a class="nav-link nav-link-label" href="#" data-toggle="dropdown" aria-expanded="false"><i class="ficon ft-bell {{$validated_users == "0" ? "" : "bell-shake"}}" id="notification-navbar-link"></i><span class="badge badge-pill badge-sm badge-danger badge-up badge-glow {{$validated_users == "0" ? "d-none" : ""}}">{{$validated_users}}</span></a>
                        <ul class="dropdown-menu dropdown-menu-media dropdown-menu-right">
                            <div class="arrow_box_right">
                                <li class="dropdown-menu-header">
                                    <h6 class="dropdown-header m-0"><span class="grey darken-2">Notifications</span></h6>
                                </li>
                                <div class="scrollable-container media-list w-100" style="overflow-y: auto">
                                    @if ($validated_users > 0)
                                        @php
                                            $collection_items = session("unvalidated");
                                            function timeAgo($datetime)
                                            {
                                                // Convert string to DateTime object
                                                $dt = DateTime::createFromFormat('YmdHis', $datetime);
                                                if (!$dt) {
                                                    return "Invalid date format.";
                                                }

                                                $now = new DateTime();
                                                $timestamp = $dt->getTimestamp();
                                                $nowTimestamp = $now->getTimestamp();
                                                $seconds = $nowTimestamp - $timestamp;

                                                if ($seconds < 0) {
                                                    return "In the future";
                                                }

                                                if ($seconds < 60) {
                                                    return $seconds . ' second' . ($seconds !== 1 ? 's' : '') . ' ago';
                                                }

                                                $minutes = floor($seconds / 60);
                                                if ($minutes < 60) {
                                                    return $minutes . ' minute' . ($minutes !== 1 ? 's' : '') . ' ago';
                                                }

                                                $hours = floor($minutes / 60);
                                                if ($hours < 24) {
                                                    return $hours . ' hour' . ($hours !== 1 ? 's' : '') . ' ago';
                                                }

                                                $days = floor($hours / 24);
                                                if ($days < 7) {
                                                    return $days . ' day' . ($days !== 1 ? 's' : '') . ' ago';
                                                }

                                                $weeks = floor($days / 7);
                                                if ($days < 30) {
                                                    return $weeks . ' week' . ($weeks !== 1 ? 's' : '') . ' ago';
                                                }

                                                $months = floor($days / 30);
                                                if ($months < 12) {
                                                    return $months . ' month' . ($months !== 1 ? 's' : '') . ' ago';
                                                }

                                                $years = floor($months / 12);
                                                return $years . ' year' . ($years !== 1 ? 's' : '') . ' ago';
                                            }
                                        @endphp
                                        @foreach ($collection_items as $item)
                                            <a href="/Clients/View/{{$item->client_id}}">
                                                <div class="media">
                                                    <div class="media-left align-self-center"><i class="ft-users info font-medium-4 mt-2"></i></div>
                                                    <div class="media-body">
                                                        <h6 class="media-heading info">Client ({{ucwords(strtolower($item->client_name))}}) Registered Successfully!</h6>
                                                        <p class="notification-text font-small-3 text-muted text-bold-600">{{ucwords(strtolower($item->client_name))}} was registered at {{date("H:i:sA", strtotime($item->clients_reg_date))}} on {{date("D dS M Y", strtotime($item->clients_reg_date))}}!</p><small>
                                                            <time class="media-meta text-muted" datetime="2015-06-11T18:29:20+08:00">{{timeAgo($item->clients_reg_date)}}</time></small>
                                                    </div>
                                                </div>
                                            </a>
                                        @endforeach
                                    @else
                                        <a href="javascript:void(0)">
                                            <div class="media">
                                                <div class="media-left align-self-center"><i class="ft-stop-circle danger font-medium-4 mt-2"></i></div>
                                                <div class="media-body">
                                                    <p class="notification-text font-small-3 text-muted text-bold-600">No Notifications Present!</p>
                                                </div>
                                            </div>
                                        </a>
                                    @endif
                                    {{-- <a href="javascript:void(0)">
                                        <div class="media">
                                            <div class="media-left align-self-center"><i class="ft-share info font-medium-4 mt-2"></i></div>
                                            <div class="media-body">
                                                <h6 class="media-heading info">New Order Received</h6>
                                                <p class="notification-text font-small-3 text-muted text-bold-600">Lorem ipsum dolor sit amet!</p><small>
                                                    <time class="media-meta text-muted" datetime="2015-06-11T18:29:20+08:00">3:30 PM</time></small>
                                            </div>
                                        </div>
                                    </a><a href="javascript:void(0)">
                                        <div class="media">
                                            <div class="media-left align-self-center"><i class="ft-save font-medium-4 mt-2 warning"></i></div>
                                            <div class="media-body">
                                                <h6 class="media-heading warning">New User Registered</h6>
                                                <p class="notification-text font-small-3 text-muted text-bold-600">Aliquam tincidunt mauris eu risus.</p><small>
                                                    <time class="media-meta text-muted" datetime="2015-06-11T18:29:20+08:00">10:05 AM</time></small>
                                            </div>
                                        </div>
                                    </a><a href="javascript:void(0)">
                                        <div class="media">
                                            <div class="media-left align-self-center"><i class="ft-repeat font-medium-4 mt-2 danger"></i></div>
                                            <div class="media-body">
                                                <h6 class="media-heading danger">New Purchase</h6>
                                                <p class="notification-text font-small-3 text-muted text-bold-600">Lorem ipsum dolor sit ametest?</p><small>
                                                    <time class="media-meta text-muted" datetime="2015-06-11T18:29:20+08:00">Yesterday</time></small>
                                            </div>
                                        </div>
                                    </a><a href="javascript:void(0)">
                                        <div class="media">
                                            <div class="media-left align-self-center"><i class="ft-shopping-cart font-medium-4 mt-2 primary"></i></div>
                                            <div class="media-body">
                                                <h6 class="media-heading primary">New Item In Your Cart</h6><small>
                                                    <time class="media-meta text-muted" datetime="2015-06-11T18:29:20+08:00">Last week</time></small>
                                            </div>
                                        </div>
                                    </a><a href="javascript:void(0)">
                                        <div class="media">
                                            <div class="media-left align-self-center"><i class="ft-heart font-medium-4 mt-2 info"></i></div>
                                            <div class="media-body">
                                                <h6 class="media-heading info">New Sale</h6><small>
                                                    <time class="media-meta text-muted" datetime="2015-06-11T18:29:20+08:00">Last month</time></small>
                                            </div>
                                        </div>
                                    </a> --}}
                                    <div class="ps__rail-x" style="left: 0px; bottom: -224px;">
                                        <div class="ps__thumb-x" tabindex="0" style="left: 0px; width: 0px;"></div>
                                    </div>
                                    <div class="ps__rail-y" style="top: 224px; right: 0px; height: 255px;">
                                        <div class="ps__thumb-y" tabindex="0" style="top: 120px; height: 135px;"></div>
                                    </div>
                                </div>
                                {{-- <li class="dropdown-menu-footer"><a class="dropdown-item info text-right pr-1" href="javascript:void(0)">Read all</a></li> --}}
                            </div>
                        </ul>
                    </li>
                    <li>
                        <div class="dropdown-menu dropdown-menu-right">
                            <div class="arrow_box_right"><a class="dropdown-item" href="#"><i
                                        class="ft-book"></i> Read Notices</a><a class="dropdown-item"
                                    href="#"><i class="ft-check-square"></i> Mark all Read </a></div>
                        </div>
                    </li>
                    <li class="dropdown dropdown-user nav-item"><a
                            class="dropdown-toggle nav-link dropdown-user-link" href="#" data-toggle="dropdown">
                            <span class="avatar avatar-online"> <img style="width: 100px; height: 40px;"
                                    src="{{ session('dp_locale') ? session('dp_locale') : '/theme-assets/images/pngegg.png' }}"
                                    alt="avatar"><i></i> </span></a>
                        <div class="dropdown-menu dropdown-menu-right">
                            <div class="arrow_box_right"><a class="dropdown-item" href="#"><span
                                        class="avatar avatar-online"><img style="width: 100px; height: 30px;"
                                            src="{{ session('dp_locale') ? session('dp_locale') : '/theme-assets/images/pngegg.png' }}"
                                            alt="avatar"><br><br><span
                                            class="user-name text-bold-700 ml-1">{{ session('Usernames') }}</span></span></a>
                                <div class="dropdown-divider"></div><a class="dropdown-item" href="/Accounts"><i
                                        class="ft-user"></i>Account & Settings</a>
                                {{-- <a class="dropdown-item" href="#"><i class="ft-mail"></i> My Inbox</a><a class="dropdown-item" href="#"><i class="ft-check-square"></i> Task</a><a class="dropdown-item" href="#"><i class="ft-message-square"></i> Chats</a> --}}
                                <div class="dropdown-divider"></div><a class="dropdown-item" href="/Login"><i
                                        class="ft-power"></i> Logout</a>
                            </div>
                        </div>
                    </li>
                </ul>
            </div>
        </div>
    </div>
</nav>
<!-- ////////////////////////////////////////////////////////////////////////////-->
@php
    $privilleged = session("priviledges");
    $priviledges = ($privilleged);
    function showOption($priviledges,$name){
        if (isJson($priviledges)) {
            $priviledges = json_decode($priviledges);
            for ($index=0; $index < count($priviledges); $index++) { 
                if ($priviledges[$index]->option == $name) {
                    if ($priviledges[$index]->view) {
                        return "";
                    }else {
                        return "d-none";
                    }
                }
            }
        }
        return "";
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
    $readonly_1 = readOnly($priviledges,"Transactions");
    $readonly_2 = readOnly($priviledges,"SMS");
    $readonly_3 = readOnly($priviledges,"My Clients");

    function isJson($string) {
        return ((is_string($string) &&
                (is_object(json_decode($string)) ||
                is_array(json_decode($string))))) ? true : false;
    }
@endphp

<div class="main-menu menu-fixed menu-light menu-accordion menu-shadow " data-scroll-to-active="true" data-img="/theme-assets/images/backgrounds/02.jpg">
    <div class="navbar-header" style="height: 120px">
        <ul class="nav navbar-nav flex-row p-0 justify-content-center align-item-center">
            <li class="nav-item mr-auto p-0 w-75 text-center" style="width: fit-content"><a class="navbar-brand "
                    href="/Dashboard">
                    <img class="w-100 mx-auto" height="100" alt="Your Logo Appear Here"
                        src="{{session("organization_logo") != null ? session("organization_logo") :'/theme-assets/images/logoplaceholder.svg'}}" />
                </a></li>
            <li class="nav-item d-md-none"><a class="nav-link close-navbar"><i class="ft-x"></i></a></li>
        </ul>
    </div>
    <div class="main-menu-content">
        <ul class="navigation navigation-main" id="main-menu-navigation" data-menu="menu-navigation">
            <li class="{{$active == "dashboard" ? "active" : ""}}"><a href="/Dashboard"><i class="ft-home"></i><span class="menu-title" data-i18n="">Dashboard</span></a>
            </li>
            <li class="{{showOption($priviledges,"My Clients") == "d-none" && showOption($priviledges,"Clients Issues") == "d-none" ? "d-none" : ""}} nav-item has-sub {{$active == "myclients" || $active == "client_issues" || $active == "quick_register" ? "active open" : ""}}"><a href="#"><i class="ft-users"></i><span class="menu-title" data-i18n="">Clients</span></a>
                <ul class="menu-content" style="">
                    <li class="{{showOption($priviledges,"My Clients")}} {{$active == "myclients" ? "active" : ""}} nav-item"><a href="/Clients"><span><i class="ft-user"></i> My Clients</span></a></li>
                    <li class="{{showOption($priviledges,"Clients Issues")}} {{$active == "client_issues" ? "active" : ""}} nav-item"><a href="/Client-Reports"><i class="ft-flag"></i> Client Issues</a></li>
                    <li class="{{showOption($priviledges,"Clients Issues")}} {{$active == "quick_register" ? "active" : ""}} nav-item"><a href="/Quick-Register"><i class="ft-cloud-lightning"></i> Quick Register</a></li>
                </ul>
            </li>
            <li class="{{(showOption($priviledges,"Transactions") == "d-none" && showOption($priviledges,"Expenses") == "d-none") ? "d-none" : ""}} {{$active == "transactions" || $active == "expenses" ? "active open" : ""}} nav-item has-sub"><a href="#"><i class="ft-activity"></i><span class="menu-title" data-i18n="">Accounts</span></a>
                <ul class="menu-content" style="">
                    <li class="{{showOption($priviledges,"Transactions")}} nav-item {{$active == "transactions" ? "active" : ""}}"><a href="/Transactions"><span><i class="ft-award"></i> Transactions</span></a>
                    </li>
                  <li class="{{showOption($priviledges,"Expenses")}} nav-item {{$active == "expenses" ? "active" : ""}}"><a href="/Expenses"><i class="ft-bar-chart-2"></i> Expenses</a></li>
                </ul>
            </li>
            <li class="{{showOption($priviledges,"My Routers")}} nav-item {{$active == "myrouters" ? "active" : ""}}"><a href="/Routers"><i class="ft-layers"></i><span class="menu-title" data-i18n="">My Routers</span></a>
            </li>
            <li class="{{showOption($priviledges,"SMS")}} nav-item {{$active == "sms" ? "active" : ""}}"><a href="/sms"><i class="ft-mail"></i><span class="menu-title" data-i18n="">SMS</span></a>
            </li>
            <li class="{{showOption($priviledges,"Account and Profile")}} nav-item {{$active == "account_and_profile" ? "active" : ""}}"><a href="/Accounts"><i class="ft-lock"></i><span class="menu-title" data-i18n="">Account and Profile</span></a>
            </li>
        </ul>
    </div>
    <!-- <a class="btn btn-danger btn-block btn-glow btn-upgrade-pro mx-1" href="https://themeselection.com/products/chameleon-admin-modern-bootstrap-webapp-dashboard-html-template-ui-kit/" target="_blank">Download PRO!</a> -->
    <div class="navigation-background">
    </div>
</div>