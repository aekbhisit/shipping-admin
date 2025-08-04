@extends('layouts.tab')
@section('styles')

@endsection

@section('content')
<!--start page wrapper -->
<div class="page-wrapper">
    <div class="page-content">
        <!--breadcrumb-->
        <div class="page-breadcrumb d-none d-sm-flex align-items-center mb-3">
            <div class="breadcrumb-title pe-3">ลูกค้า</div>
            <div class="ps-3">
                <nav aria-label="breadcrumb">
                    <ol class="breadcrumb mb-0 p-0">
                        <li class="breadcrumb-item"><a href="javascript:;"><i class="bx bx-home-alt"></i></a>
                        </li>
                        <li class="breadcrumb-item active" aria-current="page">รายละเอียดลูกค้า</li>
                    </ol>
                </nav>
            </div>
            <div class="ms-auto">
                <div class="btn-group">
                    <a href="{{ route('admin.customer.customer.index') }}">
                    <button type="button" class="btn btn-primary"><i class="fadeIn animated bx bx-arrow-back"></i> กลับ</button>
                    </a>
                </div>
            </div>
        </div>
        <!--end breadcrumb-->
        <div class="row">
            <div class="col-xl-12 mx-auto">
                {{-- <h6 class="mb-0 text-uppercase">Warning Nav Tabs</h6>
                <hr/> --}}
                <div class="card">
                    <div class="card-body">
                        <div class="alert alert-outline-success shadow-sm alert-dismissible fade show py-2">
                            <div class="d-flex align-items-center">
                                <div class="font-35 text-success"><i class="fadeIn animated bx bx-user"></i>
                                </div>
                                <div class="ms-3">
                                    <h6 class="mb-0 text-success">{{ !empty($data->username) ? $data->username : '' }}</h6>
                                    <div><strong class="text-success">ชื่อ</strong> {{ !empty($data->name) ? $data->name : '' }} <strong class="text-info">เบอร์โทร</strong> {{ !empty($data->mobile) ? $data->mobile : '' }}</div>
                                    <input type="hidden" class="datatable_filter" name="cust_id" id="cust_id" value="{{ !empty($data->id) ? $data->id : '0' }}">
                                </div>
                            </div>
                            
                        </div>

                        <ul class="nav nav-tabs nav-warning" role="tablist" id="main-tab">
                            <li class="nav-item" role="presentation">
                                <a class="nav-link active" data-bs-toggle="tab" href="#main_tab_1" role="tab" aria-selected="true">
                                    <div class="d-flex align-items-center">
                                        <div class="tab-icon"><i class='bx bx-user-pin font-18 me-1'></i>
                                        </div>
                                        <div class="tab-title">ข้อมูล</div>
                                    </div>
                                </a>
                            </li>
                            <li class="nav-item" role="presentation">
                                <a class="nav-link" data-bs-toggle="tab" href="#main_tab_2" role="tab" aria-selected="false" onclick="initUserDatatable();">
                                    <div class="d-flex align-items-center">
                                        <div class="tab-icon"><i class="bx bx-user-check font-18 me-1"></i>
                                        </div>
                                        <div class="tab-title">ยูเซอร์</div>
                                    </div>
                                </a>
                            </li>
                            <li class="nav-item" role="presentation">
                                <a class="nav-link" data-bs-toggle="tab" href="#main_tab_3" role="tab" aria-selected="false" onclick="initBankDatatable()">
                                    <div class="d-flex align-items-center">
                                        <div class="tab-icon"><i class="bx bx-credit-card-front font-18 me-1"></i>
                                        </div>
                                        <div class="tab-title">บัญชี</div>
                                    </div>
                                </a>
                            </li>
                            <li class="nav-item" role="presentation">
                                <a class="nav-link" data-bs-toggle="tab" href="#main_tab_4" role="tab" aria-selected="false" onclick="initJobDatatable()"> 
                                    <div class="d-flex align-items-center">
                                        <div class="tab-icon"><i class="bx bx-transfer-alt font-18 me-1"></i>
                                        </div>
                                        <div class="tab-title">รายการ ฝาก - ถอน</div>
                                    </div>
                                </a>
                            </li>
                            <li class="nav-item" role="presentation">
                                <a class="nav-link" data-bs-toggle="tab" href="#main_tab_5" role="tab" aria-selected="false" onclick="initManualCreditDatatable()"> 
                                    <div class="d-flex align-items-center">
                                        <div class="tab-icon"><i class="bx bx-dollar-circle font-18 me-1"></i>
                                        </div>
                                        <div class="tab-title">รายการ เพิ่ม-ลด เครดิต</div>
                                    </div>
                                </a>
                            </li>
                            <li class="nav-item" role="presentation">
                                <a class="nav-link" data-bs-toggle="tab" href="#main_tab_6" role="tab" aria-selected="false" onclick="initTempProDatatable()"> 
                                    <div class="d-flex align-items-center">
                                        <div class="tab-icon"><i class="bx bx-purchase-tag font-18 me-1"></i>
                                        </div>
                                        <div class="tab-title">รายการรับโปร</div>
                                    </div>
                                </a>
                            </li>
                            <li class="nav-item" role="presentation">
                                <a class="nav-link" data-bs-toggle="tab" href="#main_tab_7" role="tab" aria-selected="false" onclick="initActivitiesDatatable()">
                                    <div class="d-flex align-items-center">
                                        <div class="tab-icon"><i class="bx bx-history font-18 me-1"></i>
                                        </div>
                                        <div class="tab-title">ประวัตการแก้ไข</div>
                                    </div>
                                </a>
                            </li>
                            <li class="nav-item" role="presentation">
                                <a class="nav-link" data-bs-toggle="tab" href="#main_tab_8" role="tab" aria-selected="false" onclick="initErrorDatatable()">
                                    <div class="d-flex align-items-center">
                                        <div class="tab-icon"><i class="bx bx-error font-18 me-1"></i>
                                        </div>
                                        <div class="tab-title">ปัญหาล่าสุด</div>
                                    </div>
                                </a>
                            </li>
                        </ul>
                        <div class="tab-content py-3">
                            <!-- tab::main_tab_1 -->
                                @includeIf('customer::customer.tab.tab.tab1')
                            <!-- tab::main_tab_1 -->
                            <!-- tab::main_tab_2 -->
                                @includeIf('customer::customer.tab.tab.tab2')
                            <!-- tab::main_tab_2 -->
                            <!-- tab::main_tab_3 -->
                                @includeIf('customer::customer.tab.tab.tab3')
                            <!-- tab::main_tab_3 -->
                            <!-- tab::main_tab_4 -->
                                @includeIf('customer::customer.tab.tab.tab4')
                            <!-- tab::main_tab_4 -->
                            <!-- tab::main_tab_5 -->
                                @includeIf('customer::customer.tab.tab.tab5')
                            <!-- tab::main_tab_5 -->
                            <!-- tab::main_tab_6 -->
                                @includeIf('customer::customer.tab.tab.tab6')
                            <!-- tab::main_tab_6 -->
                            <!-- tab::main_tab_7 -->
                                @includeIf('customer::customer.tab.tab.tab7')
                            <!-- tab::main_tab_7 -->
                            <!-- tab::main_tab_8 -->
                                @includeIf('customer::customer.tab.tab.tab8')
                            <!-- tab::main_tab_8 -->
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
    <link rel="stylesheet" href="{{ mix('css/customer.css') }}">
    <script src="{{ mix('js/customer.js') }}"></script>
    <script src="{{ mix('js/customer_user.js') }}"></script>
    <script src="{{ mix('js/customer_bank.js') }}"></script>
    <script src="{{ mix('js/customer_job.js') }}"></script>
    <script src="{{ mix('js/customer_manual_credit.js') }}"></script>
    <script src="{{ mix('js/customer_temppromotion.js') }}"></script>
    <script src="{{ mix('js/customer_activities_log.js') }}"></script>
    <script src="{{ mix('js/customer_error_log.js') }}"></script>

@endsection
