@extends('backend.layouts.app')

@section('title') {{ __($module_action) }} Themes @stop

@section('breadcrumbs')
<x-backend-breadcrumbs>
    <x-backend-breadcrumb-item type="active" icon='{{ $module_icon }}'>{{ $module_title }}</x-backend-breadcrumb-item>
</x-backend-breadcrumbs>
@stop

@section('content')
<div class="card">
    <div class="card-body">
        <div class="row">
            <div class="col-8">
                <h4 class="card-title mb-0">
                    <i class="{{ $module_icon }}"></i> Themes <small class="text-muted">{{ __($module_action) }}</small>
                </h4>
                <div class="small text-muted">
                    @lang(":module_name Management Dashboard", ['module_name'=>Str::title('Themes')])
                </div>
            </div>
            <!--/.col-->
            <div class="col-4">
                <div class="float-right">
                    <x-buttons.create route='{{ route("backend.$module_name.create") }}' title="{{__('Create')}} {{ ucwords(Str::singular($module_name)) }}"/>
                    <x-buttons.disable route='{{ route("backend.$module_name.disable") }}' title="{{__('Disable')}}"/>
                    <x-buttons.refresh route='{{ route("backend.$module_name.refresh") }}' title="{{__('Refresh List')}}"/>
                </div>
            </div>
            <!--/.col-->
        </div>
        <!--/.row-->

        <div class="row mt-4">
            @php $cnt = 0; @endphp
            @foreach($themes as $theme)
                @php
                    $img_src = null;
                        $settings = json_decode($theme->settings);
                        $img_file = base_path('public/themes/'.$settings->slug.'/screenshot.jpeg');
                        $img_src = file_exists($img_file) ? '/themes/'.$settings->slug.'/screenshot.jpeg' : 'https://via.placeholder.com/1170x780';
                @endphp
            <div class="col-4">
                <div class="card">
                    <img class="card-img-top" src="{{ $img_src }}" alt="Theme Name">
                    <div class="card-body">
                        <h5 class="card-title">{{ ucwords($settings->name) }}&nbsp;<small>by: @if($settings->web)<a href="{{ $settings->web }}" target="_blank">@endif {{ $settings->author }}@if($settings->web)</a> @endif</small></h5>
                        <p class="card-text">{{ $settings->description }}</p>
                        <div class="row">
                            <div class="col">
                                <a href="#" class="btn btn-primary @if($theme->active) disabled @endif"><i class="fas fa-check-double"></i> &nbsp;Make Active</a>
                            </div>
                            <div class="col">
                                <a href="/admin/thememanager/{{ $settings->slug }}/edit" class="btn btn-primary">Settings &nbsp;<i class="fas fa-user-cog"></i></a>
                            </div>
                        </div>


                    </div>
                </div>
            </div>
                @php($cnt++)
                @if($cnt == 3)@php( $cnt = 0)</div><div class="row mt-4"> @endif
            @endforeach
        </div>
    </div>
    <div class="card-footer">
        <div class="row">
            <div class="col-7">
                <div class="float-left">
                    Total {{ $$module_name->total() }} Themes
                </div>
            </div>
            <div class="col-5">
                <div class="float-right">
                    {!! $$module_name->render() !!}
                </div>
            </div>
        </div>
    </div>
</div>
@stop
