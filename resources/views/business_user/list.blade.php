@extends('layouts.app')
@section('title', 'Business Users')

@section('header-css')
    <link rel="stylesheet" href="//cdn.datatables.net/1.13.1/css/jquery.dataTables.min.css">
    <link rel="stylesheet" href="{{ asset('plugins/select2/css/select2.min.css') }}">
    <style>
        .select2 {
            width: 100% !important;
        }
    </style>
@endsection
@section('main')
    <div class="content-wrapper pb-5">
        <section class="content-header">
            <div class="container-fluid">
                <div class="row mb-2">
                    <div class="col-sm-6">
                        <h1 class="m-0">Business Users</h1>
                    </div>
                </div>
            </div>
        </section>
        <section class="content">
            <div class="container-fluid">
                <div class="table-responsive">
                    <table id="serverTable" class="table text-sm">
                        <thead>
                            <th>ID</th>
                            <th>Name</th>
                            <th class="text-nowrap">Business Name</th>
                            <th class="text-nowrap">Business Type</th>
                            <th>Category</th>
                            <th class="text-nowrap">Phone</th>
                            <th>Email</th>
                            <th>City</th>
                            <th>Address</th>
                            <th class="text-nowrap">User Status</th>
                            <th class="text-nowrap">Content Status</th>
                            <th class="text-nowrap">Images Status</th>
                            <th class="text-nowrap">Migrated Business ID</th>
                            <th class="text-center">Action</th>
                        </thead>
                        <tbody>
                            @foreach ($business_users as $list)
                                <tr>
                                    <td>{{ $list->id }}</td>
                                    <td>{{ $list->name }}</td>
                                    <td>{{ $list->business_name }}</td>
                                    <td>
                                        @if ($list->business_type == 1)
                                            <span class="badge text-xs text-light"
                                                style="background: var(--wb-dark-red);">Venue</span>
                                        @else
                                            <span class="badge text-xs text-light"
                                                style="background: var(--wb-renosand);">Vendor</span>
                                        @endif
                                    </td>
                                    <td>{{ $list->business_type == 1 ? $list->get_venue_category->name : $list->get_vendor_category->name }}
                                    </td>
                                    <td>{{ $list->phone }}</td>
                                    <td>{{ $list->email }}</td>
                                    <td>{{ $list->getCity->name }}</td>
                                    <td>{{ $list->address }}</td>
                                    <td>
                                        @canany(['publish business users', 'super power'])
                                            @php
                                                if ($list->user_status == 0) {
                                                    $element_class = 'fa fa-toggle-off text-danger';
                                                    $link = route('business_user.update_user_status', [$list->id, 1]);
                                                } else {
                                                    $element_class = 'fa fa-toggle-on text-success';
                                                    $link = route('business_user.update_user_status', [$list->id, 0]);
                                                }
                                            @endphp
                                        @else
                                            @php
                                                if ($list->user_status == 0) {
                                                    $element_class = 'fa fa-toggle-off text-danger';
                                                    $link = '';
                                                } else {
                                                    $element_class = 'fa fa-toggle-on text-success';
                                                    $link = '';
                                                }
                                            @endphp
                                        @endcanany
                                        <a href="{{ $link }}" style="font-size: 22px;"><i
                                                class="fa {{ $element_class }}"
                                                onclick="return confirm('Are you sure want to update the status?')"></i></a>
                                    </td>
                                    <td>
                                        @if ($list->content_status == 0)
                                            <span class="badge text-xs badge-secondary">Updated</span>
                                        @else
                                            <div class="dropdown">
                                                <button
                                                    class="btn btn-xs rounded-pill btn-{{ $list->content_status == 1 ? 'warning' : 'danger' }} dropdown-toggle"
                                                    type="button" data-bs-toggle="dropdown"
                                                    aria-expanded="false">{{ $list->content_status == 1 ? 'Pending' : 'Reject' }}</button>
                                                <ul class="dropdown-menu">
                                                    <li><a class="dropdown-item"
                                                            href="{{ route('business_user.manage_content', $list->id) }}"
                                                            target="_blank">Update</a></li>
                                                    <li><a class="dropdown-item" href="{{ route('business_user.update_content_status', [$list->id, 2]) }}">Reject</a>
                                                    </li>
                                                </ul>
                                            </div>
                                        @endif
                                    </td>
                                    <td>
                                        @if ($list->images_status == 0)
                                            <span class="badge text-xs badge-secondary">Updated</span>
                                        @else
                                            <div class="dropdown">
                                                <button
                                                    class="btn btn-xs rounded-pill btn-{{ $list->content_status == 1 ? 'warning' : 'danger' }} dropdown-toggle"
                                                    type="button" data-bs-toggle="dropdown"
                                                    aria-expanded="false">{{ $list->content_status == 1 ? 'Pending' : 'Reject' }}</button>
                                                <ul class="dropdown-menu">
                                                    <li><a class="dropdown-item"
                                                            href="{{ route('business_user.manage_images', $list->id) }}">Update</a>
                                                    </li>
                                                    <li><a class="dropdown-item"
                                                            href="{{ route('business_user.update_images_status', $list->id, 2) }}">Reject</a>
                                                    </li>
                                                </ul>
                                            </div>
                                        @endif
                                    </td>
                                    <td class="text-center">
                                        @if ($list->migrated_business_id == null)
                                            <a href="javascript:void(0);" data-user-id="{{ $list->id }}"
                                                data-bussiness_type-id="{{ $list->business_type }}"
                                                onclick="handle_user_migrate(this)" class="btn p-0"><span
                                                    class="badge text-xs badge-warning">Click to migrate</span></a>
                                        @else
                                            {{ $list->migrated_business_id }}
                                        @endif
                                    </td>
                                    <td class="text-center text-nowrap">

                                        @canany(['edit business users', 'super power'])
                                            <a href="javascript:void(0);"
                                                onclick="handle_business_user_edit({{ $list->id }})"
                                                class="text-success mx-2" title="Edit">
                                                <i class="fa fa-edit" style="font-size: 15px;"></i>
                                            </a>
                                        @endcanany

                                        @canany(['delete business users', 'super power'])
                                            <a href="{{ route('business_user.delete', $list->id) }}"
                                                onclick="return confirm('Are you sure want to delete?')"
                                                class="text-danger mx-2" title="Delete">
                                                <i class="fa fa-trash-alt" style="font-size: 15px;"></i>
                                            </a>
                                        @endcanany
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </section>
        @include('includes.update_phone_no_modal')
        @include('includes.update_meta_modal')
        <div class="modal fade" id="migrateUserModal" tabindex="-1">
            <div class="modal-dialog modal-md">
                <div class="modal-content">
                    <div class="modal-header">
                        <h4 class="modal-title">Migrate User with Listed Businesses </h4>
                        <button type="button" class="btn text-secondary" data-bs-dismiss="modal" aria-label="Close"><i
                                class="fa fa-times"></i></button>
                    </div>
                    <form action="{{ route('business_user.migrate') }}" method="post">
                        @csrf
                        <div class="modal-body text-sm">
                            <input type="hidden" name="user_id" value="">
                            <div class="">
                                <label>Select Listed Business <span class="text-danger">*</span></label>
                                <select id="bussiness_select" class="select2 w-100" name="listed_business" required>
                                </select>
                            </div>
                        </div>
                        <div class="modal-footer text-sm">
                            <a href="javascript:void(0);" class="btn btn-sm bg-secondary m-1"
                                data-bs-dismiss="modal">Close</a>
                            <button type="submit" class="btn btn-sm text-light m-1"
                                style="background-color: var(--wb-dark-red);">Submit</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
        <div class="modal fade" id="editBusinessUserModal" tabindex="-1">
            <div class="modal-dialog modal-lg">
                <div class="modal-content">
                    <div class="modal-header">
                        <h4 class="modal-title">Edit Business User</h4>
                        <button type="button" class="btn text-secondary" data-bs-dismiss="modal" aria-label="Close"><i
                                class="fa fa-times"></i></button>
                    </div>
                    <form action="{{ route('business_user.edit_process') }}" method="post">
                        @csrf
                        <div class="modal-body text-sm">
                            <div class="row">
                                <div class="col-sm-4 mb-3">
                                    <div class="form-group">
                                        <input type="hidden" name="user_id">
                                        <label>Name <span class="text-danger">*</span></label>
                                        <input type="text" class="form-control" placeholder="Enter user name" name="name" required>
                                    </div>
                                </div>
                                <div class="col-sm-4 mb-3">
                                    <div class="form-group">
                                        <label>Business Name <span class="text-danger">*</span></label>
                                        <input type="text" class="form-control" placeholder="Enter business name"
                                            name="business_name" required>
                                    </div>
                                </div>
                                <div class="col-sm-4 mb-3">
                                    <div class="form-group">
                                        <label>Category <span class="text-danger">*</span></label>
                                        <select class="form-control" name="business_category" required>
                                        </select>
                                    </div>
                                </div>
                                <div class="col-sm-4 mb-3">
                                    <div class="form-group">
                                        <label>Phone No. <span class="text-danger">*</span></label>
                                        <input type="number" class="form-control" placeholder="Enter phone no."
                                            name="phone_number" required minlength="11" maxlength="11">
                                    </div>
                                </div>
                                <div class="col-sm-4 mb-3">
                                    <div class="form-group">
                                        <label>Email</label>
                                        <input type="email" class="form-control" placeholder="Enter email"
                                            name="email">
                                    </div>
                                </div>
                                <div class="col-sm-4 mb-3">
                                    <div class="form-group">
                                        <label>City <span class="text-danger">*</span></label>
                                        <select class="form-control" name="city" required>
                                            @foreach ($cities as $list)
                                                <option value="{{ $list->id }}">{{ $list->name }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>
                                <div class="col-sm-12 mb-3">
                                    <div class="form-group">
                                        <label>Address <span class="text-danger">*</span></label>
                                        <textarea type="text" class="form-control" placeholder="Enter business name" name="address" required></textarea>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="modal-footer text-sm">
                            <a href="javascript:void(0);" class="btn btn-sm bg-secondary m-1"
                                data-bs-dismiss="modal">Close</a>
                            <button type="submit" class="btn btn-sm text-light m-1"
                                style="background-color: var(--wb-dark-red);">Submit</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection
@section('footer-script')
    <script src="//cdn.datatables.net/1.13.1/js/jquery.dataTables.min.js"></script>
    <script src="{{ asset('plugins/select2/js/select2.min.js') }}"></script>

    <script>
        $('#serverTable').DataTable();

        function handle_update_phone_no(vendor_id) {
            const action_url = `{{ route('vendor.update_phoneNumber') }}/${vendor_id}`;
            const updatePhoneNoModal = document.getElementById('updatePhoneNoModal');
            const modal = new bootstrap.Modal(updatePhoneNoModal);
            updatePhoneNoModal.querySelector('form').action = action_url;
            updatePhoneNoModal.querySelector('input[name="phone_number"]').value = "";
            modal.show();
        }

        function initialize_select2(route) {
            $("#bussiness_select").select2({
                dropdownParent: $("#migrateUserModal"),
                ajax: {
                    url: route,
                    dataType: "json"
                }
            })
        }

        function handle_user_migrate(elem) {
            const user_id = elem.getAttribute('data-user-id');
            const business_type_id = elem.getAttribute('data-bussiness_type-id');

            if (business_type_id == 1) {
                route = `{{ route('listed_venues.fetch') }}`;
            } else {
                route = `{{ route('listed_vendors.fetch') }}`;
            }
            initialize_select2(route)
            const migrateUserModal = document.getElementById('migrateUserModal');
            migrateUserModal.querySelector('input[name="user_id"]').value = user_id;
            const modal = new bootstrap.Modal(migrateUserModal);
            modal.show();

        }

        function handle_business_user_edit(user_id) {
            const editBusinessUserModal = document.getElementById('editBusinessUserModal');
            const modal = new bootstrap.Modal(editBusinessUserModal);
            fetch(`{{ route('business_user.edit') }}/${user_id}`).then(response => response.json()).then(data => {
                if (data.success == true) {
                    const user = data.user;
                    editBusinessUserModal.querySelector('input[name="user_id"]').value = user.id;
                    editBusinessUserModal.querySelector('input[name="name"]').value = user.name;
                    editBusinessUserModal.querySelector('textarea[name="address"]').innerText = user.address;
                    editBusinessUserModal.querySelector('input[name="business_name"]').value = user.business_name;
                    editBusinessUserModal.querySelector('input[name="phone_number"]').value = user.phone;
                    editBusinessUserModal.querySelector('input[name="email"]').value = user.email;

                    editBusinessUserModal.querySelector('select[name="city"]').value = user.city_id;
                    const business_category = editBusinessUserModal.querySelector(
                        'select[name="business_category"]');
                    business_category.innerHTML = "";
                    for (let category of data.categories) {
                        business_category.innerHTML +=
                            `<option value="${category.id}" ${data.user.business_category_id == category.id ? 'selected': ''}>${category.name}</option>`;
                    }
                    modal.show();
                } else {
                    toastr.error(data.message);
                }
            })
        }
    </script>

@endsection
