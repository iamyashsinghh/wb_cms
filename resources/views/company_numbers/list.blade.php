@extends('layouts.app')
@section('title', 'Number List')
@section('header-css')
    <link rel="stylesheet" href="//cdn.datatables.net/1.13.1/css/jquery.dataTables.min.css">
@endsection
@section('main')
    <div class="content-wrapper pb-5">
        <section class="content-header">
            <div class="container-fluid">
                <div class="row mb-2">
                    <div class="col-sm-6">
                        <h1 class="m-0">Numbers List</h1>
                    </div>
                </div>
                <div class="button-group my-4">
                    <a href="javascript:void(0);" onclick="handle_manage_number(0, 0)" class="btn btn-sm text-light buttons-print"
                        style="background-color: var(--wb-renosand)"><i class="fa fa-plus mr-1"></i>Add New</a>
                </div>
            </div>
        </section>
        <section class="content">
            <div class="container-fluid">
                <div class="table-responsive">
                    <table id="serverTable" class="table text-sm">
                        <thead>
                            <th>ID</th>
                            <th>Number</th>
                            <th class="text-center no-sort">Action</th>
                        </thead>
                    </table>
                </div>
            </div>
        </section>
    </div>
    @include('company_numbers.manage');
    @include('company_numbers.delete');
@endsection
@section('footer-script')
    <script src="//cdn.datatables.net/1.13.1/js/jquery.dataTables.min.js"></script>
    <script>

function handle_manage_number(c_num_id, number) {
    const manageNumberModal = new bootstrap.Modal(document.getElementById('manageNumber'));
    const manageNumberForm = document.getElementById('manageNumberForm');
    const form_title = document.getElementById('form_title');
    const form_input = document.getElementById('phone_inp');
    const form_input_id = document.getElementById('phone_inp_id');
    if(c_num_id == 0){
        form_title.innerHTML = "Add new phone number";
    }else{
        form_input.value  = number;
        form_title.innerHTML = "Edit phone number";
    }
    form_input_id.value  = c_num_id;
    const actionUrl = `{{ route('c_num.manage_process', ':id') }}`.replace(':id', c_num_id);
    manageNumberForm.action = actionUrl;
    manageNumberModal.show();
}

function handle_delete_number(c_num_id) {
    const deleteNumberModal = new bootstrap.Modal(document.getElementById('deleteNumber'));
    const deleteNumberForm = document.getElementById('deleteNumberForm');
    const actionUrl = `{{ route('c_num.destroy', ':id') }}`.replace(':id', c_num_id);
    deleteNumberForm.action = actionUrl;
    deleteNumberModal.show();
}

        $(document).ready(function() {
            $('#serverTable').DataTable({
                pageLength: 10,
                processing: true,
                searchable: true,
                ordering: true,
                language: {
                    "search": "_INPUT_",
                    "searchPlaceholder": "Type here to search..",
                },
                serverSide: true,
                ajax: "{{ route('c_num.ajax_list') }}",
                dataSrc: function(json) {
                    console.log(json);
                    return json;
                },
                order: [
                    [0, 'desc']
                ],
                columnDefs: [{
                    targets: 'no-sort',
                    "defaultContent": "-"
                }],
                rowCallback: function(row, data, index) {
                    const td_elements = row.querySelectorAll('td');
                    td_elements[2].innerHTML = `<a href="javascript:void(0);" onclick="handle_manage_number(${data[0]}, ${data[1]})" class="text-success mx-2" title="Edit">
                            <i class="fa fa-edit" style="font-size: 15px;"></i>
                        </a>
                        <a href="javascript:void(0);" onclick="handle_delete_number(${data[0]})" class="text-danger mx-2" title="Delete">
                            <i class="fa fa-trash-alt" style="font-size: 15px;"></i>
                        </a>`;
                }
            });
        });

    </script>
@endsection
