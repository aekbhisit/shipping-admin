@extends('layouts.datatable')
@section('styles')
    
@endsection

@section('content')
    
    <!--start page wrapper -->
    <div class="page-wrapper">
        <div class="page-content">
            <!--breadcrumb-->
            <div class="page-breadcrumb d-none d-sm-flex align-items-center mb-3">
                <div class="breadcrumb-title pe-3">ใบงาน</div>
                <div class="ps-3">
                    <nav aria-label="breadcrumb">
                        <ol class="breadcrumb mb-0 p-0">
                            <li class="breadcrumb-item"><a href="javascript:;"><i class="bx bx-home-alt"></i></a>
                            </li>
                            <li class="breadcrumb-item" aria-current="page">ใบงาน</li>
                            <li class="breadcrumb-item active" aria-current="page"><?=$show_status_text?></li>
                        </ol>
                    </nav>
                </div>
                <div class="ms-auto">
                    <div class="btn-group">
                        {{-- <div onclick="testTimeAgo();" id="time_ago">time ago</div>
                        <a href="javascript:void(0);" onclick="setAddJobRow()" target="_blank">
                            <button type="button" class="btn btn-info"><i class="lni lni-plus"></i> Datatable Add</button>
                        </a>
                        <a href="{{ route('admin.job.job.test_pusher') }}" target="_blank">
                            <button type="button" class="btn btn-info"><i class="lni lni-plus"></i> Pusher Add</button>
                        </a>
                        <a href="{{ route('admin.job.job.test_locked_job') }}" target="_blank">
                            <button type="button" class="btn btn-info"><i class="lni lni-plus"></i> Pusher Locked</button>
                        </a> --}}
                    </div>
                </div>
            </div>

            <!--end breadcrumb-->
          
            <div class="card">
                <div class="card-body">
                    @includeIf('job::job.table.filter')
                    <div class="table-responsive">
                        <table id="job-datatable" attr-status="<?=$show_status?>" class="table table-bordered mb-0  table-responsive">
                            <thead>
                                <tr>
                                    <th scope="col">#</th>
                                    <th scope="col">ประเภท</th>
                                    <th scope="col">รหัส</th>
                                    <th scope="col">ลูกค้า</th>
                                    <th scope="col">ยูสเซอร์</th>
                                    <th scope="col">ยอดเงิน</th>
                                    <th scope="col">ธนาคาร</th>
                                    <th scope="col">สถานะ</th>
                                    <th scope="col">สร้างโดย</th>
                                    <th scope="col">ล็อกโดย</th>
                                    <th scope="col">สร้างเมื่อ</th>
                                    <th scope="col">manage</th>
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
    <script src="{{ mix('js/job.js') }}"></script>
    {{-- <script src="{{ mix('js/pusher.js') }}"></script> --}}
  
@endsection
