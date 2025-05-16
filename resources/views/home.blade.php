@extends('layouts.app')

@section('content')
<div class="container-fluid">
    <!-- Page Header -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="h3 mb-0">Welcome, {{ Auth::user()->name }}!</h1>
        <div class="d-flex gap-2">
            <button class="btn btn-outline-primary" id="refreshBtn" onclick="location.reload();">
                <i class="fas fa-sync-alt me-2"></i>Refresh
            </button>
        </div>
    </div>

    <!-- Statistics Cards with Animation -->
    <div class="row g-4 mb-4">
        <div class="col-xl-3 col-md-6">
            <div class="card stats-card primary" data-aos="fade-up" data-aos-delay="100">
                <div class="card-body">
                    <div class="icon text-primary">
                        <i class="fas fa-book"></i>
                    </div>
                    <div class="title text-primary">My Courses</div>
                    <div class="value counter" data-target="{{ Auth::user()->courses()->count() }}">0</div>
                    <div class="progress mt-2" style="height: 4px;">
                        <div class="progress-bar bg-primary" style="width: 75%"></div>
                    </div>
                    <a href="{{ route('courses.index') }}" class="btn btn-sm btn-primary mt-3">View Courses</a>
                </div>
            </div>
        </div>

        <div class="col-xl-3 col-md-6">
            <div class="card stats-card success" data-aos="fade-up" data-aos-delay="200">
                <div class="card-body">
            <div class="icon text-success">
                <i class="fas fa-tasks"></i>
            </div>
            <div class="title text-success">Total Users</div>
            <div class="value counter" data-target="{{ $usersCount ?? 0 }}">0</div>
            <div class="progress mt-2" style="height: 4px;">
                <div class="progress-bar bg-success" style="width: 60%"></div>
            </div>
            @if(Auth::user() && Auth::user()->role === 'admin')
                <a href="{{ route('admin.users') }}" class="btn btn-sm btn-success mt-3">Manage Users</a>
            @endif
                </div>
            </div>
        </div>

        <div class="col-xl-3 col-md-6">
            <div class="card stats-card info" data-aos="fade-up" data-aos-delay="300">
                <div class="card-body">
                    <div class="icon text-info">
                        <i class="fas fa-comments"></i>
                    </div>
                    <div class="title text-info">Messages</div>
                    <div class="value counter" data-target="{{ $messagesCount ?? 0 }}">0</div>
                    <div class="progress mt-2" style="height: 4px;">
                        <div class="progress-bar bg-info" style="width: 45%"></div>
                    </div>
                    <a href="{{ route('chat.messages') }}" class="btn btn-sm btn-info mt-3">View Messages</a>
                </div>
            </div>
        </div>

        <div class="col-xl-3 col-md-6">
            <div class="card stats-card warning" data-aos="fade-up" data-aos-delay="400">
                <div class="card-body">
                    <div class="icon text-warning">
                        <i class="fas fa-calendar-alt"></i>
                    </div>
                    <div class="title text-warning">Upcoming Events</div>
                    <div class="value counter" data-target="{{ $upcomingEventsCount ?? 0 }}">0</div>
                    <div class="progress mt-2" style="height: 4px;">
                        <div class="progress-bar bg-warning" style="width: 30%"></div>
                    </div>
                    <a href="{{ route('calendar.index') }}" class="btn btn-sm btn-warning mt-3">View Calendar</a>
                </div>
            </div>
        </div>
    </div>

    <!-- Charts Row -->
    <div class="row mb-4">
        <div class="col-lg-8">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h5 class="card-title">My Progress</h5>
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
                            <a href="{{ route('courses.index') }}" class="btn btn-primary w-100 action-btn">
                                <i class="fas fa-book me-2"></i>View Courses
                            </a>
                        </div>
                        @if(Auth::user() && (Auth::user()->role === 'admin' || Auth::user()->role === 'teacher'))
                        <div class="col-md-3">
                            <a href="{{ route('admin.course_subscriptions.approve') }}" class="btn btn-success w-100 action-btn">
                                <i class="fas fa-tasks me-2"></i>Manage subscription
                            </a>
                        </div>
                        @endif
                        <div class="col-md-3">
                            <a href="{{ route('chat.messages') }}" class="btn btn-info w-100 action-btn">
                                <i class="fas fa-comments me-2"></i>Send Message
                            </a>
                        </div>
                        <div class="col-md-3">
                            <a href="{{ route('calendar.index') }}" class="btn btn-warning w-100 action-btn">
                                <i class="fas fa-calendar me-2"></i>View Schedule
                            </a>
                        </div>
                        @if(Auth::user() && Auth::user()->role === 'student')
                        <div class="col-md-3">
                            <a href="{{ route('course_subscriptions.student_subscribe') }}" class="btn btn-success w-100 action-btn">
                                <i class="fas fa-book me-2"></i>Subscribe to Courses
                            </a>
                        </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Recent Activity & Upcoming Events -->
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
                            <th>Activity</th>
                            <th></th>
                            <!--<th>Course</th>-->
                            <th>Date</th>
                        </tr>
                    </thead>
                    <tbody>
                        @if($lastSubscription)
                        <tr>
                            <td>Subscribed to Course</td>
                            <td><!--{{ $lastSubscription->course->name ?? 'N/A' }}--></td>
                            <td>{{ $lastSubscription->created_at->diffForHumans() }}</td>
                        </tr>
                        @endif
                        <tr>
                            <td>Profile Updated</td>
                            <td></td>
                            <td>{{ $lastProfileUpdate->diffForHumans() }}</td>
                        </tr>
                        @if($lastLogin)
                        <tr>
                            <td>Last Login</td>
                            <td></td>
                            <td>{{ $lastLogin->login_at->diffForHumans() }}</td>
                        </tr>
                        @endif
                    </tbody>
                </table>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-lg-4">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h5 class="card-title">Upcoming Events</h5>
                    <button class="btn btn-sm btn-outline-primary" id="refreshEvents">
                        <i class="fas fa-sync-alt"></i>
                    </button>
                </div>
                <div class="card-body">
                <div class="list-group list-group-flush">
                    @forelse($upcomingEvents as $event)
                    <div class="list-group-item d-flex justify-content-between align-items-center">
                        <div>
                            <h6 class="mb-0">{{ $event->title }}</h6>
                            <small class="text-muted">{{ \Carbon\Carbon::parse($event->start_time)->format('l, F j, g:i A') }}</small>
                        </div>
                        @if($event->is_important)
                        <span class="badge bg-danger">Important</span>
                        @elseif($event->is_due_soon)
                        <span class="badge bg-warning">Due Soon</span>
                        @else
                        <span class="badge bg-info">Scheduled</span>
                        @endif
                    </div>
                    @empty
                    <div class="list-group-item">
                        No upcoming events.
                    </div>
                    @endforelse
                </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('styles')
<link href="https://unpkg.com/aos@2.3.1/dist/aos.css" rel="stylesheet">
<style>
    /* Additional styles for the dashboard */
    .stats-card {
        border-radius: 10px;
        transition: all 0.3s ease;
        border: none;
        box-shadow: 0 4px 6px rgba(0, 0, 0, 0.05);
    }
    
    .stats-card:hover {
        transform: translateY(-5px);
        box-shadow: 0 8px 15px rgba(0, 0, 0, 0.1);
    }
    
    .stats-card .icon {
        font-size: 2rem;
        margin-bottom: 0.5rem;
    }
    
    .stats-card .title {
        font-size: 0.875rem;
        font-weight: 500;
        margin-bottom: 0.5rem;
    }
    
    .stats-card .value {
        font-size: 1.5rem;
        font-weight: 700;
        margin-bottom: 0.5rem;
    }
    
    .action-btn {
        transition: all 0.3s ease;
    }
    
    .action-btn:hover {
        transform: translateY(-3px);
    }
    
    .rotating {
        animation: rotate 1s linear infinite;
    }
    
    @keyframes rotate {
        from { transform: rotate(0deg); }
        to { transform: rotate(360deg); }
    }
    
    .table-container {
        max-height: 300px;
        overflow-y: auto;
    }
</style>
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
    let labels = @json($labels);
    let data = @json($data);
    let currentPeriod = '@json($period)';

    let userActivityChart = new Chart(userActivityCtx, {
        type: 'line',
        data: {
            labels: labels,
            datasets: [{
                label: 'Study Hours',
                data: data,
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

    // Period buttons functionality
    document.querySelectorAll('.btn-group button').forEach(button => {
        button.addEventListener('click', function() {
            if (this.classList.contains('active')) return;

            // Remove active class from all buttons
            document.querySelectorAll('.btn-group button').forEach(btn => btn.classList.remove('active'));

            // Add active class to clicked button
            this.classList.add('active');

            // Get selected period
            const selectedPeriod = this.getAttribute('data-period');

            // Fetch new data via AJAX
            fetch(`?period=${selectedPeriod}`, {
                headers: {
                    'X-Requested-With': 'XMLHttpRequest'
                }
            })
            .then(response => response.json())
            .then(json => {
                // Update chart data
                userActivityChart.data.labels = json.labels;
                userActivityChart.data.datasets[0].data = json.data;
                userActivityChart.update();
            })
            .catch(error => {
                console.error('Error fetching activity data:', error);
            });
        });
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

    // Refresh Button
    document.getElementById('refreshBtn').addEventListener('click', function() {
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

    // Refresh Events Button
    document.getElementById('refreshEvents').addEventListener('click', function() {
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
