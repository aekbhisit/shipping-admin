@extends('layouts.app')
@section('styles')
@endsection

@section('content')
    <div class="page-wrapper">
        <div class="page-content">
            <!--shipments overview-->
            <div class="row">
                <div class="col-12 col-lg-9 col-xl-9">
                    <div class="card radius-10 overflow-hidden">
                        <div class="card-body">
                            <div class="d-flex align-items-center">
                                <div>
                                    <h6 class="mb-0">ภาพรวมการจัดส่ง</h6>
                                </div>
                                <div class="font-22 ms-auto"><i class='bx bx-dots-horizontal-rounded'></i>
                                </div>
                            </div>
                            <div class="chart-container">
                                <div class="text-center" id="shipment_graph">

                                </div>
                                <div class="text-center py-5" id="shipment_spinner">
                                    <div class="spinner-border" role="status">
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-12 col-lg-3 col-xl-3" id="shipment_stats">
                    <div class="col">
                        <div class="card radius-10 bg-primary">
                            <div class="card-body">
                                <div class="d-flex align-items-center">
                                    <div>
                                        <p class="mb-0 text-white">จัดส่งวันนี้</p>
                                        <h4 class="my-1 text-white"><span id="shipment_day_new"></span> ชิ้น</h4>
                                        <p class="mb-0 font-13 text-white">
                                            <i id="shipment_day_trend" class="bx bxs-up-arrow align-middle"></i><span
                                                id="shipment_day_change"></span>
                                            จากวันก่อน
                                        </p>
                                    </div>
                                    <div class="widgets-icons bg-light-transparent text-white ms-auto"><i
                                            class="bx bxs-package"></i>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col">
                        <div class="card radius-10 bg-danger">
                            <div class="card-body">
                                <div class="d-flex align-items-center">
                                    <div>
                                        <p class="mb-0 text-white">จัดส่งอาทิตย์นี้</p>
                                        <h4 class="my-1 text-white"><span id="shipment_week_new"></span> ชิ้น</h4>
                                        <p class="mb-0 font-13 text-white"><i id="shipment_week_trend"
                                                class="bx bxs-up-arrow align-middle"></i><span id="shipment_week_change"></span>
                                            จากอาทิตย์ก่อน</p>
                                    </div>
                                    <div class="widgets-icons bg-light-transparent text-white ms-auto"><i
                                            class="bx bxs-truck"></i>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col">
                        <div class="card radius-10 bg-success">
                            <div class="card-body">
                                <div class="d-flex align-items-center">
                                    <div>
                                        <p class="mb-0 text-white">จัดส่งเดือนนี้</p>
                                        <h4 class="my-1 text-white">
                                            <span id="shipment_month_new"></span> ชิ้น
                                        </h4>
                                        <p class="mb-0 font-13 text-white">
                                            <i id="shipment_month_trend" class="bx bxs-up-arrow align-middle"></i>
                                            <span id="shipment_month_change"></span>
                                            จากเดือนก่อน
                                        </p>
                                    </div>
                                    <div class="widgets-icons bg-light-transparent text-white ms-auto"><i
                                            class="bx bxs-calendar"></i>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <!--end shipments overview-->
            <hr>
            
            <!--revenue analytics-->
            <div class="row">
                <div class="col-12 col-lg-9 col-xl-9">
                    <div class="card radius-10 overflow-hidden">
                        <div class="card-body">
                            <div class="d-flex align-items-center">
                                <div>
                                    <h6 class="mb-0">รายได้จากการจัดส่ง</h6>
                                </div>
                                <div class="font-22 ms-auto"><i class='bx bx-dots-horizontal-rounded'></i>
                                </div>
                            </div>
                            <div class="chart-container">
                                <div class="text-center" id="revenue_graph"></div>
                                <div class="text-center py-5" id="revenue_spinner">
                                    <div class="spinner-border" role="status">
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-12 col-lg-3 col-xl-3">
                    <div class="col">
                        <div class="card radius-10 bg-warning">
                            <div class="card-body">
                                <div class="d-flex align-items-center">
                                    <div>
                                        <p class="mb-0 text-white">รายได้วันนี้</p>
                                        <h4 class="my-1 text-white">
                                            ฿<span id="revenue_day_new"></span>
                                        </h4>
                                        <p class="mb-0 font-13 text-white">
                                            <i id="revenue_day_trend" class="bx bxs-up-arrow align-middle"></i>
                                            <span id="revenue_day_change"></span>%
                                            จากวันก่อน
                                        </p>
                                    </div>
                                    <div class="widgets-icons bg-light-transparent text-white ms-auto"><i
                                            class="bx bxs-dollar-circle"></i>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col">
                        <div class="card radius-10 bg-info">
                            <div class="card-body">
                                <div class="d-flex align-items-center">
                                    <div>
                                        <p class="mb-0 text-white">รายได้อาทิตย์นี้</p>
                                        <h4 class="my-1 text-white">
                                            ฿<span id="revenue_week_new"></span>
                                        </h4>
                                        <p class="mb-0 font-13 text-white">
                                            <i id="revenue_week_trend" class="bx bxs-up-arrow align-middle"></i>
                                            <span id="revenue_week_change"></span>%
                                            จากอาทิตย์ก่อน
                                        </p>
                                    </div>
                                    <div class="widgets-icons bg-light-transparent text-white ms-auto"><i
                                            class="bx bxs-wallet"></i>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col">
                        <div class="card radius-10 bg-dark">
                            <div class="card-body">
                                <div class="d-flex align-items-center">
                                    <div>
                                        <p class="mb-0 text-white">รายได้เดือนนี้</p>
                                        <h4 class="my-1 text-white">
                                            ฿<span id="revenue_month_new"></span>
                                        </h4>
                                        <p class="mb-0 font-13 text-white">
                                            <i id="revenue_month_trend" class="bx bxs-up-arrow align-middle"></i>
                                            <span id="revenue_month_change"></span>%
                                            จากเดือนก่อน
                                        </p>
                                    </div>
                                    <div class="widgets-icons bg-light-transparent text-white ms-auto"><i
                                            class="bx bxs-credit-card"></i>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <!--end revenue analytics-->
            <hr>
            
            <!--branch performance -->
            <div class="row">
                <div class="col-12 col-lg-8 col-xl-8">
                    <div class="card radius-10 overflow-hidden">
                        <div class="card-body">
                            <div class="d-flex align-items-center">
                                <div>
                                    <h6 class="mb-0">ประสิทธิภาพสาขา</h6>
                                </div>
                                <div class="font-22 ms-auto"><i class='bx bx-dots-horizontal-rounded'></i>
                                </div>
                            </div>
                            <div class="chart-container">
                                <div class="text-center" id="branch_performance_graph"></div>
                                <div class="text-center py-5" id="branch_performance_spinner">
                                    <div class="spinner-border" role="status">
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-12 col-lg-4 col-xl-4">
                    <div class="card radius-10">
                        <div class="card-body">
                            <div class="d-flex align-items-center">
                                <div>
                                    <h6 class="mb-0">สาขาที่ดีที่สุด</h6>
                                </div>
                            </div>
                            <div class="mt-3" id="top_branches">
                                <!-- Top performing branches will be loaded here -->
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <!--end branch performance-->
            <hr>
            
            <!--carrier analytics & recent activity-->
            <div class="row">
                <div class="col-12 col-lg-6 col-xl-6">
                    <div class="card radius-10 overflow-hidden">
                        <div class="card-body">
                            <div class="d-flex align-items-center">
                                <div>
                                    <h6 class="mb-0">การใช้งานผู้ให้บริการขนส่ง</h6>
                                </div>
                                <div class="font-22 ms-auto"><i class='bx bx-dots-horizontal-rounded'></i>
                                </div>
                            </div>
                            <div class="chart-container">
                                <div class="text-center" id="carrier_usage_chart"></div>
                                <div class="text-center py-5" id="carrier_spinner">
                                    <div class="spinner-border" role="status">
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-12 col-lg-6 col-xl-6">
                    <div class="card radius-10">
                        <div class="card-body">
                            <div class="d-flex align-items-center">
                                <div>
                                    <h6 class="mb-0">กิจกรรมล่าสุด</h6>
                                </div>
                                <div class="font-22 ms-auto"><i class='bx bx-dots-horizontal-rounded'></i>
                                </div>
                            </div>
                            <div class="timeline mt-3" id="recent_activities">
                                <!-- Recent activities will be loaded here -->
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <!--end carrier analytics & recent activity-->
            <hr>
            
            <!--shipment status overview-->
            <div class="row">
                <div class="col-12">
                    <div class="card radius-10">
                        <div class="card-body">
                            <div class="d-flex align-items-center">
                                <div>
                                    <h6 class="mb-0">สถานะการจัดส่งปัจจุบัน</h6>
                                </div>
                                <div class="font-22 ms-auto"><i class='bx bx-dots-horizontal-rounded'></i>
                                </div>
                            </div>
                            <div class="row mt-3">
                                <div class="col-md-2">
                                    <div class="card border-0 bg-light-primary">
                                        <div class="card-body text-center">
                                            <div class="font-35 text-primary mb-2"><i class="bx bxs-edit"></i></div>
                                            <h5 class="mb-0 text-primary" id="status_draft">0</h5>
                                            <p class="mb-0">แบบร่าง</p>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-2">
                                    <div class="card border-0 bg-light-warning">
                                        <div class="card-body text-center">
                                            <div class="font-35 text-warning mb-2"><i class="bx bxs-quote-alt-right"></i></div>
                                            <h5 class="mb-0 text-warning" id="status_quoted">0</h5>
                                            <p class="mb-0">ใบเสนอราคา</p>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-2">
                                    <div class="card border-0 bg-light-info">
                                        <div class="card-body text-center">
                                            <div class="font-35 text-info mb-2"><i class="bx bxs-check-circle"></i></div>
                                            <h5 class="mb-0 text-info" id="status_confirmed">0</h5>
                                            <p class="mb-0">ยืนยันแล้ว</p>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-2">
                                    <div class="card border-0 bg-light-secondary">
                                        <div class="card-body text-center">
                                            <div class="font-35 text-secondary mb-2"><i class="bx bxs-car"></i></div>
                                            <h5 class="mb-0 text-secondary" id="status_picked_up">0</h5>
                                            <p class="mb-0">เก็บแล้ว</p>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-2">
                                    <div class="card border-0 bg-light-dark">
                                        <div class="card-body text-center">
                                            <div class="font-35 text-dark mb-2"><i class="bx bxs-truck"></i></div>
                                            <h5 class="mb-0 text-dark" id="status_in_transit">0</h5>
                                            <p class="mb-0">ระหว่างขนส่ง</p>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-2">
                                    <div class="card border-0 bg-light-success">
                                        <div class="card-body text-center">
                                            <div class="font-35 text-success mb-2"><i class="bx bxs-package"></i></div>
                                            <h5 class="mb-0 text-success" id="status_delivered">0</h5>
                                            <p class="mb-0">จัดส่งแล้ว</p>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <!--end shipment status overview-->

        </div>
    </div>
@endsection('content')

@section('scripts')
    <script src="{{ URL::asset('assets/plugins/apexcharts-bundle/js/apexcharts.min.js') }}"></script>
    <!-- admin js css -->
    <link rel="stylesheet" href="{{ mix('css/admin.css') }}">
    <script src="{{ mix('js/admin.js') }}"></script>

    <!-- module js css -->
    <link rel="stylesheet" href="{{ asset('css/dashboard.css') }}">
    <script src="{{ asset('js/dashboard.js') }}"></script>
@endsection
