{{-- Activity log for a router: the latest task on one line, every task's steps collapsed under it.
     $activities from Hotspot\Routers::activities(). Re-rendered while a task is queued/running. --}}
@php
    $badges = ["success" => "success", "failed" => "danger", "queued" => "warning", "running" => "info"];
@endphp
@if (count($activities) == 0)
    <p class="text-muted mb-0">No hotspot tasks yet.</p>
@else
    @php $latest = $activities[0]; @endphp
    <div class="d-flex flex-wrap align-items-center" data-status="{{ $latest['status'] }}">
        <span class="mr-1">Latest:</span>
        <span class="badge badge-{{ $badges[$latest['status']] ?? 'secondary' }} mr-1">{{ $latest["status"] }}</span>
        <b class="mr-1">{{ $latest["action"] }}</b> <span class="mr-1">{{ $latest["target"] }}</span>
        <small class="text-muted mr-1">— {{ $latest["when"] }}</small>
        @php $btnText = "<i class=\"ft-list\"></i> Show details (" . count($activities) . ")"; @endphp
        <x-button :btnText="$btnText" btnType="secondary" btnSize="sm" btnId="hotspot_activity_toggle" otherClasses="ml-auto" otherAttributes="data-toggle=collapse data-target=#hotspot_activity_details aria-expanded=false" />
    </div>
    <div class="collapse mt-1" id="hotspot_activity_details">
        @foreach ($activities as $activity)
            <div class="border-top pt-1 mt-1" data-status="{{ $activity['status'] }}">
                <span class="badge badge-{{ $badges[$activity['status']] ?? 'secondary' }}">{{ $activity["status"] }}</span>
                <b>{{ $activity["action"] }}</b> {{ $activity["target"] }}
                <small class="text-muted">— {{ $activity["when"] }}</small>
                @if (count($activity["log"]) > 0)
                    <ul class="mb-0 mt-1" style="font-size: 0.85rem;">
                        @foreach ($activity["log"] as $line)
                            <li><span class="text-{{ in_array($line['result'] ?? '', ['failed']) ? 'danger' : 'muted' }}">{{ $line["step"] ?? "" }}: {{ $line["result"] ?? "" }}</span> {{ $line["detail"] ?? "" }}</li>
                        @endforeach
                    </ul>
                @endif
            </div>
        @endforeach
    </div>
@endif
