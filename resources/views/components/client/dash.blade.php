<div class="row">
    <div class="col-md-9">
        <p><strong>Note: </strong><br> - User status when active the user will recieve
            internet connection. <br>
            - Automate transaction when active the system will monitor the clients payment
            process and activate or deactivate the client when necessary <br>
            - When a user is frozen don`t activate any option either the <b>Automate Transaction</b> or the <b>User Status</b>
        </p>
    </div>
    <div class="col-md-3 border-left border-secondary my-1">
        <h6 class="text-center"><b><u>Quick Actions</u></b></h6>
        @php
            $btnText = "<i class=\"fas fa-trash\"></i> Delete";
            $validated = $clients_data[0]->validated == 0 ? "float-right my-1 d-none" : "float-right my-1";
        @endphp
        <x-button :btnText="$btnText" btnType="danger" btnSize="sm" :otherClasses="$validated" btnId="prompt_delete" :readOnly="$readonly" />
        @php
            $btnText = '<i class="fas fa-refresh"></i> Convert Client';
            $validated = $clients_data[0]->validated == 0 ? "d-none my-1" : "my-1";
        @endphp
        <x-button :btnText="$btnText" btnType="info" btnSize="sm" :otherClasses="$validated" btnId="convert_client" :readOnly="$readonly" />
    </div>
</div>
<div class="container">
    @if ($clients_data[0]->assignment == "static")
        {{-- CONVERT CLIENT TO PPPOE --}}
        <div class="modal fade text-left hide" style="background-color: rgba(0, 0, 0, 0.5);" id="convert_client_modal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel3" style="padding-right: 17px;" aria-modal="true">
            <div class="modal-dialog modal-dialog-centered" role="document">
                <div class="modal-content">
                    <div class="modal-header bg-info white">
                        <h5 class="modal-title white" id="myModalLabel3">Convert "{{ucwords(strtolower($clients_data[0]->client_name))}}" to PPPOE.</h5>
                        <input type="hidden" id="convert_client_id">
                        <button id="hide_convert_client" type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">×</span>
                        </button>
                    </div>
                    <div class="modal-body">
                        <form class="container" method="POST" action="/Client/Convert">
                            @csrf;
                            <div class="form-group">
                                @if (session('network_error'))
                                    <p class="danger">{{ session('network_error') }}</p>
                                @endif
                                <input type="hidden" name="change_to" value="topppoe">
                                <input type="hidden" name="client_id" value="{{$clients_data[0]->client_id}}">
                                <label  id="errorMsg" for="client_secret_username" class="form-control-label">Clients Username</label>
                                <input type="text" name="client_secret_username" id="client_secret_username"
                                    class="form-control rounded-lg p-1" placeholder="Client Username"
                                    required value="{{ $clients_data[0]->client_account }}">
                            </div>
                            <div class="form-group">
                                @php
                                    $password = rand(100000,999999);
                                @endphp
                                <span class="d-none" id="secret_holder"></span>
                                <label  id="errorMsg1" for="client_secret_password" class="form-control-label">Clients Secret Password <span class="">({{$clients_data[0]->client_secret_password != "" ? $clients_data[0]->client_secret_password : $password}})</span></label>
                                <input type="password" name="client_secret_password" id="client_secret_password"
                                    class="form-control rounded-lg p-1" placeholder="Client Password"
                                    required value="{{$clients_data[0]->client_secret_password != "" ? $clients_data[0]->client_secret_password : $password}}">
                            </div>
                            <div class="form-group">
                                <label for="router_list" class="form-control-label">Router Name: <span class="invisible" id="secrets_load"><i class="fas ft-rotate-cw fa-spin"></i></span></label>
                                <select name="router_list" id="router_list" class="form-control" onchange="getRouterProfiles()" required>
                                    <option hidden value="">Select an option</option>
                                    @foreach ($router_data as $router)
                                        <option value="{{$router->router_id}}">{{$router->router_name}}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="form-group">
                                <label for="pppoe_profile" class="form-control-label">Router Profile:</label>
                                <p class="text-secondary" id="router_profile_holder">The router secret profiles
                                    will appear here If the router is selected.If this message is still
                                    present a router is not selected.</p>
                            </div>
                            <input type="submit" class="d-none" id="submit_convert">
                        </form>
                    </div>
                    <div class="modal-footer">
                        <div class="row w-100">
                            <div class="col-md-6">
                                @php
                                    $btnText = "<i class=\"fas fa-refresh\"></i> Convert";
                                @endphp
                                <x-button :btnText="$btnText" btnType="info" btnSize="sm" otherClasses="w-100 my-1" btnId="confirm_client_convert" :readOnly="$readonly"/>
                                
                            </div>
                            <div class="col-md-6">
                                @php
                                    $btnText = "<i class=\"ft-x\"></i> Close";
                                @endphp
                                <x-button :btnText="$btnText" btnType="secondary" btnSize="sm" otherClasses="w-100 my-1" btnId="close_convert_client" :readOnly="$readonly"/>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    @else
        {{-- CONVERT CLIENT TO PPPOE --}}
        <div class="modal fade text-left hide" style="background-color: rgba(0, 0, 0, 0.5);" id="convert_client_modal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel3" style="padding-right: 17px;" aria-modal="true">
            <div class="modal-dialog modal-dialog-centered" role="document">
                <div class="modal-content">
                    <div class="modal-header bg-info white">
                        <h5 class="modal-title white" id="myModalLabel3">Convert "{{ucwords(strtolower($clients_data[0]->client_name))}}" to Static Assignment.</h5>
                        <input type="hidden" id="convert_client_id">
                        <button id="hide_convert_client" type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">×</span>
                        </button>
                    </div>
                    <div class="modal-body">
                        <form class="container" method="POST" action="/Client/Convert">
                            @csrf
                            <div class="form-group">
                                <input type="hidden" name="change_to" value="tostatic">
                                <input type="hidden" name="client_id" value="{{$clients_data[0]->client_id}}">
                                @if (session('network_error'))
                                    <p class="danger">{{ session('network_error') }}</p>
                                @endif
                                <label  id="errorMsg" for="client_network" class="form-control-label">Clients Network
                                    { <span class="primary" id="networks">{{$last_client_details[0]->client_network ?? "[pp]"}}</span> }</label>
                                <input type="text" name="client_network" id="client_network"
                                    class="form-control rounded-lg p-1" placeholder="ex 10.10.30.0"
                                    required value="{{$clients_data[0]->client_network}}">
                            </div>
                            <div class="form-group">
                                <label  id="errorMsg1" for="client_gw" class="form-control-label">Clients Gateway {<span class="primary">{{$last_client_details[0]->client_default_gw ?? ""}}</span> } </label>
                                <input type="text" name="client_gw" id="client_gw"
                                    class="form-control rounded-lg p-1" placeholder="ex 10.10.30.1/24"
                                    required value="{{$clients_data[0]->client_default_gw}}">
                            </div>
                            <div class="form-group row">
                                @php
                                    $upload = explode("/", $clients_data[0]->max_upload_download)[0] ?? "";
                                    $download = explode("/", $clients_data[0]->max_upload_download)[1] ?? "";
                                @endphp
                                <div class="col-md-6">
                                    <label for="upload_speed" class="form-control-label">Upload { <span class="primary">{{ $clients_data[0]->max_upload_download }}</span> }</label>
                                    <input class="form-control" type="number" name="upload_speed"
                                        id="upload_speed" placeholder="128" required
                                        value="{{substr($upload, 0, strlen($upload)-1)}}">
                                    <select class="form-control" name="unit1" id="unit1" required
                                        value="">
                                        <option class="innit" value="" hidden>Select unit
                                        </option>
                                        <option class="innit" {{substr($upload, -1) == "K" ? "selected" : ""}} value="K">Kbps</option>
                                        <option class="innit" {{substr($upload, -1) == "M" ? "selected" : ""}} value="M">Mbps</option>
                                    </select>
                                </div>
                                <div class="col-md-6">
                                    <label for="download_speeds" class="form-control-label">Download
                                    </label>
                                    <input class="form-control" type="number" name="download_speed" id="download_speeds" placeholder="128" value="{{substr($download, 0, strlen($download)-1)}}" required>
                                    <select class="form-control" name="unit2" id="unit2" required>
                                        <option class="downinit" value="" hidden>Select unit</option>
                                        <option class="downinit" {{substr($download, -1) == "K" ? "selected" : ""}} value="K">Kbps</option>
                                        <option class="downinit" {{substr($download, -1) == "M" ? "selected" : ""}} value="M">Mbps</option>
                                    </select>
                                </div>
                            </div>
                            <div class="form-group">
                                <label for="router_list" class="form-control-label">Router Name: <span class="invisible" id="interface_loader"><i class="fas ft-rotate-cw fa-spin"></i></span></label>
                                <select name="router_list" id="router_list" class="form-control" onchange="getRouterInterfaces()" required>
                                    <option hidden value="">Select an option</option>
                                    @foreach ($router_data as $router)
                                        <option value="{{$router->router_id}}">{{$router->router_name}}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="form-group">
                                <label for="client_address" class="form-control-label">Router Interface:</label>
                                <p class="text-secondary" id="interface_holder">The router interfaces
                                    will appear here If the router is selected.If this message is still
                                    present the router is not selected.</p>
                            </div>
                            <input type="submit" class="d-none" id="submit_convert">
                        </form>
                    </div>
                    <div class="modal-footer">
                        <div class="row w-100">
                            <div class="col-md-6">
                                @php
                                    $btnText = "<i class=\"fas fa-refresh\"></i> Convert";
                                @endphp
                                <x-button :btnText="$btnText" btnType="info" btnSize="sm" otherClasses="w-100 my-1" btnId="confirm_client_convert" :readOnly="$readonly"/>
                                
                            </div>
                            <div class="col-md-6">
                                @php
                                    $btnText = "<i class=\"ft-x\"></i> Close";
                                @endphp
                                <x-button :btnText="$btnText" btnType="secondary" btnSize="sm" otherClasses="w-100 my-1" btnId="close_convert_client" :readOnly="$readonly"/>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    @endif
    {{-- DELETE THE CLIENT --}}
    <div class="modal fade text-left hide" style="background-color: rgba(0, 0, 0, 0.5);" id="delete_client_modal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel2" style="padding-right: 17px;" aria-modal="true">
        <div class="modal-dialog modal-dialog-centered" role="document">
            <div class="modal-content">
                <div class="modal-header bg-danger white">
                <h4 class="modal-title white" id="myModalLabel2">Confirm Delete Of {{ucwords(strtolower($clients_data[0]->client_name))}}.</h4>
                <input type="hidden" id="delete_columns_ids">
                <button id="hide_delete_column" type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">×</span>
                </button>
                </div>
                <div class="modal-body">
                    <div class="container">
                        <p class="text-dark"><b>Are you sure you want to delete "{{ucwords(strtolower($clients_data[0]->client_name))}}"?</b></p>
                    </div>
                </div>
                <div class="modal-footer">
                    <div class="row w-100">
                        <div class="col-md-6">
                            @php
                                $btnText = "<i class=\"ft-trash\"></i> Delete";
                                $validated = $clients_data[0]->validated == 0 ? "w-100 d-none" : "w-100";
                                $btnLink = "/delete_user/".$clients_data[0]->client_id;
                            @endphp
                            <x-button-link :btnText="$btnText" :btnLink="$btnLink" btnType="danger" btnSize="sm" :otherClasses="$validated" :readOnly="$readonly" />
                        </div>
                        <div class="col-md-6">
                            @php
                                $btnText = "<i class=\"fas fa-x\"></i> Close";
                                $validated = $clients_data[0]->validated == 0 ? "w-100 grey d-none" : "w-100 grey";
                            @endphp
                            <x-button :btnText="$btnText" btnType="secondary" btnSize="sm" :otherClasses="$validated" btnId="close_this_window_delete" :readOnly="$readonly" />
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- UPDATE CLIENT PHONE NUMBER --}}
    <div class="modal fade text-left hide" style="background-color: rgba(0, 0, 0, 0.5);" id="update_phone_modal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel3" style="padding-right: 17px;" aria-modal="true">
        <div class="modal-dialog modal-dialog-centered" role="document">
            <div class="modal-content">
                <div class="modal-header bg-info white">
                <h4 class="modal-title white" id="myModalLabel3">Update "{{ucwords(strtolower($clients_data[0]->client_name))}}" Phone Number.</h4>
                <input type="hidden" id="delete_columns_ids">
                <button id="close_update_phone_1" type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">×</span>
                </button>
                </div>
                <div class="modal-body">
                    <div class="container">
                        <form action="/change_client_phone" method="post" class="form-control-group">
                            @csrf
                            <h6 class="text-center" >Change Phone Number</h6>
                            <input type="hidden" name="clients_id"
                                value="{{ $clients_data[0]->client_id }}">
                            <label for="client_new_phone" class="form-control-label" id="">New Phone Number</label>
                            <input type="number" required name="client_new_phone" id="client_new_phone" class="form-control" value="{{ $clients_data[0]->clients_contacts }}" placeholder="New Phone Number">
                            <div class="row w-100">
                                <div class="col-md-6">
                                    @php
                                        $btnText = "<i class=\"fas fa-save\"></i> Save";
                                        $otherClasses = "w-100 my-1";
                                    @endphp
                                    <x-button :btnText="$btnText" btnType="info" type="submit" btnSize="sm" :otherClasses="$otherClasses" btnId="" :readOnly="$readonly" />
                                </div>
                                <div class="col-md-6">
                                    @php
                                        $btnText = "<i class=\"fas fa-x\"></i> Cancel";
                                        $otherClasses = "w-100 my-1";
                                    @endphp
                                    <x-button :btnText="$btnText" btnType="secondary" type="button" btnSize="sm" :otherClasses="$otherClasses" btnId="close_update_phone_2" :readOnly="$readonly" />
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
                <div class="modal-footer">
                </div>
            </div>
        </div>
    </div>

    {{-- UPDATE EXPIRATION DATE --}}
    <div class="modal fade text-left hide" style="background-color: rgba(0, 0, 0, 0.5);" id="update_expiration_date_modal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel4" style="padding-right: 17px;" aria-modal="true">
        <div class="modal-dialog modal-dialog-centered" role="document">
            <div class="modal-content">
                <div class="modal-header bg-info white">
                <h4 class="modal-title white" id="myModalLabel4">Update "{{ucwords(strtolower($clients_data[0]->client_name))}}" Expiration Date.</h4>
                <input type="hidden" id="delete_columns_ids">
                <button id="close_update_expiration_date_modal_1" type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">×</span>
                </button>
                </div>
                <div class="modal-body">
                    <div class="container">
                        <form action="/changeExpDate" method="post" class="form-control-group">
                            @csrf
                            <h6 class="text-center" >Change Expiration Date</h6>
                            <input type="hidden" name="clients_id"
                                value="{{ $clients_data[0]->client_id }}">

                            <label for="expiration_date_edits" class="form-control-label" id="">New Expiration Date</label>
                            <input type="date" value="<?=date("Y-m-d", strtotime($clients_data[0]->next_expiration_date))?>" required name="expiration_date_edits" id="expiration_date_edits" class="form-control" placeholder="New Expiration Date">

                            <label for="expiration_time_edits" class="form-control-label" id="">New Expiration Time</label>
                            <input type="time" value="<?=date("H:i", strtotime($clients_data[0]->next_expiration_date))?>" required name="expiration_time_edits" id="expiration_time_edits" class="form-control" placeholder="New Expiration Time">

                            <div class="row w-100">
                                <div class="col-md-6">
                                    @php
                                        $btnText = "<i class=\"fas fa-save\"></i> Save";
                                        $otherClasses = "w-100 my-1";
                                    @endphp
                                    <x-button :btnText="$btnText" btnType="info" type="submit" btnSize="sm" :otherClasses="$otherClasses" btnId="" :readOnly="$readonly" />
                                </div>
                                <div class="col-md-6">
                                    @php
                                        $btnText = "<i class=\"fas fa-x\"></i> Cancel";
                                        $otherClasses = "w-100 my-1";
                                    @endphp
                                    <x-button :btnText="$btnText" btnType="secondary" type="button" btnSize="sm" :otherClasses="$otherClasses" btnId="close_update_expiration_date_modal_2" :readOnly="$readonly" />
                                    {{-- <button class="btn btn-secondary btn-sm w-100 my-1" type="button" id="close_update_expiration_date_modal_2">Cancel</button> --}}
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
                <div class="modal-footer">
                </div>
            </div>
        </div>
    </div>

    {{-- UPDATE MONTHLY PAYMENT --}}
    <div class="modal fade text-left hide" style="background-color: rgba(0, 0, 0, 0.5);" id="update_monthly_payment" tabindex="-1" role="dialog" aria-labelledby="myModalLabel5" style="padding-right: 17px;" aria-modal="true">
        <div class="modal-dialog modal-dialog-centered" role="document">
            <div class="modal-content">
                <div class="modal-header bg-info white">
                <h4 class="modal-title white" id="myModalLabel5">Update "{{ucwords(strtolower($clients_data[0]->client_name))}}" Monthly Payment.</h4>
                <input type="hidden" id="delete_columns_ids">
                <button id="close_update_monthly_payment_1" type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">×</span>
                </button>
                </div>
                <div class="modal-body">
                    <div class="container">
                        <form action="/change_client_monthly_payment" method="post" class="form-control-group">
                            @csrf
                            <h6 class="text-center" >Change Monthly Payment</h6>
                            <input type="hidden" name="clients_id"
                                value="{{ $clients_data[0]->client_id }}">
                            <label for="client_monthly_payment" class="form-control-label" id="">New Monthly Payment</label>
                            <input type="number" required name="client_monthly_payment" id="client_monthly_payment" class="form-control" value="{{ $clients_data[0]->monthly_payment }}" placeholder="New Phone Number">
                            <div class="row w-100">
                                <div class="col-md-6">
                                    @php
                                        $btnText = "<i class=\"fas fa-save\"></i> Save";
                                        $otherClasses = "w-100 my-1";
                                    @endphp
                                    <x-button :btnText="$btnText" btnType="info" type="submit" btnSize="sm" :otherClasses="$otherClasses" btnId="" :readOnly="$readonly" />
                                </div>
                                <div class="col-md-6">
                                    @php
                                        $btnText = "<i class=\"fas fa-x\"></i> Cancel";
                                        $otherClasses = "w-100 my-1";
                                    @endphp
                                    <x-button :btnText="$btnText" btnType="secondary" type="button" btnSize="sm" :otherClasses="$otherClasses" btnId="close_update_monthly_payment_2" :readOnly="$readonly" />
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
                <div class="modal-footer">
                </div>
            </div>
        </div>
    </div>

    {{-- UPDATE MONTHLY MINIMUM PAYMENT --}}
    <div class="modal fade text-left hide" style="background-color: rgba(0, 0, 0, 0.5);" id="update_monthly_min_pay_modal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel6" style="padding-right: 17px;" aria-modal="true">
        <div class="modal-dialog modal-dialog-centered" role="document">
            <div class="modal-content">
                <div class="modal-header bg-info white">
                <h4 class="modal-title white" id="myModalLabel6">Update "{{ucwords(strtolower($clients_data[0]->client_name))}}" Monthly Minimum Payment.</h4>
                <input type="hidden" id="delete_columns_ids">
                <button id="close_update_monthly_min_pay_modal_1" type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">×</span>
                </button>
                </div>
                <div class="modal-body">
                    <div class="container">
                        <form method="POST" action="{{route("client.update.minimum_payment.static")}}" class="form-control-group border border-primary rounded p-1">
                            @csrf
                            <h6 class="text-center">Change Minimum Payment</h6>
                            <input type="hidden" value="{{$clients_data[0]->client_id}}" name="client_id">
                            <label for="change_minimum_payment" class="form-control-label">Change Minimum Payment</label>
                            <select name="change_minimum_payment" id="change_minimum_payment" class="form-control" required>
                                <option hidden value="">Select Payment Option</option>
                                <option {{$clients_data[0]->min_amount == "10" ? "selected" : ""}} value="10">10%</option>
                                <option {{$clients_data[0]->min_amount == "15" ? "selected" : ""}} value="15">15%</option>
                                <option {{$clients_data[0]->min_amount == "25" ? "selected" : ""}} value="25">25% (¼ Payment)</option>
                                <option {{$clients_data[0]->min_amount == "50" ? "selected" : ""}} value="50">50% (½ Payment)</option>
                                <option {{$clients_data[0]->min_amount == "75" ? "selected" : ""}} value="75">75% (¾ Payment)</option>
                                <option {{$clients_data[0]->min_amount == "80" ? "selected" : ""}} value="80">80%</option>
                                <option {{$clients_data[0]->min_amount == "90" ? "selected" : ""}} value="90">90%</option>
                                <option {{$clients_data[0]->min_amount == "100" ? "selected" : ""}} value="100">Full Payment</option>
                            </select>
                            <div class="row w-100">
                                <div class="col-md-6">
                                    @php
                                        $btnText = "<i class=\"fas fa-save\"></i> Save";
                                        $otherClasses = "w-100 my-1";
                                    @endphp
                                    <x-button :btnText="$btnText" btnType="info" type="submit" btnSize="sm" :otherClasses="$otherClasses" btnId="" :readOnly="$readonly" />
                                </div>
                                <div class="col-md-6">
                                    @php
                                        $btnText = "<i class=\"fas fa-x\"></i> Close";
                                        $otherClasses = "w-100 my-1";
                                    @endphp
                                    <x-button :btnText="$btnText" btnType="secondary" type="button" btnSize="sm" :otherClasses="$otherClasses" btnId="close_update_monthly_min_pay_modal_2" :readOnly="$readonly" />
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
                <div class="modal-footer">
                </div>
            </div>
        </div>
    </div>

    {{-- UPDATE WALLET AMOUNT --}}
    <div class="modal fade text-left hide" style="background-color: rgba(0, 0, 0, 0.5);" id="update_wallet_amount_modal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel7" style="padding-right: 17px;" aria-modal="true">
        <div class="modal-dialog modal-dialog-centered" role="document">
            <div class="modal-content">
                <div class="modal-header bg-info white">
                <h4 class="modal-title white" id="myModalLabel7">Update "{{ucwords(strtolower($clients_data[0]->client_name))}}" wallet amount.</h4>
                <input type="hidden" id="delete_columns_ids">
                <button id="close_update_wallet_amount_modal_1" type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">×</span>
                </button>
                </div>
                <div class="modal-body">
                    <div class="container">
                        <form action="/changeWallet" method="post" class="form-control-group">
                            @csrf
                            <h6 class="text-center" >Change wallet balance</h6>
                            <input type="hidden" name="clients_id"
                                value="{{ $clients_data[0]->client_id }}">
                            <label for="wallet_amounts" class="form-control-label" id="">New Wallet Amount</label>
                            <input type="number" required name="wallet_amounts" id="wallet_amounts" class="form-control" value="{{$clients_data[0]->wallet_amount}}" placeholder="New wallet amounts">
                            <div class="row w-100">
                                <div class="col-md-6">
                                    @php
                                        $btnText = "<i class=\"fas fa-save\"></i> Save";
                                        $otherClasses = "w-100 my-1";
                                    @endphp
                                    <x-button :btnText="$btnText" btnType="info" type="submit" btnSize="sm" :otherClasses="$otherClasses" btnId="" :readOnly="$readonly" />
                                </div>
                                <div class="col-md-6">
                                    @php
                                        $btnText = "<i class=\"fas fa-x\"></i> Cancel";
                                        $otherClasses = "w-100 my-1";
                                    @endphp
                                    <x-button :btnText="$btnText" btnType="secondary" type="button" btnSize="sm" :otherClasses="$otherClasses" btnId="close_update_wallet_amount_modal_2" :readOnly="$readonly" />
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
                <div class="modal-footer">
                </div>
            </div>
        </div>
    </div>

    {{-- EDIT FREEZE --}}
    <div class="modal fade text-left hide" style="background-color: rgba(0, 0, 0, 0.5);" style="background-color: rgba(0, 0, 0, 0.5);" id="update_freeze_status_modal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel8" style="padding-right: 17px;" aria-modal="true">
        <div class="modal-dialog modal-dialog-centered" role="document">
            <div class="modal-content">
                <div class="modal-header bg-info white">
                <h4 class="modal-title white" id="myModalLabel8">Update "{{ucwords(strtolower($clients_data[0]->client_name))}}" Freeze status.</h4>
                <input type="hidden" id="delete_columns_ids">
                <button id="close_update_freeze_status_modal_1" type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">×</span>
                </button>
                </div>
                <div class="modal-body">
                    <div class="container">
                        <form action="/set_freeze" method="post" class="form-control-group border border-primary rounded p-1">
                            @csrf
                            <h6 class="text-center" >Freeze Until</h6>
                            @if ($clients_data[0]->client_freeze_status == "1" || date("YmdHis") < date("YmdHis",strtotime($clients_data[0]->freeze_date)))
                                @php
                                    $btnText = "Deactivate Freeze";
                                    $otherClasses = "".$readonly;
                                    $btnLink = "/Client/deactivate_freeze/".$clients_data[0]->client_id;
                                @endphp
                                <x-button-link :btnLink="$btnLink" :btnText="$btnText" toolTip="Print Invoice" btnType="info" btnSize="sm" :otherClasses="$otherClasses" :readOnly="$readonly"/>
                                {{-- <a href="/Client/deactivate_freeze/{{$clients_data[0]->client_id}}" class="btn btn-secondary btn-sm">Deactivate Freeze</a> --}}
                                <hr>
                            @else
                                {{-- <a href="/Client/activate_freeze/{{$clients_data[0]->client_id}}" class="btn btn-danger">Activate</a> 
                                <hr>--}}
                            @endif
                            <br>
                            <div class="container">
                                <label for="freeze_date">Freeze Date</label>
                                <select name="freeze_date" required id="freeze_date" class="form-control">
                                    <option value="" hidden>Select Option</option>
                                    <option value="set_freeze">Set Freezing Date</option>
                                    <option selected value="freeze_now">Freeze Now</option>
                                </select>
                            </div>
                            <div class="container d-none" id="setFreezeDate">
                                <label for="freezing_date">Select Freeze Date</label>
                                <input required type="date" name="freezing_date" id="freezing_date" value="{{date("Y-m-d",strtotime("1 day"))}}" min="{{date("Y-m-d")}}" class="form-control">
                            </div>
                            <div class="container">
                                <label for="freeze_type">Freeze Type</label>
                                <select name="freeze_type" required id="freeze_type" class="form-control">
                                    <option value="" hidden>Select Option</option>
                                    <option selected value="definate">Definate Freezing</option>
                                    <option value="Indefinite">In-definate Freezing</option>
                                </select>
                            </div>
                            <div class="container" id="freeze_window">
                                <input type="hidden" name="clients_id"
                                    value="{{ $clients_data[0]->client_id }}">
                                <input type="hidden" name="indefinate_freezing" value="00000000000000">
                                <label for="freez_dates_edit" class="form-control-label" id="">Freeze until</label>
                                <input type="date" required name="freez_dates_edit" id="freez_dates_edit" class="form-control" min="<?php echo date("Y-m-d",strtotime("1 day"));?>" value='{{date("Y-m-d",strtotime("1 day"))}}' placeholder="New Expiration Date">
                            </div>
                            <div class="row">
                                <div class="col-md-6">
                                    @php
                                        $btnText = "<i class=\"fas fa-save\"></i> Save";
                                        $otherClasses = "w-100 my-1";
                                    @endphp
                                    <x-button :btnText="$btnText" btnType="info" type="submit" btnSize="sm" :otherClasses="$otherClasses" btnId="" :readOnly="$readonly" />
                                </div>
                                <div class="col-md-6">
                                    @php
                                        $btnText = "<i class=\"fas fa-x\"></i> Cancel";
                                        $otherClasses = "w-100 my-1";
                                    @endphp
                                    <x-button :btnText="$btnText" btnType="secondary" type="button" btnSize="sm" :otherClasses="$otherClasses" btnId="close_update_freeze_status_modal_2" :readOnly="$readonly" />
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
                <div class="modal-footer">
                </div>
            </div>
        </div>
    </div>

    {{-- UPDATE REFEREE --}}
    <div class="modal fade text-left hide" style="background-color: rgba(0, 0, 0, 0.5);" id="update_refferee_by_modal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel9" style="padding-right: 17px;" aria-modal="true">
        <div class="modal-dialog modal-dialog-centered" role="document">
            <div class="modal-content">
                <div class="modal-header bg-info white">
                <h4 class="modal-title white" id="myModalLabel9">Set "{{ucwords(strtolower($clients_data[0]->client_name))}}" Refferee.</h4>
                <input type="hidden" id="delete_columns_ids">
                <button id="close_update_refferee_by_modal_1" type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">×</span>
                </button>
                </div>
                <div class="modal-body">
                    <div class="container">
                        <div class="form-control-group">
                            <p><b>What you need to know:</b></p>
                            <p>- Start by searching the refferer<br>
                                - If the refferer is valid set the refferers cut<br>
                                - Then save. <br>
                                - If there was a refferer before it will replace their details with the new refferer
                            </p>
                            <label for="wallet_amounts" class="form-control-label" id="">Search Refferer
                                <span class="invisible" id="search_referer_loader"><i class="fas ft-rotate-cw fa-spin"></i></span></label>
                            <div class="row">
                                <div class="col-md-9">
                                    <div class="autocomplete">
                                        <input type="text" required name="search_refferer_keyword" id="search_refferer_keyword" class="form-control" placeholder="Type keyword: name, acc no, phone number">
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    {{-- <button class="btn btn-infor" id="find_user_refferal" type="button"><i class="fas fa-search"></i></button> --}}
                                    @php
                                        $btnText = "<i class=\"ft-search\"></i> Search";
                                        $otherClasses = "";
                                        $btn_id = "find_user_refferal";
                                    @endphp
                                    <x-button :btnText="$btnText" btnType="infor" type="button" btnSize="sm" :otherClasses="$otherClasses" :btnId="$btn_id" :readOnly="$readonly" />
                                </div>
                            </div>
                            <p id="refferer_data" class="d-none"></p>
                            <span id="show_data_inside"></span>
                            <hr class="border border-primary">
                            <div class="container my-2">
                                <h6 class="text-center"><u>Refferer Details</u></h6>
                            </div>
                            <form action="/set_refferal" method="post">
                                @csrf
                                <div class="row my-2">
                                    <input type="hidden" name="clients_id"
                                value="{{ $clients_data[0]->client_id }}">
                                    <input type="hidden" name="refferal_account_no" id="refferer_acc_no2">
                                    <div class="col-md-6">
                                        <p><b>Refferer Fullname</b></p>
                                    </div>
                                    <div class="col-md-6">
                                        <p class="user_data" id="refferer_name">{{$reffer_details[0] ?? 'Unknown'}}</p>
                                    </div>
                                    <div class="col-md-6">
                                        <p><b>Refferer Acc No</b></p>
                                    </div>
                                    <div class="col-md-6">
                                        <p class="user_data" id="refferer_acc_no">{{$reffer_details[1] ?? 'Unknown'}}</p>
                                    </div>
                                    <div class="col-md-6">
                                        <p><b>Refferer wallet</b></p>
                                    </div>
                                    <div class="col-md-6">
                                        <p class="user_data" id="reffer_wallet">{{$reffer_details[2] ?? 'Unknown'}}</p>
                                    </div>
                                    <div class="col-md-6">
                                        <p><b>Refferer Location</b></p>
                                    </div>
                                    <div class="col-md-6">
                                        <p class="user_data" id="refferer_location">{{$reffer_details[3] ?? 'Unknown'}}</p>
                                    </div>
                                </div>
                                <div class="">
                                    <label for="refferer_amount" class="form-control-label">Refferer Cut</label>
                                    <input type="number" class="form-control" name="refferer_amount" id="refferer_amount" placeholder="Refferers Cut - how much is he given" required>
                                </div>
                                <div class="row">
                                    <div class="col-md-6">
                                        @php
                                            $btnText = "<i class=\"fas fa-save\"></i> Set";
                                            $otherClasses = "w-100 my-1";
                                        @endphp
                                        <x-button disabled="disabled" :btnText="$btnText" btnType="info" type="submit" btnSize="sm" :otherClasses="$otherClasses" btnId="save_data_inside" :readOnly="$readonly" />
                                        {{-- <button disabled type="submit" class="btn btn-info my-1 btn-sm w-100" id="save_data_inside"><i class="fas fa-save"></i> Set</button> --}}
                                    </div>
                                    <div class="col-md-6">
                                        @php
                                            $btnText = "<i class=\"fas fa-x\"></i> Cancel";
                                            $otherClasses = "w-100 my-1";
                                        @endphp
                                        <x-button :btnText="$btnText" btnType="secondary" type="button" btnSize="sm" :otherClasses="$otherClasses" btnId="close_update_refferee_by_modal_2" :readOnly="$readonly" />
                                        {{-- <button class="btn btn-secondary my-1 btn-sm w-100" type="button" id="close_update_refferee_by_modal_2">Cancel</button> --}}
                                    </div>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                </div>
            </div>
        </div>
    </div>

    {{-- UPDATE COMMENT --}}
    <div class="modal fade text-left hide" style="background-color: rgba(0, 0, 0, 0.5);" id="update_comments_modal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel110" style="padding-right: 17px;" aria-modal="true">
        <div class="modal-dialog modal-dialog-centered" role="document">
            <div class="modal-content">
                <div class="modal-header bg-info white">
                <h4 class="modal-title white" id="myModalLabel110">Update "{{ucwords(strtolower($clients_data[0]->client_name))}}" Comment.</h4>
                <input type="hidden" id="delete_columns_ids">
                <button id="close_update_comments_modal_1" type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">×</span>
                </button>
                </div>
                <div class="modal-body">
                    <div class="container">
                        <form action="/update_client_comment" method="post" class="form-control-group">
                            @csrf
                            <h6 class="text-center">Update Comment</h6>
                            <input type="hidden" name="clients_id" value="{{ $clients_data[0]->client_id }}">
                            <label for="comments" class="form-control-label" id="">Update Comment</label>
                            <textarea name="comments" id="comments" cols="30" rows="3" class="form-control" placeholder="Comment here">{{ $clients_data[0]->comment}}</textarea>
                            <div class="row w-100">
                                <div class="col-md-6">
                                    @php
                                        $btnText = "<i class=\"fas fa-save\"></i> Save";
                                        $otherClasses = "w-100 my-1";
                                    @endphp
                                    <x-button :btnText="$btnText" btnType="info" type="submit" btnSize="sm" :otherClasses="$otherClasses" btnId="" :readOnly="$readonly" />
                                    {{-- <button {{$readonly}} type="submit" class="btn btn-info my-1 btn-sm w-100"><i class="fas fa-save"></i> Save</button> --}}
                                </div>
                                <div class="col-md-6">
                                    @php
                                        $btnText = "<i class=\"fas fa-x\"></i> Cancel";
                                        $otherClasses = "w-100 my-1";
                                    @endphp
                                    <x-button :btnText="$btnText" btnType="secondary" type="button" btnSize="sm" :otherClasses="$otherClasses" btnId="close_update_comments_modal_2" :readOnly="$readonly" />
                                    {{-- <button class="btn btn-secondary my-1 btn-sm w-100" type="button" id="close_update_comments_modal_2">Cancel</button> --}}
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
                <div class="modal-footer">
                </div>
            </div>
        </div>
    </div>
</div>
<div class="container my-2">
    <h4 class="text-center"><u>Edit "{{ucwords(strtolower($clients_data[0]->client_name))}}" Data</u></h4>
    <table class="table table-bordered mb-0">
        <tr>
            <td>
                <div class="row">
                    <div class="col-sm-6"><strong>Account Number:</strong></div>
                    <div class="col-sm-6">{{ $clients_data[0]->client_account }}</div>
                </div>
            </td>
            <td>
                {{-- client payment automatiom --}}
                @if ($clients_data[0]->validated == "1")
                    @if ($clients_data[0]->payments_status == 1)
                        <div class="row">
                            <div class="col-sm-7"><strong>Automate Transaction:</strong><div class='badge badge-success'>Activated</div>
                            </div>
                            <div class="col-sm-5">
                            @php
                                $btnText = "De-Activate";
                                $otherClasses = ($clients_data[0]->client_freeze_status == "1" ? "disabled":"")." w-100 my-1";
                                $btnLink = "/deactivatePayment/".$clients_data[0]->client_id;
                            @endphp
                            <x-button-link :btnText="$btnText" :btnLink="$btnLink" btnType="danger" btnSize="sm" :otherClasses="$otherClasses" :readOnly="$readonly" />
                            <p class="text-success d-none"><b>Activated</b></p></div>
                        </div>
                    @else
                        <div class="row">
                            <div class="col-sm-7"><strong>Automate Transaction:</strong><div class='badge badge-danger'>De-activated</div>
                            </div>
                            <div class="col-sm-5">
                            @php
                                $btnText = "Activate";
                                $otherClasses = ($clients_data[0]->client_freeze_status == "1" ? "disabled":"")." w-100 my-1";
                                $btnLink = "/activatePayment/".$clients_data[0]->client_id;
                            @endphp
                            <x-button-link :btnText="$btnText" :btnLink="$btnLink" btnType="success" btnSize="sm" :otherClasses="$otherClasses" :readOnly="$readonly" />
                            <p class="text-danger d-none"><b>De-activated</b></p></div>
                        </div>
                    @endif
                @else
                    <div class="row">
                        <div class="col-sm-6"><strong>Automate Transaction:{!!$clients_data[0]->payments_status == "1" ? "<div class='badge badge-success'>Activated</div>" : "<div class='badge badge-danger'>De-activated</div>"!!}</strong></div>
                        <div class="col-sm-6">
                            <p>User is not validated yet!</p>
                        </div>
                    </div>
                @endif
            </td>
        </tr>
        <tr>
            <td>
                <div class="row">
                    <div class="col-sm-6"><strong>Phone Number:</strong> <br>{{ $clients_data[0]->clients_contacts }}</div>
                    <div class="col-sm-6">
                        @php
                            $btnText = "<i class=\"fas fa-pen\"></i> Edit";
                            $otherClasses = "w-100 ".($clients_data[0]->validated == 0 ? "d-none" : "");
                            $btn_id = "edit_phone_number";
                        @endphp
                        <x-button :btnText="$btnText" btnType="secondary" type="button" btnSize="sm" :otherClasses="$otherClasses" :btnId="$btn_id" :readOnly="$readonly" />
                        {{-- <button class="text-secondary btn btn-infor btn-sm mx-1 {{$clients_data[0]->validated == 0 ? "d-none" : ""}}" {{$readonly}} style="width: fit-content;" id="edit_phone_number"><i class="fas fa-pen"></i> Edit</button> --}}
                    </div>
                </div>
            </td>
            <td>
                <div class="row">
                    <div class="col-sm-7"><strong>Monthly Payment:</strong> <br>Kes {{ number_format($clients_data[0]->monthly_payment) }}</div>
                    <div class="col-sm-5">
                        @php
                            $btnText = "<i class=\"fas fa-pen\"></i> Edit";
                            $otherClasses = "w-100 ".($clients_data[0]->validated == 0 ? "d-none" : "");
                            $btn_id = "edit_monthly_payments";
                        @endphp
                        <x-button :btnText="$btnText" btnType="secondary" type="button" btnSize="sm" :otherClasses="$otherClasses" :btnId="$btn_id" :readOnly="$readonly" />
                        {{-- <button class="text-secondary btn btn-infor btn-sm mx-1 {{$clients_data[0]->validated == 0 ? "d-none" : ""}}" {{$readonly}} style="width: fit-content;" id="edit_monthly_payments"><i class="fas fa-pen"></i> Edit</button> --}}
                    </div>
                </div>
            </td>
        </tr>
        <tr>
            <td>
                @if ($clients_data[0]->validated == "1")
                    @if ($clients_data[0]->client_status == 1)
                        <div class="row">
                            <div class="col-sm-6"><strong>User status: <div class='badge badge-success'>Activated</div></strong></div>
                            <div class="col-sm-6">
                            @php
                                $btnText = "De-Activate";
                                $otherClasses = ($clients_data[0]->client_freeze_status == "1" ? "disabled":"")." w-100 my-1";
                                $btnLink = "/deactivate/".$clients_data[0]->client_id;
                            @endphp
                            <x-button-link :btnText="$btnText" :btnLink="$btnLink" btnType="danger" btnSize="sm" :otherClasses="$otherClasses" :readOnly="$readonly" />
                            <p class="text-success d-none"><b>Activated</b></p></div>
                        </div>
                    @else
                        <div class="row">
                            <div class="col-sm-6"><strong>User status: <div class='badge badge-danger'>De-activated</div></strong></div>
                            <div class="col-sm-6">
                                @php
                                    $btnText = "Activate";
                                    $otherClasses = ($clients_data[0]->client_freeze_status == "1" ? "disabled $readonly":"$readonly")." w-100 my-1";
                                    $btnLink = "/activate/".$clients_data[0]->client_id;
                                @endphp
                                <x-button-link :btnText="$btnText" :btnLink="$btnLink" btnType="success" btnSize="sm" :otherClasses="$otherClasses" :readOnly="$readonly" />
                                <p class="text-danger d-none"><b>De-activated</b></p>
                            </div>
                        </div>
                    @endif
                @else
                    <div class="row">
                        <div class="col-sm-6"><strong>User status: {!!$clients_data[0]->client_status == "1" ? "<div class='badge badge-success'>Activated</div>" : "<div class='badge badge-danger'>De-activated</div>"!!}</strong></div>
                        <div class="col-sm-6">
                            <p>User is not validated yet!</p>
                        </div>
                    </div>
                @endif
            </td>
            <td>
                <div class="row">
                    <div class="col-sm-7"><strong class="text-secondary">Minimum Payment:</strong> <br>{{$clients_data[0]->min_amount != 100 ? "Kes ".number_format(($clients_data[0]->min_amount / 100) * $clients_data[0]->monthly_payment)." (".$clients_data[0]->min_amount."%) of Kes ".number_format($clients_data[0]->monthly_payment) : "Full Payment (Kes ".number_format($clients_data[0]->monthly_payment).")"}}</div>
                    <div class="col-sm-5">
                        @php
                            $btnText = "<i class=\"fas fa-pen\"></i> Edit";
                            $otherClasses = "w-100 my-1 ".($clients_data[0]->validated == 0 ? "d-none" : "");
                        @endphp
                        <x-button :btnText="$btnText" btnType="secondary" type="button" btnSize="sm" :otherClasses="$otherClasses" btnId="edit_minimum_amount" :readOnly="$readonly" />
                            {{-- <button class="text-secondary btn btn-infor btn-sm mx-1 {{$clients_data[0]->validated == 0 ? "d-none" : ""}}" {{$readonly}} id="edit_minimum_amount"><i class="fas fa-pen"></i> Edit</button> --}}
                    </div>
                </div>
            </td>
        </tr>
        <tr>
            <td>
                <div class="row">
                    <div class="col-sm-6"><strong>Registration Date:</strong></div>
                    <div class="col-sm-6">{{ $registration_date }}</div>
                </div>
            </td>
            <td>
                <div class="row">
                    <div class="col-sm-7"><strong>Wallet Amount:</strong> <br>Kes {{ $clients_data[0]->wallet_amount }}</div>
                    <div class="col-sm-5">
                        @php
                            $btnText = "<i class=\"fas fa-pen\"></i> Edit";
                            $otherClasses = "w-100 ".($clients_data[0]->validated == 0 ? "d-none" : "");
                            $btn_id = "edit_wallet";
                        @endphp
                        <x-button :btnText="$btnText" btnType="secondary" type="button" btnSize="sm" :otherClasses="$otherClasses" :btnId="$btn_id" :readOnly="$readonly" />
                        {{-- <button {{$readonly}} class="btn btn-infor btn-sm mx-1 text-xxs text-secondary {{$clients_data[0]->validated == 0 ? "d-none" : ""}}" style="width: fit-content;" id="edit_wallet"><i class="fas fa-pen"></i> Edit</button> --}}
                    </div>
                </div>
            </td>
        </tr>
        <tr>
            <td>
                <div class="row">
                    <div class="col-sm-6"><strong>Expiration Date:</strong> <br>{{$expire_date ? $expire_date : "Null"}}</div>
                    <div class="col-sm-6"> 
                        @php
                            $btnText = "<i class=\"fas fa-pen\"></i> Edit";
                            $otherClasses = "w-100 ".($clients_data[0]->validated == 0 ? "d-none" : "");
                            $btn_id = "edit_expiration_date";
                        @endphp
                        <x-button :btnText="$btnText" btnType="secondary" type="button" btnSize="sm" :otherClasses="$otherClasses" :btnId="$btn_id" :readOnly="$readonly" />
                        {{-- <button class="text-secondary btn btn-infor btn-sm mx-1 {{$clients_data[0]->validated == 0 ? "d-none" : ""}}" {{$readonly}} style="width: fit-content;" id="edit_expiration_date"><i class="fas fa-pen"></i> Edit</button> --}}
                    </div>
                </div>
            </td>
            <td>
                <div class="row">
                    <div class="col-sm-7"><strong class="text-secondary">Freeze Client:</strong> <span class="badge {{$clients_data[0]->client_freeze_status == "1" || date("YmdHis") < date("YmdHis",strtotime($clients_data[0]->freeze_date)) ? "badge-success" : "badge-danger";}}">{{$clients_data[0]->client_freeze_status == "1" || date("YmdHis") < date("YmdHis",strtotime($clients_data[0]->freeze_date)) ? "Active" : "In-Active";}}</span> <br><p>{{date("YmdHis") < date("YmdHis",strtotime($clients_data[0]->freeze_date)) ? "Client will be frozen on : ".date("D dS M Y",strtotime($clients_data[0]->freeze_date))." until " : "Frozen Until:"}} {{isset($freeze_date) && strlen($freeze_date) > 0 ? $freeze_date : "Not Set"}}</p></div>
                    <div class="col-sm-5">
                        @php
                            $btnText = "<i class=\"fas fa-pen\"></i> Edit";
                            $otherClasses = "w-100 ".($clients_data[0]->validated == 0 ? "d-none" : "");
                            $btn_id = "edit_freeze_client";
                        @endphp
                        <x-button :btnText="$btnText" btnType="secondary" type="button" btnSize="sm" :otherClasses="$otherClasses" :btnId="$btn_id" :readOnly="$readonly" />
                        {{-- <button class="text-secondary btn btn-infor btn-sm mx-1 {{$clients_data[0]->validated == 0 ? "d-none" : ""}}" {{$readonly}} id="edit_freeze_client"><i class="fas fa-pen"></i> Edit</button> --}}
                    </div>
                </div>
            </td>
        </tr>
        <tr>
            <td>
                <div class="row">
                    <div class="col-sm-6"><strong>Location:</strong></div>
                    <div class="col-sm-6">
                        @php
                            echo $clients_data[0]->location_coordinates ? "<a class='text-danger' href = 'https://www.google.com/maps/place/".$clients_data[0]->location_coordinates."' target = '_blank'><u>Locate Client</u> </a>" :"No Co-ordinates provided for the client!" ;
                        @endphp
                    </div>
                </div>
            </td>
            <td>
                <div class="row">
                    <div class="col-sm-7"><strong>Reffered By:</strong> <br><p>{{$client_refferal ?? 'Refferal Not set'}} </p></div>
                    <div class="col-sm-5">
                        @php
                            $btnText = "<i class=\"fas fa-pen\"></i> Edit";
                            $otherClasses = "w-100 ".($clients_data[0]->validated == 0 ? "d-none" : "");
                            $btn_id = "edit_refferal";
                        @endphp
                        <x-button :btnText="$btnText" btnType="secondary" type="button" btnSize="sm" :otherClasses="$otherClasses" :btnId="$btn_id" :readOnly="$readonly" />
                        {{-- <button {{$readonly}} class="btn btn-infor btn-sm mx-1 text-xxs text-secondary {{$clients_data[0]->validated == 0 ? "d-none" : ""}}" id="edit_refferal"><i class="fas fa-pen"></i> Edit</button> --}}
                    </div>
                </div>
            </td>
        </tr>
        <tr>
            <td colspan="2">
                <div class="row">
                    <div class="col-md-9">
                        <strong>Comment:</strong> <br><p>{{isset($clients_data[0]->comment) ? ucwords(strtolower($clients_data[0]->comment)) : "No comments set!"}} </p>
                    </div>
                    <div class="col-md-3">
                        @php
                            $btnText = "<i class=\"fas fa-pen\"></i> Edit";
                            $otherClasses = "w-100 ".($clients_data[0]->validated == 0 ? "d-none" : "");
                            $btn_id = "edit_comments";
                        @endphp
                        <x-button :btnText="$btnText" btnType="secondary" type="button" btnSize="sm" :otherClasses="$otherClasses" :btnId="$btn_id" :readOnly="$readonly" />
                        {{-- <button {{$readonly}} class="btn btn-infor btn-sm mx-1 text-xxs text-secondary {{$clients_data[0]->validated == 0 ? "d-none" : ""}}" id="edit_comments"><i class="fas fa-pen"></i> Edit</button> --}}
                    </div>
                </div>
            </td>
        </tr>
    </table>
</div>