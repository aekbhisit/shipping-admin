@extends('layouts.datatable')

@section('title', 'Product Categories')

@section('content')
<div class="container-fluid">
    <!-- Page Header -->
    <div class="row">
        <div class="col-12">
            <div class="page-title-box d-sm-flex align-items-center justify-content-between">
                <h4 class="mb-sm-0">Product Categories</h4>
                <div class="page-title-right">
                    <ol class="breadcrumb m-0">
                        <li class="breadcrumb-item"><a href="{{ route('admin.dashboard.index') }}"><i class="bx bx-home-alt"></i></a></li>
                        <li class="breadcrumb-item active">Categories</li>
                    </ol>
                </div>
            </div>
        </div>
    </div>

    <!-- Alert Messages -->
    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    <!-- Main Content Card -->
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <div class="row align-items-center">
                        <div class="col">
                            <h5 class="card-title mb-0">Categories List</h5>
                        </div>
                        <div class="col-auto">
                            <a href="{{ route('admin.product-categories.create') }}" class="btn btn-primary">
                                <i class="bx bx-plus me-1"></i> Add Category
                            </a>
                        </div>
                    </div>
                </div>
                <div class="card-body">
                    <p class="text-muted">Category management interface will be implemented here.</p>
                    <p>Categories found: {{ $categories->count() }}</p>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection 