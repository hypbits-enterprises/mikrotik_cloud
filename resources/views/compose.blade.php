<!DOCTYPE html>
<html class="loading" lang="en" data-textdirection="ltr">

<head>
    <meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, user-scalable=0, minimal-ui">
    <meta name="description"
        content="My ISP is the number one kenyan webserver software that helps you manage and monitor your webserver.">
    <meta name="keywords"
        content="admin template, Client template, dashboard template, gradient admin template, responsive client template, webapp, eCommerce dashboard, analytic dashboard">
    <meta name="author" content="ThemeSelect">
    <title>Hypbits - {{ isset($messages) ? 'Resend Message' : 'Write Message' }}</title>
    <link rel="apple-touch-icon" href="/theme-assets/images/logo2.jpeg">
    <link rel="shortcut icon" href="/theme-assets/images/logo2.jpeg">

    {{-- CSS COMPONENT --}}
    <x-css></x-css>

    
</head>
<style>
    .hide{
        display: none;
    }
</style>


<style>
    /*the container must be positioned relative:*/
    .autocomplete {
        position: relative;
        display: inline-block;
        width: 100%
    }
    
    .autocomplete-items {
        position: absolute;
        border: 1px solid #d4d4d4;
        border-bottom: none;
        border-top: none;
        z-index: 99;
        /*position the autocomplete items to be the same width as the container:*/
        top: 100%;
        left: 0;
        right: 0;
        max-height: 250; /* Set the maximum height */
        overflow-y: auto; /* Enable vertical scrolling */
    }

    .autocomplete-items div {
        padding: 10px;
        cursor: pointer;
        background-color: #fff;
        border-bottom: 1px solid #d4d4d4;
    }

    /*when hovering an item:*/
    .autocomplete-items div:hover {
        background-color: #e9e9e9;
    }

    /*when navigating through the items using the arrow keys:*/
    .autocomplete-active {
        background-color: DodgerBlue !important;
        color: #ffffff;
    }

</style>

<body class="vertical-layout vertical-menu 2-columns  menu-expanded fixed-navbar" data-open="click"
    data-menu="vertical-menu" data-color="bg-chartbg" data-col="2-columns">
    
    <x-menu active="sms"></x-menu>
    @php
        $priviledges = session("priviledges");
        $readonly = readOnly($priviledges,"SMS");
    @endphp

    <div class="app-content content">
        <div class="content-wrapper">
            <div class="content-wrapper-before"></div>
            <div class="content-header row">
                <div class="content-header-left col-md-4 col-12 mb-2">
                    <h3 class="content-header-title">{{ isset($messages) ? 'Resend Message' : 'Write Message' }}</h3>
                </div>
                <div class="content-header-right col-md-8 col-12">
                    <div class="breadcrumbs-top float-md-right">
                        <div class="breadcrumb-wrapper mr-1">
                            <ol class="breadcrumb">
                                <li class="breadcrumb-item"><a href="/Dashboard">Dashboard</a>
                                </li>
                                <li class="breadcrumb-item"><a href="/Transactions">SMS</a>
                                </li>
                                <li class="breadcrumb-item">{{ isset($messages) ? 'Resend Message' : 'Write Message' }}
                                </li>
                            </ol>
                        </div>
                    </div>
                </div>
            </div>
            <div class="content-body">
                <!-- Basic Tables start -->
                <div class="row">
                    <div class="col-12">
                        <div class="card">
                            <div class="card-header">
                                <h4 class="card-title">{{ isset($messages) ? 'Resend Message' : 'Write Message' }}
                                </h4>
                                <a class="heading-elements-toggle"><i class="la la-ellipsis-v font-medium-3"></i></a>
                                <div class="heading-elements">
                                    <ul class="list-inline mb-0">
                                        <li><a data-action="collapse"><i class="ft-minus"></i></a></li>
                                        {{-- <li><a data-action="reload"><i class="ft-rotate-cw"></i></a></li> --}}
                                        <li><a data-action="expand"><i class="ft-maximize"></i></a></li>
                                        <!-- <li><a data-action="close"><i class="ft-x"></i></a></li> -->
                                    </ul>
                                </div>
                            </div>
                            <div class="card-content collapse show">
                                <div class="card-body">
                                    @if (session('success'))
                                        <p class="text-success">{{ session('success') }}</p>
                                    @endif
                                    <a href="/sms" class="btn btn-infor"><i class="fas fa-arrow-left"></i>
                                        Back to list</a>
                                    @if ($errors->any())
                                        <h6 style="color: orangered">Errors</h6>
                                        <ul class="text-danger" style="color: orangered">
                                            @foreach ($errors->all() as $item)
                                                <li>{{ $item }}</li>
                                            @endforeach
                                        </ul>
                                    @endif

                                    @if (session('error_sms'))
                                        <p class="text-danger">{{ session('error_sms') }}</p>
                                    @endif
                                    @if (session('message_success'))
                                        <p class="text-success">{{ session('message_success') }}</p>
                                    @endif

                                </div>
                                <div class="card-body">
                                    {{-- write a message --}}
                                    <form class="row" method="POST" action="/sendsms">
                                        @csrf
                                        <div class="col-md-6">
                                            <label for="select_recipient" class="form-control-label">Select
                                                Recipient</label>
                                            <select name="select_recipient" id="select_recipient"
                                                class="form-control" required>
                                                <option value="" hidden>Select an option</option>
                                                <option {{ isset($messages) ? 'selected' : '' }} value="1">Insert
                                                    Number</option>
                                                <option value="5">Select Client</option>
                                                <option value="2">Send to all active clients</option>
                                                <option value="3">Send to all in-active clients</option>
                                                <option value="4">Send to all clients</option>
                                            </select>
                                        </div>
                                        <div class="col-md-6  {{ isset($messages) ? '' : 'd-none' }}"
                                            id="number_lists">
                                            <label for="select_recipient" class="form-control-label">Insert
                                                Number</label>
                                            <div class="autocomplete">
                                                <textarea class="form-control" name="phone_numbers" id="phone_numbers" cols="30" rows="2"
                                                    placeholder="to send to multiple numbers separate the number with commas like number1,number2">{{ isset($phone_number) ? $phone_number : '' }}</textarea>
                                            </div>
                                        </div>
                                        <div class="col-md-6 d-none" id="select_clients">
                                            <label for="select_recipient" id="phone_number2" name=""
                                                class="form-control-label">Type Customer
                                                Name, Account No or Phone number</label>
                                            <div class="autocomplete">
                                                <input id="myInput" type="text" class="form-control"
                                                    name="phone_number"
                                                    placeholder="Phone number, Account Number, Name">
                                            </div>
                                        </div>
                                        <div class="col-md-12 my-1">
                                            <label for="messages" class="form-control-label">Write Message <small>(162
                                                    characters cost 1 unit of sms)</small></label>
                                            <textarea name="messages" class="form-control" id="messages" cols="30" rows="2" placeholder="Write your message here"
                                                required>{{ isset($messages) ? $messages : '' }}</textarea>
                                        </div>
                                        <div class="col-md-6 my-1">
                                            @php
                                                $btnText = "<i class=\"fa-solid fa-paper-plane\"></i> Send Message";
                                                $otherClasses = "".$readonly;
                                                $btn_id = "";
                                                $otherAttributes = "";
                                            @endphp
                                            <x-button :otherAttributes="$otherAttributes" :btnText="$btnText" toolTip="" btnType="primary" type="submit" btnSize="sm" :otherClasses="$otherClasses" :btnId="$btn_id" :readOnly="$readonly" />
                                            {{-- <button type="submit" {{$readonly}} class="btn btn-primary"><i
                                                    class="fa-solid fa-paper-plane"></i> Send Message</button> --}}
                                        </div>
                                        <div class="col-md-6 my-1">
                                            @php
                                                $btnText = "<i class=\"fa-solid fa-xmark\"></i> Cancel";
                                                $otherClasses = "";
                                                $btnLink = "/sms";
                                                $otherAttributes = "";
                                            @endphp
                                            <x-button-link btnType="danger" btnSize="sm" toolTip="" :otherAttributes="$otherAttributes" :btnText="$btnText" :btnLink="$btnLink" :otherClasses="$otherClasses" :readOnly="$readonly" />
                                            {{-- <a href="/sms" class="btn btn-danger"><i class="fa-solid fa-xmark"></i>
                                                Cancel</a> --}}
                                        </div>
                                    </form>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <!-- Basic Tables end -->
            {{-- send for the routers --}}
            <div class="content-body {{ isset($messages) ? 'd-none' : '' }}">
                <!-- Basic Tables start -->
                <div class="row">
                    <div class="col-12">
                        <div class="card">
                            <div class="card-header">
                                <h4 class="card-title">Send Message to clients per router
                                </h4>
                                <a class="heading-elements-toggle"><i class="la la-ellipsis-v font-medium-3"></i></a>
                                <div class="heading-elements">
                                    <ul class="list-inline mb-0">
                                        <li><a data-action="collapse"><i class="ft-minus"></i></a></li>
                                        {{-- <li><a data-action="reload"><i class="ft-rotate-cw"></i></a></li> --}}
                                        <li><a data-action="expand"><i class="ft-maximize"></i></a></li>
                                        <!-- <li><a data-action="close"><i class="ft-x"></i></a></li> -->
                                    </ul>
                                </div>
                            </div>
                            <div class="card-content collapse show">
                                <div class="card-body">
                                    @if (session('success'))
                                        <p class="text-success">{{ session('success') }}</p>
                                    @endif
                                    <a href="/sms" class="btn btn-infor"><i class="fas fa-arrow-left"></i>
                                        Back to list</a>
                                    @if ($errors->any())
                                        <h6 style="color: orangered">Errors</h6>
                                        <ul class="text-danger" style="color: orangered">
                                            @foreach ($errors->all() as $item)
                                                <li>{{ $item }}</li>
                                            @endforeach
                                        </ul>
                                    @endif

                                    @if (session('error_sms'))
                                        <p class="text-danger">{{ session('error_sms') }}</p>
                                    @endif
                                    @if (session('message_success'))
                                        <p class="text-success">{{ session('message_success') }}</p>
                                    @endif

                                </div>
                                <div class="card-body">
                                    {{-- write a message --}}
                                    <form class="row" method="POST" action="/sendsms_routers">
                                        @csrf
                                        <div class="col-md-6">
                                            <label for="select_router" class="form-control-label">Select
                                                Router</label>
                                                @if (isset($router_infor))
                                                    <select name="select_router" id="select_router"
                                                        class="form-control" required>
                                                        <option value="" hidden>Select an option</option>
                                                    @for ($i = 0; $i < count($router_infor); $i++)
                                                        <option value="{{$router_infor[$i]->router_id}}" >{{$router_infor[$i]->router_name}}</option>
                                                        {{-- {{"<option value=".$router_infor[$i]->router_id." >".$router_infor[$i]->router_id."</option>"}} --}}
                                                    @endfor
                                                    </select>
                                                @else
                                                    <p class="text-secondary">No routers found! Please add a router to proceed</p>
                                                @endif
                                        </div>
                                        <div class="col-md-6"
                                            id="number_lists">
                                            <label for="select_client_group" class="form-control-label">Client Group</label>
                                            <select name="select_client_group" id="select_client_group" class="form-control" required>
                                                <option value="" hidden>Select an option</option>
                                                <option value="0">In-Active</option>
                                                <option value="1">Active</option>
                                                <option value="all">All</option>
                                            </select>
                                        </div>
                                        <div class="col-md-12 my-1">
                                            <label for="messages" class="form-control-label">Write Message <small>(162
                                                    characters cost 1 unit of sms)</small></label>
                                            <textarea name="messages" class="form-control" id="messages" cols="30" rows="2" placeholder="Write your message here"
                                                required>{{ isset($messages) ? $messages : '' }}</textarea>
                                        </div>
                                        <div class="col-md-6 my-1">
                                            @php
                                                $btnText = "<i class=\"fa-solid fa-paper-plane\"></i> Send Message";
                                                $otherClasses = "".$readonly;
                                                $btn_id = "";
                                                $otherAttributes = "";
                                            @endphp
                                            <x-button :otherAttributes="$otherAttributes" :btnText="$btnText" toolTip="" btnType="primary" type="submit" btnSize="sm" :otherClasses="$otherClasses" :btnId="$btn_id" :readOnly="$readonly" />
                                            {{-- <button {{$readonly}} type="submit" class="btn btn-primary"><i
                                                    class="fa-solid fa-paper-plane"></i> Send Message</button> --}}
                                        </div>
                                        <div class="col-md-6 my-1">
                                            @php
                                                $btnText = "<i class=\"fas fa-x\"></i> Cancel";
                                                $otherClasses = "";
                                                $btnLink = "/sms";
                                                $otherAttributes = "";
                                            @endphp
                                            <x-button-link btnType="danger" btnSize="sm" toolTip="" :otherAttributes="$otherAttributes" :btnText="$btnText" :btnLink="$btnLink" :otherClasses="$otherClasses" :readOnly="$readonly" />
                                            {{-- <a href="/sms" class="btn btn-danger"><i class="fa-solid fa-xmark"></i>
                                                Cancel</a> --}}
                                        </div>
                                    </form>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            {{-- end of send for the routers --}}
        </div>
    </div>
    </div>
    <!-- ////////////////////////////////////////////////////////////////////////////-->
    <!-- The footer -->
    <footer style="margin-bottom: 0% !important" class="footer footer-static footer-light navbar-border navbar-shadow">
        <div class="clearfix blue-grey lighten-2 text-sm-center mb-0 px-2"><span
                class="float-md-left d-block d-md-inline-block"><?php echo date('Y'); ?> &copy; Copyright Hypbits
                Enterprises</span>
            <ul class="list-inline float-md-right d-block d-md-inline-blockd-none d-lg-block mb-0">
                <li class="list-inline-item">Created By<a class="my-1" href="https://ladybirdsmis.com"
                        target="_blank"> Ladybird Softech Co.</a></li>
            </ul>
        </div>
    </footer>
    <!-- ////////////////////////// -->

    <!-- BEGIN VENDOR JS-->
    <script src="/theme-assets/vendors/js/vendors.min.js" type="text/javascript"></script>
    <!-- BEGIN VENDOR JS-->
    <!-- BEGIN PAGE VENDOR JS-->

    <!-- END PAGE VENDOR JS-->
    <!-- BEGIN CHAMELEON  JS-->
    <script src="/theme-assets/js/core/app-menu-lite.js" type="text/javascript"></script>
    <script src="/theme-assets/js/core/app-lite.js" type="text/javascript"></script>
    <!-- END CHAMELEON  JS-->
    <script src="/theme-assets/js/core/smssend.js"></script>
    <script>
        var client_names = @json($client_names ?? '');
        var client_contacts = @json($client_contacts ?? '');
        var client_account = @json($client_account ?? '');
    </script>
    <script>
        var phone_number = document.getElementById("phone_numbers");
        phone_number.onkeyup = function() {
            // console.log(this.value)
            this.value = this.value.trim();
        }
        var phone_number = document.getElementById("phone_number2");
        phone_number.onkeyup = function() {
            // console.log(this.value)
            this.value = this.value.trim();
        }
    </script>
    <script>
        function autocomplete(inp, arr, arr2, arr3) {
            /*the autocomplete function takes two arguments,
            the text field element and an array of possible autocompleted values:*/
            var currentFocus;
            /*execute a function when someone writes in the text field:*/
            inp.addEventListener("input", function(e) {
                var a, b, i, val = this.value;
                /*close any already open lists of autocompleted values*/
                closeAllLists();
                if (!val) {
                    return false;
                }
                currentFocus = -1;
                /*create a DIV element that will contain the items (values):*/
                a = document.createElement("DIV");
                a.setAttribute("id", this.id + "autocomplete-list");
                a.setAttribute("class", "autocomplete-items");
                a.style.maxHeight = "250px";
                a.style.overflowY = "auto";
                a.style.overflowX = "hidden";
                /*append the DIV element as a child of the autocomplete container:*/
                this.parentNode.appendChild(a);
                /*for each item in the array...*/
                var counter = 0;
                for (i = 0; i < arr.length; i++) {
                    if (counter > 10) {
                        break;
                    }
                    /*check if the item starts with the same letters as the text field value:*/
                    if (arr[i].substr(0, val.length).toUpperCase() == val.toUpperCase() ||
                        arr2[i].substr(0, val.length).toUpperCase() == val.toUpperCase() ||
                        arr3[i].substr(0, val.length).toUpperCase() == val.toUpperCase()
                    ) {
                        /*create a DIV element for each matching element:*/
                        b = document.createElement("DIV");
                        /*make the matching letters bold:*/
                        b.innerHTML = /**"<strong>" +*/ arr3[i] + " (" + arr[i] + ") - " + arr2[
                            i] /**.substr(0, val.length)*/ /**+ "</strong>"*/ ;
                        // b.innerHTML += arr[i].substr(val.length);
                        /*insert a input field that will hold the current array item's value:*/
                        b.innerHTML += "<input type='hidden' value='" + arr[i] + "'>";
                        /*execute a function when someone clicks on the item value (DIV element):*/
                        b.addEventListener("click", function(e) {
                            /*insert the value for the autocomplete text field:*/
                            inp.value = this.getElementsByTagName("input")[0].value;
                            /*close the list of autocompleted values,
                            (or any other open lists of autocompleted values:*/
                            closeAllLists();
                        });
                        a.appendChild(b);
                        counter++;
                    }
                    console.log(counter);
                }
            });
            /*execute a function presses a key on the keyboard:*/
            inp.addEventListener("keydown", function(e) {
                var x = document.getElementById(this.id + "autocomplete-list");
                if (x) x = x.getElementsByTagName("div");
                if (e.keyCode == 40) {
                    /*If the arrow DOWN key is pressed,
                    increase the currentFocus variable:*/
                    currentFocus++;
                    /*and and make the current item more visible:*/
                    addActive(x);
                } else if (e.keyCode == 38) { //up
                    /*If the arrow UP key is pressed,
                    decrease the currentFocus variable:*/
                    currentFocus--;
                    /*and and make the current item more visible:*/
                    addActive(x);
                } else if (e.keyCode == 13) {
                    /*If the ENTER key is pressed, prevent the form from being submitted,*/
                    e.preventDefault();
                    if (currentFocus > -1) {
                        /*and simulate a click on the "active" item:*/
                        if (x) x[currentFocus].click();
                    }
                }
            });

            function addActive(x) {
                /*a function to classify an item as "active":*/
                if (!x) return false;
                /*start by removing the "active" class on all items:*/
                removeActive(x);
                if (currentFocus >= x.length) currentFocus = 0;
                if (currentFocus < 0) currentFocus = (x.length - 1);
                /*add class "autocomplete-active":*/
                x[currentFocus].classList.add("autocomplete-active");
            }

            function removeActive(x) {
                /*a function to remove the "active" class from all autocomplete items:*/
                for (var i = 0; i < x.length; i++) {
                    x[i].classList.remove("autocomplete-active");
                }
            }

            function closeAllLists(elmnt) {
                /*close all autocomplete lists in the document,
                except the one passed as an argument:*/
                var x = document.getElementsByClassName("autocomplete-items");
                for (var i = 0; i < x.length; i++) {
                    if (elmnt != x[i] && elmnt != inp) {
                        x[i].parentNode.removeChild(x[i]);
                    }
                }
            }
            /*execute a function when someone clicks in the document:*/
            document.addEventListener("click", function(e) {
                closeAllLists(e.target);
            });
        }
        function autocomplete2(inp, arr, arr2, arr3) {
            /*the autocomplete function takes two arguments,
            the text field element and an array of possible autocompleted values:*/
            var currentFocus;
            /*execute a function when someone writes in the text field:*/
            inp.addEventListener("input", function(e) {
                var a, b, i, val = this.value.split(",")[this.value.split(",").length - 1];
                /*close any already open lists of autocompleted values*/
                closeAllLists();
                if (!val) {
                    return false;
                }
                currentFocus = -1;
                /*create a DIV element that will contain the items (values):*/
                a = document.createElement("DIV");
                a.setAttribute("id", this.id + "autocomplete-list");
                a.setAttribute("class", "autocomplete-items");
                a.style.maxHeight = "250px";
                a.style.overflowY = "auto";
                a.style.overflowX = "hidden";
                /*append the DIV element as a child of the autocomplete container:*/
                this.parentNode.appendChild(a);
                /*for each item in the array...*/
                var counter = 0;
                for (i = 0; i < arr.length; i++) {
                    if (counter > 10) {
                        break;
                    }
                    /*check if the item starts with the same letters as the text field value:*/
                    if (arr[i].substr(0, val.length).toUpperCase() == val.toUpperCase() ||
                        arr2[i].substr(0, val.length).toUpperCase() == val.toUpperCase() ||
                        arr3[i].substr(0, val.length).toUpperCase() == val.toUpperCase()
                    ) {
                        /*create a DIV element for each matching element:*/
                        b = document.createElement("DIV");
                        /*make the matching letters bold:*/
                        b.innerHTML = /**"<strong>" +*/ arr3[i] + " (" + arr[i] + ") - " + arr2[
                            i] /**.substr(0, val.length)*/ /**+ "</strong>"*/ ;
                        // b.innerHTML += arr[i].substr(val.length);
                        /*insert a input field that will hold the current array item's value:*/
                        b.innerHTML += "<input type='hidden' value='" + arr[i] + "'>";
                        /*execute a function when someone clicks on the item value (DIV element):*/
                        b.addEventListener("click", function(e) {
                            /*insert the value for the autocomplete text field:*/
                            var input_value = inp.value;
                            var final_str = "";
                            if (input_value.length > 1) {
                                input_value = input_value.split(",");
                                for (let index = 0; index < input_value.length; index++) {
                                    const element = input_value[index];
                                    if (index+2 > input_value.length) {
                                        continue;
                                    }
                                    if (element.trim().length > 0) {
                                        final_str+=element+",";
                                    }
                                }
                            }
                            inp.value = final_str+this.getElementsByTagName("input")[0].value;
                            /*close the list of autocompleted values,
                            (or any other open lists of autocompleted values:*/
                            closeAllLists();
                        });
                        a.appendChild(b);
                        counter++;
                    }
                    console.log(counter);
                }
            });
            /*execute a function presses a key on the keyboard:*/
            inp.addEventListener("keydown", function(e) {
                var x = document.getElementById(this.id + "autocomplete-list");
                if (x) x = x.getElementsByTagName("div");
                if (e.keyCode == 40) {
                    /*If the arrow DOWN key is pressed,
                    increase the currentFocus variable:*/
                    currentFocus++;
                    /*and and make the current item more visible:*/
                    addActive(x);
                } else if (e.keyCode == 38) { //up
                    /*If the arrow UP key is pressed,
                    decrease the currentFocus variable:*/
                    currentFocus--;
                    /*and and make the current item more visible:*/
                    addActive(x);
                } else if (e.keyCode == 13) {
                    /*If the ENTER key is pressed, prevent the form from being submitted,*/
                    e.preventDefault();
                    if (currentFocus > -1) {
                        /*and simulate a click on the "active" item:*/
                        if (x) x[currentFocus].click();
                    }
                }
            });

            function addActive(x) {
                /*a function to classify an item as "active":*/
                if (!x) return false;
                /*start by removing the "active" class on all items:*/
                removeActive(x);
                if (currentFocus >= x.length) currentFocus = 0;
                if (currentFocus < 0) currentFocus = (x.length - 1);
                /*add class "autocomplete-active":*/
                x[currentFocus].classList.add("autocomplete-active");
            }

            function removeActive(x) {
                /*a function to remove the "active" class from all autocomplete items:*/
                for (var i = 0; i < x.length; i++) {
                    x[i].classList.remove("autocomplete-active");
                }
            }

            function closeAllLists(elmnt) {
                /*close all autocomplete lists in the document,
                except the one passed as an argument:*/
                var x = document.getElementsByClassName("autocomplete-items");
                for (var i = 0; i < x.length; i++) {
                    if (elmnt != x[i] && elmnt != inp) {
                        x[i].parentNode.removeChild(x[i]);
                    }
                }
            }
            /*execute a function when someone clicks in the document:*/
            document.addEventListener("click", function(e) {
                closeAllLists(e.target);
            });
        }

        /*An array containing all the country names in the world:*/
        var countries = client_contacts;

        /*initiate the autocomplete function on the "myInput" element, and pass along the countries array as possible autocomplete values:*/
        autocomplete(document.getElementById("myInput"), client_contacts, client_account, client_names);
        autocomplete2(document.getElementById("phone_numbers"), client_contacts, client_account, client_names);
    </script>
    <script>
      var milli_seconds = 1200;
      setInterval(() => {
          if (milli_seconds == 0) {
              window.location.href = "/";
          }
          milli_seconds--;
      }, 1000);
    </script>
</body>

</html>
