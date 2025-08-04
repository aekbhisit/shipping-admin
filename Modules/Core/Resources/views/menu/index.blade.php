@extends('layouts.datatable')
@section('styles')
@endsection

@section('content')
    <!--start page wrapper -->
    <div class="page-wrapper">
        <div class="page-content">
            <!--breadcrumb-->
            <div class="page-breadcrumb d-none d-sm-flex align-items-center mb-3">
                <div class="breadcrumb-title pe-3">Admin Menu</div>
                <div class="ps-3">
                    <nav aria-label="breadcrumb">
                        <ol class="breadcrumb mb-0 p-0">
                            <li class="breadcrumb-item">
                                <a href="{{ route('admin.homepage') }}"><i class="bx bx-home-alt"></i></a>
                            </li>
                            <li class="breadcrumb-item active" aria-current="page">Menu</li>
                        </ol>
                    </nav>
                </div>
                @if (roles('admin.admin_menu.admin_menu.add'))
                    <div class="ms-auto">
                        <div class="btn-group">
                            <a href="{{ route('admin.admin_menu.admin_menu.add') }}">
                                <button type="button" class="btn btn-info"><i class="lni lni-plus me-1"></i> Add</button>
                            </a>
                        </div>
                    </div>
                @endif
            </div>

            <!--end breadcrumb-->

            <div class="card">
                <div class="card-body">

                    <div class="table-responsive">
                        <table id="menu-datatable" class="table table-striped table-bordered" style="width:100%">
                            <thead>
                                <tr>
                                    <th>#</th>
                                    <th>icon</th>
                                    <th>name</th>
                                    <th>link</th>
                                    <th>sort</th>
                                    <th>updated_at</th>
                                    <th width="15%">action</th>
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
    <!--end page wrapper -->
@endsection

@section('scripts')
    <!-- module js css -->
    {{-- <link rel="stylesheet" href="{{ mix('css/default.css') }}"> --}}
    <script src="{{ mix('js/admin_menu.js') }}"></script>
@endsection
