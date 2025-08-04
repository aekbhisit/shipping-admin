@extends('layouts.datatable')
@section('styles')
    <link href="{{ asset('assets/plugins/datatable/css/dataTables.bootstrap5.min.css') }}" rel="stylesheet" />
@endsection

@section('content')
    <!--start page wrapper -->
    <div class="page-wrapper">
        <div class="page-content">
            <!--breadcrumb-->
            <div class="page-breadcrumb d-none d-sm-flex align-items-center mb-3">
                <div class="breadcrumb-title pe-3">User Activity Timeline</div>
                <div class="ps-3">
                    <nav aria-label="breadcrumb">
                        <ol class="breadcrumb mb-0 p-0">
                            <li class="breadcrumb-item">
                                <a href="{{ route('admin.homepage') }}"><i class="bx bx-home-alt"></i></a>
                            </li>
                            <li class="breadcrumb-item">
                                <a href="{{ route('admin.user-activity.index') }}">User Activity Logs</a>
                            </li>
                            <li class="breadcrumb-item active" aria-current="page">Timeline</li>
                        </ol>
                    </nav>
                </div>
            </div>
            <!--end breadcrumb-->

            <div class="row">
                <div class="col-12">
                    <div class="card">
                        <div class="card-header">
                            <h5 class="mb-0">Activity Timeline for {{ $user->name ?? 'User #' . $userId }}</h5>
                        </div>
                        <div class="card-body">
                            <div class="timeline">
                                @forelse($activities ?? [] as $activity)
                                    <div class="timeline-item">
                                        <div class="timeline-marker bg-{{ $activity->action == 'login' ? 'success' : ($activity->action == 'logout' ? 'warning' : 'primary') }}"></div>
                                        <div class="timeline-content">
                                            <div class="d-flex justify-content-between align-items-start">
                                                <div>
                                                    <h6 class="mb-1">{{ ucfirst($activity->action) }}</h6>
                                                    <p class="mb-1 text-muted">{{ $activity->description }}</p>
                                                    @if($activity->module)
                                                        <span class="badge bg-info">{{ $activity->module }}</span>
                                                    @endif
                                                </div>
                                                <small class="text-muted">{{ $activity->created_at->format('M d, Y H:i') }}</small>
                                            </div>
                                            <div class="mt-2">
                                                <small class="text-muted">
                                                    <i class="bx bx-map-pin"></i> {{ $activity->ip_address }}
                                                </small>
                                            </div>
                                        </div>
                                    </div>
                                @empty
                                    <div class="text-center py-4">
                                        <i class="bx bx-time bx-lg text-muted"></i>
                                        <p class="mt-2 text-muted">No activity found for this user</p>
                                    </div>
                                @endforelse
                            </div>
                        </div>
                        <div class="card-footer">
                            <a href="{{ route('admin.user-activity.index') }}" class="btn btn-secondary">
                                <i class="bx bx-arrow-back"></i> Back to List
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <!--end page wrapper -->

    <style>
        .timeline {
            position: relative;
            padding-left: 30px;
        }
        
        .timeline::before {
            content: '';
            position: absolute;
            left: 15px;
            top: 0;
            bottom: 0;
            width: 2px;
            background: #e9ecef;
        }
        
        .timeline-item {
            position: relative;
            margin-bottom: 30px;
        }
        
        .timeline-marker {
            position: absolute;
            left: -22px;
            top: 0;
            width: 12px;
            height: 12px;
            border-radius: 50%;
            border: 2px solid #fff;
            box-shadow: 0 0 0 2px #e9ecef;
        }
        
        .timeline-content {
            background: #f8f9fa;
            padding: 15px;
            border-radius: 8px;
            border-left: 4px solid #007bff;
        }
    </style>
@endsection

@section('scripts')
@endsection 