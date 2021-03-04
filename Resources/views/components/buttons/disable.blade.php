@props(["route"=>"", "icon"=>"fas fa-stop", "title", "small"=>"", "class"=>""])

@if($route)
    <a href='{{$route}}'
       class='btn btn-danger {{($small=='true')? 'btn-sm' : ''}} {{$class}}'
       data-toggle="tooltip"
       title="{{ $title }}">
        <i class="{{$icon}}"></i>
        {{ $slot }}
    </a>
@else
    <button type="submit"
            class='btn btn-danger {{($small=='true')? 'btn-sm' : ''}} {{$class}}'
            data-toggle="tooltip"
            title="{{ $title }}">
        <i class="{{$icon}}"></i>
        {{ $slot }}
    </button>
@endif

