@extends('layouts.app')

@section('content')
<div class="container">
    <h1>My Progress</h1>
    <canvas id="progressChart" width="600" height="400"></canvas>
</div>

<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
    document.addEventListener('DOMContentLoaded', function () {
        fetch('{{ route('progress.monthly') }}')
            .then(response => response.json())
            .then(data => {
                const ctx = document.getElementById('progressChart').getContext('2d');
                const progressChart = new Chart(ctx, {
                    type: 'line',
                    data: {
                        labels: data.labels,
                        datasets: [{
                            label: 'Activities Completed',
                            data: data.data,
                            fill: false,
                            borderColor: 'rgb(75, 192, 192)',
                            tension: 0.1
                        }]
                    },
                    options: {
                        scales: {
                            y: {
                                beginAtZero: true,
                                precision: 0
                            }
                        }
                    }
                });
            })
            .catch(error => {
                console.error('Error fetching progress data:', error);
            });
    });
</script>
@endsection
