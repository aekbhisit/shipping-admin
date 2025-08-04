@extends('layouts.datatable')
@section('styles')
@endsection

@section('content')
    <!--start page wrapper -->
    <div class="page-wrapper">
        <div class="page-content">
            <!--breadcrumb-->
            <div class="page-breadcrumb d-none d-sm-flex align-items-center mb-3">
                <div class="breadcrumb-title pe-3">Log</div>
                <div class="ps-3">
                    <nav aria-label="breadcrumb">
                        <ol class="breadcrumb mb-0 p-0">
                            <li class="breadcrumb-item">
                                <a href="{{ route('admin.homepage') }}"><i class="bx bx-home-alt"></i></a>
                            </li>
                            <li class="breadcrumb-item active" aria-current="page">Activities Log</li>
                        </ol>
                    </nav>
                </div>

            </div>

            <!--end breadcrumb-->
            <h6 class="mb-0 text-uppercase">Activities Log</h6>
            <hr />
            <div class="card">
                <div class="card-body">
                    @includeIf('default::default.filter')

                    <div class="table-responsive">
                        <table id="logs-datatable" class="table table-bordered key-buttons text-nowrap w-100">
                            <thead>
                                <tr>
                                    <th class="border-bottom-0" width="5%">{{ __('log::module.datatable.id') }}</th>
                                    <th class="border-bottom-0" width="30%">{{ __('log::module.datatable.log_name') }}
                                    </th>
                                    <th class="border-bottom-0" width="40%">{{ __('log::module.datatable.description') }}
                                    </th>
                                    <th class="border-bottom-0" width="40%">
                                        {{ __('log::module.datatable.subject_type') }}</th>
                                    <th class="border-bottom-0" width="40%">{{ __('log::module.datatable.event') }}</th>
                                    <th class="border-bottom-0" width="40%">{{ __('log::module.datatable.subject_id') }}
                                    </th>
                                    <th class="border-bottom-0" width="40%">{{ __('log::module.datatable.updated_at') }}
                                    </th>
                                    <th class="border-bottom-0" width="5%">{{ __('log::module.datatable.manage') }}</th>
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
    <link rel="stylesheet" href="{{ mix('css/log.css') }}">
    <script src="{{ mix('js/log.js') }}"></script>
@endsection
