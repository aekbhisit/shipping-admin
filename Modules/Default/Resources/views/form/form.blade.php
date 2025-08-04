@extends('layouts.form')
@section('styles')

@endsection


@section('content')
<!--start page wrapper -->
<div class="page-wrapper">
    <div class="page-content">
        <!--breadcrumb-->
        <div class="page-breadcrumb d-none d-sm-flex align-items-center mb-3">
            <div class="breadcrumb-title pe-3">Module</div>
            <div class="ps-3">
                <nav aria-label="breadcrumb">
                    <ol class="breadcrumb mb-0 p-0">
                        <li class="breadcrumb-item">
<a href="{{ route('admin.homepage') }}"><i class="bx bx-home-alt"></i></a>
                        </li>
                        <li class="breadcrumb-item active" aria-current="page">Page</li>
                    </ol>
                </nav>
            </div>
            <div class="ms-auto">
                <div class="btn-group">
                    <a href="{{ route('admin.user.user.index') }}">
                    <button type="button" class="btn btn-primary"><i class="fadeIn animated bx bx-arrow-back"></i> กลับ</button>
                    </a>
                </div>
            </div>
        </div>
        <!--end breadcrumb-->
        <div class="row">
            <div class="col-xl-11 mx-auto">
                {{-- <h6 class="mb-0 text-uppercase">Basic Form</h6>
                <hr/> --}}
                <div class="card border-top border-0 border-4 border-primary">
                    <div class="card-body p-5">
                        <div class="card-title d-flex align-items-center">
                            <div><i class="bx bxs-user me-1 font-22 text-primary"></i>
                            </div>
                            <h5 class="mb-0 text-primary">User Registration</h5>
                        </div>
                        <hr>
                        <form class="row g-3" id="user_frm" name="user_frm" method="POST" onsubmit="setSave(); return false;" enctype="multipart/form-data">
                            @csrf
                            <input type="hidden" name="id" id="user_id" value="{{ !empty($user->id) ? $user->id : '0' }}">
                            {{-- field --}}

                            {{-- .field --}}
                            <div class="col-12 text-center">
                                <button type="submit" class="btn btn-info px-5"><i class="lni lni-save"></i> Save</button>
                                <a href="{{ route('admin.user.user.index') }}">
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
    <link rel="stylesheet" href="{{ mix('css/default.css') }}">
    <script src="{{ mix('js/default.js') }}"></script>
@endsection
