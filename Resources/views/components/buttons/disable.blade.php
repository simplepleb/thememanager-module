@props(["small"=>""])
<button class="btn btn-warning ml-1 {{($small=='true')? 'btn-sm' : ''}}" data-toggle="tooltip" title="{{__('Disable')}}"><i class="fas fa-stop"></i>{{ $slot }}</button>
