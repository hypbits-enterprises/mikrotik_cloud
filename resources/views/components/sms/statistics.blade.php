<div class="row w-100">
    <div class="col-md-4 py-1 px-auto">
        <div class="card rounded border border-secondary" style="box-shadow: 0 4px 6px rgba(0,0,0,0.1) !important;">
            <div class="card-header p-1 ps-3">
                <div class="d-flex justify-content-between">
                    <div>
                        <small class="text-sm mb-0 text-capitalize">SMS Sent Today</small>
                        <h4 class="mb-0">{{number_format($dailyStats['today'])." SMS"}}</h4>
                    </div>
                    <div class="icon icon-md icon-shape bg-primary text-white shadow pt-1 px-1 text-center rounded">
                        <h5 class="text-white"><i class="ft-calendar"></i></h5>
                    </div>
                </div>
            </div>
            <hr class="dark horizontal my-0">
            <div class="card-footer" style="padding: 2px">
                <small class="ml-1 mb-0 text-sm"><span class="{{$dailyStats['isIncrease'] ? "text-success" : "text-danger"}} text-bold">{{$dailyStats['percentage']}} </span>{{$dailyStats['percentage']!=0 ? "than yesterday" : ""}}</small>
            </div>
        </div>
    </div>
    <div class="col-md-4 py-1 px-auto">
        <div class="card rounded border border-secondary" style="box-shadow: 0 4px 6px rgba(0,0,0,0.1) !important;">
            <div class="card-header p-1 ps-3">
                <div class="d-flex justify-content-between">
                    <div>
                        <small class="text-sm mb-0 text-capitalize">SMS Sent Last 7 days</small>
                        <h4 class="mb-0">{{number_format($weeklyStats['this_week'])." SMS"}}</h4>
                    </div>
                    <div class="icon icon-md icon-shape bg-primary text-white shadow pt-1 px-1 text-center rounded">
                        <h5 class="text-white"><i class="ft-clock"></i></h5>
                    </div>
                </div>
            </div>
            <hr class="dark horizontal my-0">
            <div class="card-footer" style="padding: 2px">
                <small class="ml-1 mb-0 text-sm"><span class="{{$weeklyStats['isIncrease'] ? "text-success" : "text-danger"}} text-bold">{{$weeklyStats['percentage']}} </span>{{$weeklyStats['percentage']!=0 ? "than last week" : ""}}</small>
            </div>
        </div>
    </div>
    <div class="col-md-4 py-1 px-auto">
        <div class="card rounded border border-secondary" style="box-shadow: 0 4px 6px rgba(0,0,0,0.1) !important;">
            <div class="card-header p-1 ps-3">
                <div class="d-flex justify-content-between">
                    <div>
                        <small class="text-sm mb-0 text-capitalize">SMS Sent Last 30 days</small>
                        <h4 class="mb-0">{{($monthlyStats['this_month'])." SMS"}}</h4>
                    </div>
                    <div class="icon icon-md icon-shape bg-primary text-white shadow pt-1 px-1 text-center rounded">
                        <h4 class="text-white"><i class="ft-bar-chart-2"></i></h4>
                    </div>
                </div>
            </div>
            <hr class="dark horizontal my-0">
            <div class="card-footer" style="padding: 2px">
                <small class="ml-1 mb-0 text-sm"><span class="{{$monthlyStats['isIncrease'] ? "text-success" : "text-danger"}} text-bold">{{$monthlyStats['percentage']}} </span>{{$monthlyStats['percentage']!=0 ? "than last month" : ""}}</small>
            </div>
        </div>
    </div>
    <hr>
    <div class="col-md-12" style="height:400px;">
        <canvas class="w-75 mx-auto" id="sms_graph_plot" aria-label="Data will appear here" role="img" >
            <p class="text-secondary text-bold-700" >Data will be displayed here!</p>
        </canvas>
    </div>
</div>