@extends('layouts.app')

@section('title') {{ __($module_action) }} Themes @stop

{{--
@section('breadcrumbs')
<x-backend-breadcrumbs>
    <x-backend-breadcrumb-item type="active" icon='{{ $module_icon }}'>{{ $module_title }}</x-backend-breadcrumb-item>
</x-backend-breadcrumbs>
@stop
--}}

@section('content')
    @include('partials.header_space', [
        'title' => __('Theme Manager') ,
        'description' => __('Manage all of your clients from here'),
        'class' => 'col-lg-12'
    ])
    <div class="container-fluid mt--7">
        <div class="card">
            <div class="card-header">
                <div class="row">
                    <div class="col">
                        <h2 class="h3 mb-0">Themes <small class="text-muted">{{ __($module_action) }}</h2>
                    </div>
                    <div class="col-auto">
                        {{--<x-buttons.create route='{{ route("backend.module_builder.builder.create") }}' title="{{__('Create')}} Module" small="true"/>--}}
                        <x-buttons.refresh route='{{ route("backend.$module_name.refresh") }}' title="{{__('Refresh List')}}" icon="fas fa-sync" small="true"/>
                    </div>
                </div>
            </div>
            <div class="card-body">
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
                        <div class="card @if($theme->active) border-success @endif">
                            <img class="card-img-top" src="{{ $img_src }}" alt="Theme Name">
                            <div class="card-body">
                                <h5 class="card-title">{{ ucwords($settings->name) }}&nbsp;<small>by: @if($settings->web)<a href="{{ $settings->web }}" target="_blank">@endif {{ $settings->author }}@if($settings->web)</a> @endif</small></h5>
                                <p class="card-text">{{ $settings->description }}</p>

                                <div class="row justify-content-center align-content-center bg-primary">
                                    <div class="btn-group" role="group" aria-label="Basic example">
                                        <button type="button" data-id="{{ $theme->id }}" class="btn @if($theme->active)btn-success @else btn-primary @endif btn-sm @if($theme->active) disabled @endif activate_theme"><i class="fas fa-check-double"></i></button>
                                        <a href="{{ route('backend.thememanager.edit', [ 'thememanager'=> $settings->slug ]) }}"><button type="button" class="btn btn-primary btn-sm" title="Theme Settings"><i class="fas fa-cogs"></i></button></a>
                                        @if(\Module::has('Menumaker'))
                                            <a @if( theme_has_menus($theme->id) == false) disabled="disabled" @endif href="/admin/menumaker"><button type="button" class="btn btn-primary btn-sm" title="Menus" @if( theme_has_menus($theme->id) == false) disabled @endif><i class="fas fa-sitemap"></i></button></a>
                                        @endif
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
        @include('layouts.footers.auth')
    </div>
@stop

@push ('js')
    <!-- DataTables Core and Extensions -->
    {{--<script type="text/javascript" src="{{ asset('vendor/datatable/datatables.min.js') }}"></script>--}}

    <script type="text/javascript">
        $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            }
        });
        $(document).ready(function(){
            $(".activate_theme").on("click", function(){
                var theme_id = $(this).data("id"); //$(this).attr("data-id");
                var url = '{{ route('backend.thememanager.activate_theme') }}'
                swal({
                    title: "{{ __('Activate Theme') }}?",
                    text: "{{ __('Are you sure you want to change themes?') }}",
                    type: "warning",
                    showCancelButton: !0,
                    confirmButtonText: "{{ __('Yes, activate it') }}!",
                    cancelButtonText: "{{ __('No, cancel') }}!",
                    reverseButtons: !0
                }).then(function (e) {
                    if (e.value === true) {
                        var CSRF_TOKEN = $('meta[name="csrf-token"]').attr('content');
                        $.ajax({
                            type: 'POST',
                            url: url,
                            theme_id: theme_id,
                            data: {_token: CSRF_TOKEN, theme_id: theme_id},
                            dataType: 'JSON',
                            success: function (results) {
                                if (results.success === true) {
                                    swal("{{ __('Done') }}!", results.message, "success");
                                    location.reload();
                                } else {
                                    swal("{{ __('Error') }}!", results.message, "error");
                                }
                            }
                        });
                    } else {
                        e.dismiss;
                    }
                }, function (dismiss) {
                    return false;
                })
            });
        });



    </script>

@endpush
