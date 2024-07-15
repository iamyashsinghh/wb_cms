@extends('layouts.app')

@section('title', $page_heading)

@section('header-css')
    <link rel="stylesheet" href="{{ asset('plugins/select2/css/select2.min.css') }}">
    <meta name="csrf-token" content="{{ csrf_token() }}">
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
                                @foreach (['name', 'email'] as $field)
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
                                        <label for="phone">Phone</label>
                                        <input type="text" id="phone" name="phone"
                                            class="form-control @error('phone') is-invalid @enderror"
                                            value="{{ old('phone') ?? $meta->phone }}" required>
                                        <div id="phone-feedback" class="invalid-feedback"></div>
                                        @error('phone')
                                            <div class="invalid-feedback" role="alert">
                                                <strong>{{ $message }}</strong>
                                            </div>
                                        @enderror
                                    </div>
                                </div>
                                <div class="col-sm-4">
                                    <div class="form-group">
                                        <label for="roles">Roles</label>
                                        <select id="roles" name="roles[]" class="form-control select2"
                                            multiple="multiple">
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
        @if ($meta->id > 0)
        <div class="container-fluid p-3">
            <div class="card">
                <div class="card-header">
                    <h4 class="mb-0 float-left">Registered Devices</h4>
                    <a class="float-right text-dark"
                        title="{{ $meta->can_add_device == 1 ? 'Remove permision to add device' : 'Give permision to add device' }}"
                        href="{{route('admin.permit.unpermit.canadddevice', [$meta->id, $meta->can_add_device == 1 ? 0 : 1])}}" style="font-size: 22px;"><i
                            class="fa {{ $meta->can_add_device == 1 ? 'fa-toggle-on text-success' : 'fa-toggle-off text-danger' }}"></i></a>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table">
                            <thead>
                                <tr>
                                    <th>S.No.</th>
                                    <th>Devices Name</th>
                                    <th class="text-nowrap">Created At</th>
                                    <th class="text-center">Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                @if (sizeof($meta->get_registered_devices) > 0)
                                    @foreach ($meta->get_registered_devices as $key => $list)
                                        <tr>
                                            <td>{{ $key + 1 }}</td>
                                            <td>{{ $list->device_name }}</td>
                                            <td class="text-nowrap">{{ date('d-m-Y h:i a', strtotime($list->created_at)) }}
                                            </td>
                                            <td class="text-center">
                                                <a href="{{route('admin.devices.manage.delete', $list->id)}}" onclick="return confirm('Are you sure want to delete.')"
                                                    class="text-danger mx-2"><i class="fa fa-trash-alt"></i></a>
                                            </td>
                                        </tr>
                                    @endforeach
                                @else
                                    <tr>
                                        <td class="text-center" colspan="4">No data available in table</td>
                                    </tr>
                                @endif
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
        @endif
    </div>
@endsection

@section('footer-script')
    <script src="{{ asset('plugins/select2/js/select2.min.js') }}"></script>
    <script>
        $(document).ready(function() {
            $('.select2').select2();
        });

        var phoneValid = true;

        document.querySelector('form').addEventListener('submit', function(e) {
            var password = document.getElementById('password').value;
            var confirmPassword = document.getElementById('password_confirmation').value;

            if (password !== '' && password !== confirmPassword) {
                e.preventDefault();
                alert('Passwords do not match.');
                return;
            }

            if (!phoneValid) {
                e.preventDefault();
                alert('Phone number is invalid.');
            }
        });

        document.getElementById('phone').addEventListener('input', function() {
            var phone = this.value;
            var token = document.querySelector('meta[name="csrf-token"]').getAttribute('content');

            fetch('{{ route('phone.validate') }}', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': token
                    },
                    body: JSON.stringify({
                        phone: phone
                    })
                })
                .then(response => response.json())
                .then(data => {
                    var feedback = document.getElementById('phone-feedback');
                    var phoneInput = document.getElementById('phone');
                    if (data.valid) {
                        feedback.textContent = 'Phone number is valid';
                        feedback.classList.remove('invalid-feedback');
                        feedback.classList.add('valid-feedback');
                        phoneInput.classList.remove('is-invalid');
                        phoneInput.classList.add('is-valid');
                        phoneValid = true;
                    } else {
                        feedback.textContent = data.message;
                        feedback.classList.remove('valid-feedback');
                        feedback.classList.add('invalid-feedback');
                        phoneInput.classList.remove('is-valid');
                        phoneInput.classList.add('is-invalid');
                        phoneValid = false;
                    }
                });
        });
    </script>

@endsection
