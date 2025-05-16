@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header">Add New Chatbot Response</div>

                <div class="card-body">
                    <form method="POST" action="{{ route('admin.chatbot.responses.store') }}">
                        @csrf

                        <div class="form-group row">
                            <label for="question" class="col-md-4 col-form-label text-md-right">Question Pattern</label>
                            <div class="col-md-6">
                                <textarea id="question" class="form-control @error('question') is-invalid @enderror" 
                                    name="question" required rows="3">{{ old('question') }}</textarea>
                                <small class="form-text text-muted">Enter the question pattern that the chatbot should recognize</small>
                                @error('question')
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                @enderror
                            </div>
                        </div>

                        <div class="form-group row">
                            <label for="answer" class="col-md-4 col-form-label text-md-right">Response</label>
                            <div class="col-md-6">
                                <textarea id="answer" class="form-control @error('answer') is-invalid @enderror" 
                                    name="answer" required rows="5">{{ old('answer') }}</textarea>
                                <small class="form-text text-muted">Enter the response that the chatbot should give</small>
                                @error('answer')
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                @enderror
                            </div>
                        </div>

                        <div class="form-group row mb-0">
                            <div class="col-md-6 offset-md-4">
                                <button type="submit" class="btn btn-primary">
                                    Add Response
                                </button>
                                <a href="{{ route('admin.chatbot.responses') }}" class="btn btn-secondary">
                                    Cancel
                                </a>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection 