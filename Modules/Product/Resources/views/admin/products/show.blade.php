@extends('layouts.datatable')

@section('title', 'Product Details')

@section('content')
<div class="container-fluid">
    <!-- Page Header -->
    <div class="row">
        <div class="col-12">
            <div class="page-title-box d-sm-flex align-items-center justify-content-between">
                <h4 class="mb-sm-0">Product Details</h4>
                <div class="page-title-right">
                    <ol class="breadcrumb m-0">
                        <li class="breadcrumb-item"><a href="{{ route('admin.dashboard.index') }}"><i class="bx bx-home-alt"></i></a></li>
                        <li class="breadcrumb-item"><a href="{{ route('admin.products.index') }}">Products</a></li>
                        <li class="breadcrumb-item active">Details</li>
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

    @if(session('error'))
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            {{ session('error') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    <!-- Action Buttons -->
    <div class="row mb-3">
        <div class="col-12">
            <div class="d-flex justify-content-end gap-2">
                <a href="{{ route('admin.products.edit', $product) }}" class="btn btn-info">
                    <i class="bx bx-edit me-1"></i> Edit Product
                </a>
                <a href="{{ route('admin.products.index') }}" class="btn btn-secondary">
                    <i class="bx bx-arrow-back me-1"></i> Back to List
                </a>
            </div>
        </div>
    </div>

    <!-- Main Content -->
    <div class="row">
        <!-- Product Details -->
        <div class="col-lg-8">
            <div class="card">
                <div class="card-header">
                    <h5 class="card-title mb-0">Product Information</h5>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6">
                            <table class="table table-borderless">
                                <tr>
                                    <td class="fw-bold" width="40%">Product Name:</td>
                                    <td>{{ $product->name }}</td>
                                </tr>
                                <tr>
                                    <td class="fw-bold">SKU:</td>
                                    <td>{{ $product->sku }}</td>
                                </tr>
                                <tr>
                                    <td class="fw-bold">Category:</td>
                                    <td>{{ $product->category->name ?? 'No Category' }}</td>
                                </tr>
                                <tr>
                                    <td class="fw-bold">Price:</td>
                                    <td>฿{{ number_format($product->price, 2) }}</td>
                                </tr>
                                <tr>
                                    <td class="fw-bold">Unit:</td>
                                    <td>{{ $product->unit }}</td>
                                </tr>
                            </table>
                        </div>
                        <div class="col-md-6">
                            <table class="table table-borderless">
                                <tr>
                                    <td class="fw-bold" width="40%">Status:</td>
                                    <td>{!! $product->status_badge !!}</td>
                                </tr>
                                <tr>
                                    <td class="fw-bold">Weight:</td>
                                    <td>{{ $product->weight ? $product->weight . ' kg' : 'Not specified' }}</td>
                                </tr>
                                <tr>
                                    <td class="fw-bold">Dimensions:</td>
                                    <td>{{ $product->dimensions_string }}</td>
                                </tr>
                                <tr>
                                    <td class="fw-bold">Sort Order:</td>
                                    <td>{{ $product->sort_order }}</td>
                                </tr>
                                <tr>
                                    <td class="fw-bold">Created:</td>
                                    <td>{{ $product->created_at->format('M d, Y H:i') }}</td>
                                </tr>
                            </table>
                        </div>
                    </div>

                    @if($product->description)
                        <div class="mt-3">
                            <h6 class="fw-bold">Description:</h6>
                            <p class="text-muted">{{ $product->description }}</p>
                        </div>
                    @endif

                    @if($product->image_path)
                        <div class="mt-3">
                            <h6 class="fw-bold">Product Image:</h6>
                            <img src="{{ Storage::url($product->image_path) }}" alt="{{ $product->name }}" class="img-thumbnail" style="max-width: 300px;">
                        </div>
                    @endif
                </div>
            </div>
        </div>

        <!-- Branch Availability -->
        <div class="col-lg-4">
            <div class="card">
                <div class="card-header">
                    <h5 class="card-title mb-0">Branch Availability</h5>
                </div>
                <div class="card-body">
                    @if($product->branchProducts->count() > 0)
                        <div class="table-responsive">
                            <table class="table table-sm">
                                <thead>
                                    <tr>
                                        <th>Branch</th>
                                        <th>Available</th>
                                        <th>Price</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($product->branchProducts as $branchProduct)
                                        <tr>
                                            <td>{{ $branchProduct->branch->name }}</td>
                                            <td>
                                                @if($branchProduct->is_available)
                                                    <span class="badge bg-success">Yes</span>
                                                @else
                                                    <span class="badge bg-danger">No</span>
                                                @endif
                                            </td>
                                            <td>
                                                @if($branchProduct->branch_price)
                                                    ฿{{ number_format($branchProduct->branch_price, 2) }}
                                                @else
                                                    <span class="text-muted">Global</span>
                                                @endif
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @else
                        <p class="text-muted mb-0">No branch-specific settings found.</p>
                    @endif
                </div>
            </div>
        </div>
    </div>

    <!-- Action Buttons -->
    <div class="row mt-3">
        <div class="col-12">
            <div class="d-flex justify-content-end gap-2">
                <a href="{{ route('admin.products.index') }}" class="btn btn-secondary px-5">Back to List</a>
                <a href="{{ route('admin.products.edit', $product) }}" class="btn btn-primary px-5">Edit Product</a>
            </div>
        </div>
    </div>
</div>
@endsection 