@extends('layouts.datatable')
@section('styles')
@endsection

@section('content')
    <!--start page wrapper -->
    <div class="page-wrapper">
        <div class="page-content">
            <!--breadcrumb-->
            <div class="page-breadcrumb d-none d-sm-flex align-items-center mb-3">
                <div class="breadcrumb-title pe-3">Statement</div>
                <div class="ps-3">
                    <nav aria-label="breadcrumb">
                        <ol class="breadcrumb mb-0 p-0">
                            <li class="breadcrumb-item">
<a href="{{ route('admin.homepage') }}"><i class="bx bx-home-alt"></i></a>
                            </li>
                            <li class="breadcrumb-item active" aria-current="page">Statement</li>
                        </ol>
                    </nav>
                </div>
            </div>
            <!--end breadcrumb-->
            <div class="card">
                <div class="card-body">
                    @include('statement::statement.filter')

                    <div class="table-responsive">
                        <table id="data-datatable" class="table table-bordered w-100">
                            <thead class="text-center" style="vertical-align: middle;">
                                <tr>
                                    <th rowspan="2">id</th>
                                    <th rowspan="2">SMS</th>
                                    <th rowspan="2">Temp</th>
                                    <th rowspan="2">Account</th>
                                    <th colspan="8">Report</th>
                                    <th rowspan="2"></th>
                                </tr>
                                <tr>
                                    <th scope="col">Datetime</th>
                                    <th scope="col">Value</th>
                                    <th scope="col">Balance</th>
                                    <th scope="col">Detail</th>
                                    <th scope="col">App Detail</th>
                                    <th scope="col">Account</th>
                                    <th scope="col">Name</th>
                                    <th scope="col">Status</th>
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
    <link rel="stylesheet" href="{{ mix('css/statement.css') }}">
    <script src="{{ mix('js/statement.js') }}"></script>
@endsection
