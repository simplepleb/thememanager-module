@extends('layouts.app', [
    'title' => __('Theme Manager'),
    'parentSection' => 'app-settings',
    'elementName' => 'theme-manager'
])

@section('title') {{ __($module_action) }} {{ $module_title }} @endsection

{{--@section('breadcrumbs')
<x-backend-breadcrumbs>
    <x-backend-breadcrumb-item route='{{route("backend.$module_name.index")}}' icon='{{ $module_icon }}' >
        {{ $module_title }}
    </x-backend-breadcrumb-item>
    <x-backend-breadcrumb-item type="active">{{ __($module_action) }}</x-backend-breadcrumb-item>
</x-backend-breadcrumbs>
@endsection--}}

@section('content')
    @component('layouts.headers.auth')
        @component('layouts.headers.breadcrumbs')
            @slot('title')
                {{ __('Theme Management') }}
            @endslot

            <li class="breadcrumb-item"><a href="{{ route('backend.thememanager.index') }}">{{ __('Theme Management') }}</a></li>
            {{--<li class="breadcrumb-item active" aria-current="page">{{ __('List') }}</li>--}}
        @endcomponent
    @endcomponent

    <div class="container-fluid mt--6">
        <div class="col">
            <div class="card">
                <div class="card-header">
                    <div class="row">
                        <div class="col">
                            <h2 class="h3 mb-0">{{__('Edit Theme')}}</h2>
                        </div>
                    </div>
                </div>
                <div class="card-body">
                    <div class="row ">
                        <div class="col">
                            {{ html()->modelForm($$module_name_singular, 'PATCH', route("backend.$module_name.update", $$module_name_singular))->class('form settings_form')->open() }}

                            @include ("thememanager::backend.settings_form")

                            <div class="row">
                                <div class="col-4">
                                    <div class="form-group">
                                        {{ html()->submit($text = icon('fas fa-save')." Save")->class('btn btn-success') }}
                                    </div>
                                </div>

                                <div class="col-8">
                                    <div class="float-right">
                                        {{--@can('delete_'.$module_name)
                                            <a href="{{route("$module_name.destroy", $$module_name_singular)}}" class="btn btn-danger" data-method="DELETE" data-token="{{csrf_token()}}" data-toggle="tooltip" title="{{__('labels.backend.delete')}}"><i class="fas fa-trash-alt"></i></a>
                                        @endcan--}}
                                        <a href="{{ route("backend.$module_name.index") }}" class="btn btn-warning" data-toggle="tooltip" title="{{__('labels.backend.cancel')}}"><i class="fas fa-reply"></i> Cancel</a>
                                    </div>
                                </div>
                            </div>

                            {{ html()->form()->close() }}

                        </div>
                    </div>
                </div>

                <div class="card-footer">
                    <div class="row">
                        <div class="col">
                            <small class="float-right text-muted">
                                Updated: {{$$module_name_singular->updated_at->diffForHumans()}},
                                Created at: {{$$module_name_singular->created_at->isoFormat('LLLL')}}
                            </small>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        @include('layouts.footers.auth')
    </div>


@stop
