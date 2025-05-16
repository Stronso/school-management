@extends('layouts.admin')

@section('content')
<div class="container-fluid">
    <!-- Page Header -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="h3 mb-0">Dashboard</h1>
        <div class="d-flex gap-2">
            <button class="btn btn-outline-primary" id="refreshStats">
                <i class="fas fa-sync-alt me-2"></i>Refresh
            </button>
            <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#quickActionsModal">
                <i class="fas fa-plus me-2"></i>Quick Actions
            </button>
        </div>
    </div>

    <!-- Statistics Cards with Animation -->
    <div class="row g-4 mb-4">
        <div class="col-xl-3 col-md-6">
            <div class="card stats-card primary" data-aos="fade-up" data-aos-delay="100">
                <div class="card-body">
                    <div class="icon text-primary">
                        <i class="fas fa-users"></i>
                    </div>
                    <div class="title text-primary">Total Users</div>
                    <div class="value counter" data-target="{{ $users_count }}">{{ $users_count }}</div>
                    <div class="progress mt-2" style="height: 4px;">
                        <div class="progress-bar bg-primary" style="width: 75%"></div>
                    </div>
                    <a href="{{ route('admin.users') }}" class="btn btn-sm btn-primary mt-3">View Details</a>
                </div>
            </div>
        </div>

        <div class="col-xl-3 col-md-6">
            <div class="card stats-card success" data-aos="fade-up" data-aos-delay="200">
                <div class="card-body">
                    <div class="icon text-success">
                        <i class="fas fa-graduation-cap"></i>
                    </div>
                    <div class="title text-success">Total Courses</div>
                    <div class="value counter" data-target="{{ $courses_count }}">{{ $courses_count }}</div>
                    <div class="progress mt-2" style="height: 4px;">
                        <div class="progress-bar bg-success" style="width: 60%"></div>
                    </div>
                    <a href="{{ route('admin.courses') }}" class="btn btn-sm btn-success mt-3">View Details</a>
                </div>
            </div>
        </div>

        <div class="col-xl-3 col-md-6">
            <div class="card stats-card info" data-aos="fade-up" data-aos-delay="300">
                <div class="card-body">
                    <div class="icon text-info">
                        <i class="fas fa-comments"></i>
                    </div>
                    <div class="title text-info">Total Messages</div>
                    <div class="value counter" data-target="{{ $messages_count }}">{{ $messages_count }}</div>
                    <div class="progress mt-2" style="height: 4px;">
                        <div class="progress-bar bg-info" style="width: 45%"></div>
                    </div>
                    <a href="{{ route('admin.chat.messages') }}" class="btn btn-sm btn-info mt-3">View Details</a>
                </div>
            </div>
        </div>

        <div class="col-xl-3 col-md-6">
            <div class="card stats-card warning" data-aos="fade-up" data-aos-delay="400">
                <div class="card-body">
                    <div class="icon text-warning">
                        <i class="fas fa-robot"></i>
                    </div>
                    <div class="title text-warning">Chatbot Responses</div>
                    <div class="value counter" data-target="{{ $chatbot_responses_count ?? 0 }}">{{ $chatbot_responses_count ?? 0 }}</div>
                    <div class="progress mt-2" style="height: 4px;">
                        <div class="progress-bar bg-warning" style="width: 30%"></div>
                    </div>
                    <a href="{{ route('admin.chatbot.responses') }}" class="btn btn-sm btn-warning mt-3">View Details</a>
                </div>
            </div>
        </div>
    </div>

    <!-- Charts Row -->
    <div class="row mb-4">
        <div class="col-lg-8">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h5 class="card-title">User Activity</h5>
                    <div class="btn-group">
                        <button type="button" class="btn btn-sm btn-outline-secondary active" data-period="week">Week</button>
                        <button type="button" class="btn btn-sm btn-outline-secondary" data-period="month">Month</button>
                        <button type="button" class="btn btn-sm btn-outline-secondary" data-period="year">Year</button>
                    </div>
                </div>
                <div class="card-body">
                    <canvas id="userActivityChart" height="300"></canvas>
                </div>
            </div>
        </div>
        <div class="col-lg-4">
            <div class="card">
                <div class="card-header">
                    <h5 class="card-title">Course Distribution</h5>
                </div>
                <div class="card-body">
                    <canvas id="courseDistributionChart" height="300"></canvas>
                    <script>
                        document.addEventListener('DOMContentLoaded', function() {
                            const ctx = document.getElementById('courseDistributionChart').getContext('2d');
                            const labels = @json($labels);
                            const data = @json($data);

                            new Chart(ctx, {
                                type: 'doughnut',
                                data: {
                                    labels: labels,
                                    datasets: [{
                                        data: data,
                                        backgroundColor: [
                                            '#4f46e5',
                                            '#22c55e',
                                            '#3b82f6',
                                            '#f59e0b',
                                            '#e11d48',
                                            '#f97316',
                                            '#10b981',
                                            '#3b82f6'
                                        ]
                                    }]
                                },
                                options: {
                                    responsive: true,
                                    plugins: {
                                        legend: {
                                            position: 'bottom'
                                        }
                                    }
                                }
                            });
                        });
                    </script>
                </div>
            </div>
        </div>
    </div>

    <!-- Quick Actions Section -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h5 class="card-title">Quick Actions</h5>
                </div>
                <div class="card-body">
                    <div class="row g-3">
                        <div class="col-md-3">
                            <a href="{{ route('admin.users') }}" class="btn btn-secondary w-100 action-btn">
                                <i class="fas fa-users me-2"></i>Manage Users
                            </a>
                        </div>
                        <div class="col-md-3">
                            <a href="{{ route('admin.users.create') }}" class="btn btn-primary w-100 action-btn">
                                <i class="fas fa-user-plus me-2"></i>Add New User
                            </a>
                        </div>
                        <div class="col-md-3">
                            <a href="{{ route('admin.courses.create') }}" class="btn btn-success w-100 action-btn">
                                <i class="fas fa-book me-2"></i>Add New Course
                            </a>
                        </div>
                        <div class="col-md-3">
                            <a href="{{ route('admin.chatbot.responses.create') }}" class="btn btn-info w-100 action-btn">
                                <i class="fas fa-robot me-2"></i>Add Chatbot Response
                            </a>
                        </div>
                        <div class="col-md-3">
                    <a href="{{ route('admin.chatbot.responses') }}" class="btn btn-warning w-100 action-btn">
                        <i class="fas fa-cog me-2"></i>Manage Chatbot
                    </a>
                </div>
                <div class="col-md-3 mt-3">
                    <a href="{{ route('course_subscriptions.approve') }}" class="btn btn-primary w-100 action-btn">
                        <i class="fas fa-check me-2"></i>Approve Course Subscriptions
                    </a>
                </div>
            </div>
        </div>
    </div>
        </div>
    </div>

    <!-- Recent Activity & System Status -->
    <div class="row">
        <div class="col-lg-8">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h5 class="card-title">Recent Activity</h5>
                    <button class="btn btn-sm btn-outline-primary" id="refreshActivity">
                        <i class="fas fa-sync-alt"></i>
                    </button>
                </div>
                <div class="card-body p-0">
                    <div class="table-container">
                        <table class="table">
                            <thead>
                                <tr>
                                    <th>Action</th>
                                    <th>User</th>
                                    <th>Time</th>
                                </tr>
                            </thead>
                            <tbody id="activityTableBody">
                                @forelse($recent_activities ?? [] as $activity)
                                <tr>
                                    <td>{{ $activity->description }}</td>
                                    <td>{{ $activity->causer->name ?? 'System' }}</td>
                                    <td>{{ $activity->created_at->diffForHumans() }}</td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="3" class="text-center py-4">No recent activity</td>
                                </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-lg-4">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h5 class="card-title">System Status</h5>
                    <button class="btn btn-sm btn-outline-primary" id="refreshStatus">
                        <i class="fas fa-sync-alt"></i>
                    </button>
                </div>
                <div class="card-body">
                    <div class="list-group list-group-flush" id="systemStatus">
                        <div class="list-group-item d-flex justify-content-between align-items-center">
                            <span>Server Status</span>
                            <span class="badge bg-success">Online</span>
                        </div>
                        <div class="list-group-item d-flex justify-content-between align-items-center">
                            <span>Database Status</span>
                            <span class="badge bg-success">Connected</span>
                        </div>
                        <div class="list-group-item d-flex justify-content-between align-items-center">
                            <span>Last Backup</span>
                            <span>{{ now()->subDay()->format('M d, Y H:i') }}</span>
                        </div>
                        <div class="list-group-item d-flex justify-content-between align-items-center">
                            <span>Storage Usage</span>
                            <div class="d-flex align-items-center">
                                <div class="progress flex-grow-1 me-2" style="width: 100px; height: 6px;">
                                    <div class="progress-bar bg-primary" style="width: 45%"></div>
                                </div>
                                <span>45%</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Quick Actions Modal -->
<div class="modal fade" id="quickActionsModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Quick Actions</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <div class="d-grid gap-2">
                    <a href="{{ route('admin.users.create') }}" class="btn btn-primary action-btn">
                        <i class="fas fa-user-plus me-2"></i>Add New User
                    </a>
                    <a href="{{ route('admin.courses.create') }}" class="btn btn-success action-btn">
                        <i class="fas fa-book me-2"></i>Add New Course
                    </a>
                    <a href="{{ route('admin.chatbot.responses.create') }}" class="btn btn-info action-btn">
                        <i class="fas fa-robot me-2"></i>Add Chatbot Response
                    </a>
                    <a href="{{ route('admin.chatbot.responses') }}" class="btn btn-warning action-btn">
                        <i class="fas fa-cog me-2"></i>Manage Chatbot
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('styles')
<link href="https://unpkg.com/aos@2.3.1/dist/aos.css" rel="stylesheet">
@endpush

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script src="https://unpkg.com/aos@2.3.1/dist/aos.js"></script>
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Initialize AOS
    AOS.init({
        duration: 800,
        once: true
    });

    // Counter Animation
    const counters = document.querySelectorAll('.counter');
    counters.forEach(counter => {
        const target = parseInt(counter.getAttribute('data-target'));
        const duration = 2000; // 2 seconds
        const step = target / (duration / 16); // 60fps
        let current = 0;

        const updateCounter = () => {
            current += step;
            if (current < target) {
                counter.textContent = Math.round(current);
                requestAnimationFrame(updateCounter);
            } else {
                counter.textContent = target;
            }
        };

        updateCounter();
    });

    // User Activity Chart
    const userActivityCtx = document.getElementById('userActivityChart').getContext('2d');
    new Chart(userActivityCtx, {
        type: 'line',
        data: {
            labels: ['Mon', 'Tue', 'Wed', 'Thu', 'Fri', 'Sat', 'Sun'],
            datasets: [{
                label: 'Active Users',
                data: [65, 59, 80, 81, 56, 55, 40],
                borderColor: '#4f46e5',
                tension: 0.4,
                fill: true,
                backgroundColor: 'rgba(79, 70, 229, 0.1)'
            }]
        },
        options: {
            responsive: true,
            plugins: {
                legend: {
                    display: false
                }
            },
            scales: {
                y: {
                    beginAtZero: true
                }
            }
        }
    });

    // Course Distribution Chart
    const courseDistributionCtx = document.getElementById('courseDistributionChart').getContext('2d');
    new Chart(courseDistributionCtx, {
        type: 'doughnut',
        data: {
            labels: ['Mathematics', 'Science', 'History', 'English'],
            datasets: [{
                data: [30, 25, 20, 25],
                backgroundColor: [
                    '#4f46e5',
                    '#22c55e',
                    '#3b82f6',
                    '#f59e0b'
                ]
            }]
        },
        options: {
            responsive: true,
            plugins: {
                legend: {
                    position: 'bottom'
                }
            }
        }
    });

    // Refresh Stats Button
    document.getElementById('refreshStats').addEventListener('click', function() {
        this.classList.add('rotating');
        setTimeout(() => {
            this.classList.remove('rotating');
        }, 1000);
    });

    // Refresh Activity Button
    document.getElementById('refreshActivity').addEventListener('click', function() {
        this.classList.add('rotating');
        setTimeout(() => {
            this.classList.remove('rotating');
        }, 1000);
    });

    // Refresh Status Button
    document.getElementById('refreshStatus').addEventListener('click', function() {
        this.classList.add('rotating');
        setTimeout(() => {
            this.classList.remove('rotating');
        }, 1000);
    });

    // Action Buttons Animation
    document.querySelectorAll('.action-btn').forEach(btn => {
        btn.addEventListener('mouseenter', function() {
            this.style.transform = 'translateY(-2px)';
        });
        btn.addEventListener('mouseleave', function() {
            this.style.transform = 'translateY(0)';
        });
    });
});
</script>
@endpush 