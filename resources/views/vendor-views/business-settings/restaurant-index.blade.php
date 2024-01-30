@extends('layouts.vendor.app')

@section('title',translate('messages.settings'))

@push('css_or_js')
<link href="{{asset('assets/admin/css/croppie.css')}}" rel="stylesheet">
@endpush

@section('content')
    <div class="content container-fluid config-inline-remove-class">
        <!-- Page Heading -->
        <div class="page-header">
            <h1 class="page-header-title">
                <span class="page-header-icon">
                    <img src="{{asset('assets/admin/img/config.png')}}" class="w--30" alt="">
                </span>
                <span>
                    {{translate('messages.store_setup')}}
                </span>
            </h1>
        </div>
        <!-- Page Heading -->
        <div class="card mb-3">
            <div class="card-body py-3">
                <div class="d-flex flex-row justify-content-between align-items-center">
                    <h4 class="card-title align-items-center d-flex">
                        <img src="{{asset('assets/admin/img/store.png')}}" class="w--20 mr-1" alt="">
                        <span>{{translate('messages.store_temporarily_closed_title')}}</span>
                    </h4>
                    <label class="switch toggle-switch-lg m-0">
                        <input type="checkbox" class="toggle-switch-input" onclick="restaurant_open_status(this)"
                            {{$store->active ?'':'checked'}}>
                        <span class="toggle-switch-label">
                            <span class="toggle-switch-indicator"></span>
                        </span>
                    </label>
                </div>
            </div>
        </div>
        <div class="card mb-3">
            <div class="card-header">
                <h5 class="card-title">
                    <span class="card-header-icon">
                        <i class="tio-settings-outlined"></i>
                    </span>
                    <span>
                        {{translate('messages.store_settings')}}
                    </span>
                </h5>
            </div>
            <div class="card-body">
                <div class="row g-3">
                    <div class="col-xl-4 col-lg-4 col-md-6 col-sm-6 col-12">
                        <div class="form-group mb-0">
                            <label class="toggle-switch toggle-switch-sm d-flex justify-content-between border border-secondary rounded px-4 form-control" for="schedule_order">
                                <span class="pr-2">{{translate('messages.scheduled_order')}}<span class="input-label-secondary" data-toggle="tooltip" data-placement="right" data-original-title="{{translate('When_enabled,_store_owner_can_take_scheduled_orders_from_customers.')}}"><img src="{{asset('/assets/admin/img/info-circle.svg')}}" alt="{{translate('messages.scheduled_order_hint')}}"></span></span>
                                <input type="checkbox" class="toggle-switch-input" onclick="location.href='{{route('vendor.business-settings.toggle-settings',[$store->id,$store->schedule_order?0:1, 'schedule_order'])}}'" id="schedule_order" {{$store->schedule_order?'checked':''}}>
                                <span class="toggle-switch-label">
                                    <span class="toggle-switch-indicator"></span>
                                </span>
                            </label>
                        </div>
                    </div>
                    <div class="col-xl-4 col-lg-4 col-md-6 col-sm-6 col-12">
                        <div class="form-group mb-0">
                            <label class="toggle-switch toggle-switch-sm d-flex justify-content-between border border-secondary rounded px-4 form-control" for="delivery">
                                <span class="pr-2">{{translate('messages.delivery')}}<span class="input-label-secondary" data-toggle="tooltip" data-placement="right" data-original-title="{{translate('When_enabled,_customers_can_make_home_delivery_orders_from_this_store.')}}"><img src="{{asset('/assets/admin/img/info-circle.svg')}}" alt="{{translate('messages.home_delivery_hint')}}"></span></span>
                                <input type="checkbox" name="delivery" class="toggle-switch-input" onclick="location.href='{{route('vendor.business-settings.toggle-settings',[$store->id,$store->delivery?0:1, 'delivery'])}}'" id="delivery" {{$store->delivery?'checked':''}}>
                                <span class="toggle-switch-label">
                                    <span class="toggle-switch-indicator"></span>
                                </span>
                            </label>
                        </div>
                    </div>
                    <div class="col-xl-4 col-lg-4 col-md-6 col-sm-6 col-12">
                        <div class="form-group mb-0">
                            <label class="toggle-switch toggle-switch-sm d-flex justify-content-between border border-secondary rounded px-4 form-control" for="take_away">
                                <span class="pr-2 text-capitalize">{{translate('messages.take_away')}}<span class="input-label-secondary" data-toggle="tooltip" data-placement="right" data-original-title="{{translate('When_enabled,_customers_can_place_takeaway_orders_from_this_store.')}}"><img src="{{asset('/assets/admin/img/info-circle.svg')}}" alt="{{translate('messages.take_away_hint')}}"></span></span>
                                <input type="checkbox" class="toggle-switch-input" onclick="location.href='{{route('vendor.business-settings.toggle-settings',[$store->id,$store->take_away?0:1, 'take_away'])}}'" id="take_away" {{$store->take_away?'checked':''}}>
                                <span class="toggle-switch-label">
                                    <span class="toggle-switch-indicator"></span>
                                </span>
                            </label>
                        </div>
                    </div>

                    @if ($store->module->module_type == 'pharmacy')
                    @php($prescription_order_status = \App\Models\BusinessSetting::where('key', 'prescription_order_status')->first())
                    @php($prescription_order_status = $prescription_order_status ? $prescription_order_status->value : 0)
                        @if ($prescription_order_status)
                            <div class="col-xl-4 col-lg-4 col-md-6 col-sm-6 col-12">
                                <div class="form-group mb-0">
                                    <label class="toggle-switch toggle-switch-sm d-flex justify-content-between border border-secondary rounded px-4 form-control" for="prescription_order">
                                        <span class="pr-2 text-capitalize">{{translate('messages.prescription_order')}}:</span>
                                        <input type="checkbox" class="toggle-switch-input" onclick="location.href='{{route('vendor.business-settings.toggle-settings',[$store->id,$store->prescription_order?0:1, 'prescription_order'])}}'" id="prescription_order" {{$store->prescription_order?'checked':''}}>
                                        <span class="toggle-switch-label">
                                            <span class="toggle-switch-indicator"></span>
                                        </span>
                                    </label>
                                </div>
                            </div>
                        @endif
                    @endif
                    @if ($store->self_delivery_system == 1)
                    <div class="col-lg-4 col-sm-6">
                        <div class="form-group m-0">
                            <label class="toggle-switch toggle-switch-sm d-flex justify-content-between border rounded px-3 form-control" for="free_delivery">
                                <span class="pr-2">
                                    {{translate('messages.free_delivery')}}
                                    <span data-toggle="tooltip" data-placement="right" data-original-title="{{translate('If this option is on, customers will get free delivery')}}" class="input-label-secondary"><img src="{{asset('assets/admin/img/info-circle.svg')}}" alt="i"></span>
                                </span>
                                <input type="checkbox" name="free_delivery" class="toggle-switch-input" onclick="location.href='{{route('vendor.business-settings.toggle-settings',[$store->id,$store->free_delivery?0:1, 'free_delivery'])}}'" id="free_delivery" {{$store->free_delivery?'checked':''}}>
                                <span class="toggle-switch-label">
                                    <span class="toggle-switch-indicator"></span>
                                </span>
                            </label>
                        </div>
                    </div>
                    @endif
                    @if ($toggle_veg_non_veg && config('module.'.$store->module->module_type)['veg_non_veg'])
                    <div class="col-xl-4 col-lg-4 col-md-6 col-sm-6 col-12">
                        <div class="form-group mb-0">
                            <label class="toggle-switch toggle-switch-sm d-flex justify-content-between border border-secondary rounded px-4 form-control" for="veg">
                                <span class="pr-2 text-capitalize">{{translate('messages.veg')}}</span>
                                <input type="checkbox" class="toggle-switch-input" onclick="location.href='{{route('vendor.business-settings.toggle-settings',[$store->id,$store->veg?0:1, 'veg'])}}'" id="veg" {{$store->veg?'checked':''}}>
                                <span class="toggle-switch-label">
                                    <span class="toggle-switch-indicator"></span>
                                </span>
                            </label>
                        </div>
                    </div>

                    <div class="col-xl-4 col-lg-4 col-md-6 col-sm-6 col-12">
                        <div class="form-group mb-0">
                            <label class="toggle-switch toggle-switch-sm d-flex justify-content-between border border-secondary rounded px-4 form-control" for="non_veg">
                                <span class="pr-2 text-capitalize">{{translate('messages.non_veg')}}</span>
                                <input type="checkbox" class="toggle-switch-input" onclick="location.href='{{route('vendor.business-settings.toggle-settings',[$store->id,$store->non_veg?0:1, 'non_veg'])}}'" id="non_veg" {{$store->non_veg?'checked':''}}>
                                <span class="toggle-switch-label">
                                    <span class="toggle-switch-indicator"></span>
                                </span>
                            </label>
                        </div>
                    </div>
                    @endif
                    @if (config('module.'.$store->module->module_type)['cutlery'])
                    <div class="col-xl-4 col-lg-4 col-md-6 col-sm-6 col-12">
                        <div class="form-group mb-0">
                            <label class="toggle-switch toggle-switch-sm d-flex justify-content-between border border-secondary rounded px-4 form-control" for="cutlery">
                                <span class="pr-2 text-capitalize">{{translate('messages.cutlery')}}</span>
                                <input type="checkbox" class="toggle-switch-input" onclick="location.href='{{route('vendor.business-settings.toggle-settings',[$store->id,$store->cutlery?0:1, 'cutlery'])}}'" id="cutlery" {{$store->cutlery?'checked':''}}>
                                <span class="toggle-switch-label">
                                    <span class="toggle-switch-indicator"></span>
                                </span>
                            </label>
                        </div>
                    </div>
                    @endif
                </div>
            </div>
        </div>
        <div class="card mb-3">
            <div class="card-header">
                <h5 class="card-title">
                    <span class="card-header-icon">
                        <i class="tio-settings-outlined"></i>
                    </span>
                    <span>
                        {{translate('messages.basic_settings')}}
                    </span>
                </h5>
            </div>
            <div class="card-body">
                <form action="{{route('vendor.business-settings.update-setup',[$store['id']])}}" method="post"
                    enctype="multipart/form-data">
                    @csrf
                    <div class="row">
                        <div class="form-group mb-0 col-md-4">
                            <label class="input-label text-capitalize" for="title">{{translate('messages.minimum_order_amount')}}<span class="input-label-secondary" data-toggle="tooltip" data-placement="right" data-original-title="{{translate('Specify_the_minimum_order_amount_required_for_customers_when_ordering_from_this_store.')}}"><img src="{{asset('/assets/admin/img/info-circle.svg')}}" alt="{{translate('messages.self_delivery_hint')}}"></span></label>
                            <input type="number" name="minimum_order" step="0.01" min="0" max="100000" class="form-control" placeholder="100" value="{{$store->minimum_order>0?$store->minimum_order :''}}">
                        </div>
                        @if (config('module.'.$store->module->module_type)['order_place_to_schedule_interval'])
                        <div class="form-group mb-0 col-md-4">
                            <label class="input-label text-capitalize" for="maximum_delivery_time">{{translate('messages.minimum_processing_time')}}<span class="form-label-secondary" data-toggle="tooltip" data-placement="right"
            data-original-title="{{translate('messages.minimum_processing_time_warning')}}"><img src="{{asset('/assets/admin/img/info-circle.svg')}}" alt="{{translate('messages.minimum_processing_time_warning')}}"></span></label>
                            <input type="text" name="order_place_to_schedule_interval" class="form-control" value="{{$store->order_place_to_schedule_interval}}">
                        </div>
                        @endif
                        <div class="form-group mb-0 col-md-4">
                            <label class="input-label text-capitalize" for="maximum_delivery_time">{{translate('messages.approx_delivery_time')}}<span class="input-label-secondary" data-toggle="tooltip" data-placement="right" data-original-title="{{translate('Set_the_total_time_to_deliver_products.')}}"><img src="{{asset('/assets/admin/img/info-circle.svg')}}" alt="{{translate('Set_the_total_time_to_deliver_products.')}}"></span></label>
                            <div class="input-group">
                                <input type="number" name="minimum_delivery_time" class="form-control" placeholder="Min: 10" value="{{explode('-',$store->delivery_time)[0]}}" title="{{translate('messages.minimum_delivery_time')}}">
                                <input type="number" name="maximum_delivery_time" class="form-control" placeholder="Max: 20" value="{{explode(' ',explode('-',$store->delivery_time)[1])[0]}}" title="{{translate('messages.maximum_delivery_time')}}">
                                <select name="delivery_time_type" class="form-control text-capitalize" id="" required>
                                    <option value="min" {{explode(' ',explode('-',$store->delivery_time)[1])[1]=='min'?'selected':''}}>{{translate('messages.minutes')}}</option>
                                    <option value="hours" {{explode(' ',explode('-',$store->delivery_time)[1])[1]=='hours'?'selected':''}}>{{translate('messages.hours')}}</option>
                                    <option value="days" {{explode(' ',explode('-',$store->delivery_time)[1])[1]=='days'?'selected':''}}>{{translate('messages.days')}}</option>
                                </select>
                            </div>
                        </div>
                        {{-- @if($store->self_delivery_system)
                        <div class="col-sm-{{$store->self_delivery_system?'4':'6'}}">
                            <div class="form-group mb-0">
                                <label class="input-label text-capitalize" for="title">{{translate('messages.delivery_charge')}}</label>
                                <input type="number" name="delivery_charge" step="0.01" min="0" max="100000" class="form-control" placeholder="100" value="{{$store->delivery_charge??'0'}}">
                            </div>
                        </div>
                        @endif --}}
                        @if($store->self_delivery_system)
                        <div class="col-sm-{{$store->self_delivery_system?'4':'6'}} col-12">
                            <div class="form-group">
                                <label class="input-label text-capitalize" for="minimum_shipping_charge">{{translate('messages.minimum_shipping_charge')}} ({{\App\CentralLogics\Helpers::currency_symbol()}})
                                </label>
                                <input type="number" id="minimum_shipping_charge" min="0" max="99999999.99" step="0.01" name="minimum_delivery_charge" class="form-control shipping_input" value="{{isset($store->minimum_shipping_charge) ? $store->minimum_shipping_charge : ''}}">
                            </div>
                        </div>

                        <div class="col-sm-{{$store->self_delivery_system?'4':'6'}} col-12">
                            <div class="form-group mt-3">
                                <label class="input-label text-capitalize" for="title">{{translate('messages.delivery_charge_per_km')}} ({{\App\CentralLogics\Helpers::currency_symbol()}})</label>
                                <input type="number" name="per_km_delivery_charge" step="0.01" min="0" max="100000" class="form-control" placeholder="100" value="{{$store->per_km_shipping_charge??'0'}}">
                            </div>
                        </div>
                        <div class="col-sm-{{$store->self_delivery_system?'4':'6'}} col-12">
                            <div class="form-group mt-3">
                                <label class="input-label text-capitalize" for="title">{{translate('messages.maximum_delivery_charge')}} ({{\App\CentralLogics\Helpers::currency_symbol()}})
                                    <span data-toggle="tooltip" data-placement="right" data-original-title="{{translate('It will add a limite on total delivery charge.') }}"
                                    class="input-label-secondary"><img
                                        src="{{ asset('/assets/admin/img/info-circle.svg') }}"
                                        alt="{{ translate('messages.maximum_delivery_charge') }}"></span>
                                </label>
                                <input type="number" name="maximum_shipping_charge" step="0.01" min="0" max="999999999" class="form-control" placeholder="10000" value="{{$store->maximum_shipping_charge??''}}">
                            </div>
                        </div>
                        @endif

                        <div class="col-sm-{{$store->self_delivery_system?'4':'6'}}">
                            <div class="form-group mb-0 p-2">
                                <label class="d-flex justify-content-between switch toggle-switch-sm text-dark" for="gst_status">
                                    <span>{{translate('messages.gst')}} <span class="form-label-secondary" data-toggle="tooltip" data-placement="right"
            data-original-title="{{translate('messages.gst_status_warning')}}"><img src="{{asset('/assets/admin/img/info-circle.svg')}}" alt="{{translate('messages.gst_status_warning')}}"></span></span>
                                    <input type="checkbox" class="toggle-switch-input" name="gst_status" id="gst_status" value="1" {{$store->gst_status?'checked':''}}>
                                    <span class="toggle-switch-label">
                                        <span class="toggle-switch-indicator"></span>
                                    </span>
                                </label>
                                <input type="text" id="gst" name="gst" class="form-control" value="{{$store->gst_code}}" {{isset($store->gst_status)?'':'readonly'}}>
                            </div>
                        </div>
                        <div class="col-12">
                            <div class="btn--container mt-3 justify-content-end">
                                <button type="reset" class="btn btn--reset">{{translate('messages.reset')}}</button>
                                <button type="submit" class="btn btn--primary">{{translate('messages.update')}}</button>
                            </div>
                        </div>
                    </div>
                </form>
            </div>
        </div>
        <div class="card mb-3">
            <div class="card-header">
                <h5 class="card-title">
                    <span class="card-header-icon">
                        <img class="w--22" src="{{asset('assets/admin/img/store.png')}}" alt="">
                    </span>
                    <span class="p-md-1"> {{translate('messages.store_meta_data')}}</span>
                </h5>
            </div>
            @php($language=\App\Models\BusinessSetting::where('key','language')->first())
            @php($language = $language->value ?? null)
            @php($default_lang = 'en')
            <div class="card-body">
                <form action="{{route('vendor.business-settings.update-meta-data',[$store['id']])}}" method="post"
                enctype="multipart/form-data" class="col-12">
                @csrf
                    <div class="row g-2">
                        <div class="col-lg-6">
                            <div class="card shadow--card-2">
                                <div class="card-body">
                                    @if($language)
                                    <ul class="nav nav-tabs mb-4">
                                        <li class="nav-item">
                                            <a class="nav-link lang_link active"
                                            href="#"
                                            id="default-link">{{ translate('Default') }}</a>
                                        </li>
                                        @foreach (json_decode($language) as $lang)
                                            <li class="nav-item">
                                                <a class="nav-link lang_link"
                                                    href="#"
                                                    id="{{ $lang }}-link">{{ \App\CentralLogics\Helpers::get_language_name($lang) . '(' . strtoupper($lang) . ')' }}</a>
                                            </li>
                                        @endforeach
                                    </ul>
                                    @endif
                                    @if ($language)
                                    <div class="lang_form"
                                    id="default-form">
                                        <div class="form-group">
                                            <label class="input-label"
                                                for="default_title">{{ translate('messages.meta_title') }}
                                                ({{ translate('messages.Default') }})
                                            </label>
                                            <input type="text" name="meta_title[]" id="default_title"
                                                class="form-control" placeholder="{{ translate('messages.meta_title') }}" value="{{$store->getRawOriginal('meta_title')}}"

                                                oninvalid="document.getElementById('en-link').click()">
                                        </div>
                                        <input type="hidden" name="lang[]" value="default">
                                        <div class="form-group mb-0">
                                            <label class="input-label"
                                                for="exampleFormControlInput1">{{ translate('messages.meta_description') }} ({{ translate('messages.default') }})</label>
                                            <textarea type="text" name="meta_description[]" placeholder="{{translate('messages.meta_description')}}" class="form-control min-h-90px ckeditor">{{$store->getRawOriginal('meta_description')}}</textarea>
                                        </div>
                                    </div>
                                        @foreach (json_decode($language) as $lang)
                                        <?php
                                            if(count($store['translations'])){
                                                $translate = [];
                                                foreach($store['translations'] as $t)
                                                {
                                                    if($t->locale == $lang && $t->key=="meta_title"){
                                                        $translate[$lang]['meta_title'] = $t->value;
                                                    }
                                                    if($t->locale == $lang && $t->key=="meta_description"){
                                                        $translate[$lang]['meta_description'] = $t->value;
                                                    }
                                                }
                                            }
                                        ?>
                                            <div class="d-none lang_form"
                                                id="{{ $lang }}-form">
                                                <div class="form-group">
                                                    <label class="input-label"
                                                        for="{{ $lang }}_title">{{ translate('messages.meta_title') }}
                                                        ({{ strtoupper($lang) }})
                                                    </label>
                                                    <input type="text" name="meta_title[]" id="{{ $lang }}_title"
                                                        class="form-control" value="{{ $translate[$lang]['meta_title']??'' }}" placeholder="{{ translate('messages.meta_title') }}"
                                                        oninvalid="document.getElementById('en-link').click()">
                                                </div>
                                                <input type="hidden" name="lang[]" value="{{ $lang }}">
                                                <div class="form-group mb-0">
                                                    <label class="input-label"
                                                        for="exampleFormControlInput1">{{ translate('messages.meta_description') }} ({{ strtoupper($lang) }})</label>
                                                    <textarea type="text" name="meta_description[]" placeholder="{{translate('messages.meta_description')}}" class="form-control min-h-90px ckeditor">{{ $translate[$lang]['meta_description']??'' }}</textarea>
                                                </div>
                                            </div>
                                        @endforeach
                                    @else
                                        <div id="default-form">
                                            <div class="form-group">
                                                <label class="input-label"
                                                    for="exampleFormControlInput1">{{ translate('messages.meta_title') }} ({{ translate('messages.default') }})</label>
                                                <input type="text" name="meta_title[]" class="form-control"
                                                    placeholder="{{ translate('messages.meta_title') }}" >
                                            </div>
                                            <input type="hidden" name="lang[]" value="default">
                                            <div class="form-group mb-0">
                                                <label class="input-label"
                                                    for="exampleFormControlInput1">{{ translate('messages.meta_description') }}
                                                </label>
                                                <textarea type="text" name="meta_description[]" placeholder="{{translate('messages.meta_description')}}" class="form-control min-h-90px ckeditor"></textarea>
                                            </div>
                                        </div>
                                    @endif
                                </div>
                            </div>
                        </div>
                        <div class="col-lg-6">
                            <div class="card shadow--card-2">
                                <div class="card-header">
                                    <h5 class="card-title">
                                        <span class="card-header-icon mr-1"><i class="tio-dashboard"></i></span>
                                        <span>{{translate('store_meta_image')}}</span>
                                    </h5>
                                </div>
                                <div class="card-body">
                                    <div class="d-flex flex-wrap flex-sm-nowrap __gap-12px">
                                        <label class="__custom-upload-img mr-lg-5">
                                            <label class="form-label">
                                                {{ translate('meta_image') }} <span class="text--primary">({{ translate('1:1') }})</span>
                                            </label>
                                            <center>
                                                <img class="img--110 min-height-170px min-width-170px" id="viewer"
                                                    onerror="this.src='{{ asset('assets/admin/img/upload.png') }}'"
                                                    src="{{asset('storage/app/public/store').'/'.$store->meta_image}}" alt="{{$store->name}}"
                                                    alt="{{ translate('meta_image') }}" />
                                            </center>
                                            <input type="file" name="meta_image" id="customFileEg1" class="custom-file-input"
                                                accept=".jpg, .png, .jpeg, .gif, .bmp, .tif, .tiff|image/*">
                                        </label>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-12">
                            <div class="justify-content-end btn--container">
                                <button type="submit" class="btn btn--primary">{{translate('save_changes')}}</button>
                            </div>
                        </div>
                    </div>
                </form>
            </div>
        </div>
        @if (!config('module.'.$store->module->module_type)['always_open'])
        <div class="card mt-3">
            <div class="card-header">
                <h5 class="card-title">
                    <span class="card-header-icon">
                        <i class="tio-date-range"></i>
                    </span>
                    <span>
                        {{translate('messages.Daily time schedule')}}
                    </span>
                </h5>
            </div>
            <div class="card-body" id="schedule">
                @include('vendor-views.business-settings.partials._schedule', $store)
            </div>
        </div>
        @endif
    </div>

    <!-- Create schedule modal -->

    <div class="modal fade" id="exampleModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="exampleModalLabel">{{translate('messages.Create Schedule For ')}}</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <form method="POST" action="javascript:" method="post" id="add-schedule">
                        @csrf
                        <input type="hidden" name="day" id="day_id_input">
                        <div class="form-group">
                            <label for="recipient-name" class="col-form-label">{{translate('messages.Start time')}}:</label>
                            <input type="time" class="form-control" name="start_time" required>
                        </div>
                        <div class="form-group">
                            <label for="message-text" class="col-form-label">{{translate('messages.End time')}}:</label>
                            <input type="time" class="form-control" name="end_time" required>
                        </div>
                        <div class="btn--container justify-content-end">
                            <button type="reset" class="btn btn--reset">{{translate('messages.reset')}}</button>
                            <button type="submit" class="btn btn--primary">{{translate('messages.Submit')}}</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('script_2')
    <script>
        function restaurant_open_status(e) {
            Swal.fire({
                title: '{{translate('messages.are_you_sure')}}',
                text: '{{$store->active ? translate('messages.you_want_to_temporarily_close_this_store') : translate('messages.you_want_to_open_this_store') }}',
                type: 'warning',
                showCancelButton: true,
                cancelButtonColor: 'default',
                confirmButtonColor: '#00868F',
                cancelButtonText: '{{translate('messages.no')}}',
                confirmButtonText: '{{translate('messages.yes')}}',
                reverseButtons: true
            }).then((result) => {
                if (result.value) {
                    $.get({
                        url: '{{route('vendor.business-settings.update-active-status')}}',
                        contentType: false,
                        processData: false,
                        beforeSend: function () {
                            $('#loading').show();
                        },
                        success: function (data) {
                            toastr.success(data.message);
                        },
                        complete: function () {
                            $('#loading').hide();
                            location.reload();
                        },
                    });
                } else {
                    e.checked = !e.checked;
                }
            })
        };

        function delete_schedule(route) {
            Swal.fire({
                title: '{{translate('Want_to_delete_this_schedule?')}}',
                text: '{{translate('If_you_select_Yes,_the_time_schedule_will_be_deleted.')}}',
                type: 'warning',
                showCancelButton: true,
                cancelButtonColor: 'default',
                confirmButtonColor: '#00868F',
                cancelButtonText: '{{translate('messages.no')}}',
                confirmButtonText: '{{translate('messages.yes')}}',
                reverseButtons: true
            }).then((result) => {
                if (result.value) {
                    $.get({
                        url: route,
                        beforeSend: function () {
                            $('#loading').show();
                        },
                        success: function (data) {
                            if (data.errors) {
                                for (var i = 0; i < data.errors.length; i++) {
                                    toastr.error(data.errors[i].message, {
                                        CloseButton: true,
                                        ProgressBar: true
                                    });
                                }
                            } else {
                                $('#schedule').empty().html(data.view);
                                toastr.success('{{translate('messages.Schedule removed successfully')}}', {
                                    CloseButton: true,
                                    ProgressBar: true
                                });
                            }
                        },
                        error: function(XMLHttpRequest, textStatus, errorThrown) {
                            toastr.error('{{translate('messages.Schedule not found')}}', {
                                CloseButton: true,
                                ProgressBar: true
                            });
                        },
                        complete: function () {
                            $('#loading').hide();
                        },
                    });
                }
            })
        };


        function readURL(input) {
            if (input.files && input.files[0]) {
                var reader = new FileReader();

                reader.onload = function (e) {
                    $('#viewer').attr('src', e.target.result);
                }

                reader.readAsDataURL(input.files[0]);
            }
        }

        $("#customFileEg1").change(function () {
            readURL(this);
        });

        $(document).on('ready', function () {
            // INITIALIZATION OF SELECT2
            // =======================================================
            $('.js-select2-custom').each(function () {
                var select2 = $.HSCore.components.HSSelect2.init($(this));
            });

            $("#gst_status").on('change', function(){
                if($("#gst_status").is(':checked')){
                    $('#gst').removeAttr('readonly');
                } else {
                    $('#gst').attr('readonly', true);
                }
            });
        });

        $('#exampleModal').on('show.bs.modal', function (event) {
            var button = $(event.relatedTarget);
            var day_name = button.data('day');
            var day_id = button.data('dayid');
            var modal = $(this);
            modal.find('.modal-title').text('{{translate('messages.Create Schedule For ')}} ' + day_name);
            modal.find('.modal-body input[name=day]').val(day_id);
        })

        $('#add-schedule').on('submit', function (e) {
            e.preventDefault();
            var formData = new FormData(this);
            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                }
            });
            $.post({
                url: '{{route('vendor.business-settings.add-schedule')}}',
                data: formData,
                cache: false,
                contentType: false,
                processData: false,
                beforeSend: function () {
                    $('#loading').show();
                },
                success: function (data) {
                    if (data.errors) {
                        for (var i = 0; i < data.errors.length; i++) {
                            toastr.error(data.errors[i].message, {
                                CloseButton: true,
                                ProgressBar: true
                            });
                        }
                    } else {
                        $('#schedule').empty().html(data.view);
                        $('#exampleModal').modal('hide');
                        toastr.success('{{translate('messages.Schedule added successfully')}}', {
                            CloseButton: true,
                            ProgressBar: true
                        });
                    }
                },
                error: function(XMLHttpRequest, textStatus, errorThrown) {
                    toastr.error(XMLHttpRequest.responseText, {
                        CloseButton: true,
                        ProgressBar: true
                    });
                },
                complete: function () {
                    $('#loading').hide();
                },
            });
        });

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
@endpush
