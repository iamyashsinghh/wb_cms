@extends('layouts.app')
@section('title', $page_heading)
@section('header-css')
    <link rel="stylesheet" href="{{ asset('plugins/select2/css/select2.min.css') }}">
    <link rel="stylesheet" href="{{ asset('plugins/summernote/summernote-lite.min.css') }}">
@endsection
@section('main')
    @php
        $venue_category_id_arr = explode(',', $venue->venue_category_ids);
        $related_location_ids = explode(',', $venue->related_location_ids);
        $veg_foods = json_decode($venue->veg_foods, true);
        $nonveg_foods = json_decode($venue->nonveg_foods, true);

        $similar_venue_id_arr = explode(',', $venue->similar_venue_ids);
        $area_capacity = json_decode($venue->area_capacity);
    @endphp
    <div class="content-wrapper pb-5">
        <section class="content-header">
            <div class="container-fluid">
                <div class="row mb-2">
                    <div class="col-sm-6">
                        <h1 class="m-0">{{ $page_heading }}</h1>
                    </div>
                </div>
            </div>
        </section>
        <section class="content">
            <div class="container-fluid">
                <div class="card text-sm">
                    <div class="card-header text-light" style="background-color: var(--wb-renosand)">
                        <h3 class="card-title">Venue Details</h3>
                        @if ($venue->id >= 1)
                            @canany(['super power', 'manage venue_vendor redirect'])
                                <a class="float-right text-dark"
                                    title="{{ $venue->is_redirect == 1 ? 'Diable redirect to listing page' : 'Enable redirect to listing page' }}"
                                    href="{{ route('venue.update.redirect.manage', [$venue->id, $venue->is_redirect == 1 ? 0 : 1]) }}"
                                    style="font-size: 22px;"><i
                                        class="fa {{ $venue->is_redirect == 1 ? 'fa-toggle-on text-success' : 'fa-toggle-off text-light' }}"></i></a>
                            @endcanany
                        @endif
                    </div>
                    <form action="{{ route('venue.manage_process', $venue->id) }}" method="post"
                        enctype="multipart/form-data">
                        @csrf
                        <input type="hidden" name="business_user_id"
                            value="{{ isset($business_user_id) ? $business_user_id : 0 }}">
                        <div class="card-body">
                            <div class="row">
                                <div class="col-sm-4 mb-3">
                                    <div class="form-group">
                                        <label>Category <span class="text-danger">*</span></label>
                                        <select class="form-control select2" name="venue_category[]" required multiple>
                                            @foreach ($venue_categories as $list)
                                                <option value="{{ $list->id }}"
                                                    {{ array_search($list->id, $venue_category_id_arr) !== false ? 'selected' : '' }}>
                                                    {{ $list->name }}</option>
                                            @endforeach
                                        </select>
                                        @error('venue_category')
                                            <span class=" ml-1 text-sm text-danger">{{ $message }}</span>
                                        @enderror
                                    </div>
                                </div>
                                <div class="col-sm-4 mb-3">
                                    <div class="form-group">
                                        <label>Venue Name <span class="text-danger">*</span></label>
                                        <input type="text" class="form-control" placeholder="Enter venue name"
                                            name="venue_name" required oninput="generate_slug(this.value)"
                                            value="{{ $venue->name }}">
                                        @error('venue_name')
                                            <span class="ml-1 text-sm text-danger">{{ $message }}</span>
                                        @enderror
                                    </div>
                                </div>
                                <div class="col-sm-4 mb-3">
                                    <div class="form-group">
                                        <label>Slug URL <span class="text-danger">*</span></label>
                                        <input type="text" class="form-control" id="slug_inp" placeholder="Enter slug"
                                            name="slug" required value="{{ $venue->slug }}">
                                        @error('slug')
                                            <span class="ml-1 text-sm text-danger">{{ $message }}</span>
                                        @enderror
                                    </div>
                                </div>
                                <div class="col-sm-4 mb-3">
                                    <div class="form-group">
                                        <label>City <span class="text-danger">*</span></label>
                                        <select class="form-control" name="city" onchange="get_locations(this.value);"
                                            required>
                                            @foreach ($cities as $list)
                                                <option value="{{ $list->id }}"
                                                    {{ $venue->city_id == $list->id ? 'selected' : '' }}>
                                                    {{ $list->name }}</option>
                                            @endforeach
                                        </select>
                                        @error('city')
                                            <span class="ml-1 text-sm text-danger">{{ $message }}</span>
                                        @enderror
                                    </div>
                                </div>
                                <div class="col-sm-4 mb-3">
                                    <div class="form-group">
                                        <label>Location <span class="text-danger">*</span></label>
                                        <select class="form-control select2" name="location" id="location_select" required>
                                            @foreach ($locations as $list)
                                                <option value="{{ $list->id }}"
                                                    {{ $venue->location_id == $list->id ? 'selected' : '' }}>
                                                    {{ $list->name }}</option>
                                            @endforeach
                                        </select>
                                        @error('location')
                                            <span class="ml-1 text-sm text-danger">{{ $message }}</span>
                                        @enderror
                                    </div>
                                </div>
                                <div class="col-sm-4 mb-3">
                                    <div class="form-group">
                                        <label>Related Location</label>
                                        <select class="form-control select2" name="related_locations[]"
                                            id="related_location_select" multiple>
                                            @foreach ($locations as $list)
                                                <option value="{{ $list->id }}"
                                                    {{ array_search($list->id, $related_location_ids) !== false ? 'selected' : '' }}>
                                                    {{ $list->name }}</option>
                                            @endforeach
                                        </select>
                                        @error('related_location')
                                            <span class="ml-1 text-sm text-danger">{{ $message }}</span>
                                        @enderror
                                    </div>
                                </div>
                                <div class="col-sm-12 mb-3">
                                    <div class="form-group">
                                        <label>Address <span class="text-danger">*</span></label>
                                        <textarea type="text" class="form-control" placeholder="Enter address" name="address" required>{{ $venue->venue_address }}</textarea>
                                        @error('address')
                                            <span class="ml-1 text-sm text-danger">{{ $message }}</span>
                                        @enderror
                                    </div>
                                </div>
                                <div class="col-sm-3 mb-3">
                                    <div class="form-group">
                                        <label>Phone No. <span class="text-danger">*</span></label>
                                        <input type="number" class="form-control" placeholder="Enter phone no."
                                            name="phone_number" required minlength="11" maxlength="11"
                                            value="{{ $venue->phone }}">
                                        @error('phone_number')
                                            <span class="ml-1 text-sm text-danger">{{ $message }}</span>
                                        @enderror
                                    </div>
                                </div>
                                <div class="col-sm-3 mb-3">
                                    <div class="form-group">
                                        <label>Email</label>
                                        <input type="text" class="form-control" placeholder="Enter email"
                                            name="email" value="{{ $venue->email }}">
                                        @error('email')
                                            <span class="ml-1 text-sm text-danger">{{ $message }}</span>
                                        @enderror
                                    </div>
                                </div>
                                <div class="col-sm-3 mb-3">
                                    <div class="form-group">
                                        <label>Min Capacity <span class="text-danger">*</span></label>
                                        <input type="number" class="form-control" placeholder="Enter min capacity"
                                            name="min_capacity" value="{{ $venue->min_capacity }}" required>
                                        @error('min_capacity')
                                            <span class="ml-1 text-sm text-danger">{{ $message }}</span>
                                        @enderror
                                    </div>
                                </div>
                                <div class="col-sm-3 mb-3">
                                    <div class="form-group">
                                        <label>Max Capacity <span class="text-danger">*</span></label>
                                        <input type="number" class="form-control" placeholder="Enter max capacity"
                                            name="max_capacity" value="{{ $venue->max_capacity }}" required>
                                        @error('max_capacity')
                                            <span class="ml-1 text-sm text-danger">{{ $message }}</span>
                                        @enderror
                                    </div>
                                </div>
                                <div class="col-sm-6 mb-3">
                                    <div class="form-group">
                                        <label>Veg Price/Per Plate</label>
                                        <input type="number" class="form-control" placeholder="Enter veg price"
                                            name="veg_price" value="{{ $venue->veg_price }}">
                                    </div>
                                </div>
                                <div class="col-sm-6 mb-3">
                                    <div class="form-group">
                                        <label>Non Veg Price/Per Plate</label>
                                        <input type="number" class="form-control" placeholder="Enter nonveg price"
                                            name="non_veg_price" value="{{ $venue->nonveg_price }}">
                                    </div>
                                </div>
                                <div class="col-sm-6 mb-3 border border-grey">
                                    <label for="veg_foods_inp">Veg Foods</label>
                                    <div class="row" id="veg_food_container">
                                        @foreach ($veg_meals as $list)
                                            <div class="col-sm-3">
                                                <div class="form-group">
                                                    <label for="" class="text-xs">{{ $list->name }}</label>
                                                    <input type="number" class="form-control"
                                                        placeholder="Enter package"
                                                        name="veg_foods[{{ $list->name }}][package]"
                                                        value="{{ isset($veg_foods[$list->name]) ? $veg_foods[$list->name]['package'] : '' }}">
                                                </div>
                                            </div>
                                        @endforeach
                                    </div>
                                </div>

                                <div class="col-sm-6 mb-3 border border-grey">
                                    <label for="non_veg_foods_inp">Non-Veg Foods</label>
                                    <div class="row" id="non_veg_food_container">
                                        @foreach ($nonveg_meals as $list)
                                            <div class="col-sm-3">
                                                <div class="form-group">
                                                    <label for="" class="text-xs">{{ $list->name }}</label>
                                                    <input type="number" class="form-control"
                                                        placeholder="Enter package"
                                                        name="nonveg_foods[{{ $list->name }}][package]"
                                                        value="{{ isset($nonveg_foods[$list->name]) ? $nonveg_foods[$list->name]['package'] : '' }}">
                                                </div>
                                            </div>
                                        @endforeach
                                    </div>
                                </div>

                                <div class="col-sm-4 mb-3">
                                    <div class="form-group">
                                        <label>Budget <span class="text-danger">*</span></label>
                                        <select class="form-control" name="budget" required>
                                            @foreach ($budgets as $list)
                                                <option value="{{ $list->id }}"
                                                    {{ $venue->budget_id == $list->id ? 'selected' : '' }}>
                                                    {{ $list->name }}</option>
                                            @endforeach
                                        </select>
                                        @error('budget')
                                            <span class="ml-1 text-sm text-danger">{{ $message }}</span>
                                        @enderror
                                    </div>
                                </div>
                                <div class="col-sm-8 mb-3">
                                    <div class="form-group">
                                        <label>Similar Venues</label>
                                        <select class="form-control select2" id="similar_venue_select"
                                            name="similar_venues[]" multiple>
                                            @foreach ($similar_venues as $list)
                                                <option value="{{ $list->id }}"
                                                    {{ array_search($list->id, $similar_venue_id_arr) !== false ? 'selected' : '' }}>
                                                    {{ $list->name }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>
                                <div class="col-sm-6 mb-3">
                                    <div class="form-group">
                                        <label>Meta Title</label>
                                        <textarea type="text" class="form-control" placeholder="Enter meta title" id="meta_title_count"
                                            name="meta_title">{{ $venue->meta_title }}</textarea>
                                        <div id="charCountmeta_title_count">0 Characters</div>
                                    </div>
                                </div>
                                <div class="col-sm-6 mb-3">
                                    <div class="form-group">
                                        <label>Meta Description</label>
                                        <textarea type="text" class="form-control" placeholder="Enter meta description" id="meta_description_count"
                                            name="meta_description">{{ $venue->meta_description }}</textarea>
                                        <div id="charCountmeta_description_count">0 Characters</div>
                                    </div>
                                </div>
                                <div class="col-sm-6 mb-3">
                                    <div class="form-group">
                                        <label>Meta Keywords</label>
                                        <textarea type="text" class="form-control" placeholder="Enter meta keywords" name="meta_keywords">{{ $venue->meta_keywords }}</textarea>
                                    </div>
                                </div>
                                <div class="col-sm-12 mb-3">
                                    <div class="form-group">
                                        <label>Summary</label>
                                        <textarea type="text" class="form-control summernote" placeholder="Enter summary" name="summary">{{ $venue->summary }}</textarea>
                                    </div>
                                </div>
                                <div class="col-sm-12 mb-3">
                                    <div class="row">
                                        <div class="col mb-3">
                                            <div class="form-group">
                                                <label for="" class="text-xs">Start Time (Morning)</label>
                                                <input type="time" class="form-control" name="start_time_morning"
                                                    value="{{ $venue->start_time_morning }}">
                                            </div>
                                        </div>
                                        <div class="col mb-3">
                                            <div class="form-group">
                                                <label for="" class="text-xs">End Time (Morning)</label>
                                                <input type="time" class="form-control" name="end_time_morning"
                                                    value="{{ $venue->end_time_morning }}">
                                            </div>
                                        </div>
                                        <div class="col mb-3">
                                            <div class="form-group">
                                                <label for="" class="text-xs">Start Time (Evening)</label>
                                                <input type="time" class="form-control" name="start_time_evening"
                                                    value="{{ $venue->start_time_evening }}">
                                            </div>
                                        </div>
                                        <div class="col mb-3">
                                            <div class="form-group">
                                                <label for="" class="text-xs">End Time (Evening)</label>
                                                <input type="time" class="form-control" name="end_time_evening"
                                                    value="{{ $venue->end_time_evening }}">
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-sm-12 mb-3">
                                    <div class="form-group">
                                        <label for="" class="">Parking Space <span
                                                class="text-danger">*</span></label>
                                        <input class="form-control" id=""
                                            placeholder="Parking Space ex.(approx 50 - 100)" name="parking_space"
                                            value="{{ $venue->parking_space }}" required>
                                    </div>
                                </div>
                                <div class="col-sm-12 mb-3">
                                    <div class="form-group">
                                        <label for="" class="">Location Map</label>
                                        <textarea class="form-control" id="" placeholder="Enter map iframe" name="location_map" rows="5">{{ $venue->location_map }}</textarea>
                                    </div>
                                </div>
                                <div class="col-sm-6 mb-3">
                                    <div class="form-group">
                                        <label for="" class="">Location Place Id</label>
                                        <input class="form-control" id="" placeholder="Enter Place Id"
                                            name="location_place_id" value="{{ $venue->location_place_id }}">
                                    </div>
                                </div>
                                <div class="col-sm-6 mb-3">
                                    <div class="form-group">
                                        <label for="" class="">Review</label>
                                        <input class="form-control" id="" placeholder="Enter Average Review"
                                            name="place_rating" value="{{ $venue->place_rating }}">
                                    </div>
                                </div>
                                <div class="col-sm-12 mb-3" id="area_capacity_container">
                                    <label>Area Capacity</label>
                                    <button type="button" class="btn btn-success btn-xs ml-3"
                                        onclick="handle_add_area_capacity(this)"><i class="fa fa-add"></i></button>
                                    @foreach ($area_capacity as $list)
                                        <div class="row">
                                            <div class="col mb-3">
                                                <div class="form-group">
                                                    <label class="text-xs">Name</label>
                                                    <input type="text" class="form-control" placeholder="Enter name"
                                                        name="area_capacity_name[]" value="{{ $list->name }}">
                                                </div>
                                            </div>
                                            <div class="col mb-3">
                                                <div class="form-group">
                                                    <label class="text-xs">Seating</label>
                                                    <input type="number" class="form-control" placeholder="Enter name"
                                                        name="area_capacity_seating[]" value="{{ $list->seating }}">
                                                </div>
                                            </div>
                                            <div class="col mb-3">
                                                <div class="form-group">
                                                    <label class="text-xs">Floating</label>
                                                    <input type="number" class="form-control" id=""
                                                        placeholder="Enter " name="area_capacity_floating[]"
                                                        value="{{ $list->floating }}">
                                                </div>
                                            </div>
                                            <div class="col mb-3">
                                                <div class="form-group">
                                                    <label class="text-xs">Area Type</label>
                                                    <select class="form-control" name="area_capacity_type[]">
                                                        <option value="Indoor"
                                                            {{ $list->type == 'Indoor' ? 'selected' : '' }}>Indoor</option>
                                                        <option value="Outdoor"
                                                            {{ $list->type == 'Outdoor' ? 'selected' : '' }}>Outdoor
                                                        </option>
                                                        <option value="Indoor+Outdoor"
                                                            {{ $list->type == 'Indoor+Outdoor' ? 'selected' : '' }}>
                                                            Indoor+Outdoor</option>
                                                        <option value="Poolside"
                                                            {{ $list->type == 'Poolside' ? 'selected' : '' }}>Poolside
                                                        </option>
                                                    </select>
                                                </div>
                                            </div>
                                            <div class="col m-auto">
                                                <button class="btn btn-sm text-danger"
                                                    onclick="handle_remove_area_capacity(this)"><i
                                                        class="fa fa-times"></i></button>
                                            </div>
                                        </div>
                                    @endforeach
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
                                    <a href="{{ route('venue.list') }}" class="btn btn-sm bg-secondary m-1">Back</a>
                                    <button type="submit" class="btn btn-sm m-1 text-light"
                                        style="background-color: var(--wb-dark-red);">Submit</button>
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
    <script src="{{ asset('plugins/select2/js/select2.min.js') }}"></script>
    <script src="{{ asset('plugins/summernote/summernote-lite.min.js') }}"></script>
    <script>
        $('.select2').select2({
            placeholder: 'Select an option'
        });

        $('.summernote').summernote({
            placeholder: 'Type here content',
            tabsize: 2,
        });

        function handle_add_area_capacity() {
            const area_capacity_container = document.getElementById('area_capacity_container');
            const div = document.createElement('div');
            div.classList.add('row');

            const elem = `<div class="col mb-3">
            <div class="form-group">
                <label class="text-xs">Name</label>
                <input type="text" class="form-control" placeholder="Enter name" name="area_capacity_name[]">
            </div>
        </div>
        <div class="col mb-3">
            <div class="form-group">
                <label class="text-xs">Seating</label>
                <input type="number" class="form-control" placeholder="Enter name" name="area_capacity_seating[]">
            </div>
        </div>
        <div class="col mb-3">
            <div class="form-group">
                <label class="text-xs">Floating</label>
                <input type="number" class="form-control" id="" placeholder="Enter " name="area_capacity_floating[]">
            </div>
        </div>
        <div class="col mb-3">
            <div class="form-group">
            <label class="text-xs">Area Type</label>
                <select class="form-control" name="area_capacity_type[]">
                    <option value="Indoor">Indoor</option>
                    <option value="Outdoor">Outdoor</option>
                    <option value="Indoor+Outdoor">Indoor+Outdoor</option>
                    <option value="Poolside">Poolside</option>
                </select>
            </div>
        </div>
        <div class="col m-auto">
            <button class="btn btn-sm text-danger" onclick="handle_remove_area_capacity(this)"><i class="fa fa-times"></i></button>
        </div>`;
            div.innerHTML = elem;
            area_capacity_container.append(div);

        }

        function handle_remove_area_capacity(elem) {
            elem.parentElement.parentElement.remove();
        }

        function get_locations(city_id) {
            const location_select = document.getElementById('location_select');
            const related_location_select = document.getElementById('related_location_select');
            location_select.innerHTML = "";
            related_location_select.innerHTML = "";
            fetch(`{{ route('location.get_locations') }}/${city_id}`).then(response => response.json()).then(data => {
                for (let item of data.locations) {
                    let option = document.createElement('option');
                    option.value = item.id;
                    option.innerText = item.name;
                    location_select.appendChild(option);

                    let option2 = document.createElement('option');
                    option2.value = item.id;
                    option2.innerText = item.name;
                    related_location_select.appendChild(option2);
                }
            })
            get_similar_venues(city_id);
        }

        function get_similar_venues(city_id) {
            const similar_venue_select = document.getElementById('similar_venue_select');
            similar_venue_select.innerHTML = "";
            fetch(`{{ route('venue.get_similar_venues') }}/${city_id}`).then(response => response.json()).then(data => {
                if (data.success == true) {
                    for (let item of data.venues) {
                        let option = document.createElement('option');
                        option.value = item.id;
                        option.innerText = item.name;
                        similar_venue_select.appendChild(option);
                    }
                } else {
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
                const fixedSlug = newSlug.toLowerCase()
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
