@extends('layouts.admin.app')

@section('title',translate('Update_Business_Module'))

@push('css_or_js')
<link rel="stylesheet" href="{{asset('assets/admin/css/radio-image.css')}}">
@endpush

@section('content')
    <div class="content container-fluid">
        <!-- Page Header -->
        <div class="page-header">
            <h1 class="page-header-title">
                <span class="page-header-icon">
                    <img src="{{asset('assets/admin/img/module.png')}}" alt="">
                </span>
                <span>
                    {{translate('Edit_Business_Module')}}
                </span>
            </h1>
        </div>
        <!-- End Page Header -->
        <div class="card">
            <div class="card-body">
                <form action="{{route('admin.business-settings.module.update',[$module['id']])}}" method="post" enctype="multipart/form-data">
                    @method('PUT')
                    @csrf
                    @php($language=\App\Models\BusinessSetting::where('key','language')->first())
                    @php($language = $language->value ?? null)
                    @php($default_lang = str_replace('_', '-', app()->getLocale()))
                    @if($language)
                        <ul class="nav nav-tabs mb-4 border-0">
                            <li class="nav-item">
                                <a class="nav-link lang_link active"
                                href="#"
                                id="default-link">{{translate('messages.default')}}</a>
                            </li>
                            @foreach (json_decode($language) as $lang)
                                <li class="nav-item">
                                    <a class="nav-link lang_link"
                                        href="#"
                                        id="{{ $lang }}-link">{{ \App\CentralLogics\Helpers::get_language_name($lang) . '(' . strtoupper($lang) . ')' }}</a>
                                </li>
                            @endforeach
                        </ul>
                        <div class="lang_form" id="default-form">
                            <div class="form-group" >
                                <label class="input-label" for="exampleFormControlInput1">{{ translate('messages.Business_Module_name')}} ({{ translate('messages.default') }})</label>
                                <input type="text" name="module_name[]" class="form-control" maxlength="191" value="{{$module?->getRawOriginal('module_name')}}" oninvalid="document.getElementById('en-link').click()">
                            </div>
                            <div class="form-group">
                                <label class="input-label d-flex" for="module_type">{{translate('messages.description')}} ({{ translate('messages.default') }})<span class="form-label-secondary text-danger d-flex"
                                    data-toggle="tooltip" data-placement="right"
                                    data-original-title="{{ translate('messages.Write_a_short_description_of_your_new_business_module_within_100_words_(550_characters)')}}"><img
                                        src="{{ asset('/assets/admin/img/info-circle.svg') }}"
                                        alt="{{ translate('messages.veg_non_veg') }}"></span></label>
                                <textarea class="ckeditor form-control" name="description[]">{!! $module?->getRawOriginal('description') !!}</textarea>
                            </div>
                        </div>

                        <input type="hidden" name="lang[]" value="default">
                        @foreach(json_decode($language) as $lang)
                            <?php
                                if(count($module['translations'])){
                                    $translate = [];
                                    foreach($module['translations'] as $t)
                                    {
                                        if($t->locale == $lang && $t->key=="module_name"){
                                            $translate[$lang]['module_name'] = $t->value;
                                        }

                                        if($t->locale == $lang && $t->key=="description"){
                                            $translate[$lang]['description'] = $t->value;
                                        }
                                    }
                                }
                            ?>
                            <div class="d-none lang_form" id="{{$lang}}-form">
                                <div class="form-group" >
                                    <label class="input-label" for="exampleFormControlInput1">{{ translate('messages.Business_Module_name')}} ({{strtoupper($lang)}})</label>
                                    <input type="text" name="module_name[]" class="form-control" maxlength="191" value="{{$translate[$lang]['module_name']??''}}" oninvalid="document.getElementById('en-link').click()">
                                </div>
                                <div class="form-group">
                                    <label class="input-label d-flex" for="module_type">{{translate('messages.description')}} ({{strtoupper($lang)}})<span class="form-label-secondary text-danger d-flex"
                                        data-toggle="tooltip" data-placement="right"
                                        data-original-title="{{ translate('messages.Write_a_short_description_of_your_new_business_module_within_100_words_(550_characters)')}}"><img
                                            src="{{ asset('/assets/admin/img/info-circle.svg') }}"
                                            alt="{{ translate('messages.veg_non_veg') }}"></span></label>
                                    <textarea class="ckeditor form-control" name="description[]">{!! $translate[$lang]['description']??'' !!}</textarea>
                                </div>
                            </div>

                            <input type="hidden" name="lang[]" value="{{$lang}}">
                        @endforeach
                    @else
                        <div class="form-group">
                            <label class="input-label" for="exampleFormControlInput1">{{ translate('messages.Business_Module_name')}}</label>
                            <input type="text" name="module_name" class="form-control" placeholder="{{translate('messages.new_category')}}" value="{{old('name')}}" maxlength="191">
                        </div>
                        <div class="form-group">
                            <label class="input-label" for="module_type">{{translate('messages.description')}}</label>
                            <textarea class="ckeditor form-control" name="description">{!! $module->description !!}</textarea>
                        </div>
                        <input type="hidden" name="lang[]" value="default">
                    @endif
                    <div class="row mt-2">
                        <div class="col-sm-6">
                            <div class="form-group">
                                <label class="input-label" for="module_type">{{translate('messages.business_module_type')}}</label>
                                <select name="module_type" id="module_type" class="form-control text-capitalize" disabled>
                                    @foreach (config('module.module_type') as $key)
                                        <option value="{{$key}}" {{$key==$module->module_type?'selected':''}}>{{translate($key)}}</option>
                                    @endforeach
                                </select>
                                <div class="card mt-1" id="module_des_card">
                                    <div class="card-body" id="module_description">{{config('module.'.$module->module_type)['description']}}</div>
                                </div>
                            </div>
                        </div>
                        {{-- <div class="col-sm-6">
                            <div class="form-group" id="zone_check">
                                <label class="input-label">{{ translate('Store can serve in') }} <small class="text-danger"><span class="input-label-secondary"
                                        title="{{ translate('messages.business_module_all_zone_hint') }}">
                                        <img src="{{ asset('/assets/admin/img/info-circle.svg') }}"
                                            alt="{{ translate('messages.business_module_all_zone_hint') }}" class="initial--14">
                                </span> *</small></label>

                                <div class="input-group input-group-md-down-break">
                                    <!-- Custom Radio -->
                                    <div class="form-control">
                                        <div class="custom-control custom-radio">
                                            <input type="radio" class="custom-control-input" value="1"
                                                name="all_zone_service" id="all_zone_service1" {{$module->all_zone_service == 1? 'checked': ''}}>
                                            <label class="custom-control-label" for="all_zone_service1">{{ translate('messages.All_Zones') }}</label>
                                        </div>
                                    </div>
                                    <!-- End Custom Radio -->

                                    <!-- Custom Radio -->
                                    <div class="form-control">
                                        <div class="custom-control custom-radio">
                                            <input type="radio" class="custom-control-input" value="0"
                                                name="all_zone_service" id="all_zone_service2" {{$module->all_zone_service == 1? '': 'checked'}}>
                                            <label class="custom-control-label"
                                                for="all_zone_service2">{{ translate('One Zone') }}</label>
                                        </div>
                                    </div>
                                    <!-- End Custom Radio -->
                                </div>
                            </div>
                        </div> --}}
                    </div>
                    <div class="card h-100 module-logo-card mb-3">
                        <div class="card-body">
                            <div class="row h-100">
                                <div class="col-sm-6">
                                    <div class="form-group m-0 h-100 d-flex flex-column justify-content-center">
                                        <label>
                                            {{translate('messages.icon')}}
                                            <small class="text-danger">* ( {{translate('messages.ratio')}} 1:1)</small>
                                        </label>
                                        <center class="my-auto py-3">
                                            <img class="initial--15 " id="viewer" onerror="this.src='{{asset('assets/admin/img/400x400/img2.jpg')}}'" src="{{asset('storage/app/public/module/'.$module['icon'])}}" alt="image" />
                                        </center>
                                        <div class="custom-file">
                                            <input type="file" name="icon" id="customFileEg1" class="custom-file-input" accept=".jpg, .png, .jpeg, .gif, .bmp, .tif, .tiff|image/*">
                                            <label class="custom-file-label" for="customFileEg1">{{translate('messages.choose_file')}}</label>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-sm-6">
                                    <div class="form-group m-0 h-100 d-flex flex-column justify-content-center">
                                        <label>
                                            {{translate('messages.thumbnail')}}
                                            <small class="text-danger">* ( {{translate('messages.ratio')}} 1:1)</small>
                                        </label>
                                        <center class="my-auto py-3">
                                            <img class="initial--15 " id="viewer2" onerror="this.src='{{asset('assets/admin/img/400x400/img2.jpg')}}'" src="{{asset('storage/app/public/module/'.$module['thumbnail'])}}" alt="image" />
                                        </center>
                                        <div class="custom-file">
                                            <input type="file" name="thumbnail" id="customFileEg2" class="custom-file-input" accept=".jpg, .png, .jpeg, .gif, .bmp, .tif, .tiff|image/*">
                                            <label class="custom-file-label" for="customFileEg2">{{translate('messages.choose_file')}}</label>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                <div class="btn--container justify-content-end">
                    <button type="reset" id="reset_btn" class="btn btn--reset">{{translate('messages.reset')}}</button>
                    <button type="submit" class="btn btn--primary">{{translate('messages.Save_changes')}}</button>
                </div>
                </form>
            </div>
            <!-- End Table -->
        </div>
    </div>

@endsection

@push('script_2')
    <script>
        function modulChange(id)
        {
            $.get({
                url: "{{url('/')}}/admin/module/type/?module_type="+id,
                dataType: 'json',
                success: function (data) {
                    if(data.data.description.length)
                    {
                        $('#module_des_card').show();
                        $('#module_description').html(data.data.description);
                    }
                    else
                    {
                        $('#module_des_card').hide();
                    }
                    if(id=='parcel')
                    {
                        $('#module_theme').hide();

                    }
                },
            });
        }

        function readURL(input, id) {
            if (input.files && input.files[0]) {
                var reader = new FileReader();

                reader.onload = function (e) {
                    $('#'+id).attr('src', e.target.result);
                }

                reader.readAsDataURL(input.files[0]);
            }
        }

        $("#customFileEg1").change(function () {
            readURL(this,'viewer');
        });

        $("#customFileEg2").change(function () {
            readURL(this,'viewer2');
        });
    </script>
    <script>
        $(".lang_link").click(function(e){
            e.preventDefault();
            $(".lang_link").removeClass('active');
            $(".lang_form").addClass('d-none');
            $(this).addClass('active');

            let form_id = this.id;
            let lang = form_id.substring(0, form_id.length - 5);
            console.log(lang);
            $("#"+lang+"-form").removeClass('d-none');
            if(lang == '{{$default_lang}}')
            {
                $(".from_part_2").removeClass('d-none');
            }
            else
            {
                $(".from_part_2").addClass('d-none');
            }
        });
    </script>
    <script src="//cdn.ckeditor.com/4.14.1/standard/ckeditor.js"></script>
    <script type="text/javascript">
        $(document).ready(function () {
            @if ($module->module_type=='parcel')
                $('#module_des_card').hide();
                $('#module_theme').hide();
                $('#zone_check').hide();
            @endif
            $('.ckeditor').ckeditor();
        });
    </script>
    <script>
        $('#reset_btn').click(function(){
            $('#viewer').attr('src','{{asset('storage/app/public/module/'.$module['icon'])}}');
            $('#viewer2').attr('src','{{asset('storage/app/public/module/'.$module['thumbnail'])}}');
        })
</script>
@endpush
