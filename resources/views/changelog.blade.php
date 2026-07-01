<div class="modal fade" id="changelogModal" tabindex="-1" role="dialog" aria-labelledby="changelogModalLabel" aria-hidden="true" data-backdrop="static" data-keyboard="false">
    <div class="modal-dialog modal-lg modal-dialog-centered" role="document">
        <div class="modal-content">
            <div class="modal-header bg-primary white">
                <h5 class="modal-icon-title" id="changelogModalLabel">
                    <i class="ft-zap mr-1"></i> What's New &nbsp; {{ env('APP_UPDATE_VERSION') }}
                </h5>
            </div>
            <div class="modal-body" style="max-height:65vh; overflow-y:auto;">

                <h6 class="text-primary font-weight-bold mb-1">Login and Email</h6>
                <ul class="mb-3">
                    <li>You can now receive your login code by email even if you have not set up a personal email on your account. The system will send it to the default email instead.</li>
                    <li>If the default email is used, you will see a note on the verification page telling you where the code was sent and how to add your own email.</li>
                </ul>

                <h6 class="text-primary font-weight-bold mb-1">Email Setup Guide</h6>
                <ul class="mb-3">
                    <li>The Email Settings page now has a setup guide. Pick your email provider and the system will fill in the correct settings for you and show you the steps to follow.</li>
                </ul>

                <h6 class="text-primary font-weight-bold mb-1">WhatsApp</h6>
                <ul class="mb-3">
                    <li>You can now send and receive WhatsApp messages directly from the system.</li>
                    <li>Messages from unknown numbers are saved in a shared inbox.</li>
                    <li>You can send messages to many clients at once using templates.</li>
                    <li>The chat updates on its own every few seconds so you do not need to refresh the page.</li>
                    <li>You can insert client details like name, phone and expiry date into your message with one click.</li>
                </ul>

                <h6 class="text-primary font-weight-bold mb-1">Update Notices</h6>
                <ul class="mb-0">
                    <li>After every system update you will see this window once when you log in to show you what has changed.</li>
                    <li>You can open it again any time by clicking the <i class="ft-zap"></i> icon at the top of the page.</li>
                </ul>

            </div>
            <div class="modal-footer">
                @php
                    $btnText = '<i class="ft-check"></i> Got it';
                    $otherClasses = "";
                    $otherAttributes = "";
                @endphp
                <x-button toolTip="" btnType="primary" :otherAttributes="$otherAttributes" :btnText="$btnText" type="button" btnSize="md" :otherClasses="$otherClasses" btnId="changelogDismissBtn" :readOnly="false" />
            </div>
        </div>
    </div>
</div>

<script>
    window.addEventListener('load', function () {
        document.getElementById('changelogDismissBtn').addEventListener('click', function () {
            $.post('/changelog/acknowledge', { _token: '{{ csrf_token() }}' }, function () {
                $('#changelogModal').modal('hide');
            });
        });

        @if(session('show_changelog'))
            $('#changelogModal').modal('show');
        @endif
    });
</script>
