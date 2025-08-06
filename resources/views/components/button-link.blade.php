<a href="{{$btnLink}}" class="btn btn-{{$btnType}} btn-{{$btnSize}} {{$otherClasses}} {{$readOnly ?? ""}}" style="padding: 3px;" id="{{$btnId}}" 
@if ($toolTip != null)
    data-toggle="tooltip" title="{{$toolTip}}"
@endif
@if ($target != null)
    target="{{$target}}"
@endif
{!!$otherAttributes!!}
>
    <span class="d-inline-block border border-white w-100 text-center" style="border-radius: 2px; padding: {{$paddingSize}};">{!!$btnText!!}</span>
</a>