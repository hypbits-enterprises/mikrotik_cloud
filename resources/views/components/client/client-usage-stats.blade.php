<div {!!$isTab == "true" ? "class='tab-pane fade' id='tab4' role='tabpanel'" : "class=''"!!} >
    <h6 class="text-center">Client Usage Statistics</h6>
    @if($isTab == "false")
        <input type="hidden" name="isTabElem" id="isTabElem" value="true">
    @endif
    <hr>
    <div class="container mx-auto">
        <div class="row">
            <div class="col-md-3 form-group">
                <label for="report_on" class="form-control-label">Report On</label>
                <select name="report_on" id="report_on" class="form-control">
                    <option value="" hidden>Select an option</option>
                    <option selected value="bandwidth">Bandwidth Usage</option>
                    <option value="data">Data Usage</option>
                </select>
            </div>
            <div class="col-md-3 form-group">
                <label for="report_type" class="form-control-label">Report Type</label>
                <select name="report_type" id="report_type" class="form-control">
                    <option value="" hidden>Select an option</option>
                    <option selected value="daily">Daily Usage Report</option>
                    <option value="weekly">Weekly Usage Report</option>
                    <option value="monthly">Monthly Usage Report</option>
                    <option value="Yearly">Yearly Usage Report</option>
                </select>
            </div>
            <div class="col-md-3 form-group">
                @php
                    $btnText = "<i class=\"ft-activity\"></i> Generate Report <span class=\"invisible\" id=\"client_report_loader\"><i class=\"fas ft-rotate-cw fa-spin\"></i></span>";
                    $otherClasses = "mt-2";
                    $btn_id = "client_usage_report_btn";
                @endphp
                <x-button :btnText="$btnText" btnType="primary" type="submit" btnSize="sm" :otherClasses="$otherClasses" :btnId="$btn_id" :readOnly="$readonly" />
            </div>
        </div>
        <hr>
        <div class="container d-none" id="report_error_holder">
        </div>
        <div class="container" id="navigate_reports">
            <div class="d-flex align-items-center align-items-stretch" style="cursor: pointer;">
                <button class="btn btn-sm btn-secondary" onclick="prevDate()" id="previous_date" type="button"><i class="ft-arrow-left"></i> Earlier</button>
                <span id="selective_data">
                    <select name="select_the_next_date" id="select_the_next_date" class="page-item border border-primary bg-white p-1">
                        <option value="" hidden="">Select Option</option>
                    </select>
                </span>
                <button class="btn btn-sm btn-secondary" onclick="nextDate()" id="next_date" type="button">Later <i class="ft-arrow-right"></i></button>
            </div>
        </div>
        <canvas id="report-charts"></canvas>
        <ul class="ct-legend"></ul>
    </div>
</div>