@extends('layouts.admin.app')

@section('title',translate('messages.about_us'))

@push('css_or_js')

@endpush

@section('content')
    <div class="content container-fluid">
        <!-- Page Header -->
        <div class="page-header">
            <h1 class="page-header-title">
                <span class="page-header-icon">
                    <img src="{{asset('assets/admin/img/privacy-policy.png')}}" class="w--26" alt="">
                </span>
                <span>
                    {{translate('messages.about_us')}}
                </span>
            </h1>
        </div>
        <!-- End Page Header -->
        <div class="row gx-2 gx-lg-3">
            <div class="col-sm-12 col-lg-12 mb-3 mb-lg-2">
                <form action="{{route('admin.business-settings.about-us')}}" method="post" id="about_us-form">

                    @csrf

                    @php($language=\App\Models\BusinessSetting::where('key','language')->first())
                    @php($language = $language->value ?? null)
                    @php($default_lang = str_replace('_', '-', app()->getLocale()))
                    @if ($language)
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
                    @endif
                    <div class="lang_form" id="default-form">
                        <div class="form-group">
                            <label for="about_title">{{ translate('messages.about_title') }}({{ translate('messages.Default') }})</label>
                            <input type="text" id="about_title" name="about_title[]" class="form-control"
                              value="{{ $about_title?->getRawOriginal('value') ?? '' }}" >
                        </div>

                        <div class="form-group">
                            <label for="about_us">{{ translate('messages.about_us_description') }}({{ translate('messages.Default') }})</label>
                            <textarea class="ckeditor form-control" name="about_us[]">{!! $about_us?->getRawOriginal('value') ?? '' !!}</textarea>
                        </div>
                        <input type="hidden" name="lang[]" value="default">
                    </div>

                    @if ($language)
                        @forelse(json_decode($language) as $lang)
                            <?php
                                if($about_us?->translations){
                                    $translate = [];
                                    foreach($about_us?->translations as $t)
                                    {
                                        if($t->locale == $lang && $t->key=="about_us"){
                                            $translate[$lang]['about_us'] = $t->value;
                                        }
                                    }
                                }
                                if($about_title?->translations){
                                    $translate = [];
                                    foreach($about_title?->translations as $t)
                                    {
                                        if($t->locale == $lang && $t->key=="about_title"){
                                            $translate[$lang]['about_title'] = $t->value;
                                        }
                                    }
                                }

                                ?>

                            <div class="d-none lang_form" id="{{$lang}}-form">
                                <div class="form-group">
                                    <label for="about_title">{{ translate('messages.about_title') }}({{ $lang }})</label>
                                    <input type="text" id="about_title" name="about_title[]" class="form-control"
                                    value="{{ $translate[$lang]['about_title'] ?? null }}" >
                                </div>

                                <div class="form-group">
                                    <label for="about_us">{{ translate('messages.about_us_description') }}({{ $lang }})</label>
                                    <textarea class="ckeditor form-control" name="about_us[]">{!!  $translate[$lang]['about_us'] ?? null !!}</textarea>
                                </div>
                                <input type="hidden" name="lang[]" value="{{$lang}}">
                            </div>

                            @empty
                        @endforelse
                    @endif
                    <div class="btn--container justify-content-end">
                        <button type="submit" class="btn btn--primary">{{translate('messages.submit')}}</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

@endsection

@push('script_2')
<script src="//cdn.ckeditor.com/4.14.1/standard/ckeditor.js"></script>
<script type="text/javascript">
    $(document).ready(function () {
        $('.ckeditor').ckeditor();
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
        $("#"+lang+"-form").removeClass('d-none');
    });
</script>
@endpush
