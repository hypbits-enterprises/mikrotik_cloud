<button type="{{$type}}"
@if ($toolTip != null)
    data-toggle="tooltip" title="{{$toolTip}}"
@endif
 {{$disabled}} class="btn btn-{{$btnType}} btn-{{$btnSize}} {{$otherClasses}}" {!!$otherAttributes!!} style="padding: 3px;" id="{{$btnId}}"><span class="d-inline-block border border-white w-100 " {{$readOnly ?? ""}} style="border-radius: 2px; padding: {{$paddingSize}};">{!!$btnText!!}</span></button>