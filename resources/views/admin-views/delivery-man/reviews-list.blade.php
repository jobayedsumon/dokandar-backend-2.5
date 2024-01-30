@extends('layouts.admin.app')

@section('title',translate('messages.Review List'))

@push('css_or_js')

@endpush

@section('content')
    <div class="content container-fluid">
        <!-- Page Header -->
        <div class="page-header">
            <h1 class="page-header-title text-break">
                <span class="page-header-icon">
                    <img src="{{asset('assets/admin/img/delivery-man.png')}}" class="w--26" alt="">
                </span>
                <span>{{translate('messages.deliveryman_reviews')}}<span class="badge badge-soft-dark ml-2" id="itemCount">{{$reviews->total()}}</span></span>
            </h1>
        </div>
        <!-- End Page Header -->
        <div class="row gx-2 gx-lg-3">
            <div class="col-sm-12 col-lg-12 mb-3 mb-lg-2">
                <!-- Card -->
                <div class="card">
                    <!-- Header -->
                    <div class="card-header py-2 border-0">
                        <span class="card-header-title"></span>
                        <div class="search--button-wrapper justify-content-end">
                            <form class="search-form">
                            {{-- @csrf --}}
                                <!-- Search -->
                                <div class="input-group input--group">
                                    <input id="datatableSearch" name="search" type="search" class="form-control" placeholder="{{translate('ex_:_search_delivery_man')}}" value="{{ request()->get('search') }}" aria-label="{{translate('messages.search_here')}}">
                                    <button type="submit" class="btn btn--secondary"><i class="tio-search"></i></button>
                                </div>
                                <!-- End Search -->
                            </form>
                            <!-- Unfold -->
                            <div class="hs-unfold mr-2">
                                <a class="js-hs-unfold-invoker btn btn-sm btn-white dropdown-toggle min-height-40" href="javascript:;"
                                    data-hs-unfold-options='{
                                            "target": "#usersExportDropdown",
                                            "type": "css-animation"
                                        }'>
                                    <i class="tio-download-to mr-1"></i> {{ translate('messages.export') }}
                                </a>

                                <div id="usersExportDropdown"
                                    class="hs-unfold-content dropdown-unfold dropdown-menu dropdown-menu-sm-right">
                                    <span class="dropdown-header">{{ translate('messages.download_options') }}</span>
                                    <a id="export-excel" class="dropdown-item" href="{{route('admin.delivery-man.reviews.export', ['type'=>'excel',request()->getQueryString()])}}">
                                        <img class="avatar avatar-xss avatar-4by3 mr-2"
                                            src="{{ asset('assets/admin') }}/svg/components/excel.svg"
                                            alt="Image Description">
                                        {{ translate('messages.excel') }}
                                    </a>
                                    <a id="export-csv" class="dropdown-item" href="{{route('admin.delivery-man.reviews.export', ['type'=>'csv',request()->getQueryString()])}}">
                                        <img class="avatar avatar-xss avatar-4by3 mr-2"
                                            src="{{ asset('assets/admin') }}/svg/components/placeholder-csv-format.svg"
                                            alt="Image Description">
                                        .{{ translate('messages.csv') }}
                                    </a>
                                    {{-- <a id="export-pdf" class="dropdown-item" href="javascript:;">
                                        <img class="avatar avatar-xss avatar-4by3 mr-2"
                                            src="{{ asset('assets/admin') }}/svg/components/pdf.svg"
                                            alt="Image Description">
                                        {{ translate('messages.pdf') }}
                                    </a> --}}
                                </div>
                            </div>
                            <!-- End Unfold -->
                        </div>
                    </div>
                    <!-- End Header -->

                    <!-- Table -->
                    <div class="table-responsive datatable-custom">
                        <table id="columnSearchDatatable"
                               class="table table-borderless table-thead-bordered table-nowrap table-align-middle card-table"
                               data-hs-datatables-options='{
                                 "order": [],
                                 "orderCellsTop": true,
                                 "paging": false
                               }'>
                            <thead class="thead-light">
                            <tr>
                                <th class="border-0">{{translate('sl')}}</th>
                                <th class="border-0">{{translate('messages.deliveryman')}}</th>
                                <th class="border-0">{{translate('messages.customer')}}</th>
                                <th class="border-0">{{translate('messages.review')}}</th>
                                <th class="border-0">{{translate('messages.rating')}}</th>
                                {{-- <th class="border-0">{{translate('messages.status')}}</th> --}}
                            </tr>
                            </thead>

                            <tbody id="set-rows">
                            @foreach($reviews as $key=>$review)
                                @if(isset($review->delivery_man))
                                    <tr>
                                        <td>{{$key+1}}</td>
                                        <td>
                                        <span class="d-block font-size-sm text-body">
                                            <a href="{{route('admin.users.delivery-man.preview',[$review['delivery_man_id']])}}">
                                                {{$review->delivery_man->f_name.' '.$review->delivery_man->l_name}}
                                            </a>
                                        </span>
                                        </td>
                                        <td>
                                            @if ($review->customer)
                                            <a href="{{route('admin.users.customer.view',[$review->user_id])}}">
                                                {{$review->customer?$review->customer->f_name:""}} {{$review->customer?$review->customer->l_name:""}}
                                            </a>
                                            @else
                                                {{translate('messages.customer_not_found')}}
                                            @endif

                                        </td>
                                        <td>
                                            {{$review->comment}}
                                        </td>
                                        <td>
                                            <label class="rating">
                                                {{$review->rating}} <i class="tio-star"></i>
                                            </label>
                                        </td>
                                        {{-- <td>
                                            <label class="toggle-switch toggle-switch-sm" for="reviewCheckbox{{$review->id}}">
                                                <input type="checkbox" onclick="status_form_alert('status-{{$review['id']}}','{{$review->status?translate('messages.you_want_to_hide_this_review_for_customer'):translate('messages.you_want_to_show_this_review_for_customer')}}', event)" class="toggle-switch-input" id="reviewCheckbox{{$review->id}}" {{$review->status?'checked':''}}>
                                                <span class="toggle-switch-label">
                                                    <span class="toggle-switch-indicator"></span>
                                                </span>
                                            </label>
                                            <form action="{{route('admin.users.delivery-man.reviews.status',[$review['id'],$review->status?0:1])}}" method="get" id="status-{{$review['id']}}">
                                            </form>
                                        </td> --}}
                                    </tr>
                                @endif
                            @endforeach
                            </tbody>
                        </table>
                        @if(count($reviews) !== 0)
                        <hr>
                        @endif
                        <div class="page-area">
                            {!! $reviews->links() !!}
                        </div>
                        @if(count($reviews) === 0)
                        <div class="empty--data">
                            <img src="{{asset('/assets/admin/svg/illustrations/sorry.svg')}}" alt="public">
                            <h5>
                                {{translate('no_data_found')}}
                            </h5>
                        </div>
                        @endif
                    </div>
                    <!-- End Table -->
                </div>
                <!-- End Card -->
            </div>
        </div>
    </div>

@endsection

@push('script_2')
    <script>
        $(document).on('ready', function () {
            // INITIALIZATION OF DATATABLES
            // =======================================================
            var datatable = $.HSCore.components.HSDatatables.init($('#columnSearchDatatable'));

        });

        function status_form_alert(id, message, e) {
            e.preventDefault();
            Swal.fire({
                title: '{{translate('messages.are_you_sure')}}',
                text: message,
                type: 'warning',
                showCancelButton: true,
                cancelButtonColor: 'default',
                confirmButtonColor: '#FC6A57',
                cancelButtonText: '{{translate('messages.no')}}',
                confirmButtonText: '{{translate('messages.yes')}}',
                reverseButtons: true
            }).then((result) => {
                if (result.value) {
                    $('#'+id).submit()
                }
            })
        }

        $('#search-form').on('submit', function (e) {
            e.preventDefault();
            var formData = new FormData(this);
            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                }
            });
            $.post({
                url: '{{route('admin.users.delivery-man.reviews.search')}}',
                data: formData,
                cache: false,
                contentType: false,
                processData: false,
                beforeSend: function () {
                    $('#loading').show();
                },
                success: function (data) {
                    $('#set-rows').html(data.view);
                    $('#itemCount').html(data.count);
                    $('.page-area').hide();
                },
                complete: function () {
                    $('#loading').hide();
                },
            });
        });
    </script>
@endpush
