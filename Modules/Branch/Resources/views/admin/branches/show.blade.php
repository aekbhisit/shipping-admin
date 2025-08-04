@extends('layouts.datatable')

@section('styles')
@endsection

@section('content')
    <!--start page wrapper -->
    <div class="page-wrapper">
        <div class="page-content">
            <!--breadcrumb-->
            <div class="page-breadcrumb d-none d-sm-flex align-items-center mb-3">
                <div class="breadcrumb-title pe-3">Branch Details</div>
                <div class="ps-3">
                    <nav aria-label="breadcrumb">
                        <ol class="breadcrumb mb-0 p-0">
                            <li class="breadcrumb-item">
                                <a href="{{ route('admin.dashboard.index') }}"><i class="bx bx-home-alt"></i></a>
                            </li>
                            <li class="breadcrumb-item">
                                <a href="{{ route('admin.branches.index') }}">Branches</a>
                            </li>
                            <li class="breadcrumb-item active" aria-current="page">{{ $branch->name }}</li>
                        </ol>
                    </nav>
                </div>
                <div class="ms-auto">
                    <div class="btn-group">
                        <a href="{{ route('admin.branches.edit', $branch) }}">
                            <button type="button" class="btn btn-warning"><i class="bx bx-edit me-1"></i>Edit Branch</button>
                        </a>
                        <a href="{{ route('admin.branches.markups', $branch) }}">
                            <button type="button" class="btn btn-info"><i class="bx bx-dollar-circle me-1"></i>Manage Markups</button>
                        </a>
                        <a href="{{ route('admin.branches.index') }}">
                            <button type="button" class="btn btn-secondary"><i class="bx bx-arrow-back me-1"></i>Back to List</button>
                        </a>
                    </div>
                </div>
            </div>
            <!--end breadcrumb-->

            <div class="row">
                <!-- Branch Information -->
                <div class="col-lg-8">
                    <div class="card">
                        <div class="card-header">
                            <h5 class="mb-0"><i class="bx bx-building me-2"></i>Branch Information</h5>
                        </div>
                        <div class="card-body">
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label class="form-label fw-bold">Branch Name</label>
                                        <p class="form-control-plaintext">{{ $branch->name }}</p>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label class="form-label fw-bold">Branch Code</label>
                                        <p class="form-control-plaintext">
                                            <span class="badge bg-secondary">{{ $branch->code }}</span>
                                        </p>
                                    </div>
                                </div>
                            </div>
                            
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label class="form-label fw-bold">Contact Person</label>
                                        <p class="form-control-plaintext">{{ $branch->contact_person }}</p>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label class="form-label fw-bold">Phone</label>
                                        <p class="form-control-plaintext">
                                            <i class="bx bx-phone me-1"></i>{{ $branch->formatted_phone }}
                                        </p>
                                    </div>
                                </div>
                            </div>
                            
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label class="form-label fw-bold">Email</label>
                                        <p class="form-control-plaintext">
                                            <i class="bx bx-envelope me-1"></i>{{ $branch->email }}
                                        </p>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label class="form-label fw-bold">Status</label>
                                        <p class="form-control-plaintext">
                                            <span class="{{ $branch->status_badge }}">{{ $branch->status_text }}</span>
                                        </p>
                                    </div>
                                </div>
                            </div>
                            
                            <div class="mb-3">
                                <label class="form-label fw-bold">Address</label>
                                <p class="form-control-plaintext">{{ $branch->address }}</p>
                            </div>
                            
                            <div class="mb-3">
                                <label class="form-label fw-bold">Operating Hours</label>
                                @php
                                    $operatingHours = is_array($branch->operating_hours) ? $branch->operating_hours : [];
                                    $days = [
                                        'monday' => 'Monday',
                                        'tuesday' => 'Tuesday', 
                                        'wednesday' => 'Wednesday',
                                        'thursday' => 'Thursday',
                                        'friday' => 'Friday',
                                        'saturday' => 'Saturday',
                                        'sunday' => 'Sunday'
                                    ];
                                @endphp
                                
                                @if(!empty($operatingHours))
                                    <div class="table-responsive">
                                        <table class="table table-sm table-borderless">
                                            <tbody>
                                                @foreach($days as $dayKey => $dayName)
                                                    @php
                                                        $dayData = isset($operatingHours[$dayKey]) && is_array($operatingHours[$dayKey]) ? $operatingHours[$dayKey] : [];
                                                        $isOpen = isset($dayData['open']) && !empty($dayData['open']) && isset($dayData['close']) && !empty($dayData['close']);
                                                    @endphp
                                                    <tr>
                                                        <td class="fw-bold" style="width: 100px;">{{ $dayName }}</td>
                                                        <td>
                                                            @if($isOpen)
                                                                <span class="badge bg-success">
                                                                    <i class="bx bx-time me-1"></i>
                                                                    {{ \Carbon\Carbon::parse($dayData['open'])->format('g:i A') }} - 
                                                                    {{ \Carbon\Carbon::parse($dayData['close'])->format('g:i A') }}
                                                                </span>
                                                            @else
                                                                <span class="badge bg-secondary">
                                                                    <i class="bx bx-x me-1"></i>Closed
                                                                </span>
                                                            @endif
                                                        </td>
                                                    </tr>
                                                @endforeach
                                            </tbody>
                                        </table>
                                    </div>
                                @else
                                    <p class="text-muted">No operating hours set</p>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Branch Statistics -->
                <div class="col-lg-4">
                    <div class="card">
                        <div class="card-header">
                            <h5 class="mb-0"><i class="bx bx-bar-chart me-2"></i>Branch Statistics</h5>
                        </div>
                        <div class="card-body">
                            @php
                                $stats = $branch->getStats();
                                $performance = $branch->getPerformanceMetrics();
                            @endphp
                            
                            <div class="row text-center">
                                <div class="col-6 mb-3">
                                    <div class="border rounded p-3">
                                        <h4 class="text-primary mb-1">{{ $stats['total_users'] }}</h4>
                                        <small class="text-muted">Total Users</small>
                                    </div>
                                </div>
                                <div class="col-6 mb-3">
                                    <div class="border rounded p-3">
                                        <h4 class="text-success mb-1">{{ $stats['active_users'] }}</h4>
                                        <small class="text-muted">Active Users</small>
                                    </div>
                                </div>
                                <div class="col-6 mb-3">
                                    <div class="border rounded p-3">
                                        <h4 class="text-info mb-1">{{ $stats['total_markups'] }}</h4>
                                        <small class="text-muted">Total Markups</small>
                                    </div>
                                </div>
                                <div class="col-6 mb-3">
                                    <div class="border rounded p-3">
                                        <h4 class="text-warning mb-1">{{ $stats['active_markups'] }}</h4>
                                        <small class="text-muted">Active Markups</small>
                                    </div>
                                </div>
                            </div>
                            
                            <hr>
                            
                            <div class="text-center">
                                <h6 class="text-muted mb-2">30-Day Performance</h6>
                                <h3 class="text-success mb-1">฿{{ number_format($performance['total_revenue'], 0) }}</h3>
                                <small class="text-muted">{{ $performance['total_shipments'] }} shipments</small>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Recent Activity -->
            <div class="row mt-4">
                <div class="col-12">
                    <div class="card">
                        <div class="card-header">
                            <h5 class="mb-0"><i class="bx bx-time me-2"></i>Recent Activity</h5>
                        </div>
                        <div class="card-body">
                            <div class="text-center py-4">
                                <i class="bx bx-time display-4 text-muted"></i>
                                <h5 class="mt-3 text-muted">No recent activity</h5>
                                <p class="text-muted">Activity tracking will be available when the system is fully integrated.</p>
                            </div>
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