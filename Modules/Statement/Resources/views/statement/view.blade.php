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
                            <li class="breadcrumb-item"><a href="{{ route('admin.statement.list.index') }}">Statement</a>
                            </li>
                            <li class="breadcrumb-item active" aria-current="page">รายละเอียด</li>
                        </ol>
                    </nav>
                </div>
                <div class="ms-auto">
                    <div class="btn-group">
                        <a href="{{ route('admin.statement.list.index') }}" role="button" class="btn btn-primary"><i
                                class="fadeIn animated bx bx-arrow-back"></i>
                            กลับ
                        </a>
                    </div>
                </div>
            </div>

            <div class="row justify-content-center">
                <div class="col-lg-12">
                    <div class="card">
                        <div class="card-header px-2 pb-0 ">
                            <ul class="nav nav-list" role="tablist" id="statement-tab">
                                <li class="nav-item" role="presentation">
                                    <a class="nav-link active" data-bs-toggle="tab" href="#detail_tab" role="tab"
                                        aria-selected="true">
                                        <div class="d-flex align-items-center">
                                            <div class="tab-title">รายละเอียด</div>
                                        </div>
                                    </a>
                                </li>
                                <li class="nav-item" role="presentation">
                                    <a class="nav-link" data-bs-toggle="tab" href="#report_tab" role="tab"
                                        aria-selected="false">
                                        <div class="d-flex align-items-center">
                                            <div class="tab-title">Report</div>
                                        </div>
                                    </a>
                                </li>
                            </ul>
                        </div>
                        <div class="card-body">
                            <div class="tab-content">
                                {{-- statement list_tab --}}
                                @include('statement::statement.list_tab')
                                {{-- statement list_tab --}}
                                {{-- statement report_tab --}}
                                @include('statement::statement.report_tab')
                                {{-- statement report_tab --}}

                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <!--end page wrapper -->
@endsection

@section('scripts')
    <!-- core master js css -->
    <link rel="stylesheet" href="{{ mix('css/admin.css') }}">
    <script src="{{ mix('js/admin.js') }}"></script>
    <script src="{{ mix('js/lang.th.js') }}"></script>

    <!-- module js css -->
    <link rel="stylesheet" href="{{ mix('css/statement.css') }}">
    <script src="{{ mix('js/statement.js') }}"></script>
@endsection
