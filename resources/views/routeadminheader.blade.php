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
                <ul class="nav navbar-nav float-right">
                    <li>
                        <div class="dropdown-menu dropdown-menu-right">
                            <div class="arrow_box_right"><a class="dropdown-item" href="#"><i
                                        class="ft-book"></i> Read Notices</a><a class="dropdown-item"
                                    href="#"><i class="ft-check-square"></i> Mark all Read </a></div>
                        </div>
                    </li>
                    <li class="dropdown dropdown-user nav-item"><a class="dropdown-toggle nav-link dropdown-user-link"
                            href="#" data-toggle="dropdown">
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
