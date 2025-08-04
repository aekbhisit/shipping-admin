@extends('layouts.datatable')

@section('styles')
@endsection

@section('content')
    <!--start page wrapper -->
    <div class="page-wrapper">
        <div class="page-content">
            <!-- End page-header -->
            <div class="page-breadcrumb d-none d-sm-flex align-items-center mb-3">
                <div class="breadcrumb-title pe-3">ตั้งค่า</div>
                <nav aria-label="breadcrumb">
                    <ol class="breadcrumb ms-3 mb-0 p-0">
                        <li class="breadcrumb-item">
                            <a href="{{ route('admin.homepage') }}"><i class="bx bx-home-alt"></i></a>
                        </li>
                        <li class="breadcrumb-item active" aria-current="page">Slug</li>
                    </ol>
                </nav>
                @if (roles('admin.setting.slug.sitemap'))
                    <div class="btn-group ms-auto">
                        <a href="{{ route('admin.setting.slug.sitemap') }}" class="btn btn-primary btn-sm">
                            <span>
                                <i class="bx bx-sitemap" aria-hidden="true"></i>Sitemap
                            </span>
                        </a>
                    </div>
                @endif
            </div>
            <!-- row opened -->
            <div class="row">
                <div class="col-md-12 col-lg-12">
                    <div class="card">
                        <div class="card-body">
                            <div class="table-responsive">
                                <table id="slug-datatable" class="table table-bordered key-buttons text-nowrap w-100">
                                    <thead>
                                        <tr>
                                            <th class="border-bottom-0" width="5%">ID</th>
                                            <th class="border-bottom-0" width="5%">level</th>
                                            <th class="border-bottom-0" width="20%">Slug</th>
                                            <th class="border-bottom-0" width="10%">module</th>
                                            <th class="border-bottom-0" width="10%">method</th>
                                            <th class="border-bottom-0" width="10%">data id</th>
                                            <th class="border-bottom-0" width="5%">จัดการ</th>
                                        </tr>
                                    </thead>
                                    <tbody>

                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <!-- row closed -->
        </div>
    </div>
@endsection('content')

@section('scripts')
    <!-- core master js css -->
    <link rel="stylesheet" href="{{ mix('css/admin.css') }}">
    <script src="{{ mix('js/admin.js') }}"></script>
    <script src="{{ mix('js/lang.th.js') }}"></script>

    <!-- module js css -->
    <link rel="stylesheet" href="{{ mix('css/setting.css') }}">
    <script src="{{ mix('js/setting.slug.js') }}"></script>
@endsection
