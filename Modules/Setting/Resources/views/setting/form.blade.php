@extends('layouts.form')

@section('styles')
@endsection

@section('content')
    <!--start page wrapper -->
    <div class="page-wrapper">
        <div class="page-content">
            <!-- page-header -->
            <div class="page-breadcrumb d-none d-sm-flex align-items-center mb-3">
                <div class="breadcrumb-title pe-3">ตั้งค่า</div>
                <nav aria-label="breadcrumb">
                    <ol class="breadcrumb ms-3 mb-0 p-0">
                        <li class="breadcrumb-item">
                            <a href="{{ route('admin.homepage') }}"><i class="bx bx-home-alt"></i></a>
                        </li>
                        <li class="breadcrumb-item active" aria-current="page">{{ __('admin.websetting') }}</li>
                    </ol>
                </nav>
            </div>
            <!-- End page-header -->
            <!-- row -->

            <div class="row justify-content-center">
                <div class="col-md-11">
                    <div class="card border-top border-0 border-4 border-primary">
                        <div class="card-header px-2 pb-0">
                            <ul class="nav nav-list" role="tablist" id="main-tab">
                                <li class="nav-item" role="presentation">
                                    <a class="nav-link active" data-bs-toggle="tab" href="#setting_tab" role="tab"
                                        aria-selected="true">
                                        <div class="d-flex align-items-center">
                                            <div class="tab-title">ตั้งค่าเว็บไซต์</div>
                                        </div>
                                    </a>
                                </li>
                                <li class="nav-item" role="presentation">
                                    <a class="nav-link" data-bs-toggle="tab" href="#seo_tab" role="tab"
                                        aria-selected="false">
                                        <div class="d-flex align-items-center">
                                            <div class="tab-title">SEO</div>
                                        </div>
                                    </a>
                                </li>
                            </ul>
                        </div>
                        <form id="setting_frm" name="setting_frm" method="POST" onsubmit="setSave(); return false;"
                            enctype="multipart/form-data">
                            @csrf
                            <input type="hidden" name="id" value="{{ !empty($setting->id) ? $setting->id : '0' }}">
                            <div class="card-body">
                                <div class="tab-content border-1 py-3">
                                    <!-- form_tab_websetting -->
                                    @include('setting::setting.form_tab')
                                    <!-- .form_tab_websetting -->
                                    <!-- form_tab_seo -->
                                    @include('setting::setting.form_seo')
                                    <!-- .form_tab_seo -->
                                </div>
                            </div>
                            @if (roles('admin.setting.web.save'))
                                <div class="card-footer py-3">
                                    <div class="btn-list">
                                        <button type="submit" class="btn btn-info px-5"><i class="lni lni-save"></i>
                                            Save</button>
                                    </div>
                                </div>
                            @endif
                        </form>
                    </div>
                </div>
            </div>
            <!-- .row -->
        </div>
    </div>
@endsection('content')

@section('scripts')
    <!-- core master js css -->
    <link rel="stylesheet" href="{{ mix('css/admin.css') }}">
    <script src="{{ mix('js/admin.js') }}"></script>
    {{-- <script src="{{ mix('js/lang.th.js') }}"></script> --}}

    <!-- module js css -->
    <link rel="stylesheet" href="{{ mix('css/setting.css') }}">
    <script src="{{ mix('js/setting.js') }}"></script>
@endsection
