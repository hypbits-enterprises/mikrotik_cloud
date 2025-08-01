<div class="tab-pane fade" id="tab3" role="tabpanel">
    <p class="card-text">In this table below you will see previously generated invoices for <b>{{ ucwords(strtolower($clients_data[0]->client_name)) }}</b>.</p>
    
    {{-- GENERATE INVOICE --}}
    <div class="modal fade text-left hide" style="background-color: rgba(0, 0, 0, 0.5);" id="generate_client_invoice" tabindex="-1" role="dialog" aria-labelledby="myModalLabel11" style="padding-right: 17px;" aria-modal="true">
        <div class="modal-dialog modal-dialog-centered" role="document">
            <div class="modal-content">
                <div class="modal-header bg-info white">
                <h4 class="modal-title white" id="myModalLabel11">Generate Invoice</h4>
                <button id="close_generate_client_invoice_1" type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">×</span>
                </button>
                </div>
                <div class="modal-body">
                    <form class="container" action="/New-Invoice" method="POST">
                        <div class="form-group">
                            <input type="hidden" required id="client_id_invoice" name="client_id" value="{{ $clients_data[0]->client_id }}">
                            <label for="invoice_id" class="form-control-label">Invoice No</label>
                            <input type="text" class="form-control" readonly required name="invoice_number" value="{{$invoice_id}}" id="invoice_id" placeholder="Invoice number eg HYP-AB1">
                        </div>
                        <div class="form-group">
                            <label for="amount_to_pay" class="form-control-label">Amount to Pay</label>
                            <input type="number" required class="form-control" name="amount_to_pay" id="amount_to_pay" value="{{$clients_data[0]->monthly_payment}}" placeholder="e.g: Kes 3,000">
                        </div>
                        <div class="form-group">
                            <label for="amount_to_pay" class="form-control-label">Payment Duration</label>
                            <div class="row">
                                <div class="col-md-6">
                                    <input class="form-control" value="1" required type="number" name="period_duration" id="period_duration" placeholder="e.g: 1">
                                </div>
                                <div class="col-md-6">
                                    <select required name="period_unit" id="period_unit" class="form-control">
                                        <option value="" hidden>Select period</option>
                                        <option selected value="month">Months</option>
                                        <option value="week">Weeks</option>
                                        <option value="year">Years</option>
                                    </select>
                                </div>
                            </div>
                        </div>
                        <div class="form-group">
                            <label for="payment_from" class="form-control-label">Payment From</label>
                            <div class="row">
                                <div class="col-md-6">
                                    <input type="date" required name="payment_from_date" id="payment_from_date" class="form-control" value="{{date("Ymd", strtotime($clients_data[0]->next_expiration_date)) < date("Ymd") ? date("Y-m-d") : date("Y-m-d", strtotime($clients_data[0]->next_expiration_date))}}">
                                </div>
                                <div class="col-md-6">
                                    <input type="time" required name="payment_from_time" id="payment_from_time" class="form-control" value="{{date("h:i", strtotime($clients_data[0]->next_expiration_date))}}">
                                </div>
                            </div>
                        </div>
                        <div class="form-group">
                            <label for="invoice_deadline" class="form-control-label">Invoice Deadline</label>
                            <div class="input-group">
                                <input type="text" class="form-control position-relative" name="invoice_deadline" id="invoice_deadline" placeholder="30 days" value="0" required>
                                <div class="input-group-postpend">
                                    <span class="input-group-text" id="validationTooltipUsernamePrepend">Days</span>
                                </div>
                            </div>
                        </div>
                        <div class="form-group">
                            <label for="vat_included" class="form-control-label">Include VAT</label>
                            <select name="vat_included" required id="vat_included" class="form-control">
                                <option value="" hidden>Select an option</option>
                                <option value="include_vat">Include 16% VAT in the total</option>
                                <option value="exclude_vat">Exclude 16% VAT in the total</option>
                                <option selected value="no_vat">No VAT</option>
                            </select>
                        </div>
                        <div class="container" id="reponse_holder_invoices"></div>
                        <div class="row w-100">
                            <div class="col-md-6">
                                @php
                                    $btnText = "<i class=\"fas fa-save\"></i> Generate Invoice <span class=\"invisible\" id=\"invoice_loader\"><i class=\"fas ft-rotate-cw fa-spin\"></i></span>";
                                    $otherClasses = "w-100 my-1";
                                @endphp
                                <x-button :btnText="$btnText" btnType="info" type="submit" btnSize="sm" :otherClasses="$otherClasses" btnId="generate_invoice" :readOnly="$readonly_finance" />
                                {{-- <button {{$readonly_finance}} type="submit" id="generate_invoice" class="btn btn-info my-1 btn-sm w-100"><i class="fas fa-save"></i> Generate Invoice <span class="invisible" id="invoice_loader"><i class="fas ft-rotate-cw fa-spin"></i></span></button> --}}
                            </div>
                            <div class="col-md-6">
                                @php
                                    $btnText = "<i class=\"fas fa-x\"></i> Cancel";
                                    $otherClasses = "w-100 my-1".($clients_data[0]->validated == 0 ? "d-none" : "");
                                @endphp
                                <x-button :btnText="$btnText" btnType="secondary" type="button" btnSize="sm" :otherClasses="$otherClasses" btnId="close_generate_client_invoice_2" :readOnly="$readonly_finance" />
                                {{-- <button class="btn btn-secondary my-1 btn-sm w-100" type="button" id="close_generate_client_invoice_2">Cancel</button> --}}
                            </div>
                        </div>
                    </form>
                </div>
                <div class="modal-footer">
                </div>
            </div>
        </div>
    </div>

    {{-- UPDATE INVOICE --}}
    <div class="modal fade text-left hide" style="background-color: rgba(0, 0, 0, 0.5);" id="view_client_invoice" tabindex="-1" role="dialog" aria-labelledby="myModalLabel12" style="padding-right: 17px;" aria-modal="true">
        <div class="modal-dialog modal-dialog-centered" role="document">
            <div class="modal-content">
                <div class="modal-header bg-primary white">
                <h4 class="modal-title white" id="myModalLabel12">Update Invoice</h4>
                <button id="close_view_client_invoice_1" type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">×</span>
                </button>
                </div>
                <div class="modal-body">
                    <form class="container" method="POST" action="/Update-Invoice">
                        @csrf
                        <div class="form-group">
                            <input type="hidden" id="edit_client_id_invoice" name="client_id" value="{{ $clients_data[0]->client_id }}">
                            <label for="edit_invoice_id" class="form-control-label">Invoice No</label>
                            <input type="text" class="form-control" name="edit_invoice_id" readonly id="edit_invoice_id" placeholder="Invoice number eg HYP-AB1">
                        </div>
                        <div class="form-group">
                            <label for="edit_amount_to_pay" class="form-control-label">Amount to Pay</label>
                            <input type="number" class="form-control" name="edit_amount_to_pay" id="edit_amount_to_pay" placeholder="e.g: Kes 3,000">
                        </div>
                        <div class="form-group">
                            <label for="amount_to_pay" class="form-control-label">Payment Duration</label>
                            <div class="row">
                                <div class="col-md-6">
                                    <input class="form-control" value="1" type="number" name="edit_period_duration" id="edit_period_duration" placeholder="e.g: 1">
                                </div>
                                <div class="col-md-6">
                                    <select name="edit_period_unit" id="edit_period_unit" class="form-control">
                                        <option value="" hidden>Select period</option>
                                        <option selected value="month">Months</option>
                                        <option value="week">Weeks</option>
                                        <option value="year">Years</option>
                                    </select>
                                </div>
                            </div>
                        </div>
                        <div class="form-group">
                            <label for="payment_from" class="form-control-label">Payment From</label>
                            <div class="row">
                                <div class="col-md-6">
                                    <input type="date" name="edit_payment_from_date" id="edit_payment_from_date" class="form-control">
                                </div>
                                <div class="col-md-6">
                                    <input type="time" name="edit_payment_from_time" id="edit_payment_from_time" class="form-control">
                                </div>
                            </div>
                        </div>
                        <div class="form-group">
                            <label for="edit_invoice_deadline" class="form-control-label">Invoice Deadline</label>
                            <div class="input-group">
                                <input type="text" class="form-control position-relative" id="edit_invoice_deadline" name="edit_invoice_deadline" placeholder="30 days" value="0" required>
                                <div class="input-group-postpend">
                                    <span class="input-group-text" id="edit_validationTooltipUsernamePrepend">Days</span>
                                </div>
                            </div>
                        </div>
                        <div class="form-group">
                            <label for="edit_vat_included" class="form-control-label">Include VAT</label>
                            <select name="edit_vat_included" id="edit_vat_included" class="form-control">
                                <option value="" hidden>Select an option</option>
                                <option value="include_vat">Include 16% VAT in the total</option>
                                <option value="exclude_vat">Exclude 16% VAT in the total</option>
                                <option selected value="no_vat">No VAT</option>
                            </select>
                        </div>
                        <div class="row w-100">
                            <div class="col-md-6">
                                @php
                                    $btnText = "<i class=\"fas fa-save\"></i> Update Invoice <span class=\"invisible\" id=\"edit_invoice_loader\"><i class=\"fas ft-rotate-cw fa-spin\"></i>";
                                    $otherClasses = "w-100 my-1";
                                @endphp
                                <x-button :btnText="$btnText" btnType="primary" type="submit" btnSize="sm" :otherClasses="$otherClasses" btnId="edit_generate_invoice" :readOnly="$readonly_finance" />
                                {{-- <button {{$readonly_finance}} type="submit" id="edit_generate_invoice" class="btn btn-primary my-1 btn-sm w-100"><i class="fas fa-save"></i> Update Invoice <span class="invisible" id="edit_invoice_loader"><i class="fas ft-rotate-cw fa-spin"></i></span></button> --}}
                            </div>
                            <div class="col-md-6">
                                @php
                                    $btnText = "<i class=\"fas fa-x\"></i> Cancel";
                                    $otherClasses = "w-100 my-1".($clients_data[0]->validated == 0 ? "d-none" : "");
                                @endphp
                                <x-button :btnText="$btnText" btnType="secondary" type="button" btnSize="sm" :otherClasses="$otherClasses" btnId="close_view_client_invoice_2" :readOnly="$readonly_finance" />
                                {{-- <button class="btn btn-secondary my-1 btn-sm w-100" type="button" id="close_view_client_invoice_2">Cancel</button> --}}
                            </div>
                        </div>
                    </form>
                </div>
                <div class="modal-footer">
                </div>
            </div>
        </div>
    </div>

    {{-- SEND INVOICE --}}
    <div class="modal fade text-left hide" style="background-color: rgba(0, 0, 0, 0.5);" id="send_client_invoice" tabindex="-1" role="dialog" aria-labelledby="myModalLabel13" style="padding-right: 17px;" aria-modal="true">
        <div class="modal-dialog modal-lg modal-dialog-centered"  role="document">
            <div class="modal-content">
                <div class="modal-header bg-success white">
                <h4 class="modal-title white" id="myModalLabel13">Send Invoice</h4>
                <button id="close_send_client_invoice_1" type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">×</span>
                </button>
                </div>
                <div class="modal-body">
                    <form class="container" method="POST" action="/Send-Invoice">
                        @csrf
                        <div class="form-group">
                            <p><b>Invoice Number : </b><span id="invoice_number_holder">NULL</span></p>
                        </div>
                        <div class="row">
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
                            <div class="col-md-6  border border-light">
                                <p>5. Monthly Fees</p>
                            </div>
                            <div class="col-md-6  border border-light">
                                <p><code>[monthly_fees]</code></p>
                            </div>
                            <div class="col-md-6  border border-light">
                                <p>6. Account No.</p>
                            </div>
                            <div class="col-md-6  border border-light">
                                <p><code>[acc_no]</code></p>
                            </div>
                            <div class="col-md-6  border border-light">
                                <p>7. Wallet</p>
                            </div>
                            <div class="col-md-6  border border-light">
                                <p><code>[client_wallet]</code></p>
                            </div>
                            <div class="col-md-6  border border-light">
                                <p>8. Transaction Amount</p>
                            </div>
                            <div class="col-md-6  border border-light">
                                <p><code>[trans_amnt]</code></p>
                            </div>
                            <div class="col-md-6  border border-light">
                                <p>9. Transaction Amount</p>
                            </div>
                            <div class="col-md-6  border border-light">
                                <p><code>[trans_amnt]</code></p>
                            </div>
                            <div class="col-md-6  border border-light">
                                <p>10. Invoice Link</p>
                            </div>
                            <div class="col-md-6  border border-light">
                                <p><code>[inv_link]</code></p>
                            </div>
                        </div>
                        <div class="form-group mt-2">
                            <input type="hidden" class="form-control" name="send_invoice_id" id="send_invoice_id">
                            <label for="invoice_message" class="form-control-label"><b>Invoice Message</b></label>
                            <textarea name="invoice_message" id="invoice_message" cols="30" rows="3" class="form-control" placeholder="Compose..">Hello [client_name], use this link to download your invoice, Incase you have any questions call us via {{session()->has("organization") ? session("organization")->organization_main_contact : "0720268519"}}. Link: [inv_link].</textarea>
                        </div>
                        <div class="row w-100">
                            <div class="col-md-6">
                                @php
                                    $btnText = "<i class=\"ft-mail\"></i> Send Invoice Link <span class=\"invisible\" id=\"edit_invoice_loader\"><i class=\"fas ft-rotate-cw fa-spin\"></i>";
                                    $otherClasses = "w-100 my-1";
                                @endphp
                                <x-button :btnText="$btnText" btnType="success" type="submit" btnSize="sm" :otherClasses="$otherClasses" btnId="send_client_invoice" :readOnly="$readonly" />
                                {{-- <button {{$readonly}} type="submit" {{$readonly_finance}} id="send_client_invoice" class="btn btn-success my-1 btn-sm w-100"><i class="ft-mail"></i> Send Invoice Link <span class="invisible" id="edit_invoice_loader"><i class="fas ft-rotate-cw fa-spin"></i></span></button> --}}
                            </div>
                            <div class="col-md-6">
                                @php
                                    $btnText = "<i class=\"fas fa-x\"></i> Cancel";
                                    $otherClasses = "w-100 my-1 ".($clients_data[0]->validated == 0 ? "d-none" : "");
                                @endphp
                                <x-button :btnText="$btnText" btnType="secondary" type="button" btnSize="sm" :otherClasses="$otherClasses" btnId="close_send_client_invoice_2" :readOnly="$readonly" />
                                {{-- <button class="btn btn-secondary my-1 btn-sm w-100" type="button" id="close_send_client_invoice_2">Cancel</button> --}}
                            </div>
                        </div>
                    </form>
                </div>
                <div class="modal-footer">
                </div>
            </div>
        </div>
    </div>

    {{-- SEND INVOICE --}}
    <div class="modal fade text-left hide" style="background-color: rgba(0, 0, 0, 0.5);" id="delete_client_invoice" tabindex="-1" role="dialog" aria-labelledby="myModalLabel13" style="padding-right: 17px;" aria-modal="true">
        <div class="modal-dialog modal-dialog-centered"  role="document">
            <div class="modal-content">
                <div class="modal-header bg-danger white">
                <h4 class="modal-title white" id="myModalLabel13">Confirm Invoice Delete</h4>
                <button id="close_delete_client_invoice_1" type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">×</span>
                </button>
                </div>
                <div class="modal-body">
                <div class="container" id="delete_invoice_notice"></div>
                    <div class="row w-100">
                        <div class="col-md-6">
                            @php
                                $btnText = "<i class=\"ft-trash\"></i> Delete";
                                $otherClasses = "text-bolder my-1 w-100 ".$readonly_finance;
                                $btnLink = "/Delete-Invoice";
                            @endphp
                            <x-button-link :btnText="$btnText" btnId="delete_client_invoice_btn" :btnLink="$btnLink" btnType="danger" btnSize="sm" :otherClasses="$otherClasses" :readOnly="$readonly_finance" />
                        </div>
                        <div class="col-md-6">
                            @php
                                $btnText = "<i class=\"ft-x\"></i> Cancel";
                                $otherClasses = "w-100 my-1 float-right";
                            @endphp
                            <x-button :btnText="$btnText" btnId="close_delete_client_invoice_2" btnType="secondary" btnSize="sm" :otherClasses="$otherClasses"/>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                </div>
            </div>
        </div>
    </div>

    <p><span class="text-bold-600">Client Invoice Table:</span></p>
    <div class="row">
        <div class="col-md-6 form-group">
            <input type="text" name="search" id="searchkey_3"
                class="form-control rounded-lg p-1" placeholder="Search here ..">
        </div>
        <div class="col-md-3">
            <p id="errors"></p>
        </div>
        <div class="col-md-3">
            {{-- <button class="btn btn-info btn-sm" id="new_invoice" {{$readonly_finance}}><i class="ft-file-plus"></i> Generate Invoice</button> --}}
            @php
                $btnText = "<i class=\"ft-file-plus\"></i> Generate Invoice";
                $otherClasses = "w-100 my-1 ".($clients_data[0]->validated == 0 ? "d-none" : "");
            @endphp
            <x-button :btnText="$btnText" btnType="info" type="button" btnSize="sm" :otherClasses="$otherClasses" btnId="new_invoice" :readOnly="$readonly_finance" />
        </div>
    </div>
    <div class="table-responsive" id="invoice_table_holder">
        @php
            function dateDifference($date1, $date2, $only_days = false) {
                $d1 = new DateTime($date1);
                $d2 = new DateTime($date2);
                $interval = $d1->diff($d2);
                if($only_days){
                    return $d1->diff($d2)->days;
                }

                // Check if the difference is in exact years
                if ($interval->m === 0 && $interval->d === 0) {
                    return $interval->y . ' year';
                }

                // Check if the difference is in exact months
                if ($interval->d === 0 && $interval->y === 0) {
                    return $interval->m . ' month';
                }

                // Else return difference in days
                return ($d1->diff($d2)->days / 7) . ' week';
            }
        @endphp
        <div class="container text-center my-2" id="logo_loaders_2">
            <img class=" mx-auto fa-beat-fade"  width="100" alt="Your Logo Appear Here"
                src="{{session("organization_logo") != null ? session("organization_logo") :'/theme-assets/images/logoplaceholder.svg'}}" />
        </div>
        <table class="table" id="invoice_table">
            <thead>
                <tr>
                    <th>#</th>
                    <th>Invoice No</th>
                    <th>Amount to Pay</th>
                    <th>Date Generated</th>
                    <th>Payment for</th>
                    <th>Action</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($invoices as $key => $invoice)
                        @php
                            $invoice->invoice_for_duration = "0 days";
                            $invoice->payment_from_date = date("Y-m-d", strtotime($invoice->date_generated));
                            $invoice->payment_from_time = date("H:i", strtotime($invoice->date_generated));
                            $date_selected = "NULL";
                            if (isJson($invoice->invoice_for)) {
                                $dates = json_decode($invoice->invoice_for);
                                $date_selected = date("d-M-Y", strtotime($dates[0]))." - ".date("d-M-Y", strtotime($dates[1]));
                                $invoice->invoice_for = $date_selected;
                                $invoice->payment_from_date = date("Y-m-d", strtotime($dates[0]));
                                $invoice->payment_from_time = date("H:i", strtotime($dates[0]));
                                $invoice->invoice_for_duration = dateDifference($dates[0],$dates[1]);
                            }else{
                                $invoice->invoice_for = "NULL";
                            }
                            $deadline_expiry = dateDifference($invoice->date_generated, $invoice->invoice_deadline, true);
                            $invoice->deadline_duration = $deadline_expiry;
                            $invoice->date_generated_1 = date("D dS M Y", strtotime($invoice->date_generated));
                            $invoice->date_generated_2 = date("Y-m-d", strtotime($invoice->date_generated));
                            $invoice->amount_to_pay_f = "Kes ".number_format($invoice->amount_to_pay);
                        @endphp
                    <tr>
                        <th scope="row">{{$key + 1}} <input type="hidden" value="{{json_encode($invoice)}}" id="invoice_data_holder_{{$invoice->invoice_id}}"> </th>
                        <td>{{$invoice->invoice_number ?? "NULL"}}</td>
                        <td>{{$invoice->amount_to_pay_f}}</td>
                        <td>{{$invoice->date_generated_1}}</td>
                        <td>{{$date_selected}}</td>
                        <td>
                            @php
                                $btnText = "<i class=\"ft-eye\"></i>";
                                $otherClasses = "view_invoice";
                                $btn_id = "view_invoice_".$invoice->invoice_id;
                            @endphp
                            <x-button :btnText="$btnText" toolTip="View Invoice" btnType="primary" type="button" btnSize="sm" :otherClasses="$otherClasses" :btnId="$btn_id" :readOnly="$readonly_finance" />
                            {{-- <button {{$readonly_finance}} data-toggle="tooltip" title="View Invoice" class="btn btn-sm btn-primary view_invoice" id="view_invoice_{{$invoice->invoice_id}}"><i class="ft-eye"></i></button> --}}
                            @php
                                $btnText = "<i class=\"ft-printer\"></i>";
                                $otherClasses = "".$readonly_finance;
                                $btnLink = "/Invoice/Print/".$invoice->invoice_id;
                            @endphp
                            <x-button-link :btnLink="$btnLink" :btnText="$btnText" target="_blank" toolTip="Print Invoice" btnType="info" btnSize="sm" :otherClasses="$otherClasses" :readOnly="$readonly_finance" />
                            @php
                                $btnText = "<i class=\"ft-trash\"></i>";
                                $otherClasses = "delete_invoice";
                                $btn_id = "delete_invoice_".$invoice->invoice_id;
                            @endphp
                            <x-button :btnText="$btnText" toolTip="Delete Invoice" btnType="danger" type="button" btnSize="sm" :otherClasses="$otherClasses" :btnId="$btn_id" :readOnly="$readonly_finance" />
                            {{-- <button {{$readonly_finance}} data-toggle="tooltip" title="Delete Invoice" class="btn btn-sm btn-danger delete_invoice" id="delete_invoice_{{$invoice->invoice_id}}"><i class="ft-trash"></i></button> --}}
                            @php
                                $btnText = "<i class=\"ft-mail\"></i>";
                                $otherClasses = "send_invoice";
                                $btn_id = "send_invoice_".$invoice->invoice_id;
                            @endphp
                            <x-button :btnText="$btnText" toolTip="Send Invoice" btnType="success" type="button" btnSize="sm" :otherClasses="$otherClasses" :btnId="$btn_id" :readOnly="$readonly_finance" />
                            {{-- <button {{$readonly_finance}} data-toggle="tooltip" title="Send Invoice" class="btn btn-sm btn-success send_invoice" id="send_invoice_{{$invoice->invoice_id}}"><i class="ft-mail"></i></button> --}}
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</div>