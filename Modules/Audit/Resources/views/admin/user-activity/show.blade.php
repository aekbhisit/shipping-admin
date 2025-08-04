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
                <div class="breadcrumb-title pe-3">User Activity Details</div>
                <div class="ps-3">
                    <nav aria-label="breadcrumb">
                        <ol class="breadcrumb mb-0 p-0">
                            <li class="breadcrumb-item">
                                <a href="{{ route('admin.homepage') }}"><i class="bx bx-home-alt"></i></a>
                            </li>
                            <li class="breadcrumb-item">
                                <a href="{{ route('admin.user-activity.index') }}">User Activity Logs</a>
                            </li>
                            <li class="breadcrumb-item active" aria-current="page">Details</li>
                        </ol>
                    </nav>
                </div>
            </div>
            <!--end breadcrumb-->

            <div class="row">
                <div class="col-12">
                    <div class="card">
                        <div class="card-header">
                            <h5 class="mb-0">User Activity #{{ $userActivity->id }}</h5>
                        </div>
                        <div class="card-body">
                            <div class="row">
                                <div class="col-md-6">
                                    <table class="table table-borderless">
                                        <tr>
                                            <th width="150">User:</th>
                                            <td>{{ $userActivity->user_name ?? 'System' }}</td>
                                        </tr>
                                        <tr>
                                            <th>Action:</th>
                                            <td>
                                                <span class="badge bg-primary">{{ ucfirst($userActivity->action) }}</span>
                                            </td>
                                        </tr>
                                        <tr>
                                            <th>Module:</th>
                                            <td>{{ $userActivity->module }}</td>
                                        </tr>
                                        <tr>
                                            <th>IP Address:</th>
                                            <td>{{ $userActivity->ip_address }}</td>
                                        </tr>
                                        <tr>
                                            <th>User Agent:</th>
                                            <td>{{ $userActivity->user_agent }}</td>
                                        </tr>
                                    </table>
                                </div>
                                <div class="col-md-6">
                                    <table class="table table-borderless">
                                        <tr>
                                            <th width="150">Created:</th>
                                            <td>{{ $userActivity->created_at->format('Y-m-d H:i:s') }}</td>
                                        </tr>
                                        <tr>
                                            <th>Updated:</th>
                                            <td>{{ $userActivity->updated_at->format('Y-m-d H:i:s') }}</td>
                                        </tr>
                                        <tr>
                                            <th>Description:</th>
                                            <td>{{ $userActivity->description }}</td>
                                        </tr>
                                        <tr>
                                            <th>Details:</th>
                                            <td>
                                                @if($userActivity->details)
                                                    <pre class="bg-light p-2 rounded">{{ json_encode($userActivity->details, JSON_PRETTY_PRINT) }}</pre>
                                                @else
                                                    <span class="text-muted">No details</span>
                                                @endif
                                            </td>
                                        </tr>
                                    </table>
                                </div>
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
@endsection

@section('scripts')
@endsection 