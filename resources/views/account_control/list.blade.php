@extends('layouts.app')
@section('title', "All Accounts")
@section('header-css')
    <link rel="stylesheet" href="//cdn.datatables.net/1.13.1/css/jquery.dataTables.min.css">
@endsection
@section('main')
<div class="content-wrapper pb-5">
    <section class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6">
                    <h1 class="m-0">All Accounts</h1>
                </div>
            </div>
            <div class="button-group my-4">
                <a href="{{route('account.manage')}}" class="btn btn-sm text-light buttons-print" style="background-color: var(--wb-renosand)"><i class="fa fa-plus mr-1"></i>Add New</a>
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
                        <th>Email</th>
                        <th>Phone</th>
                        <th class="text-center">Action</th>
                    </thead>
                </table>
            </div>
        </div>
    </section>
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
                "search": "_INPUT_",
                "searchPlaceholder": "Type here to search..",
            },
            serverSide: true,
            ajax: `{{route('account.ajax_list')}}`,
            order: [
                [0, 'desc']
            ],
            rowCallback: function(row, data, index) {
                const td_elements = row.querySelectorAll('td');
                td_elements[4].innerHTML = `<a href="{{route('account.manage')}}/${data[0]}" class="text-success mx-2" title="Edit">
                    <i class="fa fa-edit" style="font-size: 15px;"></i>
                </a>
                <a href="{{route('account.delete') }}/${data[0]}" onclick="return alert('Are you sure want to delete the account')" class="text-danger mx-2" title="Delete">
                    <i class="fa fa-trash-alt" style="font-size: 15px;"></i>
                </a>`;
            }
        });
    });
</script>

@endsection
