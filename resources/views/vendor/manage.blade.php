@extends('layouts.app')
@section('title', $page_heading)
@section('header-css')
<link rel="stylesheet" href="{{asset('plugins/select2/css/select2.min.css')}}">
<link rel="stylesheet" href="{{asset('plugins/summernote/summernote-lite.min.css')}}">
@endsection
@section('main')
@php
 $similar_vendor_id_arr = explode(",", $vendor->similar_vendor_ids);
 $package_option = $vendor->package_option != null ? explode(",", $vendor->package_option) : [];
@endphp
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
                    <h3 class="card-title">Vendor Details</h3>
                </div>
                <form action="{{route('vendor.manage_process', $vendor->id)}}" method="post" enctype="multipart/form-data">
                    @csrf
                    <input type="hidden" name="business_user_id" value="{{isset($business_user_id) ? $business_user_id : 0}}">
                    <div class="card-body">
                        <div class="row">
                            <div class="col-sm-4 mb-3">
                                <div class="form-group">
                                    <label>Vendor Category <span class="text-danger">*</span></label>
                                    <select onchange="handle_category(this.value)" class="form-control select2" name="vendor_category" required>
                                        <option value="" disabled selected>Select an option</option>
                                        @foreach($vendor_categories as $list)
                                        <option value="{{$list->id}}" {{$list->id == $vendor->vendor_category_id ? 'selected':''}}>{{$list->name}}</option>
                                        @endforeach
                                    </select>
                                    @error('vendor_category')<span class=" ml-1 text-sm text-danger">{{$message}}</span>@enderror
                                </div>
                            </div>
                            <div class="col-sm-4 mb-3">
                                <div class="form-group">
                                    <label>Brand Name <span class="text-danger">*</span></label>
                                    <input type="text" class="form-control" placeholder="Enter brand name" name="brand_name" required oninput="generate_slug(this.value)" value="{{$vendor->brand_name}}">
                                    @error('venue_name')<span class="ml-1 text-sm text-danger">{{$message}}</span>@enderror
                                </div>
                            </div>
                            <div class="col-sm-4 mb-3">
                                <div class="form-group">
                                    <label>Slug URL <span class="text-danger">*</span></label>
                                    <input type="text" class="form-control" id="slug_inp" placeholder="Enter slug" name="slug" required value="{{$vendor->slug}}">
                                    @error('slug')<span class="ml-1 text-sm text-danger">{{$message}}</span>@enderror
                                </div>
                            </div>
                            <div class="col-sm-4 mb-3">
                                <div class="form-group">
                                    <label>Phone No. <span class="text-danger">*</span></label>
                                    <input type="number" class="form-control" placeholder="Enter phone no." name="phone_number" required minlength="11" maxlength="11" value="{{$vendor->phone}}" >
                                    @error('phone_number')<span class="ml-1 text-sm text-danger">{{$message}}</span>@enderror
                                </div>
                            </div>
                            <div class="col-sm-4 mb-3">
                                <div class="form-group">
                                    <label>City <span class="text-danger">*</span></label>
                                    <select class="form-control" name="city" onchange="get_locations(this.value);" required>
                                        @foreach($cities as $list)
                                        <option value="{{$list->id}}" {{$vendor->city_id == $list->id ? 'selected' : ''}}>{{$list->name}}</option>
                                        @endforeach
                                    </select>
                                    @error('city')<span class="ml-1 text-sm text-danger">{{$message}}</span>@enderror
                                </div>
                            </div>
                            <div class="col-sm-4 mb-3">
                                <div class="form-group">
                                    <label>Location <span class="text-danger">*</span></label>
                                    <select class="form-control select2" name="location" id="location_select" required>
                                        @foreach ($locations as $list)
                                            <option value="{{$list->id}}" {{$vendor->location_id == $list->id ? 'selected' : ''}}>{{$list->name}}</option>
                                        @endforeach
                                    </select>
                                    @error('location')<span class="ml-1 text-sm text-danger">{{$message}}</span>@enderror
                                </div>
                            </div>
                            <div class="col-sm-3 mb-3">
                                <div class="form-group">
                                    <label>Yrs of Exp.</label>
                                    <input type="number" class="form-control" placeholder="Enter years of experience" name="yrs_exp" value="{{$vendor->yrs_exp}}">
                                </div>
                                <div class="form-group">
                                    <label>Event Completed</label>
                                    <input type="number" class="form-control" placeholder="Enter no. of event completed" name="event_completed" value="{{$vendor->event_completed}}">
                                </div>
                            </div>
                            <div class="col-sm-9 mb-3">
                                <div class="form-group">
                                    <label>Address <span class="text-danger">*</span></label>
                                    <textarea type="text" class="form-control" rows="4" placeholder="Enter address" name="address" required>{{$vendor->vendor_address}}</textarea>
                                    @error('address')<span class="ml-1 text-sm text-danger">{{$message}}</span>@enderror
                                </div>
                            </div>

                            <div class="col-sm-4 mb-3">
                                <div class="form-group">
                                    <label>Package Price/Per Day</label>
                                    <input type="number" class="form-control" placeholder="Enter package price" name="package_price" value="{{$vendor->package_price}}">
                                </div>
                            </div>
                            <div class="col-sm-8 mb-3">
                                <div class="form-group">
                                    <label>Similar Vendors</label>
                                    <select class="form-control select2" id="similar_vendor_select" name="similar_vendors[]" multiple>
                                        @foreach ($similar_vendors as $list)
                                            <option value="{{$list->id}}" {{array_search($list->id, $similar_vendor_id_arr) !== false ? 'selected' : ''}}>{{$list->brand_name}} ({{$list->vendor_category}}, {{$list->city}})</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                            <div class="col-sm-4 mb-3">
                                <div class="form-group">
                                    <label>Meta Title</label>
                                    <textarea type="text" class="form-control" placeholder="Enter meta title" id="meta_title_count"  name="meta_title">{{$vendor->meta_title}}</textarea>
                                                                        <div id="charCountmeta_title_count">0 Characters</div>

                                </div>
                            </div>
                            <div class="col-sm-8 mb-3">
                                <div class="form-group">
                                    <label>Meta Description</label>
                                    <textarea type="text" class="form-control" placeholder="Enter meta description"  id="meta_description_count"  name="meta_description">{{$vendor->meta_description}}</textarea>
                                                                        <div id="charCountmeta_description_count">0 Characters</div>

                                </div>
                            </div>
                            <div class="col-sm-12 mb-3">
                                <div class="form-group">
                                    <label>Summary</label>
                                    <textarea type="text" class="form-control summernote" placeholder="Enter summary" name="summary">{{$vendor->summary}}</textarea>
                                </div>
                            </div>
                            <div class="col-sm-12 mb-3">
                                <label>Package Option</label>
                                <button type="button" class="btn btn-success btn-xs ml-3" onclick="handle_add_package_option(this)"><i class="fa fa-add"></i></button>
                                <div id="package_option_container" class="row">
                                    @foreach ($package_option as $list)
                                        <div class="col-sm-4 mb-3">
                                            <div class="form-group">
                                                <div class="d-flex justify-content-between align-items-center">
                                                    <label class="text-xs">Package</label>
                                                    <button class="btn btn-sm text-danger mr-3" onclick="handle_remove_package_option(this)"><i class="fa fa-trash"></i></button>
                                                </div>
                                                <input type="text" class="form-control" placeholder="Enter package details" name="package_option[]" value="{{$list}}">
                                            </div>
                                        </div>
                                    @endforeach
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="card-footer">
                        <div class="row">
                            <div class="col mb-3">
                                <p>
                                    <span class="text-danger text-bold">*</span>
                                    Fields are required.
                                </p>
                            </div>
                            <div class="col text-right">
                                <a href="{{route('venue.list')}}" class="btn btn-sm bg-secondary m-1">Back</a>
                                <button type="submit" class="btn btn-sm m-1 text-light" style="background-color: var(--wb-dark-red);">Submit</button>
                            </div>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </section>
</div>
@endsection
@section('footer-script')
<script src="{{asset('plugins/select2/js/select2.min.js')}}"></script>
<script src="{{asset('plugins/summernote/summernote-lite.min.js')}}"></script>
<script>
    $('.select2').select2({
        placeholder: 'Select an option'
    });

    $('.summernote').summernote({
        placeholder: 'Type here content',
        tabsize: 2,
        height: 200
    });


    let city_id = 1;
    let category_id = 1;
    function handle_category(value){
        category_id = value;
        console.log(category_id, city_id)
        get_similar_vendors(category_id, city_id);
    }

    function handle_add_package_option() {
        const package_option_container = document.getElementById('package_option_container');
        const div = document.createElement('div');
        div.classList = "col-sm-4 mb-3";

        const elem = `<div class="form-group">
            <div class="d-flex justify-content-between align-items-center">
                <label class="text-xs">Package</label>
                <button class="btn btn-sm text-danger mr-3" onclick="handle_remove_package_option(this)"><i class="fa fa-trash"></i></button>
            </div>
            <input type="text" class="form-control" placeholder="Enter package details" name="package_option[]" value="">
        </div>`;
        div.innerHTML = elem;
        package_option_container.append(div);

    }

    function handle_remove_package_option(elem) {
        elem.parentElement.parentElement.parentElement.remove();
    }

    function get_locations(updated_city_id) {
        const location_select = document.getElementById('location_select');
        location_select.innerHTML = "";
        fetch(`{{route('location.get_locations')}}/${updated_city_id}`).then(response => response.json()).then(data => {
            for (let item of data.locations) {
                let option = document.createElement('option');
                option.value = item.id;
                option.innerText = item.name;
                location_select.appendChild(option);
            }
        })
        city_id = updated_city_id;
        get_similar_vendors(category_id, city_id);
    }

    function get_similar_vendors(category_id, city_id) {
        console.log(city_id);
        const similar_vendor_select = document.getElementById('similar_vendor_select');
        similar_vendor_select.innerHTML = "";
        fetch(`{{route('vendor.get_similar_vendors')}}/${category_id}/${city_id}`).then(response => response.json()).then(data => {
            if(data.success == true){
                for (let item of data.vendors) {
                    let option = document.createElement('option');
                    option.value = item.id;
                    option.innerText = `${item.brand_name} (${item.vendor_category}, ${item.city})`;
                    similar_vendor_select.appendChild(option);
                }
            }else{
                toastr.error(data.message);
            }
        })
    }

    function generate_slug(str) {
            const localitySelect = document.querySelector('select[name="location"]');
            const citySelect = document.querySelector('select[name="city"]');

            function updateSlug() {
                const locality = localitySelect.options[localitySelect.selectedIndex]?.innerText.trim().toLowerCase() || '';
                const city = citySelect.options[citySelect.selectedIndex]?.innerText.trim().toLowerCase() || '';
                const newSlug = `${str}-${locality}`.replaceAll(" ", "-").toLowerCase();
                const fixedSlug= newSlug.toLowerCase()
                .replace(/[^a-z0-9\s-]/g, '')
                .trim()
                .replace(/\s+/g, '-')
                document.getElementById('slug_inp').value = fixedSlug;
            }

            localitySelect.addEventListener('change', updateSlug);
            citySelect.addEventListener('change', updateSlug);

            if (localitySelect.value && citySelect.value) {
                updateSlug();
            } else {
                document.getElementById('slug_inp').value = '';
                alert('Please select both Locality and City first.');
            }
        }
</script>
@endsection
