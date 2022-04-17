<div class="row">
    <div class="col-5">
        <div class="form-group">
            <?php
            $field_name = 'name';
            $field_lable = __("thememanager::themes.$field_name");
            $field_placeholder = $field_lable;
            $required = "required";
            $disabled = "disabled";
            ?>
            {{ html()->label($field_lable, $field_name) }} {!! fielf_required($required) !!}
            {{ html()->text($field_name)->placeholder($field_placeholder)->class('form-control')->attributes(["$required","$disabled"]) }}
        </div>
    </div>

    <div class="col">
        <div class="form-group">
            <?php
            $field_name = 'slug';
            $field_lable = __("thememanager::themes.$field_name");
            $field_placeholder = $field_lable;
            $required = "";
            $disabled = "disabled";
            ?>
            {{ html()->label($field_lable, $field_name) }} {!! fielf_required($required) !!}
            {{ html()->text($field_name)->placeholder($field_placeholder)->class('form-control')->attributes(["$required","$disabled"]) }}
        </div>
    </div>


</div>
<div class="row">
    <div class="col-12">
        <div class="form-group">
            <?php
            $field_name = 'custom_css';
            $field_lable = __("thememanager::themes.$field_name");
            $field_placeholder = $field_lable;
            $field_value = \Modules\Thememanager\Entities\SiteTheme::where('slug', 'blutek')->pluck('custom_css')->first();
            $required = "";
            ?>
            {{ html()->label($field_lable, $field_name) }} {!! fielf_required($required) !!}
                <small id="{{$field_name}}Help" class="form-text text-muted mt-1">Do not include the style tag</small>
            {{ html()->textarea($field_name)->placeholder($field_placeholder)->class('form-control d-none')->attributes(["$required", 'rows'=> 10]) }}

                <div class="col-12" style="position: relative; height: 150px;">
                    <div id="css_editor" style="height: 150px;">{{ $field_value }}</div>
                </div>


        </div>
    </div>
</div>
<div class="row">
    <div class="col-12">
        <div class="form-group">
            <?php
            $field_name = 'custom_script';
            $field_lable = __("thememanager::themes.$field_name");
            $field_placeholder = $field_lable;
            $required = "";
            $field_value = \Modules\Thememanager\Entities\SiteTheme::where('slug', 'blutek')->pluck('custom_script')->first();
            ?>
            {{ html()->label($field_lable, $field_name) }} {!! fielf_required($required) !!}
                <small id="{{$field_name}}Help" class="form-text text-muted mt-1">Do not include the script tag</small>
            {{ html()->textarea($field_name)->placeholder($field_placeholder)->class('form-control d-none')->attributes(["$required", 'rows'=> 10]) }}
                <div class="col-12" style="position: relative; height: 150px;">
                    <div id="js_editor" style="height: 150px;">{{ $field_value }}</div>
                </div>
                <small id="{{$field_name}}Help" class="form-text text-muted">Must be wrapped within script tag</small>
        </div>
    </div>
</div>
<div class="row">
    <div class="col-12">
        <div class="form-group">
            <?php
            $field_name = 'featured_image';
            $field_lable = __("thememanager::themes.$field_name");
            $field_placeholder = $field_lable;
            $required = "";
            ?>
            {!! Form::label("$field_name", "$field_lable") !!} {!! fielf_required($required) !!}
            <div class="input-group mb-3">
                {{ html()->text($field_name)->placeholder($field_placeholder)->class('form-control')->attributes(["$required", 'aria-label'=>'Image', 'aria-describedby'=>'button-image']) }}
                <div class="input-group-append">
                    <button class="btn btn-info" type="button" id="button-image"><i class="fas fa-folder-open"></i> @lang('Browse')</button>
                </div>
            </div>
        </div>
    </div>
</div>

@isset($custom_fields)
    @include ("custom.fields", $custom_fields)
@endisset

<div></div>



@push('css')
    <style type="text/css" media="screen">
        #css_editor {
            position: absolute;
            top: 0;
            right: 0;
            bottom: 0;
            left: 0;
        }
        #js_editor {
            position: absolute;
            top: 0;
            right: 0;
            bottom: 0;
            left: 0;
        }
    </style>
<!-- File Manager -->
{{--
<link rel="stylesheet" href="{{ asset('vendor/file-manager/css/file-manager.css') }}">
--}}


@endpush

@push ('js')
    {{--
    <script type="text/javascript" src="{{ asset('vendor/file-manager/js/file-manager.js') }}"></script>
    --}}
    <script src="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.8/js/select2.min.js" defer></script>
    <script src="/js/ace-builds/src-noconflict/ace.js" type="text/javascript" charset="utf-8"></script>

    <script type="text/javascript">

        $(function () {
            let c_editor,
                loadAceCss = function() {
                    c_editor = ace.edit( 'css_editor' );
                    c_editor.getSession().setUseWrapMode( true );
                    c_editor.setShowPrintMargin( false );
                    c_editor.getSession().setValue( $( '#custom_css' ).val() );
                    c_editor.getSession().setMode( "ace/mode/css" );

                };

            let j_editor,
                loadAceJs = function() {
                    j_editor = ace.edit( 'js_editor' );
                    j_editor.getSession().setUseWrapMode( true );
                    j_editor.setShowPrintMargin( false );
                    j_editor.getSession().setValue( $( '#custom_script' ).val() );
                    j_editor.getSession().setMode( "ace/mode/javascript" );

                };

            loadAceCss();
            loadAceJs();

            $('.settings_form').submit(function (e){

                $( '#custom_script' ).val( j_editor.getSession().getValue() );
                $( '#custom_css' ).val( c_editor.getSession().getValue() );

            });

            if( undefined !== $('.select2-category')) {
                $('.select2-category').select2({
                    theme: "bootstrap",
                    placeholder: '@lang("Select an option")',
                    minimumInputLength: 2,
                    allowClear: true,
                    ajax: {
                        /*url: '', route("backend.categories.index_list") */
                        dataType: 'json',
                        data: function (params) {
                            return {
                                q: $.trim(params.term)
                            };
                        },
                        processResults: function (data) {
                            return {
                                results: data
                            };
                        },
                        cache: true
                    }
                });
            }
            if( undefined !== $('.select2-tags')) {
                $('.select2-tags').select2({
                    theme: "bootstrap",
                    placeholder: '@lang("Select an option")',
                    minimumInputLength: 2,
                    allowClear: true,
                    ajax: {
                        url: '', // route("backend.tags.index_list")
                        dataType: 'json',
                        data: function (params) {
                            return {
                                q: $.trim(params.term)
                            };
                        },
                        processResults: function (data) {
                            return {
                                results: data
                            };
                        },
                        cache: true
                    }
                });
            }
            if( undefined !== $('.datetime'  ) ){
                $('.datetime').datetimepicker({
                    format: 'YYYY-MM-DD HH:mm:ss',
                    icons: {
                        time: 'far fa-clock',
                        date: 'far fa-calendar-alt',
                        up: 'fas fa-arrow-up',
                        down: 'fas fa-arrow-down',
                        previous: 'fas fa-chevron-left',
                        next: 'fas fa-chevron-right',
                        today: 'far fa-calendar-check',
                        clear: 'far fa-trash-alt',
                        close: 'fas fa-times'
                    }
                });
            }

        });
        document.addEventListener("DOMContentLoaded", function() {

            document.getElementById('button-image').addEventListener('click', (event) => {
                event.preventDefault();
                window.open('/file-manager/fm-button', 'fm', 'width=800,height=600');
            });
        });

        // set file link
        function fmSetLink($url) {
            document.getElementById('featured_image').value = $url;
        }
    </script>
@endpush
