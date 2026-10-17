{{--
    Hotspot rows for the administrator privileges table (addadmin / viewadmin).
    $hotspot_admin_privileges: the admin's priviledges JSON when editing, null when adding.
    Shown only when the org has hotspot enabled. The script below keeps the hidden #privileged JSON
    in sync without touching the existing per-option scripts (addadmin.js / view_admin.js).
--}}
@if (\App\Classes\HotspotAccess::enabledForOrg(session('organization_id')))
    @php
        $hotspot_options = \App\Classes\HotspotAccess::options();
    @endphp
    @foreach ($hotspot_options as $hotspot_index => $hotspot_option)
        @php
            $hotspot_view = $hotspot_admin_privileges !== null && \App\Classes\HotspotAccess::can($hotspot_admin_privileges, $hotspot_option);
            $hotspot_readonly = $hotspot_admin_privileges !== null && $hotspot_view && \App\Classes\HotspotAccess::readOnly($hotspot_admin_privileges, $hotspot_option);
        @endphp
        <tr>
            @if ($hotspot_index == 0)
                <th rowspan="{{ count($hotspot_options) }}" scope="row">6</th>
            @endif
            <td><label class="form-label"><b>{{ $hotspot_option }}</b></label></td>
            <td><input class="hotspot_privilege_view" type="checkbox" data-option="{{ $hotspot_option }}" {{ $hotspot_view ? "checked" : "" }}></td>
            <td><input class="hotspot_privilege_readonly" type="checkbox" data-option="{{ $hotspot_option }}" {{ $hotspot_readonly ? "checked" : "" }}></td>
            <td><small class="text-muted">Hotspot</small></td>
        </tr>
    @endforeach
    <script>
        (function () {
            function syncHotspotPrivileges() {
                var holder = document.getElementById("privileged");
                if (!holder) {
                    return;
                }
                var list = [];
                try {
                    list = JSON.parse(holder.value);
                } catch (e) {
                    list = [];
                }
                if (!Array.isArray(list)) {
                    list = [];
                }
                var views = document.getElementsByClassName("hotspot_privilege_view");
                for (var i = 0; i < views.length; i++) {
                    var option = views[i].getAttribute("data-option");
                    var readonly = document.querySelector('.hotspot_privilege_readonly[data-option="' + option + '"]');
                    var entry = {option: option, view: views[i].checked, readonly: readonly ? readonly.checked : false, expiry: "indefinate_expiry", expiry_date: ""};
                    var found = false;
                    for (var j = 0; j < list.length; j++) {
                        if (list[j].option == option) {
                            list[j] = entry;
                            found = true;
                        }
                    }
                    if (!found) {
                        list.push(entry);
                    }
                }
                holder.value = JSON.stringify(list);
            }

            document.addEventListener("change", function (e) {
                if (e.target.classList.contains("hotspot_privilege_view") || e.target.classList.contains("hotspot_privilege_readonly")) {
                    syncHotspotPrivileges();
                }
            });

            // the existing scripts rebuild #privileged on some changes, so write the hotspot entries again on submit
            var holder = document.getElementById("privileged");
            if (holder && holder.form) {
                holder.form.addEventListener("submit", syncHotspotPrivileges);
            }
        })();
    </script>
@endif
