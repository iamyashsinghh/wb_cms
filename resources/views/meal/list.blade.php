@extends('layouts.app')
@section('title', "Meals")

@section('header-css')
<link rel="stylesheet" href="//cdn.datatables.net/1.13.1/css/jquery.dataTables.min.css">
@endsection
@section('main')
<div class="content-wrapper pb-5">
    <section class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6">
                    <h1 class="m-0">Meals</h1>
                </div>
            </div>
            <div class="button-group my-4">
                <a href="javascript:void(0);" onclick="handle_meal_add()" class="btn btn-sm text-light mx-1" style="background-color: var(--wb-renosand)"><i class="fa fa-plus mr-1"></i>Add New</a>
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
                            <th>Meal Category</th>
                            <th class="text-center">Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($meals as $list)
                            <tr>
                                <td>{{$list->id}}</td>
                                <td>{{$list->name}}</td>
                                <td>{{$list->category_id == 1 ? 'Veg' : 'NonVeg'}}</td>
                                <td class="text-center">
                                    <a href="javascript:void(0);" class="text-success mx-2" title="Edit" onclick="handle_meal_edit({{$list->id}})">
                                        <i class="fa fa-edit" style="font-size: 15px;"></i>
                                    </a>
                                    <a href="{{route('meal.delete', $list->id)}}" onclick="return confirm('Are you sure want to delete?')" class="text-danger mx-2" title="Delete">
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
    @include('meal/manage_modal')
</div>
@endsection
@section('footer-script')
<script src="//cdn.datatables.net/1.13.1/js/jquery.dataTables.min.js"></script>
<script>
    init_client_datatables("clientTable");
    const manageMealModal = document.getElementById("manageMealModal");
    const mealModalHeading = manageMealModal.querySelector('.modal-title');
    const meal_name_inp = manageMealModal.querySelector('input[name="meal_name"]');
    const meal_form = manageMealModal.querySelector('form'); 
    const mealModal = new bootstrap.Modal(manageMealModal);

    function handle_meal_add() {
        mealModalHeading.innerText = "Add Meal";
        meal_name_inp.value = "";
        meal_form.action = `{{route('meal.manage_process')}}`;
        mealModal.show();
    }

    function handle_meal_edit(meal_id) {
        fetch(`{{route('meal.edit')}}/${meal_id}`).then(response => response.json()).then(data => {
            if (data.success) {
                mealModalHeading.innerText = "Edit Meal";
                meal_name_inp.value = data.meal.name;
                meal_form.action = `{{route('meal.manage_process')}}/${data.meal.id}`;
                let option = document.querySelector(`select[name="category"] option[value="${data.meal.category_id}"]`)
                if (option) {
                    option.selected = true;
                }
                mealModal.show();
            } else {
                toastr.error(data.message);
            }
        })
    }
</script>

@endsection