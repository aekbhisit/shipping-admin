@extends('layouts.datatable')
@section('styles')
    
@endsection

@section('content')
        
    <!--start page wrapper -->
    <div class="page-wrapper">
        <div class="page-content">
            <!--breadcrumb-->
            <div class="page-breadcrumb d-none d-sm-flex align-items-center mb-3">
                <div class="breadcrumb-title pe-3">เครดิตมือ</div>
                <div class="ps-3">
                    <nav aria-label="breadcrumb">
                        <ol class="breadcrumb mb-0 p-0">
                            <li class="breadcrumb-item"><a href="javascript:;"><i class="bx bx-home-alt"></i></a>
                            </li>
                            <li class="breadcrumb-item active" aria-current="page">รายการเครดิตมือ</li>
                        </ol>
                    </nav>
                </div>
                <div class="ms-auto">
                    <div class="btn-group">
                        {{-- <a href="{{ route('admin.customer.customer.add') }}">
                            <button type="button" class="btn btn-info"><i class="lni lni-plus"></i> Add</button>
                        </a> --}}
                    </div>
                </div>
            </div>

            <!--end breadcrumb-->
            @includeIf('job::manual_credit.table.tools')

            <div class="card">
                <div class="card-body">
                   

                    @includeIf('job::manual_credit.table.filter')
                    
                    <div class="table-responsive">
                        <table id="manual-credit-datatable" class="table table-bordered mb-0">
                            <thead>
                                <tr>
                                    <th scope="col">#</th>
                                    <th scope="col">Job ID</th>
                                    <th scope="col">ประเภท</th>
                                    <th scope="col">ลูกค้า</th>
                                    <th scope="col">ยูเซอร์</th>
                                    <th scope="col">ยอดเงิน</th>
                                    <th scope="col">เหตุผล</th>
                                    <th scope="col">Ref Code</th>
                                    <th scope="col">สถานะ</th>
                                    <th scope="col">สร้างโดย</th>
                                    <th scope="col">อัพเดทเมื่อ</th>
                                    <th scope="col" >จัดการ</th>
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
     <link rel="stylesheet" href="{{ mix('css/job.css') }}">
    <script src="{{ mix('js/manual_credit.js') }}"></script>
  
@endsection
