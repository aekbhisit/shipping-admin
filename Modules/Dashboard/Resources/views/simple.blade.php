<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href='https://unpkg.com/boxicons@2.1.4/css/boxicons.min.css' rel='stylesheet'>
</head>
<body>
    <div class="container-fluid">
        <div class="row">
            <!-- Sidebar -->
            <div class="col-md-3 col-lg-2 d-md-block bg-light sidebar">
                <div class="position-sticky pt-3">
                    <h5 class="text-center mb-4">Admin Panel</h5>
                    <ul class="nav flex-column">
                        <li class="nav-item">
                            <a class="nav-link active" href="/admin/dashboard">
                                <i class='bx bx-home'></i> Dashboard
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="/admin/user">
                                <i class='bx bx-user'></i> Users
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="/admin/logout">
                                <i class='bx bx-log-out'></i> Logout
                            </a>
                        </li>
                    </ul>
                </div>
            </div>

            <!-- Main content -->
            <main class="col-md-9 ms-sm-auto col-lg-10 px-md-4">
                <div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
                    <h1 class="h2">Dashboard</h1>
                    <div class="btn-toolbar mb-2 mb-md-0">
                        <div class="btn-group me-2">
                            <span class="badge bg-primary">Welcome, {{ session('user_name', 'Admin') }}</span>
                        </div>
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-6 col-lg-3 mb-4">
                        <div class="card text-white bg-primary">
                            <div class="card-body">
                                <div class="d-flex justify-content-between">
                                    <div>
                                        <h5 class="card-title">Jobs Waiting</h5>
                                        <h2>{{ $adminInit['job_waiting']['job_waiting_cnt'] ?? 0 }}</h2>
                                    </div>
                                    <div class="align-self-center">
                                        <i class='bx bx-time-five bx-lg'></i>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="col-md-6 col-lg-3 mb-4">
                        <div class="card text-white bg-success">
                            <div class="card-body">
                                <div class="d-flex justify-content-between">
                                    <div>
                                        <h5 class="card-title">Jobs Doing</h5>
                                        <h2>{{ $adminInit['job_doing']['job_doing_cnt'] ?? 0 }}</h2>
                                    </div>
                                    <div class="align-self-center">
                                        <i class='bx bx-cog bx-lg'></i>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="col-md-6 col-lg-3 mb-4">
                        <div class="card text-white bg-info">
                            <div class="card-body">
                                <div class="d-flex justify-content-between">
                                    <div>
                                        <h5 class="card-title">User Role</h5>
                                        <h6>{{ session('user_role', 'N/A') }}</h6>
                                    </div>
                                    <div class="align-self-center">
                                        <i class='bx bx-user-circle bx-lg'></i>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="col-md-6 col-lg-3 mb-4">
                        <div class="card text-white bg-warning">
                            <div class="card-body">
                                <div class="d-flex justify-content-between">
                                    <div>
                                        <h5 class="card-title">Status</h5>
                                        <h6>Online</h6>
                                    </div>
                                    <div class="align-self-center">
                                        <i class='bx bx-check-circle bx-lg'></i>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="row">
                    <div class="col-12">
                        <div class="card">
                            <div class="card-header">
                                <h5>System Information</h5>
                            </div>
                            <div class="card-body">
                                <div class="row">
                                    <div class="col-md-6">
                                        <p><strong>User ID:</strong> {{ session('user_id', 'N/A') }}</p>
                                        <p><strong>User Name:</strong> {{ session('user_name', 'N/A') }}</p>
                                        <p><strong>User Type:</strong> {{ session('user_type', 'N/A') }}</p>
                                    </div>
                                    <div class="col-md-6">
                                        <p><strong>Login Time:</strong> {{ now()->format('Y-m-d H:i:s') }}</p>
                                        <p><strong>Laravel Version:</strong> {{ app()->version() }}</p>
                                        <p><strong>PHP Version:</strong> {{ phpversion() }}</p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </main>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html> 