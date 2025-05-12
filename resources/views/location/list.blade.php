@extends('layouts.app')
@section('title', "Locations")

@section('header-css')
<link rel="stylesheet" href="//cdn.datatables.net/1.13.1/css/jquery.dataTables.min.css">
@endsection
@section('main')
<div class="content-wrapper pb-5">
    <section class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6">
                    <h1 class="m-0">Locations</h1>
                </div>
            </div>
            <div class="button-group my-4">
                <a href="javascript:void(0);" onclick="handle_location_add()" class="btn btn-sm text-light mx-1" style="background-color: var(--wb-renosand)"><i class="fa fa-plus mr-1"></i>Add New</a>
                <a href="{{route('location.add_group')}}" class="btn btn-sm text-light mx-1" style="background-color: var(--wb-dark-red)"><i class="fa fa-plus mr-1"></i>Add Group</a>
            </div>
        </div>
    </section>
    <section class="content">
        <div class="container-fluid">
            <div class="table-responsive">
                <table id="serverTable" class="table">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Name</th>
                            <th>Slug</th>
                            <th>City Name</th>
                            <th class="text-center">Action</th>
                        </tr>
                    </thead>
                </table>
            </div>
        </div>
    </section>
    @include('location/manage_modal')
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
            ajax: `{{route('location.ajax_list')}}`,
            order: [
                [0, 'desc']
            ],
            rowCallback: function(row, data, index) {
                const td_elements = row.querySelectorAll('td');
                td_elements[4].classList.add('text-center');
                if(data[4] == 1){
                    td_elements[1].innerHTML = `${data[1]} <span class="badge badge-primary ml-2">Group</span>`
                    edit_action_btn = `<a href="{{route('location.edit_group')}}/${data[0]}" class="text-success mx-2" title="Edit">
                        <i class="fa fa-edit" style="font-size: 15px;"></i>
                    </a>`;
                }else{
                    edit_action_btn = `<a href="javascript:void(0);" class="text-success mx-2" title="Edit" onclick="handle_location_edit(${data[0]})">
                        <i class="fa fa-edit" style="font-size: 15px;"></i>
                    </a>`;
                    convert_to_group_btn = `<a href="javascript:void(0);" class="text-primary mx-2" title="Convert to Group" onclick="convert_to_group(${data[0]})">
                        <i class="fa fa-object-group" style="font-size: 15px;"></i>
                    </a>`;
                }
                delete_action_elem = `<a href="javascript:void(0);" onclick="return confirm('Are you sure want to delete?')" class="text-danger mx-2" title="Delete">
                    <i class="fa fa-trash-alt" style="font-size: 15px;"></i>
                </a>`;

                if(data[4] == 1){
                    td_elements[4].innerHTML = edit_action_btn + delete_action_elem;
                } else {
                    td_elements[4].innerHTML = edit_action_btn + convert_to_group_btn + delete_action_elem;
                }
            }
        });
    });

    const manageLocationModal = document.getElementById("manageLocationModal");
    const locationModalHeading = manageLocationModal.querySelector('.modal-title');
    const location_name_inp = manageLocationModal.querySelector('input[name="location_name"]');
    const location_form = manageLocationModal.querySelector('form');
    const locationModal = new bootstrap.Modal(manageLocationModal);

    function handle_location_add() {
        locationModalHeading.innerText = "Add Location";
        location_name_inp.value = "";
        location_form.action = `{{route('location.manage_process')}}`;
        locationModal.show();
    }

    function handle_location_edit(location_id) {
        fetch(`{{route('location.edit')}}/${location_id}`).then(response => response.json()).then(data => {
            if (data.success) {
                locationModalHeading.innerText = "Edit Location";
                location_name_inp.value = data.location.name;
                location_form.action = `{{route('location.manage_process')}}/${data.location.id}`;
                let option = document.querySelector(`select[name="city"] option[value="${data.location.city_id}"]`)
                if (option) {
                    option.selected = true;
                }
                locationModal.show();
            } else {
                toastr.error(data.message);
            }
        })
    }

    function convert_to_group(location_id) {
        if(confirm('Are you sure you want to convert this location to a group?')) {
            fetch(`{{route('location.convert_to_group')}}/${location_id}`)
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        toastr.success('Location converted to group successfully');
                        $('#serverTable').DataTable().ajax.reload();
                    } else {
                        toastr.error(data.message);
                    }
                });
        }
    }
</script>

@endsection