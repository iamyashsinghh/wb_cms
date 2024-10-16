@extends('layouts.app')

@php
    $page_heading = 'Index Web Page'
@endphp

@section('title', $page_heading)

@section('header-css')
@endsection

@section('main')
    <div class="content-wrapper pb-5">
        <section class="content-header">
            <div class="container-fluid">
                <h1 class="m-0">{{ $page_heading }}</h1>
            </div>
        </section>

        <section class="content">
            <div class="container-fluid">
                <div class="card text-sm">
                    <div class="card-header">
                        <h3 class="card-title">Submit URL for Indexing</h3>
                    </div>
                    <div class="card-body">
                        <form action="{{ route('index-url') }}" method="POST">
                            @csrf
                            <div class="form-group">
                                <label for="url">Web Page URL</label>
                                <input type="url" name="url" id="url" class="form-control" placeholder="Enter the web page URL" required>
                            </div>
                            <div class="form-group">
                                <button type="submit" class="btn btn-primary">Submit URL for Indexing</button>
                            </div>
                        </form>
                    </div>
                </div>

                <!-- Optionally, you can add a delete URL form below -->
                <div class="card text-sm mt-4">
                    <div class="card-header">
                        <h3 class="card-title">Remove URL from Index</h3>
                    </div>
                    <div class="card-body">
                        <form action="{{ route('delete-url') }}" method="POST">
                            @csrf
                            <div class="form-group">
                                <label for="delete_url">Web Page URL to Delete</label>
                                <input type="url" name="delete_url" id="delete_url" class="form-control" placeholder="Enter the web page URL to remove" required>
                            </div>
                            <div class="form-group">
                                <button type="submit" class="btn btn-danger">Delete URL from Index</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </section>
    </div>
@endsection

@section('footer-script')
@endsection
