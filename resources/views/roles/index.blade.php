@extends('layouts.app')

@section('title', 'Roles and Permissions')

@section('header-css')
    <link rel="stylesheet" href="{{ asset('plugins/select2/css/select2.min.css') }}">
    <link rel="stylesheet" href="{{ asset('plugins/bootstrap/css/bootstrap.min.css') }}">
@endsection

@section('main')
<div class="content-wrapper pb-5">
    <section class="content-header">
        <div class="container-fluid">
            <h1 class="m-0">Roles and Permissions</h1>
        </div>
    </section>
    <section class="content">
        <div class="container-fluid">
            <div class="card">
                <div class="card-header">
                    <button class="btn btn-sm btn-primary" data-bs-toggle="modal" data-bs-target="#roleModal">Add New Role</button>
                </div>
                <div class="card-body">
                    <table class="table table-bordered">
                        <thead>
                            <tr>
                                <th>Role</th>
                                <th>Permissions</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($roles as $role)
                                <tr>
                                    <td>{{ $role->name }}</td>
                                    <td>{{ implode(', ', $role->permissions->pluck('name')->toArray()) }}</td>
                                    <td>
                                        <button class="btn btn-sm btn-info edit-role" data-id="{{ $role->id }}" data-name="{{ $role->name }}" data-permissions="{{ implode(',', $role->permissions->pluck('name')->toArray()) }}">Edit</button>
                                        <button class="btn btn-sm btn-danger delete-role" data-id="{{ $role->id }}">Delete</button>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </section>
</div>

<!-- Role Modal -->
<div class="modal fade" id="roleModal" tabindex="-1" aria-labelledby="roleModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <form id="roleForm">
            @csrf
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="roleModalLabel">Add Role</h5>
                    <h4 type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close">X</h4>
                </div>
                <div class="modal-body">
                    <div class="form-group">
                        <label for="roleName">Role Name</label>
                        <input type="text" class="form-control" id="roleName" name="name" required>
                    </div>
                    <div class="form-group">
                        <label for="permissions">Permissions</label>
                        <select id="permissions" name="permissions[]" class="form-control select2" multiple="multiple">
                            @foreach ($permissions as $permission)
                                <option value="{{ $permission->name }}">{{ ucfirst($permission->name) }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                    <button type="submit" class="btn btn-primary">Save changes</button>
                </div>
            </div>
        </form>
    </div>
</div>
@endsection

@section('footer-script')
<script src="{{ asset('plugins/jquery/jquery.min.js') }}"></script>
<script src="{{ asset('plugins/bootstrap/js/bootstrap.bundle.min.js') }}"></script>
<script src="{{ asset('plugins/select2/js/select2.min.js') }}"></script>
<script>
    $(document).ready(function() {
        $('.select2').select2();

        $('#roleModal').on('hidden.bs.modal', function () {
            $('#roleModalLabel').text('Add Role');
            $('#roleForm').trigger('reset');
            $('#permissions').val(null).trigger('change');
            $('#roleForm').attr('action', '{{ route('roles.store') }}');
            $('#roleForm').find('input[name="_method"]').remove();
        });

        $('#roleForm').submit(function(e) {
            e.preventDefault();
            var formData = $(this).serialize();
            var url = $(this).attr('action');
            var method = $(this).find('input[name="_method"]').val() || 'POST';

            $.ajax({
                url: url,
                method: method,
                data: formData,
                success: function(response) {
                    alert(response.success);
                    $('#roleModal').modal('hide');
                    location.reload();
                },
                error: function(response) {
                    alert('An error occurred.');
                }
            });
        });

        $('.edit-role').click(function() {
            var id = $(this).data('id');
            var name = $(this).data('name');
            var permissions = $(this).data('permissions').split(',');

            $('#roleModalLabel').text('Edit Role');
            $('#roleName').val(name);
            $('#permissions').val(permissions).trigger('change');
            $('#roleForm').attr('action', '/roles/' + id);
            $('#roleForm').find('input[name="_method"]').remove();
            $('#roleForm').append('<input type="hidden" name="_method" value="PUT">');
            $('#roleModal').modal('show');
        });

        $('.delete-role').click(function() {
            if (confirm('Are you sure you want to delete this role?')) {
                var id = $(this).data('id');
                $.ajax({
                    url: '/roles/' + id,
                    method: 'DELETE',
                    data: {_token: '{{ csrf_token() }}'},
                    success: function(response) {
                        alert(response.success);
                        location.reload();
                    },
                    error: function(response) {
                        alert('An error occurred.');
                    }
                });
            }
        });
    });
</script>
@endsection
