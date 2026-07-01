<div class="modal fade" id="changelogModal" tabindex="-1" role="dialog" aria-labelledby="changelogModalLabel" aria-hidden="true" data-backdrop="static" data-keyboard="false">
    <div class="modal-dialog modal-lg modal-dialog-centered" role="document">
        <div class="modal-content">
            <div class="modal-header bg-primary white">
                <h5 class="modal-icon-title" id="changelogModalLabel">
                    <i class="ft-zap mr-1"></i> What's New — {{ env('APP_UPDATE_VERSION') }}
                </h5>
            </div>
            <div class="modal-body" style="max-height:65vh; overflow-y:auto;">

                <h6 class="text-primary font-weight-bold mb-1">Login &amp; Email Improvements</h6>
                <ul class="mb-3">
                    <li>Login verification codes can now be sent via email even when a personal email is not set on your account — the system falls back to the default email automatically.</li>
                    <li>The verification page now shows a clear notice when the fallback is used, with a link to your profile to set up a personal email.</li>
                    <li>The from-address used when sending login codes and password reset emails now comes from the system email config rather than being hardcoded.</li>
                </ul>

                <h6 class="text-primary font-weight-bold mb-1">Email SMTP Setup Guide</h6>
                <ul class="mb-3">
                    <li>A new Setup Guide section has been added to the Email Settings page.</li>
                    <li>Select your email provider (Gmail, Yahoo, Outlook, or Other) to auto-fill the host, port, and encryption fields and see step-by-step App Password instructions.</li>
                    <li>A prompt in the SMTP form now points you to the guide when you open the page.</li>
                </ul>

                <h6 class="text-primary font-weight-bold mb-1">WhatsApp Module</h6>
                <ul class="mb-3">
                    <li>Full WhatsApp Business Cloud API integration — send and receive messages directly from the system.</li>
                    <li>Inbound messages are routed to the correct organisation automatically; unknown senders are captured in a shared inbox.</li>
                    <li>Real-time chat with 5-second polling on active threads and 15-second sidebar refresh.</li>
                    <li>Template management with Meta API auto-submission and status sync.</li>
                    <li>Bulk template sends to multiple clients in one action.</li>
                    <li>Conversation billing fields captured from Meta webhooks for accurate per-conversation billing.</li>
                    <li>Variable insert chips in the compose area (Name, Phone, Account, Monthly, Wallet, Expiry, Router, Address).</li>
                </ul>

                <h6 class="text-primary font-weight-bold mb-1">Update Notifications</h6>
                <ul class="mb-0">
                    <li>This popup! From now on, you will see a one-time "What's New" notice after each system update when you log in.</li>
                    <li>You can re-open it any time using the <i class="ft-zap"></i> icon in the top bar.</li>
                </ul>

            </div>
            <div class="modal-footer">
                @php
                    $btnText = '<i class="ft-check mr-1"></i> Got it';
                    $otherClasses = "";
                    $otherAttributes = "";
                @endphp
                <x-button toolTip="" btnType="primary" :otherAttributes="$otherAttributes" :btnText="$btnText" type="button" btnSize="md" :otherClasses="$otherClasses" btnId="changelogDismissBtn" :readOnly="false" />
            </div>
        </div>
    </div>
</div>

<script>
    document.getElementById('changelogDismissBtn').addEventListener('click', function () {
        $.post('{{ route('changelog.acknowledge') }}', { _token: '{{ csrf_token() }}' }, function () {
            $('#changelogModal').modal('hide');
        });
    });

    @if(session('show_changelog'))
        $(document).ready(function () {
            $('#changelogModal').modal('show');
        });
    @endif
</script>
