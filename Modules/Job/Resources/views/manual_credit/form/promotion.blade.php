@extends('layouts.form')
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
                        <li class="breadcrumb-item " aria-current="page"><a href="{{ route("admin.job.job.index") }}">ใบงาน</a></li>
                        <li class="breadcrumb-item " aria-current="page"><a href="{{ route("admin.job.manualcredit.index") }}">เครดิตมือ</a></li>
                        <li class="breadcrumb-item active" aria-current="page">เพิ่มโปรโมชั่น</li>
                    </ol>
                </nav>
            </div>
            <div class="ms-auto">
                <div class="btn-group">
                    <a href="{{ route('admin.job.manualcredit.index') }}">
                    <button type="button" class="btn btn-primary"><i class="fadeIn animated bx bx-arrow-back"></i> กลับ</button>
                    </a>
                </div>
            </div>
        </div>
        <!--end breadcrumb-->
        <div class="row">
            <div class="col-xl-6 mx-auto">
                {{-- <h6 class="mb-0 text-uppercase">Basic Form</h6>
                <hr/> --}}
                <div class="card border-top border-0 border-4 border-primary">
                    <div class="card-body p-5">
                        <div class="card-title d-flex align-items-center">
                            <div><i class="bx bxs-user me-1 font-22 text-primary"></i>
                            </div>
                            <h5 class="mb-0 text-primary"> ++ เพิ่มโปรโมชั่น</h5>
                        </div>
                        <hr>
                        <form class="row g-3" id="manual_credit_frm" name="manual_credit_frm" method="POST" onsubmit="setSave(); return false;" enctype="multipart/form-data">
                            @csrf
                            <input type="hidden" name="job_type" id="job_type" value="1">
                            {{-- field --}}
                            <div class="col-12">
                                <label class="form-label">ยูเซอร์</label>
                                <select id="ref_code" name="ref_code" class="form-control select2 form-select" 
                                    <option value="" >โปรโมชั่น</option>
                                        <?php 
                                        foreach($promotions as $pro){
                                        ?>
                                            <option value="<?=$pro->id?>" ><?=$pro->pro_name?></option>
                                        <?php 
                                        }
                                        ?>
                                </select>
                            </div>

                            <div class="form-group frm-name">
                                <label class="form-label">ยูเซอร์</label>
                                <input type="text" class="form-control" name="username" id="username" placeholder="username1,username2,username3" value="">
                            </div>

                            <div class="form-group frm-name">
                                <label class="form-label">ยอดเงิน</label>
                                <input type="text" class="form-control" name="amount" id="amount" placeholder="ยอดเงิน" value="">
                            </div>

                            <div class="form-group frm-name">
                                <label class="form-label">เหตุผล</label>
                                <input type="text" class="form-control" name="reason" id="reason" placeholder="เหตุผล" value="">
                            </div>


                            {{-- .field --}}
                            <div class="col-12 text-center">
                                <button type="submit" class="btn btn-info px-5"><i class="lni lni-save"></i> Save</button>
                                <a href="{{ route('admin.job.manualcredit.index') }}">
                                    <button type="button" class="btn btn-warning px-5"><i class="lni lni-close"></i> Cancel</button>
                                </a>
                            </div>
                        </form>
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
    <link rel="stylesheet" href="{{ mix('css/job.css') }}">
    <script src="{{ mix('js/manual_credit.js') }}"></script>
@endsection
