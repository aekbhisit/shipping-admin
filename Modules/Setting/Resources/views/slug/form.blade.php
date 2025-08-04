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
                        <li class="breadcrumb-item">{{ __('admin.websetting') }}</li>
                        <li class="breadcrumb-item active" aria-current="page">
                            <a href="{{ route('admin.setting.slug.index') }}">Slug</a>
                        </li>
                    </ol>
                </nav>
            </div>
            <!-- End page-header -->

            <!-- row -->

            <div class="row justify-content-center">
                <div class="col-md-11">
                    <div class="card border-top border-0 border-4 border-primary">
                        <form id="slug_frm" name="slug_frm" method="POST" onsubmit="setSave(); return false;"
                            enctype="multipart/form-data">
                            @csrf
                            <div class="card-body">
                                @include('setting::slug.form_seo')
                            </div>
                            <div class="card-footer py-3 text-center">
                                <button type="submit" class="btn btn-info px-5"><i class="lni lni-save"></i> Save</button>
                                <a href="{{ route('admin.setting.slug.index') }}" class="btn btn-warning px-5"
                                    role="button">
                                    <i class="lni lni-close"></i> Cancel
                                </a>
                            </div>
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

    <!-- module js css -->
    <link rel="stylesheet" href="{{ mix('css/setting.css') }}">
    <script src="{{ mix('js/setting.slug.js') }}"></script>
@endsection
