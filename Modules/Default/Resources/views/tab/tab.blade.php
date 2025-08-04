@extends('layouts.form')
@section('styles')

@endsection

@section('content')
<!--start page wrapper -->
<div class="page-wrapper">
    <div class="page-content">
        <!--breadcrumb-->
        <div class="page-breadcrumb d-none d-sm-flex align-items-center mb-3">
            <div class="breadcrumb-title pe-3">Forms</div>
            <div class="ps-3">
                <nav aria-label="breadcrumb">
                    <ol class="breadcrumb mb-0 p-0">
                        <li class="breadcrumb-item">
<a href="{{ route('admin.homepage') }}"><i class="bx bx-home-alt"></i></a>
                        </li>
                        <li class="breadcrumb-item active" aria-current="page">Form Elements</li>
                    </ol>
                </nav>
            </div>
            <div class="ms-auto">
                <div class="btn-group">
                    <button type="button" class="btn btn-primary"><i class="fadeIn animated bx bx-arrow-back"></i> กลับ</button>
                    
                </div>
            </div>
        </div>
        <!--end breadcrumb-->
        <div class="row">
            <div class="col-xl-11 mx-auto">
                <h6 class="mb-0 text-uppercase">Warning Nav Tabs</h6>
                <hr/>
                <div class="card">
                    <div class="card-body">
                        <ul class="nav nav-tabs nav-warning" role="tablist" id="main-tab">
                            <li class="nav-item" role="presentation">
                                <a class="nav-link active" data-bs-toggle="tab" href="#main_tab_1" role="tab" aria-selected="true">
                                    <div class="d-flex align-items-center">
                                        <div class="tab-icon"><i class='bx bx-home font-18 me-1'></i>
                                        </div>
                                        <div class="tab-title">Home</div>
                                    </div>
                                </a>
                            </li>
                            <li class="nav-item" role="presentation">
                                <a class="nav-link" data-bs-toggle="tab" href="#main_tab_2" role="tab" aria-selected="false">
                                    <div class="d-flex align-items-center">
                                        <div class="tab-icon"><i class='bx bx-user-pin font-18 me-1'></i>
                                        </div>
                                        <div class="tab-title">Profile</div>
                                    </div>
                                </a>
                            </li>
                            <li class="nav-item" role="presentation">
                                <a class="nav-link" data-bs-toggle="tab" href="#main_tab_3" role="tab" aria-selected="false">
                                    <div class="d-flex align-items-center">
                                        <div class="tab-icon"><i class='bx bx-microphone font-18 me-1'></i>
                                        </div>
                                        <div class="tab-title">Contact</div>
                                    </div>
                                </a>
                            </li>
                        </ul>
                        <div class="tab-content py-3">
                            <!-- tab::main_tab_1 -->
                                @includeIf('default::tab.tab.tab1')
                            <!-- tab::main_tab_1 -->
                            <!-- tab::main_tab_2 -->
                                @includeIf('default::tab.tab.tab2')
                            <!-- tab::main_tab_2 -->
                            <!-- tab::main_tab_3 -->
                                @includeIf('default::tab.tab.tab3')
                            <!-- tab::main_tab_3 -->
                            
                        </div>
                    </div>
                </div>
               
            </div>
        </div>
        <!--end row-->
    </div>
</div>
<!--end page wrapper -->
@endsection('content')

@section('scripts')
    <!-- module js css -->
    <link rel="stylesheet" href="{{ mix('css/default.css') }}">
    <script src="{{ mix('js/default.js') }}"></script>
@endsection
