{{-- Payments page modals: approve a claim, assign an unmatched payment, reject either. Filled from
     the clicked button's data- attributes. --}}
<div class="modal fade text-left" id="approve_modal" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <form method="POST" action="" id="approve_form">
                @csrf
                <div class="modal-header bg-success white"><h4 class="modal-title white">Approve M-Pesa payment <span class="hs-code"></span>?</h4></div>
                <div class="modal-body">
                    <p class="mb-0">Make sure this payment really came from this customer (for example, they showed you the M-Pesa message). The ticket code is then sent by SMS to <b class="hs-phone"></b>, and the payment can't be used again.</p>
                </div>
                <div class="modal-footer">
                    @php $btnText = "Cancel"; @endphp
                    <x-button :btnText="$btnText" btnType="secondary" btnSize="sm" otherAttributes="data-dismiss=modal" />
                    @php $btnText = "<i class=\"ft-check\"></i> Approve"; @endphp
                    <x-button :btnText="$btnText" btnType="success" btnSize="sm" type="submit" btnId="approve_submit" :readOnly="$disabled" />
                </div>
            </form>
        </div>
    </div>
</div>
<div class="modal fade text-left" id="assign_modal" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <form method="POST" action="{{ route('hotspot.payment_assign') }}">
                @csrf
                <input type="hidden" name="source" value="hotspot">
                <input type="hidden" name="transaction_id" id="assign_id" value="">
                <div class="modal-header bg-success white"><h4 class="modal-title white">Assign <span class="hs-code"></span> (Kes <span id="assign_amount"></span>) to hotspot</h4></div>
                <div class="modal-body">
                    @include("hotspot.partials.assign-fields")
                </div>
                <div class="modal-footer">
                    @php $btnText = "Cancel"; @endphp
                    <x-button :btnText="$btnText" btnType="secondary" btnSize="sm" otherAttributes="data-dismiss=modal" />
                    @php $btnText = "<i class=\"ft-tag\"></i> Create ticket and send code"; @endphp
                    <x-button :btnText="$btnText" btnType="success" btnSize="sm" type="submit" btnId="assign_submit" :readOnly="$disabled" />
                </div>
            </form>
        </div>
    </div>
</div>
<div class="modal fade text-left" id="reject_modal" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <form method="POST" action="{{ route('hotspot.payment_reject') }}">
                @csrf
                <input type="hidden" name="kind" id="reject_kind" value="">
                <input type="hidden" name="id" id="reject_id" value="">
                <div class="modal-header bg-danger white"><h4 class="modal-title white">Reject <span class="hs-code"></span>?</h4></div>
                <div class="modal-body"><p class="mb-0">No ticket is created. Do this when the payment is not for hotspot or the customer could not show it is theirs.</p></div>
                <div class="modal-footer">
                    @php $btnText = "Cancel"; @endphp
                    <x-button :btnText="$btnText" btnType="secondary" btnSize="sm" otherAttributes="data-dismiss=modal" />
                    @php $btnText = "<i class=\"ft-x\"></i> Reject"; @endphp
                    <x-button :btnText="$btnText" btnType="danger" btnSize="sm" type="submit" btnId="reject_submit" :readOnly="$disabled" />
                </div>
            </form>
        </div>
    </div>
</div>
<script>
    document.addEventListener('click', function (e) {
        var button = e.target.closest ? e.target.closest('.hs-approve, .hs-assign, .hs-reject') : null;
        if (!button) { return; }
        var modal = document.querySelector(button.getAttribute('data-target'));
        var codes = modal.querySelectorAll('.hs-code');
        for (var i = 0; i < codes.length; i++) { codes[i].textContent = button.getAttribute('data-code'); }
        if (button.classList.contains('hs-approve')) {
            document.getElementById('approve_form').action = button.getAttribute('data-url');
            modal.querySelector('.hs-phone').textContent = button.getAttribute('data-phone');
        } else if (button.classList.contains('hs-assign')) {
            document.getElementById('assign_id').value = button.getAttribute('data-id');
            document.getElementById('assign_amount').textContent = button.getAttribute('data-amount');
            document.getElementById('assign_phone').value = button.getAttribute('data-phone');
        } else {
            document.getElementById('reject_kind').value = button.getAttribute('data-kind');
            document.getElementById('reject_id').value = button.getAttribute('data-id');
        }
    });
</script>
