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
                            <li class="breadcrumb-item active" aria-current="page">รายการโอนเงิน</li>
                        </ol>
                    </nav>
                </div>
            </div>

            <div class="card">
                <div class="card-body">
                    {{-- @includeIf('default::default.filter') --}}
                    @include('statement::transfer.filter')

                    <div class="table-responsive">
                        <table id="data-datatable" class="table table-bordered w-100">
                            <thead>
                                <tr>
                                    <th scope="col">id</th>
                                    <th scope="col">job</th>
                                    <th scope="col">acc_id</th>
                                    <th scope="col">step</th>
                                    <th scope="col">status</th>
                                    <th scope="col">msg</th>
                                    <th scope="col">created_at</th>
                                    <th scope="col">updated_at</th>
                                    <th scope="col">action</th>
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
    <link rel="stylesheet" href="{{ mix('css/statement.css') }}">
    <script src="{{ mix('js/statement.transfer.js') }}"></script>
@endsection
