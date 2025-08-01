
<div class="tab-pane fade" id="tab2" role="tabpanel">
    <p class="card-text">In this table below <b>{{ ucwords(strtolower($clients_data[0]->client_name)) }}</b> Issues will be displayed.</p>
        @if (session('success'))
            <p class="success">{{ session('success') }}</p>
        @endif
    <p><span class="text-bold-600">Client Report Table:</span></p>
    <div class="row">
        <div class="col-md-6 form-group">
            <input type="text" name="search" id="searchkey_2"
                class="form-control rounded-lg p-1" placeholder="Search here ..">
        </div>
        <div class="col-md-3">

        </div>
        <div class="col-md-3">
            {{-- <a href="/Client-Reports/New" class="btn btn-purple btn-sm {{$readonly}}"><i class="ft-plus"></i> New Issue</a> --}}
        </div>
    </div>
    <div class="table-responsive" id="transDataReciever_2">
        <div class="container text-center my-2" id="logo_loaders">
            <img class=" mx-auto fa-beat-fade"  width="100" alt="Your Logo Appear Here"
                src="{{session("organization_logo") != null ? session("organization_logo") :'/theme-assets/images/logoplaceholder.svg'}}" />
        </div>
        <table class="table" id="myTable">
            <thead>
                <tr>
                    <th>#</th>
                    <th>Ticket Number</th>
                    <th>Report Title</th>
                    <th>Report Description</th>
                    <th>Reported By</th>
                    <th>Report Date</th>
                    <th>Action</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($client_issues as $key => $report)
                    <tr>
                        <th scope="row">{{$key + 1}}
                            @if ($report->status == "pending")
                                <span class="badge text-light bg-danger text-dark" data-toggle="tooltip" title="" data-original-title="Pending!">P</span>
                            @else
                                <span class="badge text-light bg-success text-dark" data-toggle="tooltip" title="" data-original-title="Resolved!">R</span>
                            @endif
                        </th>
                        <td>{{$report->report_code ?? "NULL"}}</td>
                        <td>{{$report->report_title}}</td>
                        <td data-toggle="tooltip" title="" data-original-title="{{$report->report_description}}">{{strlen($report->report_description) > 100 ? substr($report->report_description, 0, 100)."...." : $report->report_description}}</td>
                        <td>{{ucwords(strtolower($report->admin_reporter_fullname))}}</td>
                        <td>{{date("D dS M Y H:i:sA", strtotime($report->report_date))}}</td>
                        <td>
                            @php
                                $btnText = "<i class=\"ft-eye\"></i>";
                                $otherClasses = "text-bolder";
                                $btnLink = "/Client-Reports/View/".$report->report_id;
                            @endphp
                            <x-button-link :btnText="$btnText" :btnLink="$btnLink" btnType="purple" btnSize="sm" :otherClasses="$otherClasses" :readOnly="$readonly" toolTip="View this issue."/>
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</div>