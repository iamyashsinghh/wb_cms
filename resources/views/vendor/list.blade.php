@extends('layouts.app')
@section('title', "Vendor List")

@section('header-css')
    <link rel="stylesheet" href="//cdn.datatables.net/1.13.1/css/jquery.dataTables.min.css">
@endsection
@section('main')
<div class="content-wrapper pb-5">
    <section class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6">
                    <h1 class="m-0">Vendor List</h1>
                </div>
            </div>
            <div class="button-group my-4">
                <a href="{{route('vendor.add')}}" class="btn btn-sm text-light buttons-print" style="background-color: var(--wb-renosand)"><i class="fa fa-plus mr-1"></i>Add New</a>
            </div>
        </div>
    </section>
    <section class="content">
        <div class="container-fluid">
            <div class="table-responsive">
                <table id="serverTable" class="table text-sm">
                    <thead>
                        <th>ID</th>
                        <th>Brand Name</th>
                        <th>Category</th>
                        <th>Phone Number</th>
                        <th>City</th>
                        <th>Locality</th>
                        <th>WB Assured</th>
                        <th>Popular</th>
                        <th>Status</th>
                        <th>Image Status</th>
                        <th class="text-center">Action</th>
                    </thead>
                </table>
            </div>
        </div>
    </section>
    @include('includes.update_phone_no_modal')
    @include('includes.delete_vendor_venue_modal')
    @include('includes.update_meta_modal')
</div>
@endsection
@section('footer-script')
<script src="//cdn.datatables.net/1.13.1/js/jquery.dataTables.min.js"></script>
<script>
     $(document).ready(function() {
        $('#serverTable').DataTable({
            pageLength: 10,
            processing: true,
            searchable: true,
            ordering: true,
            language: {
                "search": "_INPUT_", // Removes the 'Search' field label
                "searchPlaceholder": "Type here to search..", // Placeholder for the search box
            },
            serverSide: true,
            ajax: `{{route('vendor.ajax_list')}}`,
            order: [
                [0, 'desc']
            ],
            rowCallback: function(row, data, index) {
                const td_elements = row.querySelectorAll('td');

                if(data[6] == 1){
                    wb_assured = `<a data-id="${data[0]}" data-status="0" data-submit-url="{{route('vendor.update_wb_assured_status')}}" href="javascript:void(0);" style="font-size: 22px;" onclick="handle_update_status(this)"><i class="fa fa-toggle-on text-success"></i></a>`;
                }else{
                    wb_assured = `<a data-id="${data[0]}" data-status="1" data-submit-url="{{route('vendor.update_wb_assured_status')}}" href="javascript:void(0);" style="font-size: 22px;" onclick="handle_update_status(this)"><i class="fa fa-toggle-off text-danger"></i></a>`;
                }
                if(data[7] == 1){
                    popular_status = `<a data-id="${data[0]}" data-status="0" data-submit-url="{{route('vendor.update_popular_status')}}" href="javascript:void(0);" style="font-size: 22px;" onclick="handle_update_status(this)"><i class="fa fa-toggle-on text-success"></i></a>`;
                }else{
                    popular_status = `<a data-id="${data[0]}" data-status="1" data-submit-url="{{route('vendor.update_popular_status')}}" href="javascript:void(0);" style="font-size: 22px;" onclick="handle_update_status(this)"><i class="fa fa-toggle-off text-danger"></i></a>`;
                }
                if(data[8] == 1){
                    status_elem = `<a data-id="${data[0]}" data-status="0" data-submit-url="{{route('vendor.update_status')}}" href="javascript:void(0);" style="font-size: 22px;" onclick="handle_update_status(this)"><i class="fa fa-toggle-on text-success"></i></a>`;
                }else{
                    status_elem = `<a data-id="${data[0]}" data-status="1" data-submit-url="{{route('vendor.update_status')}}" href="javascript:void(0);" style="font-size: 22px;" onclick="handle_update_status(this)"><i class="fa fa-toggle-off text-danger"></i></a>`;
                }


                td_elements[6].innerHTML = wb_assured;
                td_elements[7].innerHTML = popular_status;
                td_elements[8].innerHTML = status_elem;
                if(data[9] == null){
                    td_elements[9].innerHTML = `<span class="badge badge-danger">Not Available</span>`;
                }else{
                    td_elements[9].innerHTML = `<span class="badge badge-success">Available</span>`;
                }

                td_elements[10].classList = 'text-center text-nowrap';
                td_elements[10].innerHTML = `<a href="{{route('vendor.edit')}}/${data[0]}" class="text-success mx-2" title="Edit">
                    <i class="fa fa-edit" style="font-size: 15px;"></i>
                </a>
                <a onclick="handle_delete_vendor(${data[0]})" class="text-danger mx-2" title="Delete">
                    <i class="fa fa-trash-alt" style="font-size: 15px;"></i>
                </a>
                <div class="dropdown d-inline-block mx-2">
                    <a href="javascript:void(0);" data-bs-toggle="dropdown" aria-expanded="false">
                    <i class="fa fa-caret-down text-dark"></i>
                    </a>
                    <ul class="dropdown-menu">
                        <li><a class="dropdown-item" href="{{route('vendor.manage_images')}}/${data[0]}">Update Images</a></li>
                        <li><a class="dropdown-item" href="javascript:void(0);" onclick="handle_update_phone_no('vendor', ${data[0]})">Update Phone Number</a></li>
                        <li><a class="dropdown-item" href="javascript:void(0);" onclick="handle_update_meta('vendor', ${data[0]})">Update Meta</a></li>
                    </ul>
                </div>`;
            }
        });
    });


    function handle_delete_vendor(vendor_id) {
    const deleteVendorModal = new bootstrap.Modal(document.getElementById('deleteVendorVenueModal'));
    const deleteVendorForm = document.getElementById('deleteVendorVenueForm');
    const actionUrl = `{{ route('vendor.destroy', ':id') }}`.replace(':id', vendor_id);
    deleteVendorForm.action = actionUrl;
    deleteVendorModal.show();
}

    function handle_update_phone_no(vendor_id){
        const action_url = `{{route('vendor.update_phoneNumber')}}/${vendor_id}`;
        const updatePhoneNoModal = document.getElementById('updatePhoneNoModal');
        const modal = new bootstrap.Modal(updatePhoneNoModal);
        updatePhoneNoModal.querySelector('form').action = action_url;
        updatePhoneNoModal.querySelector('input[name="phone_number"]').value = "";
        modal.show();
    }

    function handle_update_phone_no(vendor_id){
        const action_url = `{{route('vendor.update_phoneNumber')}}/${vendor_id}`;
        const updatePhoneNoModal = document.getElementById('updatePhoneNoModal');
        const modal = new bootstrap.Modal(updatePhoneNoModal);
        updatePhoneNoModal.querySelector('form').action = action_url;
        updatePhoneNoModal.querySelector('input[name="phone_number"]').value = "";
        modal.show();
    }



    function handle_update_status(elem){
        if(confirm("Are you sure want to update the status")){
            const submit_url = elem.getAttribute('data-submit-url');
            const data_id = elem.getAttribute('data-id');
            const data_status = elem.getAttribute('data-status');
            fetch(`${submit_url}/${data_id}/${data_status}`).then(response => response.json()).then(data => {
                if(data.success === true){
                    const icon = elem.firstChild;
                    if(data_status == 0){
                        icon.classList = `fa fa-toggle-off text-danger`;
                        elem.setAttribute('data-status', 1);
                    }else{
                        icon.classList = `fa fa-toggle-on text-success`;
                        elem.setAttribute('data-status', 0);
                    }
                }
                toastr[data.alert_type](data.message);
            })
        }
    }
</script>

@endsection
