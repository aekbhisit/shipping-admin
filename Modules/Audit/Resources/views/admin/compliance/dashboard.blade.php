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
                <div class="breadcrumb-title pe-3">Compliance Dashboard</div>
                <div class="ps-3">
                    <nav aria-label="breadcrumb">
                        <ol class="breadcrumb mb-0 p-0">
                            <li class="breadcrumb-item">
                                <a href="{{ route('admin.homepage') }}"><i class="bx bx-home-alt"></i></a>
                            </li>
                            <li class="breadcrumb-item">
                                <a href="{{ route('admin.compliance.index') }}">Compliance Reports</a>
                            </li>
                            <li class="breadcrumb-item active" aria-current="page">Dashboard</li>
                        </ol>
                    </nav>
                </div>
            </div>
            <!--end breadcrumb-->

            <!-- Statistics Cards -->
            <div class="row">
                <div class="col-12 col-lg-3">
                    <div class="card radius-10">
                        <div class="card-body">
                            <div class="d-flex align-items-center">
                                <div class="">
                                    <p class="mb-1 text-secondary">Total Reports</p>
                                    <h4 class="mb-0 text-primary">{{ $stats['total_reports'] ?? 0 }}</h4>
                                </div>
                                <div class="ms-auto fs-2 text-primary">
                                    <i class="bx bx-file"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-12 col-lg-3">
                    <div class="card radius-10">
                        <div class="card-body">
                            <div class="d-flex align-items-center">
                                <div class="">
                                    <p class="mb-1 text-secondary">This Month</p>
                                    <h4 class="mb-0 text-success">{{ $stats['this_month'] ?? 0 }}</h4>
                                </div>
                                <div class="ms-auto fs-2 text-success">
                                    <i class="bx bx-calendar"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-12 col-lg-3">
                    <div class="card radius-10">
                        <div class="card-body">
                            <div class="d-flex align-items-center">
                                <div class="">
                                    <p class="mb-1 text-secondary">Pending Reports</p>
                                    <h4 class="mb-0 text-warning">{{ $stats['pending_reports'] ?? 0 }}</h4>
                                </div>
                                <div class="ms-auto fs-2 text-warning">
                                    <i class="bx bx-time"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-12 col-lg-3">
                    <div class="card radius-10">
                        <div class="card-body">
                            <div class="d-flex align-items-center">
                                <div class="">
                                    <p class="mb-1 text-secondary">Completed Reports</p>
                                    <h4 class="mb-0 text-info">{{ $stats['completed_reports'] ?? 0 }}</h4>
                                </div>
                                <div class="ms-auto fs-2 text-info">
                                    <i class="bx bx-check-circle"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Recent Reports -->
            <div class="row">
                <div class="col-12">
                    <div class="card">
                        <div class="card-header">
                            <h5 class="mb-0">Recent Compliance Reports</h5>
                        </div>
                        <div class="card-body">
                            <div class="table-responsive">
                                <table class="table table-striped table-bordered">
                                    <thead>
                                        <tr>
                                            <th>ID</th>
                                            <th>Report Type</th>
                                            <th>Period</th>
                                            <th>Status</th>
                                            <th>Generated By</th>
                                            <th>Generated At</th>
                                            <th>Actions</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @forelse($recentReports ?? [] as $report)
                                            <tr>
                                                <td>{{ $report->id }}</td>
                                                <td>
                                                    <span class="badge bg-info">{{ ucfirst($report->report_type) }}</span>
                                                </td>
                                                <td>{{ $report->period }}</td>
                                                <td>
                                                    <span class="badge bg-{{ $report->status == 'completed' ? 'success' : ($report->status == 'processing' ? 'warning' : 'danger') }}">
                                                        {{ ucfirst($report->status) }}
                                                    </span>
                                                </td>
                                                <td>{{ $report->generated_by }}</td>
                                                <td>{{ $report->created_at->format('Y-m-d H:i') }}</td>
                                                <td>
                                                    <a href="{{ route('admin.compliance.show', $report->id) }}" class="btn btn-sm btn-primary">
                                                        <i class="bx bx-show"></i> View
                                                    </a>
                                                    @if($report->file_path)
                                                        <a href="{{ route('admin.compliance.download', $report->id) }}" class="btn btn-sm btn-success">
                                                            <i class="bx bx-download"></i> Download
                                                        </a>
                                                    @endif
                                                </td>
                                            </tr>
                                        @empty
                                            <tr>
                                                <td colspan="7" class="text-center">No recent reports found</td>
                                            </tr>
                                        @endforelse
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Report Type Distribution -->
            <div class="row">
                <div class="col-12 col-lg-6">
                    <div class="card">
                        <div class="card-header">
                            <h5 class="mb-0">Report Type Distribution</h5>
                        </div>
                        <div class="card-body">
                            <div class="table-responsive">
                                <table class="table table-borderless">
                                    <tbody>
                                        @foreach($reportTypeStats ?? [] as $type => $count)
                                            <tr>
                                                <td>{{ ucfirst($type) }}</td>
                                                <td>
                                                    <div class="progress" style="height: 20px;">
                                                        <div class="progress-bar" role="progressbar" style="width: {{ ($count / max(array_values($reportTypeStats ?? [1]))) * 100 }}%">
                                                            {{ $count }}
                                                        </div>
                                                    </div>
                                                </td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-12 col-lg-6">
                    <div class="card">
                        <div class="card-header">
                            <h5 class="mb-0">Monthly Report Generation</h5>
                        </div>
                        <div class="card-body">
                            <div class="table-responsive">
                                <table class="table table-borderless">
                                    <tbody>
                                        @foreach($monthlyStats ?? [] as $month => $count)
                                            <tr>
                                                <td>{{ $month }}</td>
                                                <td>
                                                    <div class="progress" style="height: 20px;">
                                                        <div class="progress-bar bg-success" role="progressbar" style="width: {{ ($count / max(array_values($monthlyStats ?? [1]))) * 100 }}%">
                                                            {{ $count }}
                                                        </div>
                                                    </div>
                                                </td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
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