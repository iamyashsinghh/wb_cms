@extends('layouts.app')
@section('title', "Venue Categories")

@section('header-css')
<link rel="stylesheet" href="//cdn.datatables.net/1.13.1/css/jquery.dataTables.min.css">
@endsection
@section('main')
<div class="content-wrapper pb-5">
    <section class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6">
                    <h1 class="m-0">Venue Categories</h1>
                </div>
            </div>
            <div class="button-group my-4">
                <a href="javascript:void(0);" onclick="handle_category_add()" class="btn btn-sm text-light mx-1" style="background-color: var(--wb-renosand)"><i class="fa fa-plus mr-1"></i>Add New</a>
            </div>
        </div>
    </section>
    <section class="content">
        <div class="container-fluid">
            <div class="table-responsive">
                <table id="clientTable" class="table">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Name</th>
                            <th>Slug</th>
                            <th class="text-center">Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($categories as $list)
                            <tr>
                                <td>{{$list->id}}</td>
                                <td>{{$list->name}}</td>
                                <td>{{$list->slug}}</td>
                                <td class="text-center">
                                    <a href="javascript:void(0);" class="text-success mx-2" title="Edit" onclick="handle_category_edit({{$list->id}})">
                                        <i class="fa fa-edit" style="font-size: 15px;"></i>
                                    </a>
                                    <a href="{{route('venue_category.delete', $list->id)}}" onclick="return confirm('Are you sure want to delete?')" class="text-danger mx-2" title="Delete">
                                        <i class="fa fa-trash-alt" style="font-size: 15px;"></i>
                                    </a>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </section>
    @include('venue_category/manage_modal')
</div>
@endsection
@section('footer-script')
<script src="//cdn.datatables.net/1.13.1/js/jquery.dataTables.min.js"></script>
<script>
    init_client_datatables("clientTable");
    const manageVenueCategoryModal = document.getElementById("manageVenueCategoryModal");
    const ModalHeading = manageVenueCategoryModal.querySelector('.modal-title');
    const name_inp = manageVenueCategoryModal.querySelector('input[name="category_name"]');
    const modal_form = manageVenueCategoryModal.querySelector('form'); 
    const modal = new bootstrap.Modal(manageVenueCategoryModal);

    function handle_category_add() {
        ModalHeading.innerText = "Add Venue Category";
        name_inp.value = "";
        modal_form.action = `{{route('venue_category.manage_process')}}`;
        modal.show();
    }

    function handle_category_edit(id) {
        fetch(`{{route('venue_category.edit')}}/${id}`).then(response => response.json()).then(data => {
            if (data.success) {
                ModalHeading.innerText = "Edit Venue Category";
                name_inp.value = data.category.name;
                modal_form.action = `{{route('venue_category.manage_process')}}/${data.category.id}`;
                modal.show();
            } else {
                toastr.error(data.message);
            }
        })
    }
</script>

@endsection