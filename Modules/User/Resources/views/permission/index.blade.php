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
                            <li class="breadcrumb-item active" aria-current="page">สิทธิ์</li>
                        </ol>
                    </nav>
                </div>
                @if (roles('admin.user.permission.generate_permission'))
                    <div class="ms-auto">
                        <div class="btn-group">
                            <a href="javascript:setGeneratePermission();" class="px-1">
                                <button type="button" class="btn btn-info"><i class="lni lni-spinner-arrow"></i>
                                    Generate</button>
                            </a>

                        </div>
                    </div>
                @endif
            </div>

            <!--end breadcrumb-->
            <div class="card">
                <div class="card-body">

                    <div class="table-responsive">
                        <table id="permission-datatable" class="table table-bordered key-buttons text-nowrap w-100">
                            <thead>
                                <tr>
                                    <th class="border-bottom-0" width="5%">{{ __('user::permission.datatable.id') }}
                                    </th>
                                    <th class="border-bottom-0" width="">{{ __('user::permission.datatable.name') }}
                                    </th>
                                    <th class="border-bottom-0" width="">{{ __('user::permission.datatable.group') }}
                                    </th>
                                    <th class="border-bottom-0" width="15%">{{ __('user::permission.datatable.module') }}
                                    </th>
                                    <th class="border-bottom-0" width="15%">{{ __('user::permission.datatable.page') }}
                                    </th>
                                    <th class="border-bottom-0" width="15%">{{ __('user::permission.datatable.action') }}
                                    </th>
                                    <th class="border-bottom-0" width="15%">
                                        {{ __('user::permission.datatable.route_name') }}</th>
                                    <th class="border-bottom-0" width="">
                                        {{ __('user::permission.datatable.updated_at') }}</th>
                                    <th class="border-bottom-0" width="15%">{{ __('user::permission.datatable.manage') }}
                                    </th>
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
    <link rel="stylesheet" href="{{ mix('css/user_permission.css') }}">
    <script src="{{ mix('js/user_permission.js') }}?t=<?= time() ?>"></script>
@endsection
