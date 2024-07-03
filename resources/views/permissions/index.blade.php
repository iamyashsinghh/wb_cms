@extends('layouts.app')

@section('title', 'Permissions')

@section('header-css')
    <link rel="stylesheet" href="{{ asset('plugins/select2/css/select2.min.css') }}">
    <link rel="stylesheet" href="{{ asset('plugins/bootstrap/css/bootstrap.min.css') }}">
@endsection

@section('main')
<div class="content-wrapper pb-5">
    <section class="content-header">
        <div class="container-fluid">
            <h1 class="m-0">Permissions</h1>
        </div>
    </section>
    <section class="content">
        <div class="container-fluid">
            <div class="card">
                <div class="card-header">
                    <button class="btn btn-sm btn-primary" data-bs-toggle="modal" data-bs-target="#permissionModal">Add New Permission</button>
                </div>
                <div class="card-body">
                    <table class="table table-bordered">
                        <thead>
                            <tr>
                                <th>Permission</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($permissions as $permission)
                                <tr>
                                    <td>{{ $permission->name }}</td>
                                    <td>
                                        <button class="btn btn-sm btn-info edit-permission" data-id="{{ $permission->id }}" data-name="{{ $permission->name }}">Edit</button>
                                        <button class="btn btn-sm btn-danger delete-permission" data-id="{{ $permission->id }}">Delete</button>
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

<!-- Permission Modal -->
<div class="modal fade" id="permissionModal" tabindex="-1" aria-labelledby="permissionModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <form id="permissionForm">
            @csrf
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="permissionModalLabel">Add Permission</h5>
                    <h4 type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close">X</h4>
                </div>
                <div class="modal-body">
                    <div class="form-group">
                        <label for="permissionName">Permission Name</label>
                        <input type="text" class="form-control" id="permissionName" name="name" required>
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
<script>
    $(document).ready(function() {
        $('#permissionModal').on('hidden.bs.modal', function () {
            $('#permissionModalLabel').text('Add Permission');
            $('#permissionForm').trigger('reset');
            $('#permissionForm').attr('action', '{{ route('permissions.store') }}');
            $('#permissionForm').find('input[name="_method"]').remove();
        });

        $('#permissionForm').submit(function(e) {
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
                    $('#permissionModal').modal('hide');
                    location.reload();
                },
                error: function(response) {
                    alert('An error occurred.');
                }
            });
        });

        $('.edit-permission').click(function() {
            var id = $(this).data('id');
            var name = $(this).data('name');

            $('#permissionModalLabel').text('Edit Permission');
            $('#permissionName').val(name);
            $('#permissionForm').attr('action', '/permissions/' + id);
            $('#permissionForm').find('input[name="_method"]').remove();
            $('#permissionForm').append('<input type="hidden" name="_method" value="PUT">');
            $('#permissionModal').modal('show');
        });

        $('.delete-permission').click(function() {
            if (confirm('Are you sure you want to delete this permission?')) {
                var id = $(this).data('id');
                $.ajax({
                    url: '/permissions/' + id,
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
