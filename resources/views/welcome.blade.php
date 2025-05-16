@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row justify-content-center align-items-center min-vh-100">
        <div class="col-md-8 text-center">
            <h1 class="display-4 mb-4">Welcome to School Management System</h1>
            <p class="lead mb-5">A comprehensive platform for managing courses, activities, and communication between teachers and students.</p>
            
            @guest
                <div class="d-grid gap-3 d-sm-flex justify-content-sm-center">
                    <a href="{{ route('login') }}" class="btn btn-primary btn-lg px-4 gap-3">Login</a>
                    <a href="{{ route('register') }}" class="btn btn-outline-primary btn-lg px-4">Register</a>
                </div>
            @else
                <div class="d-grid gap-3 d-sm-flex justify-content-sm-center">
                    <a href="{{ route('home') }}" class="btn btn-primary btn-lg px-4">Go to Dashboard</a>
                </div>
            @endguest

            <div class="row mt-5">
                <div class="col-md-4">
                    <div class="card mb-4">
                        <div class="card-body">
                            <h3 class="h5">For Teachers</h3>
                            <p>Create and manage courses, upload materials, and track student progress.</p>
                        </div>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="card mb-4">
                        <div class="card-body">
                            <h3 class="h5">For Students</h3>
                            <p>Access course materials, submit assignments, and communicate with teachers.</p>
                        </div>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="card mb-4">
                        <div class="card-body">
                            <h3 class="h5">Smart Support</h3>
                            <p>Get instant answers to your questions with our AI-powered chatbot.</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script src="https://unpkg.com/aos@2.3.1/dist/aos.js"></script>
<link href="https://unpkg.com/aos@2.3.1/dist/aos.css" rel="stylesheet">
@endsection
