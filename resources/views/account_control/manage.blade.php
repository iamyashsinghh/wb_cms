@extends('layouts.app')

@section('title', $page_heading)

@section('header-css')
    <link rel="stylesheet" href="{{ asset('plugins/select2/css/select2.min.css') }}">
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
                    <div class="card-header text-light" style="background-color: var(--wb-renosand)">
                        <h3 class="card-title">Account Details</h3>
                    </div>
                    <form action="{{ route('account.manage_process', $meta->id) }}" method="post">
                        @csrf
                        <div class="modal-body text-sm">
                            <div class="row">
                                @foreach (['name', 'email', 'phone'] as $field)
                                    <div class="col-sm-4">
                                        <div class="form-group">
                                            <label for="{{ $field }}">{{ ucfirst($field) }}</label>
                                            <input type="{{ $field === 'email' ? 'email' : 'text' }}"
                                                id="{{ $field }}" name="{{ $field }}"
                                                class="form-control @error($field) is-invalid @enderror"
                                                value="{{ old($field) ?? $meta->$field }}" required>
                                            @error($field)
                                                <span class="invalid-feedback" role="alert">
                                                    <strong>{{ $message }}</strong>
                                                </span>
                                            @enderror
                                        </div>
                                    </div>
                                @endforeach
                                <div class="col-sm-4">
                                    <div class="form-group">
                                        <label for="roles">Roles</label>
                                        <select id="roles" name="roles[]" class="form-control select2" multiple="multiple">
                                            @foreach ($roles as $role)
                                                <option value="{{ $role->name }}"
                                                    {{ in_array($role->name, old('roles', $meta->role_names ?? [])) ? 'selected' : '' }}>
                                                    {{ ucfirst($role->name) }}</option>
                                            @endforeach
                                        </select>
                                        @error('roles')
                                            <span class="invalid-feedback" role="alert">
                                                <strong>{{ $message }}</strong>
                                            </span>
                                        @enderror
                                    </div>
                                </div>
                                <div class="col-sm-4">
                                    <div class="form-group">
                                        <label for="password">Password</label>
                                        <input type="password" id="password" name="password"
                                            class="form-control @error('password') is-invalid @enderror">
                                        @error('password')
                                            <span class="invalid-feedback" role="alert">
                                                <strong>{{ $message }}</strong>
                                            </span>
                                        @enderror
                                    </div>
                                </div>
                                <div class="col-sm-4">
                                    <div class="form-group">
                                        <label for="password_confirmation">Confirm Password</label>
                                        <input type="password" id="password_confirmation" name="password_confirmation"
                                            class="form-control">
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="modal-footer text-sm">
                            <a href="{{ route('account.list') }}" class="btn btn-sm bg-secondary m-1">Back</a>
                            <button type="submit" class="btn btn-sm text-light m-1"
                                style="background-color: var(--wb-dark-red);">Submit</button>
                        </div>
                    </form>
                </div>
            </div>
        </section>
    </div>
@endsection

@section('footer-script')
    <script src="{{ asset('plugins/select2/js/select2.min.js') }}"></script>
    <script>
        $(document).ready(function() {
            $('.select2').select2();
        });

        document.querySelector('form').addEventListener('submit', function(e) {
            var password = document.getElementById('password').value;
            var confirmPassword = document.getElementById('password_confirmation').value;
            if (password !== '' && password !== confirmPassword) {
                e.preventDefault();
                alert('Passwords do not match.');
            }
        });
    </script>
@endsection
