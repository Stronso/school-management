// Admin Panel JavaScript

document.addEventListener('DOMContentLoaded', function() {
    // Initialize tooltips
    var tooltips = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
    tooltips.map(function(tooltip) {
        return new bootstrap.Tooltip(tooltip);
    });

    // Sidebar Toggle
    const sidebarToggle = document.getElementById('sidebarToggle');
    const adminSidebar = document.querySelector('.admin-sidebar');
    const adminContent = document.querySelector('.admin-content');

    if (sidebarToggle) {
        sidebarToggle.addEventListener('click', function(e) {
            e.preventDefault();
            adminSidebar.classList.toggle('collapsed');
            adminContent.classList.toggle('expanded');
            
            // Save state
            localStorage.setItem('sidebarState', 
                adminSidebar.classList.contains('collapsed') ? 'collapsed' : 'expanded');
        });
    }

    // Restore sidebar state
    const sidebarState = localStorage.getItem('sidebarState');
    if (sidebarState === 'collapsed') {
        adminSidebar.classList.add('collapsed');
        adminContent.classList.add('expanded');
    }

    // Add fade-in animation to cards
    document.querySelectorAll('.card').forEach(function(card, index) {
        card.style.animationDelay = (index * 0.1) + 's';
        card.classList.add('fade-in');
    });

    // DataTables initialization
    const tables = document.querySelectorAll('.datatable');
    tables.forEach(table => {
        new DataTable(table, {
            pageLength: 10,
            lengthChange: false,
            responsive: true,
            dom: '<"row"<"col-sm-12 col-md-6"f><"col-sm-12 col-md-6"l>>rtip'
        });
    });

    // Confirm Delete Actions
    document.querySelectorAll('.delete-confirm').forEach(button => {
        button.addEventListener('click', function(e) {
            if (!confirm('Are you sure you want to delete this item? This action cannot be undone.')) {
                e.preventDefault();
            }
        });
    });

    // Form Validation
    document.querySelectorAll('form').forEach(form => {
        form.addEventListener('submit', function(e) {
            if (!form.checkValidity()) {
                e.preventDefault();
                e.stopPropagation();
            }
            form.classList.add('was-validated');
        });
    });

    // Statistics Cards Animation
    const animateValue = (element, start, end, duration) => {
        let startTimestamp = null;
        const step = (timestamp) => {
            if (!startTimestamp) startTimestamp = timestamp;
            const progress = Math.min((timestamp - startTimestamp) / duration, 1);
            element.innerHTML = Math.floor(progress * (end - start) + start);
            if (progress < 1) {
                window.requestAnimationFrame(step);
            }
        };
        window.requestAnimationFrame(step);
    };

    // Animate statistics on page load
    document.querySelectorAll('.stat-value').forEach(stat => {
        const endValue = parseInt(stat.getAttribute('data-value'));
        animateValue(stat, 0, endValue, 1000);
    });

    // Search Filter
    const searchInput = document.querySelector('.search-filter');
    if (searchInput) {
        searchInput.addEventListener('keyup', function() {
            const searchTerm = this.value.toLowerCase();
            const tableRows = document.querySelectorAll('tbody tr');

            tableRows.forEach(row => {
                const text = row.textContent.toLowerCase();
                row.style.display = text.includes(searchTerm) ? '' : 'none';
            });
        });
    }

    // Toggle Password Visibility
    const togglePassword = document.querySelector('.toggle-password');
    if (togglePassword) {
        togglePassword.addEventListener('click', function() {
            const passwordInput = document.querySelector(this.getAttribute('toggle'));
            const type = passwordInput.getAttribute('type') === 'password' ? 'text' : 'password';
            passwordInput.setAttribute('type', type);
            this.classList.toggle('fa-eye');
            this.classList.toggle('fa-eye-slash');
        });
    }

    // Initialize AOS
    AOS.init({
        duration: 800,
        easing: 'ease-in-out',
        once: true
    });

    // Chart.js Configuration
    const chartConfig = {
        type: 'line',
        data: {
            labels: [],
            datasets: [{
                label: 'User Activity',
                data: [],
                borderColor: '#4e73df',
                backgroundColor: 'rgba(78, 115, 223, 0.05)',
                tension: 0.4,
                fill: true
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: {
                    display: false
                }
            },
            scales: {
                y: {
                    beginAtZero: true,
                    grid: {
                        color: 'rgba(0, 0, 0, 0.05)'
                    }
                },
                x: {
                    grid: {
                        display: false
                    }
                }
            }
        }
    };

    // Initialize Charts
    let userActivityChart;
    let courseDistributionChart;

    function initializeCharts() {
        const userActivityCtx = document.getElementById('userActivityChart');
        const courseDistributionCtx = document.getElementById('courseDistributionChart');

        if (userActivityCtx) {
            userActivityChart = new Chart(userActivityCtx, chartConfig);
        }

        if (courseDistributionCtx) {
            courseDistributionChart = new Chart(courseDistributionCtx, {
                type: 'doughnut',
                data: {
                    labels: ['Active', 'Completed', 'In Progress'],
                    datasets: [{
                        data: [65, 25, 10],
                        backgroundColor: [
                            '#4e73df',
                            '#1cc88a',
                            '#f6c23e'
                        ]
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        legend: {
                            position: 'bottom'
                        }
                    }
                }
            });
        }
    }

    // Counter Animation
    function animateCounter(element, target) {
        let current = 0;
        const increment = target / 50;
        const duration = 1000;
        const step = duration / 50;

        const timer = setInterval(() => {
            current += increment;
            if (current >= target) {
                clearInterval(timer);
                current = target;
            }
            element.textContent = Math.round(current);
        }, step);
    }

    // Initialize Counters
    function initializeCounters() {
        document.querySelectorAll('.counter').forEach(counter => {
            const target = parseInt(counter.dataset.target);
            animateCounter(counter, target);
        });
    }

    // System Status Updates
    function updateSystemStatus() {
        fetch('/admin/system-status')
            .then(response => response.json())
            .then(data => {
                document.getElementById('serverStatus').textContent = data.server;
                document.getElementById('dbStatus').textContent = data.database;
                document.getElementById('storageUsage').style.width = data.storage + '%';
            })
            .catch(error => console.error('Error fetching system status:', error));
    }

    // Real-time Updates
    function startRealTimeUpdates() {
        setInterval(() => {
            updateSystemStatus();
            updateCharts();
        }, 30000); // Update every 30 seconds
    }

    // Chart Updates
    function updateCharts() {
        fetch('/admin/chart-data')
            .then(response => response.json())
            .then(data => {
                if (userActivityChart) {
                    userActivityChart.data.labels = data.labels;
                    userActivityChart.data.datasets[0].data = data.values;
                    userActivityChart.update();
                }
            })
            .catch(error => console.error('Error updating charts:', error));
    }

    // Quick Actions Modal
    function initializeQuickActions() {
        const modal = document.getElementById('quickActionsModal');
        if (modal) {
            modal.addEventListener('show.bs.modal', () => {
                // Reset form fields
                document.querySelectorAll('.quick-action-form input, .quick-action-form select').forEach(input => {
                    input.value = '';
                });
            });
        }
    }

    // Refresh Button Handler
    function initializeRefreshButton() {
        const refreshBtn = document.getElementById('refreshBtn');
        if (refreshBtn) {
            refreshBtn.addEventListener('click', () => {
                refreshBtn.classList.add('rotating');
                updateSystemStatus();
                updateCharts();
                setTimeout(() => {
                    refreshBtn.classList.remove('rotating');
                }, 1000);
            });
        }
    }

    // Initialize everything when the document is ready
    document.addEventListener('DOMContentLoaded', () => {
        initializeCharts();
        initializeCounters();
        initializeQuickActions();
        initializeRefreshButton();
        startRealTimeUpdates();
        
        // Initial updates
        updateSystemStatus();
        updateCharts();
    });

    // Dark mode support
    if (window.matchMedia && window.matchMedia('(prefers-color-scheme: dark)').matches) {
        document.body.classList.add('dark-mode');
    }

    // Listen for dark mode changes
    window.matchMedia('(prefers-color-scheme: dark)').addEventListener('change', e => {
        if (e.matches) {
            document.body.classList.add('dark-mode');
        } else {
            document.body.classList.remove('dark-mode');
        }
    });
}); 