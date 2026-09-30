<div class="audience-builder border rounded p-2" id="{{ $idPrefix }}_builder">
    <div class="row">
        <div class="col-md-4 form-group mb-1">
            <label class="form-control-label mb-0" style="font-size:0.85rem;">Status</label>
            <select name="client_status" class="form-control form-control-sm audience-filter" {{ $readOnly }}>
                <option value="">All</option>
                <option value="1">Active</option>
                <option value="0">Inactive</option>
            </select>
        </div>
        <div class="col-md-4 form-group mb-1">
            <label class="form-control-label mb-0" style="font-size:0.85rem;">Router</label>
            <select name="router_id" class="form-control form-control-sm audience-filter" {{ $readOnly }}>
                <option value="">All</option>
                @foreach ($routers as $router)
                    <option value="{{ $router->router_id }}">{{ $router->router_name }}</option>
                @endforeach
            </select>
        </div>
        <div class="col-md-4 form-group mb-1">
            <label class="form-control-label mb-0" style="font-size:0.85rem;">Region</label>
            <select name="region" class="form-control form-control-sm audience-filter" {{ $readOnly }}>
                <option value="">All</option>
                @foreach ($regions as $region)
                    <option value="{{ $region->name }}">{{ $region->name }}</option>
                @endforeach
            </select>
        </div>
        <div class="col-md-4 form-group mb-1">
            <label class="form-control-label mb-0" style="font-size:0.85rem;">Package / Profile</label>
            <select name="client_profile" class="form-control form-control-sm audience-filter" {{ $readOnly }}>
                <option value="">All</option>
                @foreach ($profiles as $profile)
                    <option value="{{ $profile }}">{{ $profile }}</option>
                @endforeach
            </select>
        </div>
        <div class="col-md-4 form-group mb-1">
            <label class="form-control-label mb-0" style="font-size:0.85rem;">Client Type</label>
            <select name="assignment" class="form-control form-control-sm audience-filter" {{ $readOnly }}>
                <option value="">All</option>
                <option value="pppoe">PPPoE</option>
                <option value="static">Static</option>
            </select>
        </div>
        <div class="col-md-4 form-group mb-1">
            <label class="form-control-label mb-0" style="font-size:0.85rem;">Preferred Channel</label>
            <select name="preferred_channel" class="form-control form-control-sm audience-filter" {{ $readOnly }}>
                <option value="">All</option>
                <option value="sms">SMS</option>
                <option value="whatsapp">WhatsApp</option>
                <option value="email">Email</option>
            </select>
        </div>
        <div class="col-md-4 form-group mb-1">
            <label class="form-control-label mb-0" style="font-size:0.85rem;">Payment Status</label>
            <select name="payments_status" class="form-control form-control-sm audience-filter" {{ $readOnly }}>
                <option value="">All</option>
                <option value="1">Paid</option>
                <option value="0">Unpaid</option>
            </select>
        </div>
    </div>

    <div class="d-flex align-items-center mt-1" style="gap:8px;">
        <span class="badge badge-info" id="{{ $idPrefix }}_count" style="font-size:0.9rem;">Matching clients: —</span>
        <small class="text-muted" id="{{ $idPrefix }}_loading" hidden>checking…</small>
    </div>

    <div class="table-responsive mt-1" id="{{ $idPrefix }}_preview_wrap" hidden>
        <table id="{{ $idPrefix }}_preview_table" class="table table-hover table-bordered table-sm" style="width:100%">
            <thead class="thead-light">
                <tr>
                    <th>Name</th>
                    <th>Phone</th>
                    <th>Region</th>
                    <th>Router</th>
                </tr>
            </thead>
            <tbody></tbody>
        </table>
    </div>
</div>

<script>
// Deferred to DOMContentLoaded: vendors.min.js and the DataTables plugin load
// near the end of <body>, after this component's own inline script tag, but
// both are normal (non-async) scripts so they've already run by the time
// DOMContentLoaded fires.
document.addEventListener('DOMContentLoaded', function () {
    var prefix    = @json($idPrefix);
    var builder   = document.getElementById(prefix + '_builder');
    var countEl   = document.getElementById(prefix + '_count');
    var loadingEl = document.getElementById(prefix + '_loading');
    var wrapEl    = document.getElementById(prefix + '_preview_wrap');
    var csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
    var debounceTimer = null;
    var previewTable = null;

    function currentFilters() {
        var data = {};
        builder.querySelectorAll('.audience-filter').forEach(function (el) {
            data[el.name] = el.value;
        });
        return data;
    }

    function refreshAudienceCount() {
        loadingEl.hidden = false;
        fetch('/audience/preview', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': csrfToken,
                'Accept': 'application/json',
            },
            body: JSON.stringify(currentFilters()),
        })
            .then(function (r) { return r.json(); })
            .then(function (data) {
                loadingEl.hidden = true;
                countEl.textContent = 'Matching clients: ' + data.count;
                builder.dispatchEvent(new CustomEvent('audience-count-changed', { detail: data.count }));

                wrapEl.hidden = data.count === 0;

                if (!previewTable) {
                    previewTable = $('#' + prefix + '_preview_table').DataTable({
                        data: data.clients,
                        pageLength: 10,
                        lengthMenu: [10, 25, 50, 100],
                        dom: '<"d-flex justify-content-between align-items-center mb-1"lf>t<"d-flex justify-content-between align-items-center mt-1"ip>',
                        language: {
                            emptyTable: 'No clients match the selected filters',
                        },
                        columns: [
                            { data: 'client_name' },
                            { data: 'clients_contacts' },
                            { data: 'region' },
                            { data: 'router_name' },
                        ],
                    });
                } else {
                    previewTable.clear();
                    previewTable.rows.add(data.clients);
                    previewTable.draw();
                }
            })
            .catch(function () {
                loadingEl.hidden = true;
                countEl.textContent = 'Matching clients: —';
            });
    }

    builder.querySelectorAll('.audience-filter').forEach(function (el) {
        el.addEventListener('change', function () {
            clearTimeout(debounceTimer);
            debounceTimer = setTimeout(refreshAudienceCount, 250);
        });
    });

    // Refresh once as soon as the panel becomes visible (e.g. a tab switch
    // elsewhere on the page toggling its wrapper's `hidden`/`d-none`).
    builder.refreshAudienceCount = refreshAudienceCount;
    refreshAudienceCount();
});
</script>
