@extends('layouts.datatable')

@section('styles')
@endsection

@section('content')
    <!--start page wrapper -->
    <div class="page-wrapper">
        <div class="page-content">
            <!--breadcrumb-->
            <div class="page-breadcrumb d-none d-sm-flex align-items-center mb-3">
                <div class="breadcrumb-title pe-3">ผู้ใช้งาน</div>
                <div class="ps-3">
                    <nav aria-label="breadcrumb">
                        <ol class="breadcrumb mb-0 p-0">
                            <li class="breadcrumb-item">
                                <a href="{{ route('admin.homepage') }}"><i class="bx bx-home-alt"></i></a>
                            </li>
                            <li class="breadcrumb-item active" aria-current="page">บทบาท</li>
                        </ol>
                    </nav>
                </div>
                @if (roles('admin.user.role.add'))
                    <div class="ms-auto">
                        <div class="btn-group">
                            <a href="{{ route('admin.user.role.add') }}">
                                <button type="button" class="btn btn-info"><i class="lni lni-plus me-1"></i>เพิ่ม</button>
                            </a>
                        </div>
                    </div>
                @endif
            </div>

            <!--end breadcrumb-->
            <div class="card">
                <div class="card-body">

                    <div class="table-responsive">
                        <table id="role-datatable" class="table table-bordered key-buttons text-nowrap w-100">
                            <thead>
                                <tr>
                                    <th class="border-bottom-0" width="5%">{{ __('user::role.datatable.id') }}</th>
                                    <th class="border-bottom-0" width="">{{ __('user::role.datatable.name') }}</th>
                                    <th class="border-bottom-0" width="20">{{ __('user::role.datatable.updated_at') }}
                                    </th>
                                    <th class="border-bottom-0" width="15%">{{ __('user::role.datatable.manage') }}</th>
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
@endsection('content')

@section('scripts')
    <!-- module js css -->
    <link rel="stylesheet" href="{{ mix('css/user_role.css') }}">
    <script src="{{ mix('js/user_role.js') }}?t=<?= time() ?>"></script>
@endsection
