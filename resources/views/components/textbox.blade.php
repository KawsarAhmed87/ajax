<div class="form-group {{$col ?? ''}} {{$required ?? ''}}">
	<label>{{$labelName}}</label>
	<input type="{{$type ?? 'text'}}" name="{{$name}}" id="{{$name}}" class="form-control {{$name ?? ''}}" placeholder="{{$placeholder ?? ''}}">
</div>