@extends('layouts.app')
@section('title', $page_heading)

@section('main')
<div class="content-wrapper pb-5">
    <section class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6">
                    <h1 class="m-0">{{$page_heading}}</h1>
                </div>
            </div>
        </div>
    </section>
    <section class="content">
        <div class="container-fluid">
            <div class="card text-sm">
                <div class="card-header text-light" style="background-color: var(--wb-renosand)">
                    <h3 class="card-title">Group Details</h3>
                </div>
                <form action="{{route('location.group_manage_process', $group->id)}}" method="post" enctype="multipart/form-data">
                    @csrf
                    <div class="card-body">
                        <div class="row">
                            <div class="col-sm-6">
                                <div class="form-group">
                                    <label>Group Name <span class="text-danger">*</span></label>
                                    <input type="text" class="form-control" placeholder="Enter venue name" name="group_name" value="{{$group->name}}" required>
                                </div>
                            </div>
                            <div class="col-sm-6">
                                <div class="form-group">
                                    <label>City <span class="text-danger">*</span></label>
                                    <select class="form-control" name="city" required onchange="handle_get_locations(this.value)">
                                        <option disabled selected>Select City</option>
                                        @foreach($cities as $list)
                                            <option value="{{$list->id}}" {{$group->city_id == $list->id ? 'selected' : ''}}>{{$list->name}}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                            <div class="col-sm-12">
                                <div id="customRowContainer" class="row">
                                    @php
                                        $group_locality_id_arr = explode(",", $group->locality_ids);
                                    @endphp
                                    @foreach ($locations as $list)
                                        <div class="col-sm-2">
                                            <div class="custom-control custom-checkbox">
                                                <input class="custom-control-input" type="checkbox" id="locality_check{{$list->id}}" value="{{$list->id}}" name="localities[]" {{array_search($list->id, $group_locality_id_arr) !== false ?'checked' : ''}}>
                                                <label for="locality_check{{$list->id}}" class="custom-control-label">{{$list->name}}</label>
                                            </div>
                                        </div>
                                    @endforeach
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="card-footer text-right">
                        <a href="{{route('location.list')}}" class="btn btn-sm text-light bg-secondary mx-1">Back</a>
                        <button type="submit" class="btn btn-sm text-light mx-1" style="background: var(--wb-renosand);">Submit</button>
                    </div>
                </form>
            </div>
        </div>
    </section>
</div>

@section('footer-script')
<script>
    function handle_get_locations(city_id){
        fetch(`{{route('location.get_locations')}}/${city_id}`).then(response => response.json()).then(data => {
            const customRowContainer = document.getElementById('customRowContainer');
            customRowContainer.innerHTML = "";

            for(let item of data.locations){
                if(item.is_group == 0){
                    let col = document.createElement('div');
                    col.classList = "col-sm-2"
                    let elem = ` <div class="custom-control custom-checkbox">
                        <input class="custom-control-input" type="checkbox" id="locality_check${item.id}" value="${item.id}" name="localities[]">
                        <label for="locality_check${item.id}" class="custom-control-label">${item.name}</label>
                    </div>`;
    
                    col.innerHTML = elem;
                    customRowContainer.append(col);
                }
            }
        
        })
    }
</script>
    
@endsection
@endsection