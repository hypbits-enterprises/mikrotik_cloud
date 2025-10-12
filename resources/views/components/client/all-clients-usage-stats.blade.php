<div class="row w-100">
    <div class="col-md-3 py-1 px-auto">
        <div class="card rounded border border-secondary" style="box-shadow: 0 4px 6px rgba(0,0,0,0.1) !important;">
            <div class="card-header p-1 ps-3">
                <div class="d-flex justify-content-between">
                    <div>
                        <small class="text-sm mb-0 text-capitalize">Usage Last 30 days</small>
                        <h4 class="mb-0">{{$monthly_stats['this_month_usage']}}</h4>
                    </div>
                    <div class="icon icon-md icon-shape bg-primary text-white shadow pt-1 px-1 text-center rounded">
                        <h4 class="text-white"><i class="ft-activity"></i></h4>
                    </div>
                </div>
            </div>
            <hr class="dark horizontal my-0">
            <div class="card-footer" style="padding: 2px">
                <small class="ml-1 mb-0 text-sm"><span class="{{$monthly_stats['increase'] ? "text-success" : "text-danger"}} text-bold">{{$monthly_stats['percentage']}} </span>{{$daily_stats['percentage']!=0 ? "than last month" : ""}}</small>
            </div>
        </div>
    </div>
    <div class="col-md-3 py-1 px-auto">
        <div class="card rounded border border-secondary" style="box-shadow: 0 4px 6px rgba(0,0,0,0.1) !important;">
            <div class="card-header p-1 ps-3">
                <div class="d-flex justify-content-between">
                    <div>
                        <small class="text-sm mb-0 text-capitalize">Usage Today</small>
                        <h4 class="mb-0">{{$daily_stats['todays_usage']}}</h4>
                    </div>
                    <div class="icon icon-md icon-shape bg-primary text-white shadow pt-1 px-1 text-center rounded">
                        <h5 class="text-white"><i class="ft-calendar"></i></h5>
                    </div>
                </div>
            </div>
            <hr class="dark horizontal my-0">
            <div class="card-footer" style="padding: 2px">
                <small class="ml-1 mb-0 text-sm"><span class="{{$daily_stats['increase'] ? "text-success" : "text-danger"}} text-bold">{{$daily_stats['percentage']}} </span>{{$daily_stats['percentage']!=0 ? "than yesterday" : ""}}</small>
            </div>
        </div>
    </div>
    <div class="col-md-3 py-1 px-auto">
        <div class="card rounded border border-secondary" style="box-shadow: 0 4px 6px rgba(0,0,0,0.1) !important;">
            <div class="card-header p-1 ps-3">
                <div class="d-flex justify-content-between">
                    <div>
                        <small class="text-sm mb-0 text-capitalize">Bandwidth This Week</small>
                        <h4 class="mb-0">{{$bandwidth_stats['this_week_band']}}PS</h4>
                    </div>
                    <div class="icon icon-md icon-shape bg-primary text-white shadow pt-1 px-1 text-center rounded">
                        <h5 class="text-white"><i class="ft-bar-chart"></i></h5>
                    </div>
                </div>
            </div>
            <hr class="dark horizontal my-0">
            <div class="card-footer" style="padding: 2px">
                <small class="ml-1 mb-0 text-sm"><span class="{{$bandwidth_stats['increase'] ? "text-success" : "text-danger"}} text-bold">{{$bandwidth_stats['percentage']}} </span>{{$bandwidth_stats['percentage']!=0 ? "than last week" : ""}}</small>
            </div>
        </div>
    </div>
    <div class="col-md-3 py-1 px-auto">
        <div class="card rounded border border-secondary" style="box-shadow: 0 4px 6px rgba(0,0,0,0.1) !important;">
            <div class="card-header p-1 ps-3">
                <div class="d-flex justify-content-between">
                    <div>
                        <small class="text-sm mb-0 text-capitalize">Client Status <span id="client_status_loader" class="invisible fa-beat-fade badge badge-secondary"> </span></small>
                        <p class="d-none" id="status_holder"></p><br>
                        <span id="online_status" class="badge bg-success">{{$client_status['online']}} Online</span>
                        <span id="offline_status" class="badge bg-danger">{{$client_status['offline']}} Offline</span>
                        <span id="never_online_status" class="badge bg-secondary">{{$client_status['never_online']}} Never Online</span>
                    </div>
                    <div class="icon icon-md icon-shape bg-primary text-white shadow pt-1 px-1 text-center rounded">
                        <h5 class="text-white"><i class="ft-clock"></i></h5>
                    </div>
                </div>
            </div>
            <hr class="dark horizontal my-0">
            <div class="card-footer" style="padding: 2px">
                <small id="online_last_seen" class="ml-1 mb-0 text-sm "><span class="fa-beat-fade badge badge-success"> </span> Online</small>
                {{-- <small id="offline_last_seen" class="ml-1 mb-0 text-sm ">Last seen: {{$client_status['last_seen'] == null ? "Never Active" : date("dS M Y : H:iA", strtotime($client_status['last_seen']))}}</small> --}}
            </div>
        </div>
    </div>
</div>