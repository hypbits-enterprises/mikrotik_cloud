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
    <title>Hypbits - System SMS </title>
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
                    <h3 class="content-header-title">Customize System Sms</h3>
                </div>
                <div class="content-header-right col-md-8 col-12">
                    <div class="breadcrumbs-top float-md-right">
                        <div class="breadcrumb-wrapper mr-1">
                            <ol class="breadcrumb">
                                <li class="breadcrumb-item"><a href="/Dashboard">Dashboard</a>
                                </li>
                                <li class="breadcrumb-item"><a href="/sms">My SMS</a>
                                </li>
                                <li class="breadcrumb-item">Customize System Sms
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
                            <div class="card-header my-0">
                                @php
                                    $btnText = "<i class=\"fas fa-arrow-left\"></i> Back to SMS";
                                    $otherClasses = "ml-1 my-0";
                                    $btnLink = "/sms";
                                    $otherAttributes = "";
                                @endphp
                                <x-button-link btnType="infor" btnSize="sm" toolTip="" :otherAttributes="$otherAttributes" :btnText="$btnText" :btnLink="$btnLink" :otherClasses="$otherClasses" :readOnly="$readonly" />
                                {{-- <a href="/sms" class="btn btn-infor my-0"><i class="fas fa-arrow-left"></i> Back to SMS</a> --}}
                                <h4 class="card-title">Customize System Sms</h4>
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
                            <div class="card-content">
                                <div class="card-body">
                                    <div class="card-text">
                                        <p>What you need to know.</p>
                                        <ul>
                                            <li>Below we have fields with different titles describing the message content its to be filled with.</li>
                                            <li>The system is designed to send the different Message at different intervals during the day as stated in the titles of the message.</li>
                                            <li>When left blank the system won`t send the message to the user.</li>
                                            <li>Below we have some dynamic content you want to include to your message and its tags.</li>
                                            <li>Include the tags in your message correctly so that the system captures it.</li>
                                            <li>The tags are case sensitive <code><strong>"A"</strong> is different from <strong>"a"</strong></code></li>
                                            <li>The tags must be inside the square brackets <code><b>"[]"</b></code></li>
                                        </ul>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="content-body">
                <!-- Basic Tables start -->
                <div class="row">
                    <div class="col-12">
                        <div class="card">
                            <div class="card-header my-0">
                                <h4 class="card-title">SMS tags</h4>
                            </div>
                            <div class="card-content">
                                <div class="card-body">
                                    <div class="card-text">
                                        <p>SMS Tags - Client details.</p>
                                        <div class="row">
                                            <div class="row col-md-6">
                                                <div class="col-md-6">
                                                    <p><b>Name</b></p>
                                                </div>
                                                <div class="col-md-6 ">
                                                    <p><b>Tag</b></p>
                                                </div>
                                                <div class="col-md-6  border border-light">
                                                    <p>1. Fullname</p>
                                                </div>
                                                <div class="col-md-6  border border-light">
                                                    <p><code>[client_name]</code></p>
                                                </div>
                                                <div class="col-md-6  border border-light">
                                                    <p>2. First Name</p>
                                                </div>
                                                <div class="col-md-6  border border-light">
                                                    <p><code>[client_f_name]</code></p>
                                                </div>
                                                <div class="col-md-6  border border-light">
                                                    <p>3. Address</p>
                                                </div>
                                                <div class="col-md-6  border border-light">
                                                    <p><code>[client_addr]</code></p>
                                                </div>
                                                <div class="col-md-6  border border-light">
                                                    <p>4. Expiration Date</p>
                                                </div>
                                                <div class="col-md-6  border border-light">
                                                    <p><code>[exp_date]</code></p>
                                                </div>
                                            </div>
                                            <div class="row col-md-6">
                                                <div class="col-md-6 ">
                                                    <p><b>Name</b></p>
                                                </div>
                                                <div class="col-md-6 ">
                                                    <p><b>Tag</b></p>
                                                </div>
                                                <div class="col-md-6  border border-light">
                                                    <p>5. Registration Date</p>
                                                </div>
                                                <div class="col-md-6  border border-light">
                                                    <p><code>[reg_date]</code></p>
                                                </div>
                                                <div class="col-md-6  border border-light">
                                                    <p>6. Monthly Fees</p>
                                                </div>
                                                <div class="col-md-6  border border-light">
                                                    <p><code>[monthly_fees]</code></p>
                                                </div>
                                                <div class="col-md-6  border border-light">
                                                    <p>7. Contact</p>
                                                </div>
                                                <div class="col-md-6  border border-light">
                                                    <p><code>[client_phone]</code></p>
                                                </div>
                                                <div class="col-md-6  border border-light">
                                                    <p>8. Account No.</p>
                                                </div>
                                                <div class="col-md-6  border border-light">
                                                    <p><code>[acc_no]</code></p>
                                                </div>
                                            </div>
                                            <div class="row col-md-6">
                                                <div class="col-md-6 ">
                                                    <p><b>Name</b></p>
                                                </div>
                                                <div class="col-md-6 ">
                                                    <p><b>Tag</b></p>
                                                </div>
                                                <div class="col-md-6  border border-light">
                                                    <p>9. Wallet</p>
                                                </div>
                                                <div class="col-md-6  border border-light">
                                                    <p><code>[client_wallet]</code></p>
                                                </div>
                                                <div class="col-md-6  border border-light">
                                                    <p>10. Username</p>
                                                </div>
                                                <div class="col-md-6  border border-light">
                                                    <p><code>[username]</code></p>
                                                </div>
                                                <div class="col-md-6  border border-light">
                                                    <p>11. Password</p>
                                                </div>
                                                <div class="col-md-6  border border-light">
                                                    <p><code>[password]</code></p>
                                                </div>
                                                <div class="col-md-6  border border-light">
                                                    <p>12. Date Today.</p>
                                                </div>
                                                <div class="col-md-6  border border-light">
                                                    <p><code>[today]</code></p>
                                                </div>
                                                <div class="col-md-6  border border-light">
                                                    <p>13. Time Now</p>
                                                </div>
                                                <div class="col-md-6  border border-light">
                                                    <p><code>[now]</code></p>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="content-body">
                <!-- Basic Tables start -->
                <div class="row">
                    <div class="col-12">
                        <div class="card">
                            <div class="card-header my-0">
                                <h4 class="card-title">Remind Payment</h4>
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
                            <div class="card-content">
                                <div class="card-body">
                                    <p>Time sent : <b>9AM Everyday</b></p>
                                    <!-- Basic Tables end -->
                                    <section id="html-headings-default" class="row match-height">
                                        <div class="col-sm-12 shadow col-md-6">
                                            <div class="card">
                                                <div class="card-header">
                                                    <h4 class="card-title">Day Before</h4>
                                                </div>
                                                <hr class="my-0">
                                                <div class="card-content">
                                                    <div class="card-body">
                                                        <form id="daybefore_win" class="form-group d-none" action="/save_sms_content" method="post">
                                                            @csrf
                                                            <label for="message_contents" class="form-control-label">
                                                                Message Content
                                                            </label>
                                                            <input type="hidden" name="date_before" value="date_before">
                                                            <input type="hidden" name="category" value="reminder_message">
                                                            <textarea name="message_contents" id="message_contents" cols="30" rows="5" class="form-control p-2" placeholder="Write your message here ...">{{$sms_data[0]->messages[0]->message ?? 'No message set for this section at the moment'}}</textarea>
                                                            <div class="row my-1">
                                                                <div class="col-md-6">
                                                                    @php
                                                                        $btnText = "<i class=\"fas fa-save\"></i> Save";
                                                                        $otherClasses = "my-1";
                                                                        $btn_id = "";
                                                                        $otherAttributes = "";
                                                                    @endphp
                                                                    <x-button :otherAttributes="$otherAttributes" :btnText="$btnText" toolTip="" btnType="primary" type="submit" btnSize="sm" :otherClasses="$otherClasses" :btnId="$btn_id" :readOnly="$readonly" />
                                                                    {{-- <button type="submit" {{$readonly}} class="btn btn-primary my-1"><i class="fas fa-save"></i> Save</button> --}}
                                                                </div>
                                                                <div class="col-md-6">
                                                                    @php
                                                                        $btnText = "<i class=\"fas fa-eye\"></i> View Sample";
                                                                        $otherClasses = "my-1";
                                                                        $btn_id = "view_sample_daybefore";
                                                                        $otherAttributes = "";
                                                                    @endphp
                                                                    <x-button :otherAttributes="$otherAttributes" :btnText="$btnText" toolTip="" btnType="primary" type="button" btnSize="sm" :otherClasses="$otherClasses" :btnId="$btn_id" :readOnly="$readonly" />
                                                                    {{-- <button id="view_sample_daybefore" type="button" class="btn btn-primary my-1"><i class="fas fa-eye"></i> View Sample</button> --}}
                                                                </div>
                                                            </div>
                                                        </form>
                                                        <div class="container" id="daybefore_win_sample">
                                                            <p id="daybefore_contents"></p>
                                                            @php
                                                                $btnText = "<i class=\"fas fa-pen\"></i> Edit";
                                                                $otherClasses = "my-1";
                                                                $btn_id = "backto_daybefore";
                                                                $otherAttributes = "";
                                                            @endphp
                                                            <x-button :otherAttributes="$otherAttributes" :btnText="$btnText" toolTip="" btnType="primary" type="button" btnSize="sm" :otherClasses="$otherClasses" :btnId="$btn_id" :readOnly="$readonly" />
                                                            {{-- <button id="backto_daybefore" type="button" class="btn btn-primary my-1"><i class="fas fa-pen"></i> Edit</button> --}}
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-sm-12 shadow col-md-6">
                                            <div class="card">
                                                <div class="card-header">
                                                    <h4 class="card-title">De-Day </h4>
                                                </div>
                                                <hr class="my-0">
                                                <div class="card-content">
                                                    <div class="card-body">
                                                        <form id="deday_win" class="form-group d-none" action="/save_sms_content" method="post">
                                                            @csrf
                                                            <label for="message_contents_2" class="form-control-label">
                                                                Message Content
                                                            </label>
                                                            <input type="hidden" name="deday" value="deday">
                                                            <input type="hidden" name="category" value="reminder_message">
                                                            <textarea name="message_contents" id="message_contents_2" cols="30" rows="5" class="form-control p-2" placeholder="Write your message here ...">{{$sms_data[0]->messages[1]->message ?? 'No message set for this section at the moment'}}</textarea>
                                                            <div class="row my-1">
                                                                <div class="col-md-6">
                                                                    @php
                                                                        $btnText = "<i class=\"fas fa-save\"></i> Save";
                                                                        $otherClasses = "my-1";
                                                                        $btn_id = "";
                                                                        $otherAttributes = "";
                                                                    @endphp
                                                                    <x-button :otherAttributes="$otherAttributes" :btnText="$btnText" toolTip="" btnType="primary" type="submit" btnSize="sm" :otherClasses="$otherClasses" :btnId="$btn_id" :readOnly="$readonly" />
                                                                    {{-- <button type="submit" {{$readonly}} class="btn btn-primary my-1"><i class="fas fa-save"></i> Save</button> --}}
                                                                </div>
                                                                <div class="col-md-6">
                                                                    @php
                                                                        $btnText = "<i class=\"fas fa-eye\"></i> View Sample";
                                                                        $otherClasses = "my-1";
                                                                        $btn_id = "view_sample_deday";
                                                                        $otherAttributes = "";
                                                                    @endphp
                                                                    <x-button :otherAttributes="$otherAttributes" :btnText="$btnText" toolTip="" btnType="primary" type="button" btnSize="sm" :otherClasses="$otherClasses" :btnId="$btn_id" :readOnly="$readonly" />
                                                                    {{-- <button id="view_sample_deday" type="button" class="btn btn-primary my-1"><i class="fas fa-eye"></i> View Sample</button> --}}
                                                                </div>
                                                            </div>
                                                        </form>
                                                        <div class="container" id="deday_win_sample">
                                                            <p id="deday_contents"></p>
                                                            @php
                                                                $btnText = "<i class=\"fas fa-pen\"></i> Edit";
                                                                $otherClasses = "my-1";
                                                                $btn_id = "backto_deday";
                                                                $otherAttributes = "";
                                                            @endphp
                                                            <x-button :otherAttributes="$otherAttributes" :btnText="$btnText" toolTip="" btnType="primary" type="button" btnSize="sm" :otherClasses="$otherClasses" :btnId="$btn_id" :readOnly="$readonly" />
                                                            {{-- <button id="backto_deday" type="button" class="btn btn-primary my-1"><i class="fas fa-pen"></i> Edit</button> --}}
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-sm-12 my-2 shadow col-md-6">
                                            <div class="card">
                                                <div class="card-header">
                                                    <h4 class="card-title">After due date </h4>
                                                </div>
                                                <hr class="my-0">
                                                <div class="card-content">
                                                    <div class="card-body">
                                                        <form id="after_due_date_win" class="form-group d-none" action="/save_sms_content" method="post">
                                                            @csrf
                                                            <label for="message_contents_3" class="form-control-label">
                                                                Message Content
                                                            </label>
                                                            <input type="hidden" name="after_due_date" value="after_due_date">
                                                            <input type="hidden" name="category" value="reminder_message">
                                                            <textarea name="message_contents" id="message_contents_3" cols="30" rows="5" class="form-control p-2" placeholder="Write your message here ...">{{$sms_data[0]->messages[2]->message ?? 'No message set for this section at the moment'}}</textarea>
                                                            <div class="row my-1">
                                                                <div class="col-md-6">
                                                                    @php
                                                                        $btnText = "<i class=\"fas fa-save\"></i> Save";
                                                                        $otherClasses = "my-1";
                                                                        $btn_id = "";
                                                                        $otherAttributes = "";
                                                                    @endphp
                                                                    <x-button :otherAttributes="$otherAttributes" :btnText="$btnText" toolTip="" btnType="primary" type="submit" btnSize="sm" :otherClasses="$otherClasses" :btnId="$btn_id" :readOnly="$readonly" />
                                                                    {{-- <button type="submit" {{$readonly}} class="btn btn-primary my-1"><i class="fas fa-save"></i> Save</button> --}}
                                                                </div>
                                                                <div class="col-md-6">
                                                                    @php
                                                                        $btnText = "<i class=\"fas fa-eye\"></i> View Sample";
                                                                        $otherClasses = "my-1";
                                                                        $btn_id = "view_sample_after_due_date";
                                                                        $otherAttributes = "";
                                                                    @endphp
                                                                    <x-button :otherAttributes="$otherAttributes" :btnText="$btnText" toolTip="" btnType="primary" type="button" btnSize="sm" :otherClasses="$otherClasses" :btnId="$btn_id" :readOnly="$readonly" />
                                                                    {{-- <button id="view_sample_after_due_date" type="button" class="btn btn-primary my-1"><i class="fas fa-eye"></i> View Sample</button> --}}
                                                                </div>
                                                            </div>
                                                        </form>
                                                        <div class="container" id="after_due_date_win_sample">
                                                            <p id="after_due_date_contents"></p>
                                                            @php
                                                                $btnText = "<i class=\"fas fa-pen\"></i> Edit";
                                                                $otherClasses = "my-1";
                                                                $btn_id = "backto_after_due_date";
                                                                $otherAttributes = "";
                                                            @endphp
                                                            <x-button :otherAttributes="$otherAttributes" :btnText="$btnText" toolTip="" btnType="primary" type="button" btnSize="sm" :otherClasses="$otherClasses" :btnId="$btn_id" :readOnly="$readonly" />
                                                            {{-- <button id="backto_after_due_date" type="button" class="btn btn-primary my-1"><i class="fas fa-pen"></i> Edit</button> --}}
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </section>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="content-body">
                <!-- Basic Tables start -->
                <div class="row">
                    <div class="col-12">
                        <div class="card">
                            <div class="card-header my-0">
                                <h4 class="card-title">Recieve Payments</h4>
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
                            <div class="card-content">
                                <div class="card-body">
                                    <div class="card-text">
                                        <h6>Note:</h6>
                                        <p>Tags to be used in this section only!</p>
                                        <div class="row">
                                            <div class="row col-md-6">
                                                <div class="col-md-6  ">
                                                    <p><b>Name</b></p>
                                                </div>
                                                <div class="col-md-6  ">
                                                    <p><b>Tag</b></p>
                                                </div>
                                                <div class="col-md-6  border border-light">
                                                    <p>1. <span class="text-danger">Transacted Amount</span></p>
                                                </div>
                                                <div class="col-md-6  border border-light">
                                                    <p><code>[trans_amnt]</code></p>
                                                </div>
                                                <div class="col-md-6  border border-light">
                                                    <p>2. <span class="text-danger">Minimum Amount</span></p>
                                                </div>
                                                <div class="col-md-6  border border-light">
                                                    <p><code>[min_amnt]</code></p>
                                                </div>
                                                <div class="col-md-6  border border-light">
                                                    <p>3. <span class="text-danger">Refferer Amount( <small>refferer`s cut</small> )</span></p>
                                                </div>
                                                <div class="col-md-6  border border-light">
                                                    <p><code>[refferer_trans_amount]</code></p>
                                                </div>
                                                <div class="col-md-6  border border-light">
                                                    <p>4. <span class="text-danger">Refferer Fullname</span></p>
                                                </div>
                                                <div class="col-md-6  border border-light">
                                                    <p><code>[refferer_name]</code></p>
                                                </div>
                                                <div class="col-md-6  border border-light">
                                                    <p>5. <span class="text-danger">Refferer First Name</span></p>
                                                </div>
                                                <div class="col-md-6  border border-light">
                                                    <p><code>[refferer_f_name]</code></p>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="card-content">
                                <div class="card-body">
                                    <p>Time sent : <b>When clients make payments to your paybill</b></p>
                                    <!-- Basic Tables end -->
                                    <section id="html-headings-default" class="row match-height">
                                        <div class="col-sm-12 shadow col-md-6">
                                            <div class="card">
                                                <div class="card-header">
                                                    <h4 class="card-title">Correct Account Number</h4>
                                                </div>
                                                <hr class="my-0">
                                                <div class="card-content">
                                                    <div class="card-body">
                                                        <form id="correct_acc_no_win" class="form-group d-none" action="/save_sms_content" method="post">
                                                            @csrf
                                                            <label for="message_contents_4" class="form-control-label">
                                                                Message Content
                                                            </label>
                                                            <input type="hidden" name="correct_acc_no" value="correct_acc_no">
                                                            <input type="hidden" name="category" value="funds_recieved">
                                                            <textarea name="message_contents" id="message_contents_4" cols="30" rows="5" class="form-control p-2" placeholder="Write your message here ...">{{$sms_data[1]->messages[0]->message ?? 'No message set for this section at the moment'}}</textarea>
                                                            <div class="row my-1">
                                                                <div class="col-md-6">
                                                                    @php
                                                                        $btnText = "<i class=\"fas fa-save\"></i> Save";
                                                                        $otherClasses = "my-1";
                                                                        $btn_id = "";
                                                                        $otherAttributes = "";
                                                                    @endphp
                                                                    <x-button :otherAttributes="$otherAttributes" :btnText="$btnText" toolTip="" btnType="primary" type="submit" btnSize="sm" :otherClasses="$otherClasses" :btnId="$btn_id" :readOnly="$readonly" />
                                                                    {{-- <button type="submit" {{$readonly}} class="btn btn-primary my-1"><i class="fas fa-save"></i> Save</button> --}}
                                                                </div>
                                                                <div class="col-md-6">
                                                                    @php
                                                                        $btnText = "<i class=\"fas fa-eye\"></i> View Sample";
                                                                        $otherClasses = "my-1";
                                                                        $btn_id = "view_sample_correct_acc_no";
                                                                        $otherAttributes = "";
                                                                    @endphp
                                                                    <x-button :otherAttributes="$otherAttributes" :btnText="$btnText" toolTip="" btnType="primary" type="button" btnSize="sm" :otherClasses="$otherClasses" :btnId="$btn_id" :readOnly="$readonly" />
                                                                    {{-- <button id="view_sample_correct_acc_no" type="button" class="btn btn-primary my-1"><i class="fas fa-eye"></i> View Sample</button> --}}
                                                                </div>
                                                            </div>
                                                        </form>
                                                        <div class="container" id="correct_acc_no_win_sample">
                                                            <p id="correct_acc_no_contents"></p>
                                                            @php
                                                                $btnText = "<i class=\"fas fa-pen\"></i> Edit";
                                                                $otherClasses = "my-1";
                                                                $btn_id = "backto_correct_acc_no";
                                                                $otherAttributes = "";
                                                            @endphp
                                                            <x-button :otherAttributes="$otherAttributes" :btnText="$btnText" toolTip="" btnType="primary" type="button" btnSize="sm" :otherClasses="$otherClasses" :btnId="$btn_id" :readOnly="$readonly" />
                                                            {{-- <button id="backto_correct_acc_no" type="button" class="btn btn-primary my-1"><i class="fas fa-pen"></i> Edit</button> --}}
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-sm-12 shadow col-md-6">
                                            <div class="card">
                                                <div class="card-header">
                                                    <h4 class="card-title">Incorrect Account Number </h4>
                                                </div>
                                                <hr class="my-0">
                                                <div class="card-content">
                                                    <div class="card-body">
                                                        <form id="incorrect_acc_no_win" class="form-group d-none" action="/save_sms_content" method="post">
                                                            @csrf
                                                            <label for="message_contents_5" class="form-control-label">
                                                                Message Content
                                                            </label>
                                                            <input type="hidden" name="incorrect_acc_no" value="incorrect_acc_no">
                                                            <input type="hidden" name="category" value="funds_recieved">
                                                            <textarea name="message_contents" id="message_contents_5" cols="30" rows="5" class="form-control p-2" placeholder="Write your message here ...">{{$sms_data[1]->messages[1]->message ?? 'No message set for this section at the moment'}}</textarea>
                                                            <div class="row my-1">
                                                                <div class="col-md-6">
                                                                    @php
                                                                        $btnText = "<i class=\"fas fa-save\"></i> Save";
                                                                        $otherClasses = "my-1";
                                                                        $btn_id = "";
                                                                        $otherAttributes = "";
                                                                    @endphp
                                                                    <x-button :otherAttributes="$otherAttributes" :btnText="$btnText" toolTip="" btnType="primary" type="submit" btnSize="sm" :otherClasses="$otherClasses" :btnId="$btn_id" :readOnly="$readonly" />
                                                                    {{-- <button type="submit" {{$readonly}} class="btn btn-primary my-1"><i class="fas fa-save"></i> Save</button> --}}
                                                                </div>
                                                                <div class="col-md-6">
                                                                    @php
                                                                        $btnText = "<i class=\"fas fa-eye\"></i> View Sample";
                                                                        $otherClasses = "my-1";
                                                                        $btn_id = "view_sample_incorrect_acc_no";
                                                                        $otherAttributes = "";
                                                                    @endphp
                                                                    <x-button :otherAttributes="$otherAttributes" :btnText="$btnText" toolTip="" btnType="primary" type="button" btnSize="sm" :otherClasses="$otherClasses" :btnId="$btn_id" :readOnly="$readonly" />
                                                                    {{-- <button id="view_sample_incorrect_acc_no" type="button" class="btn btn-primary my-1"><i class="fas fa-eye"></i> View Sample</button> --}}
                                                                </div>
                                                            </div>
                                                        </form>
                                                        <div class="container" id="incorrect_acc_no_win_sample">
                                                            <p id="incorrect_acc_no_contents"></p>
                                                            @php
                                                                $btnText = "<i class=\"fas fa-pen\"></i> Edit";
                                                                $otherClasses = "my-1";
                                                                $btn_id = "backto_incorrect_acc_no";
                                                                $otherAttributes = "";
                                                            @endphp
                                                            <x-button :otherAttributes="$otherAttributes" :btnText="$btnText" toolTip="" btnType="primary" type="button" btnSize="sm" :otherClasses="$otherClasses" :btnId="$btn_id" :readOnly="$readonly" />
                                                            {{-- <button id="backto_incorrect_acc_no" type="button" class="btn btn-primary my-1"><i class="fas fa-pen"></i> Edit</button> --}}
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-sm-12 shadow col-md-6 my-2">
                                            <div class="card">
                                                <div class="card-header">
                                                    <h4 class="card-title">Refferer Message </h4>
                                                </div>
                                                <hr class="my-0">
                                                <div class="card-content">
                                                    <div class="card-body">
                                                        <form id="refferer_message_win" class="form-group d-none" action="/save_sms_content" method="post">
                                                            @csrf
                                                            <label for="message_contents_10" class="form-control-label">
                                                                Message Content
                                                            </label>
                                                            <input type="hidden" name="refferer_msg" value="refferer_msg">
                                                            <input type="hidden" name="category" value="funds_recieved">
                                                            <textarea name="message_contents" id="message_contents_10" cols="30" rows="5" class="form-control p-2" placeholder="Write your message here ...">{{$sms_data[1]->messages[2]->message ?? 'No message set for this section at the moment'}}</textarea>
                                                            <div class="row my-1">
                                                                <div class="col-md-6">
                                                                    @php
                                                                        $btnText = "<i class=\"fas fa-save\"></i> Save";
                                                                        $otherClasses = "my-1";
                                                                        $btn_id = "";
                                                                        $otherAttributes = "";
                                                                    @endphp
                                                                    <x-button :otherAttributes="$otherAttributes" :btnText="$btnText" toolTip="" btnType="primary" type="submit" btnSize="sm" :otherClasses="$otherClasses" :btnId="$btn_id" :readOnly="$readonly" />
                                                                    {{-- <button type="submit" {{$readonly}} class="btn btn-primary my-1"><i class="fas fa-save"></i> Save</button> --}}
                                                                </div>
                                                                <div class="col-md-6">
                                                                    @php
                                                                        $btnText = "<i class=\"fas fa-eye\"></i> View Sample";
                                                                        $otherClasses = "my-1";
                                                                        $btn_id = "view_sample_refferer_msg";
                                                                        $otherAttributes = "";
                                                                    @endphp
                                                                    <x-button :otherAttributes="$otherAttributes" :btnText="$btnText" toolTip="" btnType="primary" type="button" btnSize="sm" :otherClasses="$otherClasses" :btnId="$btn_id" :readOnly="$readonly" />
                                                                    {{-- <button id="view_sample_refferer_msg" type="button" class="btn btn-primary my-1"><i class="fas fa-eye"></i> View Sample</button> --}}
                                                                </div>
                                                            </div>
                                                        </form>
                                                        <div class="container" id="refferer_msg_win_sample">
                                                            <p id="refferer_msg_contents"></p>
                                                            @php
                                                                $btnText = "<i class=\"fas fa-pen\"></i> Edit";
                                                                $otherClasses = "my-1";
                                                                $btn_id = "backto_refferer_msg";
                                                                $otherAttributes = "";
                                                            @endphp
                                                            <x-button :otherAttributes="$otherAttributes" :btnText="$btnText" toolTip="" btnType="primary" type="button" btnSize="sm" :otherClasses="$otherClasses" :btnId="$btn_id" :readOnly="$readonly" />
                                                            {{-- <button id="backto_refferer_msg" type="button" class="btn btn-primary my-1"><i class="fas fa-pen"></i> Edit</button> --}}
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-sm-12 shadow col-md-6 my-2">
                                            <div class="card">
                                                <div class="card-header">
                                                    <h4 class="card-title">Correct Acc number but below min Amount </h4>
                                                </div>
                                                <hr class="my-0">
                                                <div class="card-content">
                                                    <div class="card-body">
                                                        <form id="below_min_amnt_win" class="form-group d-none" action="/save_sms_content" method="post">
                                                            @csrf
                                                            <label for="message_contents_11" class="form-control-label">
                                                                Message Content
                                                            </label>
                                                            <input type="hidden" name="below_min_amnt" value="below_min_amnt">
                                                            <input type="hidden" name="category" value="funds_recieved">
                                                            <textarea name="message_contents" id="message_contents_11" cols="30" rows="5" class="form-control p-2" placeholder="Write your message here ...">{{$sms_data[1]->messages[3]->message ?? 'No message set for this section at the moment'}}</textarea>
                                                            <div class="row my-1">
                                                                <div class="col-md-6">
                                                                    @php
                                                                        $btnText = "<i class=\"fas fa-save\"></i> Save";
                                                                        $otherClasses = "my-1";
                                                                        $btn_id = "";
                                                                        $otherAttributes = "";
                                                                    @endphp
                                                                    <x-button :otherAttributes="$otherAttributes" :btnText="$btnText" toolTip="" btnType="primary" type="submit" btnSize="sm" :otherClasses="$otherClasses" :btnId="$btn_id" :readOnly="$readonly" />
                                                                    {{-- <button type="submit" {{$readonly}} class="btn btn-primary my-1"><i class="fas fa-save"></i> Save</button> --}}
                                                                </div>
                                                                <div class="col-md-6">
                                                                    @php
                                                                        $btnText = "<i class=\"fas fa-eye\"></i> View Sample";
                                                                        $otherClasses = "my-1";
                                                                        $btn_id = "view_sample_below_min_amnt";
                                                                        $otherAttributes = "";
                                                                    @endphp
                                                                    <x-button :otherAttributes="$otherAttributes" :btnText="$btnText" toolTip="" btnType="primary" type="button" btnSize="sm" :otherClasses="$otherClasses" :btnId="$btn_id" :readOnly="$readonly" />
                                                                    {{-- <button id="view_sample_below_min_amnt" type="button" class="btn btn-primary my-1"><i class="fas fa-eye"></i> View Sample</button> --}}
                                                                </div>
                                                            </div>
                                                        </form>
                                                        <div class="container" id="below_min_amnt_win_sample">
                                                            <p id="below_min_amnt_contents"></p>
                                                            @php
                                                                $btnText = "<i class=\"fas fa-pen\"></i> Edit";
                                                                $otherClasses = "my-1";
                                                                $btn_id = "backto_below_min_amnt";
                                                                $otherAttributes = "";
                                                            @endphp
                                                            <x-button :otherAttributes="$otherAttributes" :btnText="$btnText" toolTip="" btnType="primary" type="button" btnSize="sm" :otherClasses="$otherClasses" :btnId="$btn_id" :readOnly="$readonly" />
                                                            {{-- <button id="backto_below_min_amnt" type="button" class="btn btn-primary my-1"><i class="fas fa-pen"></i> Edit</button> --}}
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </section>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="content-body">
                <!-- Basic Tables start -->
                <div class="row">
                    <div class="col-12">
                        <div class="card">
                            <div class="card-header my-0">
                                <h4 class="card-title">Renewal Of Accounts</h4>
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
                            <div class="card-content">
                                <div class="card-body">
                                    <p>Time sent : <b>When the client is being activated or deactivated by the system.</b></p>
                                    <!-- Basic Tables end -->
                                    <section id="html-headings-default" class="row match-height">
                                        <div class="col-sm-12 shadow col-md-6">
                                            <div class="card">
                                                <div class="card-header">
                                                    <h4 class="card-title">Account Renewed</h4>
                                                </div>
                                                <hr class="my-0">
                                                <div class="card-content">
                                                    <div class="card-body">
                                                        <form id="account_renewed_win" class="form-group d-none" action="/save_sms_content" method="post">
                                                            @csrf
                                                            <label for="message_contents_6" class="form-control-label">
                                                                Message Content
                                                            </label>
                                                            <input type="hidden" name="account_renewed" value="account_renewed">
                                                            <input type="hidden" name="category" value="funds_recieved">
                                                            <textarea name="message_contents" id="message_contents_6" cols="30" rows="5" class="form-control p-2" placeholder="Write your message here ...">{{$sms_data[2]->messages[0]->message ?? 'No message set for this section at the moment'}}</textarea>
                                                            <div class="row my-1">
                                                                <div class="col-md-6">
                                                                    @php
                                                                        $btnText = "<i class=\"fas fa-save\"></i> Save";
                                                                        $otherClasses = "my-1";
                                                                        $btn_id = "";
                                                                        $otherAttributes = "";
                                                                    @endphp
                                                                    <x-button :otherAttributes="$otherAttributes" :btnText="$btnText" toolTip="" btnType="primary" type="submit" btnSize="sm" :otherClasses="$otherClasses" :btnId="$btn_id" :readOnly="$readonly" />
                                                                    {{-- <button type="submit" {{$readonly}} class="btn btn-primary my-1"><i class="fas fa-save"></i> Save</button> --}}
                                                                </div>
                                                                <div class="col-md-6">
                                                                    @php
                                                                        $btnText = "<i class=\"fas fa-eye\"></i> View Sample";
                                                                        $otherClasses = "my-1";
                                                                        $btn_id = "view_sample_account_renewed";
                                                                        $otherAttributes = "";
                                                                    @endphp
                                                                    <x-button :otherAttributes="$otherAttributes" :btnText="$btnText" toolTip="" btnType="primary" type="button" btnSize="sm" :otherClasses="$otherClasses" :btnId="$btn_id" :readOnly="$readonly" />
                                                                    {{-- <button id="view_sample_account_renewed" type="button" class="btn btn-primary my-1"><i class="fas fa-eye"></i> View Sample</button> --}}
                                                                </div>
                                                            </div>
                                                        </form>
                                                        <div class="container" id="account_renewed_win_sample">
                                                            <p id="account_renewed_contents"></p>
                                                            @php
                                                                $btnText = "<i class=\"fas fa-pen\"></i> Edit";
                                                                $otherClasses = "my-1";
                                                                $btn_id = "backto_account_renewed";
                                                                $otherAttributes = "";
                                                            @endphp
                                                            <x-button :otherAttributes="$otherAttributes" :btnText="$btnText" toolTip="" btnType="primary" type="button" btnSize="sm" :otherClasses="$otherClasses" :btnId="$btn_id" :readOnly="$readonly" />
                                                            {{-- <button id="backto_account_renewed" type="button" class="btn btn-primary my-1"><i class="fas fa-pen"></i> Edit</button> --}}
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-sm-12 shadow col-md-6">
                                            <div class="card">
                                                <div class="card-header">
                                                    <h4 class="card-title">Account Extended </h4>
                                                </div>
                                                <hr class="my-0">
                                                <div class="card-content">
                                                    <div class="card-body">
                                                        <form id="account_extended_win" class="form-group d-none" action="/save_sms_content" method="post">
                                                            @csrf
                                                            <label for="message_contents_7" class="form-control-label">
                                                                Message Content
                                                            </label>
                                                            <input type="hidden" name="account_extended" value="account_extended">
                                                            <input type="hidden" name="category" value="funds_recieved">
                                                            <textarea name="message_contents" id="message_contents_7" cols="30" rows="5" class="form-control p-2" placeholder="Write your message here ...">{{$sms_data[2]->messages[1]->message ?? 'No message set for this section at the moment'}}</textarea>
                                                            <div class="row my-1">
                                                                <div class="col-md-6">
                                                                    @php
                                                                        $btnText = "<i class=\"fas fa-save\"></i> Save";
                                                                        $otherClasses = "my-1";
                                                                        $btn_id = "";
                                                                        $otherAttributes = "";
                                                                    @endphp
                                                                    <x-button :otherAttributes="$otherAttributes" :btnText="$btnText" toolTip="" btnType="primary" type="submit" btnSize="sm" :otherClasses="$otherClasses" :btnId="$btn_id" :readOnly="$readonly" />
                                                                    {{-- <button type="submit" {{$readonly}} class="btn btn-primary my-1"><i class="fas fa-save"></i> Save</button> --}}
                                                                </div>
                                                                <div class="col-md-6">
                                                                    @php
                                                                        $btnText = "<i class=\"fas fa-eye\"></i> View Sample";
                                                                        $otherClasses = "my-1";
                                                                        $btn_id = "view_sample_account_extended";
                                                                        $otherAttributes = "";
                                                                    @endphp
                                                                    <x-button :otherAttributes="$otherAttributes" :btnText="$btnText" toolTip="" btnType="primary" type="button" btnSize="sm" :otherClasses="$otherClasses" :btnId="$btn_id" :readOnly="$readonly" />
                                                                    {{-- <button id="view_sample_account_extended" type="button" class="btn btn-primary my-1"><i class="fas fa-eye"></i> View Sample</button> --}}
                                                                </div>
                                                            </div>
                                                        </form>
                                                        <div class="container" id="account_extended_win_sample">
                                                            <p id="account_extended_contents"></p>
                                                            @php
                                                                $btnText = "<i class=\"fas fa-pen\"></i> Edit";
                                                                $otherClasses = "my-1";
                                                                $btn_id = "backto_account_extended";
                                                                $otherAttributes = "";
                                                            @endphp
                                                            <x-button :otherAttributes="$otherAttributes" :btnText="$btnText" toolTip="" btnType="primary" type="button" btnSize="sm" :otherClasses="$otherClasses" :btnId="$btn_id" :readOnly="$readonly" />
                                                            {{-- <button id="backto_account_extended" type="button" class="btn btn-primary my-1"><i class="fas fa-pen"></i> Edit</button> --}}
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-sm-12 my-2 shadow col-md-6">
                                            <div class="card">
                                                <div class="card-header">
                                                    <h4 class="card-title">Account De-Activated </h4>
                                                </div>
                                                <hr class="my-0">
                                                <div class="card-content">
                                                    <div class="card-body">
                                                        <form id="account_deactivated_win" class="form-group d-none" action="/save_sms_content" method="post">
                                                            @csrf
                                                            <label for="message_contents_9" class="form-control-label">
                                                                Message Content
                                                            </label>
                                                            <input type="hidden" name="account_deactivated" value="account_deactivated">
                                                            <input type="hidden" name="category" value="funds_recieved">
                                                            <textarea name="message_contents" id="message_contents_9" cols="30" rows="5" class="form-control p-2" placeholder="Write your message here ...">{{$sms_data[2]->messages[2]->message ?? 'No message set for this section at the moment'}}</textarea>
                                                            <div class="row my-1">
                                                                <div class="col-md-6">
                                                                    @php
                                                                        $btnText = "<i class=\"fas fa-save\"></i> Save";
                                                                        $otherClasses = "my-1";
                                                                        $btn_id = "";
                                                                        $otherAttributes = "";
                                                                    @endphp
                                                                    <x-button :otherAttributes="$otherAttributes" :btnText="$btnText" toolTip="" btnType="primary" type="submit" btnSize="sm" :otherClasses="$otherClasses" :btnId="$btn_id" :readOnly="$readonly" />
                                                                    {{-- <button type="submit" {{$readonly}} class="btn btn-primary my-1"><i class="fas fa-save"></i> Save</button> --}}
                                                                </div>
                                                                <div class="col-md-6">
                                                                    @php
                                                                        $btnText = "<i class=\"fas fa-eye\"></i> View Sample";
                                                                        $otherClasses = "my-1";
                                                                        $btn_id = "view_sample_account_deactivated";
                                                                        $otherAttributes = "";
                                                                    @endphp
                                                                    <x-button :otherAttributes="$otherAttributes" :btnText="$btnText" toolTip="" btnType="primary" type="button" btnSize="sm" :otherClasses="$otherClasses" :btnId="$btn_id" :readOnly="$readonly" />
                                                                    {{-- <button id="view_sample_account_deactivated" type="button" class="btn btn-primary my-1"><i class="fas fa-eye"></i> View Sample</button> --}}
                                                                </div>
                                                            </div>
                                                        </form>
                                                        <div class="container" id="account_deactivated_win_sample">
                                                            <p id="account_deactivated_contents"></p>
                                                            @php
                                                                $btnText = "<i class=\"fas fa-pen\"></i> Edit";
                                                                $otherClasses = "my-1";
                                                                $btn_id = "backto_account_deactivated";
                                                                $otherAttributes = "";
                                                            @endphp
                                                            <x-button :otherAttributes="$otherAttributes" :btnText="$btnText" toolTip="" btnType="primary" type="button" btnSize="sm" :otherClasses="$otherClasses" :btnId="$btn_id" :readOnly="$readonly" />
                                                            {{-- <button id="backto_account_deactivated" type="button" class="btn btn-primary my-1"><i class="fas fa-pen"></i> Edit</button> --}}
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </section>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="content-body">
                <!-- Basic Tables start -->
                <div class="row">
                    <div class="col-12">
                        <div class="card">
                            <div class="card-header my-0">
                                <h4 class="card-title">Freezing Of Accounts 
                                    @if (date("YmdHis") < 20230630000000)
                                        <div class="badge badge-success">New</div>
                                    @endif
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

                            <div class="card-content">
                                <div class="card-body">
                                    <div class="card-text">
                                        <h6>Note:</h6>
                                        <p>Tags to be used in this section only!</p>
                                        <div class="row">
                                            <div class="row col-md-6">
                                                <div class="col-md-6  ">
                                                    <p><b>Name</b></p>
                                                </div>
                                                <div class="col-md-6  ">
                                                    <p><b>Tag</b></p>
                                                </div>
                                                <div class="col-md-6  border border-light">
                                                    <p>1. <span class="text-danger">Unfreeze Date</span></p>
                                                </div>
                                                <div class="col-md-6  border border-light">
                                                    <p><code>[unfreeze_date]</code></p>
                                                </div>
                                                <div class="col-md-6  border border-light">
                                                    <p>2. <span class="text-danger">Frozen Days</span></p>
                                                </div>
                                                <div class="col-md-6  border border-light">
                                                    <p><code>[days_frozen]</code></p>
                                                </div>
                                                <div class="col-md-6  border border-light">
                                                    <p>3. <span class="text-danger">Frozen Date</span></p>
                                                </div>
                                                <div class="col-md-6  border border-light">
                                                    <p><code>[frozen_date]</code></p>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="card-content">
                                <div class="card-body">
                                    <p>Time sent : <b>When the client account has been frozen or unfrozen.</b></p>
                                    <!-- Basic Tables end -->
                                    <section id="html-headings-default" class="row match-height">
                                        <div class="col-sm-12 shadow col-md-6">
                                            <div class="card">
                                                <div class="card-header">
                                                    <h4 class="card-title">Account Freezing</h4>
                                                </div>
                                                <hr class="my-0">
                                                <div class="card-content">
                                                    <div class="card-body">
                                                        <form id="account_frozen_win" class="form-group d-none" action="/save_sms_content" method="post">
                                                            @csrf
                                                            <label for="message_contents_17" class="form-control-label">
                                                                Message Content
                                                            </label>
                                                            <input type="hidden" name="account_frozen" value="account_frozen">
                                                            <input type="hidden" name="category" value="account_freezing">
                                                            <textarea name="message_contents" id="message_contents_17" cols="30" rows="5" class="form-control p-2" placeholder="Write your message here ...">{{$sms_data[5]->messages[0]->message ?? 'No message set for this section at the moment'}}</textarea>
                                                            <div class="row my-1">
                                                                <div class="col-md-6">
                                                                    @php
                                                                        $btnText = "<i class=\"fas fa-save\"></i> Save";
                                                                        $otherClasses = "my-1";
                                                                        $btn_id = "";
                                                                        $otherAttributes = "";
                                                                    @endphp
                                                                    <x-button :otherAttributes="$otherAttributes" :btnText="$btnText" toolTip="" btnType="primary" type="submit" btnSize="sm" :otherClasses="$otherClasses" :btnId="$btn_id" :readOnly="$readonly" />
                                                                    {{-- <button type="submit" {{$readonly}} class="btn btn-primary my-1"><i class="fas fa-save"></i> Save</button> --}}
                                                                </div>
                                                                <div class="col-md-6">
                                                                    @php
                                                                        $btnText = "<i class=\"fas fa-eye\"></i> View Sample";
                                                                        $otherClasses = "my-1";
                                                                        $btn_id = "view_sample_account_frozen";
                                                                        $otherAttributes = "";
                                                                    @endphp
                                                                    <x-button :otherAttributes="$otherAttributes" :btnText="$btnText" toolTip="" btnType="primary" type="button" btnSize="sm" :otherClasses="$otherClasses" :btnId="$btn_id" :readOnly="$readonly" />
                                                                    {{-- <button id="view_sample_account_frozen" type="button" class="btn btn-primary my-1"><i class="fas fa-eye"></i> View Sample</button> --}}
                                                                </div>
                                                            </div>
                                                        </form>
                                                        <div class="container" id="account_frozen_win_sample">
                                                            <p id="account_frozen_contents"></p>
                                                            @php
                                                                $btnText = "<i class=\"fas fa-pen\"></i> Edit";
                                                                $otherClasses = "my-1";
                                                                $btn_id = "backto_account_frozen";
                                                                $otherAttributes = "";
                                                            @endphp
                                                            <x-button :otherAttributes="$otherAttributes" :btnText="$btnText" toolTip="" btnType="primary" type="button" btnSize="sm" :otherClasses="$otherClasses" :btnId="$btn_id" :readOnly="$readonly" />
                                                            {{-- <button id="backto_account_frozen" type="button" class="btn btn-primary my-1"><i class="fas fa-pen"></i> Edit</button> --}}
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-sm-12 shadow col-md-6">
                                            <div class="card">
                                                <div class="card-header">
                                                    <h4 class="card-title">Account Unfrozen </h4>
                                                </div>
                                                <hr class="my-0">
                                                <div class="card-content">
                                                    <div class="card-body">
                                                        <form id="account_unfrozen_win" class="form-group d-none" action="/save_sms_content" method="post">
                                                            @csrf
                                                            <label for="message_contents_18" class="form-control-label">
                                                                Message Content
                                                            </label>
                                                            <input type="hidden" name="account_unfrozen" value="account_unfrozen">
                                                            <input type="hidden" name="category" value="account_freezing">
                                                            <textarea name="message_contents" id="message_contents_18" cols="30" rows="5" class="form-control p-2" placeholder="Write your message here ...">{{$sms_data[5]->messages[1]->message ?? 'No message set for this section at the moment'}}</textarea>
                                                            <div class="row my-1">
                                                                <div class="col-md-6">
                                                                    @php
                                                                        $btnText = "<i class=\"fas fa-save\"></i> Save";
                                                                        $otherClasses = "my-1";
                                                                        $btn_id = "";
                                                                        $otherAttributes = "";
                                                                    @endphp
                                                                    <x-button :otherAttributes="$otherAttributes" :btnText="$btnText" toolTip="" btnType="primary" type="submit" btnSize="sm" :otherClasses="$otherClasses" :btnId="$btn_id" :readOnly="$readonly" />
                                                                    {{-- <button type="submit" {{$readonly}} class="btn btn-primary my-1"><i class="fas fa-save"></i> Save</button> --}}
                                                                </div>
                                                                <div class="col-md-6">
                                                                    @php
                                                                        $btnText = "<i class=\"fas fa-eye\"></i> View Sample";
                                                                        $otherClasses = "my-1";
                                                                        $btn_id = "view_sample_account_unfrozen";
                                                                        $otherAttributes = "";
                                                                    @endphp
                                                                    <x-button :otherAttributes="$otherAttributes" :btnText="$btnText" toolTip="" btnType="primary" type="button" btnSize="sm" :otherClasses="$otherClasses" :btnId="$btn_id" :readOnly="$readonly" />
                                                                    {{-- <button id="view_sample_account_unfrozen" type="button" class="btn btn-primary my-1"><i class="fas fa-eye"></i> View Sample</button> --}}
                                                                </div>
                                                            </div>
                                                        </form>
                                                        <div class="container" id="account_unfrozen_win_sample">
                                                            <p id="account_unfrozen_contents"></p>
                                                            @php
                                                                $btnText = "<i class=\"fas fa-pen\"></i> Edit";
                                                                $otherClasses = "my-1";
                                                                $btn_id = "backto_account_unfrozen";
                                                                $otherAttributes = "";
                                                            @endphp
                                                            <x-button :otherAttributes="$otherAttributes" :btnText="$btnText" toolTip="" btnType="primary" type="button" btnSize="sm" :otherClasses="$otherClasses" :btnId="$btn_id" :readOnly="$readonly" />
                                                            {{-- <button id="backto_account_unfrozen" type="button" class="btn btn-primary my-1"><i class="fas fa-pen"></i> Edit</button> --}}
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-sm-12 shadow col-md-6 my-2">
                                            <div class="card">
                                                <div class="card-header">
                                                    <h4 class="card-title">Account to be Frozen in the future </h4>
                                                </div>
                                                <hr class="my-0">
                                                <div class="card-content">
                                                    <div class="card-body">
                                                        <form id="future_account_freeze_win" class="form-group d-none" action="/save_sms_content" method="post">
                                                            @csrf
                                                            <label for="message_contents_19" class="form-control-label">
                                                                Message Content
                                                            </label>
                                                            <input type="hidden" name="future_account_freeze" value="future_account_freeze">
                                                            <input type="hidden" name="category" value="account_freezing">
                                                            <textarea name="message_contents" id="message_contents_19" cols="30" rows="5" class="form-control p-2" placeholder="Write your message here ...">{{$sms_data[5]->messages[2]->message ?? 'No message set for this section at the moment'}}</textarea>
                                                            <div class="row my-1">
                                                                <div class="col-md-6">
                                                                    @php
                                                                        $btnText = "<i class=\"fas fa-save\"></i> Save";
                                                                        $otherClasses = "my-1";
                                                                        $btn_id = "";
                                                                        $otherAttributes = "";
                                                                    @endphp
                                                                    <x-button :otherAttributes="$otherAttributes" :btnText="$btnText" toolTip="" btnType="primary" type="submit" btnSize="sm" :otherClasses="$otherClasses" :btnId="$btn_id" :readOnly="$readonly" />
                                                                    {{-- <button type="submit" {{$readonly}} class="btn btn-primary my-1"><i class="fas fa-save"></i> Save</button> --}}
                                                                </div>
                                                                <div class="col-md-6">
                                                                    @php
                                                                        $btnText = "<i class=\"fas fa-eye\"></i> View Sample";
                                                                        $otherClasses = "my-1";
                                                                        $btn_id = "view_sample_future_account_freeze";
                                                                        $otherAttributes = "";
                                                                    @endphp
                                                                    <x-button :otherAttributes="$otherAttributes" :btnText="$btnText" toolTip="" btnType="primary" type="button" btnSize="sm" :otherClasses="$otherClasses" :btnId="$btn_id" :readOnly="$readonly" />
                                                                    {{-- <button id="view_sample_future_account_freeze" type="button" class="btn btn-primary my-1"><i class="fas fa-eye"></i> View Sample</button> --}}
                                                                </div>
                                                            </div>
                                                        </form>
                                                        <div class="container" id="future_account_freeze_win_sample">
                                                            <p id="future_account_freeze_contents"></p>
                                                            @php
                                                                $btnText = "<i class=\"fas fa-pen\"></i> Edit";
                                                                $otherClasses = "my-1";
                                                                $btn_id = "backto_future_account_freeze";
                                                                $otherAttributes = "";
                                                            @endphp
                                                            <x-button :otherAttributes="$otherAttributes" :btnText="$btnText" toolTip="" btnType="primary" type="button" btnSize="sm" :otherClasses="$otherClasses" :btnId="$btn_id" :readOnly="$readonly" />
                                                            {{-- <button id="backto_future_account_freeze" type="button" class="btn btn-primary my-1"><i class="fas fa-pen"></i> Edit</button> --}}
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </section>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="content-body">
                <!-- Basic Tables start -->
                <div class="row">
                    <div class="col-12">
                        <div class="card">
                            <div class="card-header my-0">
                                <h4 class="card-title">Welcome SMS</h4>
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
                            <div class="card-content">
                                <div class="card-body">
                                    <p>Time sent : <b>When a new Client is registered to the System.</b></p>
                                    <!-- Basic Tables end -->
                                    <section id="html-headings-default" class="row match-height">
                                        <div class="col-sm-12 shadow col-md-6">
                                            <div class="card">
                                                <div class="card-header">
                                                    <h4 class="card-title">Client Welcome SMS</h4>
                                                </div>
                                                <hr class="my-0">
                                                <div class="card-content">
                                                    <div class="card-body">
                                                        <form id="welcome_sms_win" class="form-group d-none" action="/save_sms_content" method="post">
                                                            @csrf
                                                            <label for="message_contents_8" class="form-control-label">
                                                                Message Content
                                                            </label>
                                                            <input type="hidden" name="welcome_sms" value="welcome_sms">
                                                            <input type="hidden" name="category" value="funds_recieved">
                                                            <textarea name="message_contents" id="message_contents_8" cols="30" rows="5" class="form-control p-2" placeholder="Write your message here ...">{{$sms_data[3]->messages[0]->message ?? 'No message set for this section at the moment'}}</textarea>
                                                            <div class="row my-1">
                                                                <div class="col-md-6">
                                                                    @php
                                                                        $btnText = "<i class=\"fas fa-save\"></i> Save";
                                                                        $otherClasses = "my-1";
                                                                        $btn_id = "";
                                                                        $otherAttributes = "";
                                                                    @endphp
                                                                    <x-button :otherAttributes="$otherAttributes" :btnText="$btnText" toolTip="" btnType="primary" type="submit" btnSize="sm" :otherClasses="$otherClasses" :btnId="$btn_id" :readOnly="$readonly" />
                                                                    {{-- <button type="submit" {{$readonly}} class="btn btn-primary my-1"><i class="fas fa-save"></i> Save</button> --}}
                                                                </div>
                                                                <div class="col-md-6">
                                                                    @php
                                                                        $btnText = "<i class=\"fas fa-eye\"></i> View Sample";
                                                                        $otherClasses = "my-1";
                                                                        $btn_id = "view_sample_welcome_sms";
                                                                        $otherAttributes = "";
                                                                    @endphp
                                                                    <x-button :otherAttributes="$otherAttributes" :btnText="$btnText" toolTip="" btnType="primary" type="button" btnSize="sm" :otherClasses="$otherClasses" :btnId="$btn_id" :readOnly="$readonly" />
                                                                    {{-- <button id="view_sample_welcome_sms" type="button" class="btn btn-primary my-1"><i class="fas fa-eye"></i> View Sample</button> --}}
                                                                </div>
                                                            </div>
                                                        </form>
                                                        <div class="container" id="welcome_sms_win_sample">
                                                            <p id="welcome_sms_contents"></p>
                                                            @php
                                                                $btnText = "<i class=\"fas fa-pen\"></i> Edit";
                                                                $otherClasses = "my-1";
                                                                $btn_id = "backto_welcome_sms";
                                                                $otherAttributes = "";
                                                            @endphp
                                                            <x-button :otherAttributes="$otherAttributes" :btnText="$btnText" toolTip="" btnType="primary" type="button" btnSize="sm" :otherClasses="$otherClasses" :btnId="$btn_id" :readOnly="$readonly" />
                                                            {{-- <button id="backto_welcome_sms" type="button" class="btn btn-primary my-1"><i class="fas fa-pen"></i> Edit</button> --}}
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </section>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="content-body d-none">
                <!-- Basic Tables start -->
                <div class="row">
                    <div class="col-12">
                        <div class="card">
                            <div class="card-header my-0">
                                <h4 class="card-title">SMSes for <span class="text-danger">Billing SMS manager</span></h4>
                                <p><b>This messages are related to the Billing SMS manager.</b></p>
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
                            <div class="card-content">
                                <div class="card-body">
                                    <div class="card-text">
                                        <h6>Note:</h6>
                                        <p>Tags to be used in this section only!</p>
                                        <div class="row">
                                            <div class="row col-md-6">
                                                <div class="col-md-6  ">
                                                    <p><b>Name</b></p>
                                                </div>
                                                <div class="col-md-6  ">
                                                    <p><b>Tag</b></p>
                                                </div>
                                                <div class="col-md-6  border border-light">
                                                    <p>1. <span class="text-danger">Transacted Amount</span></p>
                                                </div>
                                                <div class="col-md-6  border border-light">
                                                    <p><code>[trans_amnt]</code></p>
                                                </div>
                                                <div class="col-md-6  border border-light">
                                                    <p>2. <span class="text-danger">Minimum Amount</span></p>
                                                </div>
                                                <div class="col-md-6  border border-light">
                                                    <p><code>[min_amnt]</code></p>
                                                </div>
                                                <div class="col-md-6  border border-light">
                                                    <p>3. <span class="text-danger">SMS Rate</span></p>
                                                </div>
                                                <div class="col-md-6  border border-light">
                                                    <p><code>[sms_rate]</code></p>
                                                </div>
                                                <div class="col-md-6  border border-light">
                                                    <p>4. <span class="text-danger">SMS Balance</span></p>
                                                </div>
                                                <div class="col-md-6  border border-light">
                                                    <p><code>[sms_balance]</code></p>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="card-content">
                                <div class="card-body">
                                    <p>Time sent : <b> When registering a new client.</b></p>
                                    <!-- Basic Tables end -->
                                    <section id="html-headings-default" class="row match-height">
                                        <div class="col-sm-12 shadow col-md-6">
                                            <div class="card">
                                                <div class="card-header">
                                                    <h4 class="card-title">Client Welcome SMS</h4>
                                                </div>
                                                <hr class="my-0">
                                                <div class="card-content">
                                                    <div class="card-body">
                                                        <form id="welcome_client_sms_win" class="form-group d-none" action="/save_sms_content" method="post">
                                                            @csrf
                                                            <label for="message_contents_12" class="form-control-label">
                                                                Message Content
                                                            </label>
                                                            <input type="hidden" name="welcome_client_sms" value="welcome_client_sms">
                                                            <input type="hidden" name="category" value="sms_bill_manager">
                                                            <?php
                                                                $message_data = "";
                                                                if (isset($sms_data[4])) {
                                                                    for ($index=0; $index < count($sms_data[4]->messages); $index++) { 
                                                                        if ($sms_data[4]->messages[$index]->Name == "welcome_client_sms") {
                                                                            $message_data = $sms_data[4]->messages[$index]->message;
                                                                        }
                                                                    }
                                                                }
                                                                $message_data = strlen($message_data) > 0 ? $message_data : 'No message set for this section at the moment';
                                                            ?>
                                                            <textarea name="message_contents" id="message_contents_12" cols="30" rows="5" class="form-control p-2" placeholder="Write your message here ...">{{
                                                                $message_data}}</textarea>
                                                            <div class="row my-1">
                                                                <div class="col-md-6">
                                                                    @php
                                                                        $btnText = "<i class=\"fas fa-save\"></i> Save";
                                                                        $otherClasses = "my-1";
                                                                        $btn_id = "";
                                                                        $otherAttributes = "";
                                                                    @endphp
                                                                    <x-button :otherAttributes="$otherAttributes" :btnText="$btnText" toolTip="" btnType="primary" type="submit" btnSize="sm" :otherClasses="$otherClasses" :btnId="$btn_id" :readOnly="$readonly" />
                                                                    {{-- <button type="submit" {{$readonly}} class="btn btn-primary my-1"><i class="fas fa-save"></i> Save</button> --}}
                                                                </div>
                                                                <div class="col-md-6">
                                                                    @php
                                                                        $btnText = "<i class=\"fas fa-eye\"></i> View Sample";
                                                                        $otherClasses = "my-1";
                                                                        $btn_id = "view_sample_welcome_client_sms";
                                                                        $otherAttributes = "";
                                                                    @endphp
                                                                    <x-button :otherAttributes="$otherAttributes" :btnText="$btnText" toolTip="" btnType="primary" type="button" btnSize="sm" :otherClasses="$otherClasses" :btnId="$btn_id" :readOnly="$readonly" />
                                                                    {{-- <button id="view_sample_welcome_client_sms" type="button" class="btn btn-primary my-1"><i class="fas fa-eye"></i> View Sample</button> --}}
                                                                </div>
                                                            </div>
                                                        </form>
                                                        <div class="container" id="welcome_client_sms_win_sample">
                                                            <p id="welcome_client_sms_contents"></p>
                                                            @php
                                                                $btnText = "<i class=\"fas fa-pen\"></i> Edit";
                                                                $otherClasses = "my-1";
                                                                $btn_id = "backto_welcome_client_sms";
                                                                $otherAttributes = "";
                                                            @endphp
                                                            <x-button :otherAttributes="$otherAttributes" :btnText="$btnText" toolTip="" btnType="primary" type="button" btnSize="sm" :otherClasses="$otherClasses" :btnId="$btn_id" :readOnly="$readonly" />
                                                            {{-- <button id="backto_welcome_client_sms" type="button" class="btn btn-primary my-1"><i class="fas fa-pen"></i> Edit</button> --}}
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-sm-12 shadow col-md-6">
                                            <div class="card">
                                                <div class="card-header">
                                                    <h4 class="card-title">Recieve payment correct account number</h4>
                                                </div>
                                                <hr class="my-0">
                                                <div class="card-content">
                                                    <div class="card-body">
                                                        <form id="rcv_coracc_billsms_win" class="form-group d-none" action="/save_sms_content" method="post">
                                                            @csrf
                                                            <label for="message_contents_13" class="form-control-label">
                                                                Message Content
                                                            </label>
                                                            <input type="hidden" name="rcv_coracc_billsms" value="rcv_coracc_billsms">
                                                            <input type="hidden" name="category" value="sms_bill_manager">
                                                            <?php
                                                                $message_data = "";
                                                                if (isset($sms_data[4])) {
                                                                    for ($index=0; $index < count($sms_data[4]->messages); $index++) { 
                                                                        if ($sms_data[4]->messages[$index]->Name == "rcv_coracc_billsms") {
                                                                            $message_data = $sms_data[4]->messages[$index]->message;
                                                                        }
                                                                    }
                                                                }
                                                                $message_data = strlen($message_data) > 0 ? $message_data : 'No message set for this section at the moment';
                                                            ?>
                                                            <textarea name="message_contents" id="message_contents_13" cols="30" rows="5" class="form-control p-2" placeholder="Write your message here ...">{{
                                                                $message_data}}</textarea>
                                                            <div class="row my-1">
                                                                <div class="col-md-6">
                                                                    @php
                                                                        $btnText = "<i class=\"fas fa-save\"></i> Save";
                                                                        $otherClasses = "my-1";
                                                                        $btn_id = "";
                                                                        $otherAttributes = "";
                                                                    @endphp
                                                                    <x-button :otherAttributes="$otherAttributes" :btnText="$btnText" toolTip="" btnType="primary" type="submit" btnSize="sm" :otherClasses="$otherClasses" :btnId="$btn_id" :readOnly="$readonly" />
                                                                    {{-- <button type="submit" {{$readonly}} class="btn btn-primary my-1"><i class="fas fa-save"></i> Save</button> --}}
                                                                </div>
                                                                <div class="col-md-6">
                                                                    @php
                                                                        $btnText = "<i class=\"fas fa-eye\"></i> View Sample";
                                                                        $otherClasses = "my-1";
                                                                        $btn_id = "view_sample_rcv_coracc_billsms";
                                                                        $otherAttributes = "";
                                                                    @endphp
                                                                    <x-button :otherAttributes="$otherAttributes" :btnText="$btnText" toolTip="" btnType="primary" type="button" btnSize="sm" :otherClasses="$otherClasses" :btnId="$btn_id" :readOnly="$readonly" />
                                                                    {{-- <button id="view_sample_rcv_coracc_billsms" type="button" class="btn btn-primary my-1"><i class="fas fa-eye"></i> View Sample</button> --}}
                                                                </div>
                                                            </div>
                                                        </form>
                                                        <div class="container" id="rcv_coracc_billsms_win_sample">
                                                            <p id="rcv_coracc_billsms_contents"></p>
                                                            @php
                                                                $btnText = "<i class=\"fas fa-pen\"></i> Edit";
                                                                $otherClasses = "my-1";
                                                                $btn_id = "backto_rcv_coracc_billsms";
                                                                $otherAttributes = "";
                                                            @endphp
                                                            <x-button :otherAttributes="$otherAttributes" :btnText="$btnText" toolTip="" btnType="primary" type="button" btnSize="sm" :otherClasses="$otherClasses" :btnId="$btn_id" :readOnly="$readonly" />
                                                            {{-- <button id="backto_rcv_coracc_billsms" type="button" class="btn btn-primary my-1"><i class="fas fa-pen"></i> Edit</button> --}}
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-sm-12 shadow col-md-6 my-2">
                                            <div class="card">
                                                <div class="card-header">
                                                    <h4 class="card-title">Recieve payment but incorrect account number</h4>
                                                </div>
                                                <hr class="my-0">
                                                <div class="card-content">
                                                    <div class="card-body">
                                                        <form id="rcv_incoracc_billsms_win" class="form-group d-none" action="/save_sms_content" method="post">
                                                            @csrf
                                                            <label for="message_contents_13" class="form-control-label">
                                                                Message Content
                                                            </label>
                                                            <input type="hidden" name="rcv_incoracc_billsms" value="rcv_incoracc_billsms">
                                                            <input type="hidden" name="category" value="sms_bill_manager">
                                                            <?php
                                                                $message_data = "";
                                                                if (isset($sms_data[4])) {
                                                                    for ($index=0; $index < count($sms_data[4]->messages); $index++) { 
                                                                        if ($sms_data[4]->messages[$index]->Name == "rcv_incoracc_billsms") {
                                                                            $message_data = $sms_data[4]->messages[$index]->message;
                                                                        }
                                                                    }
                                                                }
                                                                $message_data = strlen($message_data) > 0 ? $message_data : 'No message set for this section at the moment';
                                                            ?>
                                                            <textarea name="message_contents" id="message_contents_14" cols="30" rows="5" class="form-control p-2" placeholder="Write your message here ...">{{
                                                                $message_data}}</textarea>
                                                            <div class="row my-1">
                                                                <div class="col-md-6">
                                                                    @php
                                                                        $btnText = "<i class=\"fas fa-save\"></i> Save";
                                                                        $otherClasses = "my-1";
                                                                        $btn_id = "";
                                                                        $otherAttributes = "";
                                                                    @endphp
                                                                    <x-button :otherAttributes="$otherAttributes" :btnText="$btnText" toolTip="" btnType="primary" type="submit" btnSize="sm" :otherClasses="$otherClasses" :btnId="$btn_id" :readOnly="$readonly" />
                                                                    {{-- <button type="submit" {{$readonly}} class="btn btn-primary my-1"><i class="fas fa-save"></i> Save</button> --}}
                                                                </div>
                                                                <div class="col-md-6">
                                                                    @php
                                                                        $btnText = "<i class=\"fas fa-eye\"></i> View Sample";
                                                                        $otherClasses = "my-1";
                                                                        $btn_id = "view_sample_rcv_incoracc_billsms";
                                                                        $otherAttributes = "";
                                                                    @endphp
                                                                    <x-button :otherAttributes="$otherAttributes" :btnText="$btnText" toolTip="" btnType="primary" type="button" btnSize="sm" :otherClasses="$otherClasses" :btnId="$btn_id" :readOnly="$readonly" />
                                                                    {{-- <button id="view_sample_rcv_incoracc_billsms" type="button" class="btn btn-primary my-1"><i class="fas fa-eye"></i> View Sample</button> --}}
                                                                </div>
                                                            </div>
                                                        </form>
                                                        <div class="container" id="rcv_incoracc_billsms_win_sample">
                                                            <p id="rcv_incoracc_billsms_contents"></p>
                                                            @php
                                                                $btnText = "<i class=\"fas fa-pen\"></i> Edit";
                                                                $otherClasses = "my-1";
                                                                $btn_id = "backto_rcv_incoracc_billsms";
                                                                $otherAttributes = "";
                                                            @endphp
                                                            <x-button :otherAttributes="$otherAttributes" :btnText="$btnText" toolTip="" btnType="primary" type="button" btnSize="sm" :otherClasses="$otherClasses" :btnId="$btn_id" :readOnly="$readonly" />
                                                            {{-- <button id="backto_rcv_incoracc_billsms" type="button" class="btn btn-primary my-1"><i class="fas fa-pen"></i> Edit</button> --}}
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-sm-12 shadow col-md-6 my-2">
                                            <div class="card">
                                                <div class="card-header">
                                                    <h4 class="card-title">Recieve payment but below the minimum amount</h4>
                                                </div>
                                                <hr class="my-0">
                                                <div class="card-content">
                                                    <div class="card-body">
                                                        <form id="rcv_belowmin_billsms_win" class="form-group d-none" action="/save_sms_content" method="post">
                                                            @csrf
                                                            <label for="message_contents_13" class="form-control-label">
                                                                Message Content
                                                            </label>
                                                            <input type="hidden" name="rcv_belowmin_billsms" value="rcv_belowmin_billsms">
                                                            <input type="hidden" name="category" value="sms_bill_manager">
                                                            <?php
                                                                $message_data = "";
                                                                if (isset($sms_data[4])) {
                                                                    for ($index=0; $index < count($sms_data[4]->messages); $index++) { 
                                                                        if ($sms_data[4]->messages[$index]->Name == "rcv_belowmin_billsms") {
                                                                            $message_data = $sms_data[4]->messages[$index]->message;
                                                                        }
                                                                    }
                                                                }
                                                                $message_data = strlen($message_data) > 0 ? $message_data : 'No message set for this section at the moment';
                                                            ?>
                                                            <textarea name="message_contents" id="message_contents_15" cols="30" rows="5" class="form-control p-2" placeholder="Write your message here ...">{{
                                                                $message_data}}</textarea>
                                                            <div class="row my-1">
                                                                <div class="col-md-6">
                                                                    @php
                                                                        $btnText = "<i class=\"fas fa-save\"></i> Save";
                                                                        $otherClasses = "my-1";
                                                                        $btn_id = "";
                                                                        $otherAttributes = "";
                                                                    @endphp
                                                                    <x-button :otherAttributes="$otherAttributes" :btnText="$btnText" toolTip="" btnType="primary" type="submit" btnSize="sm" :otherClasses="$otherClasses" :btnId="$btn_id" :readOnly="$readonly" />
                                                                    {{-- <button type="submit" {{$readonly}} class="btn btn-primary my-1"><i class="fas fa-save"></i> Save</button> --}}
                                                                </div>
                                                                <div class="col-md-6">
                                                                    @php
                                                                        $btnText = "<i class=\"fas fa-eye\"></i> View Sample";
                                                                        $otherClasses = "my-1";
                                                                        $btn_id = "view_sample_rcv_belowmin_billsms";
                                                                        $otherAttributes = "";
                                                                    @endphp
                                                                    <x-button :otherAttributes="$otherAttributes" :btnText="$btnText" toolTip="" btnType="primary" type="button" btnSize="sm" :otherClasses="$otherClasses" :btnId="$btn_id" :readOnly="$readonly" />
                                                                    {{-- <button id="view_sample_rcv_belowmin_billsms" type="button" class="btn btn-primary my-1"><i class="fas fa-eye"></i> View Sample</button> --}}
                                                                </div>
                                                            </div>
                                                        </form>
                                                        <div class="container" id="rcv_belowmin_billsms_win_sample">
                                                            <p id="rcv_belowmin_billsms_contents"></p>
                                                            @php
                                                                $btnText = "<i class=\"fas fa-pen\"></i> Edit";
                                                                $otherClasses = "my-1";
                                                                $btn_id = "backto_rcv_belowmin_billsms";
                                                                $otherAttributes = "";
                                                            @endphp
                                                            <x-button :otherAttributes="$otherAttributes" :btnText="$btnText" toolTip="" btnType="primary" type="button" btnSize="sm" :otherClasses="$otherClasses" :btnId="$btn_id" :readOnly="$readonly" />
                                                            {{-- <button id="backto_rcv_belowmin_billsms" type="button" class="btn btn-primary my-1"><i class="fas fa-pen"></i> Edit</button> --}}
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-sm-12 shadow col-md-6 my-2">
                                            <div class="card">
                                                <div class="card-header">
                                                    <h4 class="card-title">Reminder for sms balance when low</h4>
                                                </div>
                                                <hr class="my-0">
                                                <div class="card-content">
                                                    <div class="card-body">
                                                        <form id="msg_reminder_bal_win" class="form-group d-none" action="/save_sms_content" method="post">
                                                            @csrf
                                                            <label for="message_contents_16" class="form-control-label">
                                                                Message Content
                                                            </label>
                                                            <input type="hidden" name="msg_reminder_bal" value="msg_reminder_bal">
                                                            <input type="hidden" name="category" value="sms_bill_manager">
                                                            <?php
                                                                $message_data = "";
                                                                if (isset($sms_data[4])) {
                                                                    for ($index=0; $index < count($sms_data[4]->messages); $index++) { 
                                                                        if ($sms_data[4]->messages[$index]->Name == "msg_reminder_bal") {
                                                                            $message_data = $sms_data[4]->messages[$index]->message;
                                                                        }
                                                                    }
                                                                }
                                                                $message_data = strlen($message_data) > 0 ? $message_data : 'No message set for this section at the moment';
                                                            ?>
                                                            <textarea name="message_contents" id="message_contents_16" cols="30" rows="5" class="form-control p-2" placeholder="Write your message here ...">{{
                                                                $message_data}}</textarea>
                                                            <div class="row my-1">
                                                                <div class="col-md-6">
                                                                    @php
                                                                        $btnText = "<i class=\"fas fa-save\"></i> Save";
                                                                        $otherClasses = "my-1";
                                                                        $btn_id = "";
                                                                        $otherAttributes = "";
                                                                    @endphp
                                                                    <x-button :otherAttributes="$otherAttributes" :btnText="$btnText" toolTip="" btnType="primary" type="submit" btnSize="sm" :otherClasses="$otherClasses" :btnId="$btn_id" :readOnly="$readonly" />
                                                                    {{-- <button type="submit" {{$readonly}} class="btn btn-primary my-1"><i class="fas fa-save"></i> Save</button> --}}
                                                                </div>
                                                                <div class="col-md-6">
                                                                    @php
                                                                        $btnText = "<i class=\"fas fa-eye\"></i> View Sample";
                                                                        $otherClasses = "my-1";
                                                                        $btn_id = "view_sample_msg_reminder_bal";
                                                                        $otherAttributes = "";
                                                                    @endphp
                                                                    <x-button :otherAttributes="$otherAttributes" :btnText="$btnText" toolTip="" btnType="primary" type="button" btnSize="sm" :otherClasses="$otherClasses" :btnId="$btn_id" :readOnly="$readonly" />
                                                                    {{-- <button id="view_sample_msg_reminder_bal" type="button" class="btn btn-primary my-1"><i class="fas fa-eye"></i> View Sample</button> --}}
                                                                </div>
                                                            </div>
                                                        </form>
                                                        <div class="container" id="msg_reminder_bal_win_sample">
                                                            <p id="msg_reminder_bal_contents"></p>
                                                            @php
                                                                $btnText = "<i class=\"fas fa-pen\"></i> Edit";
                                                                $otherClasses = "my-1";
                                                                $btn_id = "backto_msg_reminder_bal";
                                                                $otherAttributes = "";
                                                            @endphp
                                                            <x-button :otherAttributes="$otherAttributes" :btnText="$btnText" toolTip="" btnType="primary" type="button" btnSize="sm" :otherClasses="$otherClasses" :btnId="$btn_id" :readOnly="$readonly" />
                                                            {{-- <button id="backto_msg_reminder_bal" type="button" class="btn btn-primary my-1"><i class="fas fa-pen"></i> Edit</button> --}}
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </section>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    </div>
    <!-- ////////////////////////////////////////////////////////////////////////////-->
    <!-- The footer -->
    <footer style="margin-bottom: 0% !important" class="footer footer-static footer-light navbar-border navbar-shadow">
    <div  class="clearfix blue-grey lighten-2 text-sm-center mb-0 px-2"><span class="float-md-left d-block d-md-inline-block"><?php echo date("Y"); ?> &copy; Copyright Hypbits Enterprises</span>
        <ul class="list-inline float-md-right d-block d-md-inline-blockd-none d-lg-block mb-0">
            <li class="list-inline-item">Created By<a class="my-1" href="https://ladybirdsmis.com" target="_blank"> Ladybird Softech Co.</a></li>
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
    <script src="/theme-assets/js/core/custom_sms.js" type="text/javascript"></script>
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
