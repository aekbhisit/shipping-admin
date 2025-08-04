@extends('layouts.datatable')

@section('styles')
@endsection

@section('content')
    <!--start page wrapper -->
    <div class="page-wrapper">
        <div class="page-content">
            <!--breadcrumb-->
            <div class="page-breadcrumb d-none d-sm-flex align-items-center mb-3">
                <div class="breadcrumb-title pe-3">Customer Management</div>
                <div class="ps-3">
                    <nav aria-label="breadcrumb">
                        <ol class="breadcrumb mb-0 p-0">
                            <li class="breadcrumb-item">
                                <a href="{{ route('admin.homepage') }}"><i class="bx bx-home-alt"></i></a>
                            </li>
                            <li class="breadcrumb-item">
                                <a href="{{ route('admin.customers.index') }}">Customers</a>
                            </li>
                            <li class="breadcrumb-item active" aria-current="page">{{ $customer->name }}</li>
                        </ol>
                    </nav>
                </div>
                <div class="ms-auto">
                    <div class="btn-group">
                        <a href="{{ route('admin.customers.edit', $customer->id) }}" class="btn btn-warning">
                            <i class="bx bx-edit me-1"></i>Edit Customer
                        </a>
                        <a href="{{ route('admin.customers.index') }}" class="btn btn-secondary">
                            <i class="bx bx-arrow-back me-1"></i>Back to List
                        </a>
                    </div>
                </div>
            </div>
            <!--end breadcrumb-->

            <div class="row">
                <!-- Customer Details -->
                <div class="col-12">
                    <div class="card">
                        <div class="card-header">
                            <h5 class="card-title mb-0">
                                <i class="bx bx-user me-2"></i>Customer Details
                            </h5>
                        </div>
                        <div class="card-body">
                            <div class="row">
                                <div class="col-md-6">
                                    <table class="table table-borderless">
                                        <tr>
                                            <td width="30%"><strong>Customer Code:</strong></td>
                                            <td>{{ $customer->customer_code }}</td>
                                        </tr>
                                        <tr>
                                            <td><strong>Name:</strong></td>
                                            <td>{{ $customer->name }}</td>
                                        </tr>
                                        <tr>
                                            <td><strong>Type:</strong></td>
                                            <td>
                                                @if($customer->customer_type === 'business')
                                                    <span class="badge bg-primary">Business</span>
                                                @elseif($customer->customer_type === 'corporate')
                                                    <span class="badge bg-success">Corporate</span>
                                                @else
                                                    <span class="badge bg-info">Individual</span>
                                                @endif
                                            </td>
                                        </tr>
                                        <tr>
                                            <td><strong>Email:</strong></td>
                                            <td>{{ $customer->email ?? 'N/A' }}</td>
                                        </tr>
                                        <tr>
                                            <td><strong>Phone:</strong></td>
                                            <td>{{ $customer->phone ?? 'N/A' }}</td>
                                        </tr>
                                    </table>
                                </div>
                                <div class="col-md-6">
                                    <table class="table table-borderless">
                                        <tr>
                                            <td width="30%"><strong>Status:</strong></td>
                                            <td>
                                                @if($customer->is_active)
                                                    <span class="badge bg-success">Active</span>
                                                @else
                                                    <span class="badge bg-danger">Inactive</span>
                                                @endif
                                            </td>
                                        </tr>
                                        <tr>
                                            <td><strong>Branch:</strong></td>
                                            <td>{{ $customer->branch->name ?? 'N/A' }}</td>
                                        </tr>
                                        <tr>
                                            <td><strong>Created:</strong></td>
                                            <td>{{ $customer->created_at->format('d/m/Y H:i:s') }}</td>
                                        </tr>
                                        <tr>
                                            <td><strong>Last Updated:</strong></td>
                                            <td>{{ $customer->updated_at->format('d/m/Y H:i:s') }}</td>
                                        </tr>
                                        <tr>
                                            <td><strong>Tax ID:</strong></td>
                                            <td>{{ $customer->tax_id ?? 'N/A' }}</td>
                                        </tr>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Senders -->
                <div class="col-12 mt-4">
                    <div class="card">
                        <div class="card-header">
                            <h5 class="card-title mb-0">
                                <i class="bx bx-package me-2"></i>Senders ({{ $senders->count() }})
                            </h5>
                        </div>
                        <div class="card-body">
                            @if($senders->count() > 0)
                                <div class="table-responsive">
                                    <table class="table table-bordered table-striped">
                                        <thead>
                                            <tr>
                                                <th>Name</th>
                                                <th>Phone</th>
                                                <th>Email</th>
                                                <th>Default Address</th>
                                                <th>Status</th>
                                                <th>Actions</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @foreach($senders as $sender)
                                                <tr>
                                                    <td>{{ $sender->name }}</td>
                                                    <td>{{ $sender->phone ?? 'N/A' }}</td>
                                                    <td>{{ $sender->email ?? 'N/A' }}</td>
                                                    <td>
                                                        @if($sender->defaultAddress)
                                                            {{ $sender->defaultAddress->address_line_1 }}, {{ $sender->defaultAddress->city }}
                                                        @else
                                                            <span class="text-muted">No default address</span>
                                                        @endif
                                                    </td>
                                                    <td>
                                                        @if($sender->is_active)
                                                            <span class="badge bg-success">Active</span>
                                                        @else
                                                            <span class="badge bg-danger">Inactive</span>
                                                        @endif
                                                    </td>
                                                    <td>
                                                        <a href="#" class="btn btn-sm btn-info" title="View">
                                                            <i class="bx bx-show"></i>
                                                        </a>
                                                        <a href="#" class="btn btn-sm btn-warning" title="Edit">
                                                            <i class="bx bx-edit"></i>
                                                        </a>
                                                    </td>
                                                </tr>
                                            @endforeach
                                        </tbody>
                                    </table>
                                </div>
                            @else
                                <div class="text-center py-4">
                                    <i class="bx bx-package font-50 text-muted"></i>
                                    <p class="text-muted mt-2">No senders found for this customer.</p>
                                    <a href="#" class="btn btn-primary">
                                        <i class="bx bx-plus me-1"></i>Add Sender
                                    </a>
                                </div>
                            @endif
                        </div>
                    </div>
                </div>

                <!-- Receivers -->
                <div class="col-12 mt-4">
                    <div class="card">
                        <div class="card-header">
                            <h5 class="card-title mb-0">
                                <i class="bx bx-user-check me-2"></i>Receivers ({{ $receivers->count() }})
                            </h5>
                        </div>
                        <div class="card-body">
                            @if($receivers->count() > 0)
                                <div class="table-responsive">
                                    <table class="table table-bordered table-striped">
                                        <thead>
                                            <tr>
                                                <th>Name</th>
                                                <th>Phone</th>
                                                <th>Email</th>
                                                <th>Address</th>
                                                <th>Frequent</th>
                                                <th>Actions</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @foreach($receivers as $receiver)
                                                <tr>
                                                    <td>{{ $receiver->name }}</td>
                                                    <td>{{ $receiver->phone ?? 'N/A' }}</td>
                                                    <td>{{ $receiver->email ?? 'N/A' }}</td>
                                                    <td>
                                                        @if($receiver->address)
                                                            {{ $receiver->address->address_line_1 }}, {{ $receiver->address->city }}
                                                        @else
                                                            <span class="text-muted">No address</span>
                                                        @endif
                                                    </td>
                                                    <td>
                                                        @if($receiver->is_frequent)
                                                            <span class="badge bg-warning">Frequent</span>
                                                        @else
                                                            <span class="badge bg-secondary">Occasional</span>
                                                        @endif
                                                    </td>
                                                    <td>
                                                        <a href="#" class="btn btn-sm btn-info" title="View">
                                                            <i class="bx bx-show"></i>
                                                        </a>
                                                        <a href="#" class="btn btn-sm btn-warning" title="Edit">
                                                            <i class="bx bx-edit"></i>
                                                        </a>
                                                    </td>
                                                </tr>
                                            @endforeach
                                        </tbody>
                                    </table>
                                </div>
                            @else
                                <div class="text-center py-4">
                                    <i class="bx bx-user-check font-50 text-muted"></i>
                                    <p class="text-muted mt-2">No receivers found for this customer.</p>
                                    <a href="#" class="btn btn-primary">
                                        <i class="bx bx-plus me-1"></i>Add Receiver
                                    </a>
                                </div>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <!--end page wrapper -->
@endsection

@section('scripts')
@endsection 