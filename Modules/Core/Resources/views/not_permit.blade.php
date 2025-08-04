@extends('layouts.datatable')
@section('styles')
    
@endsection

@section('content')
        
    <!--start page wrapper -->
    <div class="page-wrapper">
        <div class="page-content">
            <!--breadcrumb-->
            <div class="page-breadcrumb d-none d-sm-flex align-items-center mb-3">
                <div class="breadcrumb-title pe-3">Not Premit</div>
                <div class="ps-3">
                    <nav aria-label="breadcrumb">
                        <ol class="breadcrumb mb-0 p-0">
                            <li class="breadcrumb-item">
                                <a href="{{ route('admin.homepage') }}"><i class="bx bx-home-alt"></i></a>
                            </li>
                            <li class="breadcrumb-item active" aria-current="page">Not Premit</li>
                        </ol>
                    </nav>
                </div>
               
            </div>

            <!--end breadcrumb-->
            <h6 class="mb-0 text-uppercase">Not Premit</h6>
            <hr/>
            <div class="card">
                <div class="card-body">
                    Not Premit
                </div>
            </div>
            
        </div>
    </div>
    <!--end page wrapper -->

@endsection

@section('scripts')
    
    <!-- module js css -->
    {{-- <link rel="stylesheet" href="{{ mix('css/default.css') }}">
    <script src="{{ mix('js/default.js') }}"></script> --}}
  
@endsection
