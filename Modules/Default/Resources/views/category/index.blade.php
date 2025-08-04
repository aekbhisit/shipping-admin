@extends('layouts.datatable')
@section('styles')
   
@endsection

@section('content')
        
    <!--start page wrapper -->
    <div class="page-wrapper">
        <div class="page-content">
            <!--breadcrumb-->
            <div class="page-breadcrumb d-none d-sm-flex align-items-center mb-3">
                <div class="breadcrumb-title pe-3">Tables</div>
                <div class="ps-3">
                    <nav aria-label="breadcrumb">
                        <ol class="breadcrumb mb-0 p-0">
                            <li class="breadcrumb-item">
<a href="{{ route('admin.homepage') }}"><i class="bx bx-home-alt"></i></a>
                            </li>
                            <li class="breadcrumb-item active" aria-current="page">Data Table</li>
                        </ol>
                    </nav>
                </div>
                <div class="ms-auto">
                    <div class="btn-group">
                        <a href="{{ route('admin.default.category.add') }}">
                            <button type="button" class="btn btn-info"><i class="lni lni-plus me-1"></i> Add</button>
                        </a>
                    </div>
                </div>
            </div>

            <!--end breadcrumb-->
            <h6 class="mb-0 text-uppercase">DataTable Example</h6>
            <hr/>
            <div class="card">
                <div class="card-body">
                    @includeIf('default::category.filter')
                    
                    <div class="table-responsive">
                       <table id="category-datatable" data-type="" class="table table-bordered key-buttons text-nowrap w-100">
                            <thead>
                                <tr>
                                    <th class="border-bottom-0" width="5%">{{__('default::category.datatable.sequence')}}</th>
                                    <th class="border-bottom-0" width="15%">{{__('default::category.datatable.image')}}</th>
                                    <th class="border-bottom-0" width="">{{__('default::category.datatable.name')}}</th>
                                    <th class="border-bottom-0" width="10%">{{__('default::category.datatable.sort')}}</th>
                                    <th class="border-bottom-0" width="15%">{{__('default::category.datatable.updated_at')}}</th>
                                    <th class="border-bottom-0" width="15%">{{__('default::category.datatable.manage')}}</th>
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
    <link rel="stylesheet" href="{{ mix('css/default.css') }}">
    <script src="{{ mix('js/default_category.js') }}"></script>
  
@endsection
