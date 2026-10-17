{{-- On the billing transaction page (unassigned payment): move the payment to hotspot instead of a
     client. Shown only when the org has hotspot and the admin has the Hotspot Payments privilege. --}}
@php
    $hotspotAssign = session('organization_id') && \App\Classes\HotspotAccess::enabledForOrg(session('organization_id'))
        && \App\Classes\HotspotAccess::can(session('priviledges'), 'Hotspot Payments');
    if ($hotspotAssign) {
        $plans = \Illuminate\Support\Facades\DB::connection('mysql2')->table('hotspot_plans')->where('deleted', 0)->orderBy('sort_order')->orderBy('price')->get();
        $routers = \Illuminate\Support\Facades\DB::connection('mysql2')->table('remote_routers')->where('deleted', 0)->where('hotspot_enabled', 1)->orderBy('router_name')->get();
        $disabled = \App\Classes\HotspotAccess::readOnly(session('priviledges'), 'Hotspot Payments') ? 'disabled' : '';
    }
@endphp
@if ($hotspotAssign && count($plans) > 0 && count($routers) > 0)
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header"><h4 class="card-title"><i class="ft-wifi"></i> Assign to hotspot</h4></div>
                <div class="card-content"><div class="card-body">
                    @if (session("error_hotspot"))
                        <div class="alert alert-danger">{{ session("error_hotspot") }}</div>
                    @endif
                    <p class="text-muted">This payment was for a hotspot package? It moves to Hotspot → Payments (out of this list), a ticket is created and its code is sent by SMS to the customer's phone.</p>
                    <form method="POST" action="{{ route('hotspot.payment_assign') }}">
                        @csrf
                        <input type="hidden" name="source" value="billing">
                        <input type="hidden" name="back" value="billing">
                        <input type="hidden" name="transaction_id" value="{{ $transaction->transaction_id }}">
                        <div class="row">
                            <div class="col-md-8">@include("hotspot.partials.assign-fields")</div>
                        </div>
                        @php $btnText = "<i class=\"ft-wifi\"></i> Assign to hotspot"; @endphp
                        <x-button :btnText="$btnText" btnType="info" btnSize="sm" btnId="hotspot_assign_open" :readOnly="$disabled" otherAttributes="data-toggle=modal data-target=#hotspot_assign_modal" otherClasses="mt-1" />
                        <div class="modal fade text-left" id="hotspot_assign_modal" tabindex="-1" role="dialog" aria-hidden="true">
                            <div class="modal-dialog" role="document">
                                <div class="modal-content">
                                    <div class="modal-header bg-info white"><h4 class="modal-title white">Assign {{ $transaction->transaction_mpesa_id }} to hotspot?</h4></div>
                                    <div class="modal-body"><p class="mb-0">Kes {{ number_format($transaction->transacion_amount, 2) }} becomes a hotspot payment and is removed from your billing transactions. It can't then be assigned to a client.</p></div>
                                    <div class="modal-footer">
                                        @php $btnText = "Cancel"; @endphp
                                        <x-button :btnText="$btnText" btnType="secondary" btnSize="sm" otherAttributes="data-dismiss=modal" />
                                        @php $btnText = "<i class=\"ft-wifi\"></i> Assign to hotspot"; @endphp
                                        <x-button :btnText="$btnText" btnType="info" btnSize="sm" type="submit" btnId="hotspot_assign_submit" :readOnly="$disabled" />
                                    </div>
                                </div>
                            </div>
                        </div>
                    </form>
                </div></div>
            </div>
        </div>
    </div>
@endif
