@extends('layouts.app')
@section('title', $page_heading)
@section('header-css')
<link rel="stylesheet" href="{{asset('plugins/select2/css/select2.min.css')}}">
<link rel="stylesheet" href="{{asset('plugins/summernote/summernote-lite.min.css')}}">
@endsection
@section('main')
@php
$route = Route::currentRouteName();
    // $related_location_ids = explode(",", $venue->related_location_ids);
    // $veg_foods = json_decode($venue->veg_foods, true);
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
                    <h3 class="card-title">Review Details</h3>
                </div>
                <form action="{{route('review.manage_process', $review->id)}}" method="post" enctype="multipart/form-data">
                    @csrf
                    <div class="card-body">
                        <div class="row">
                            <div class="col-sm-4 mb-3">
                                <div class="form-group">
                                    <label>{{$route == "review.edit" ? 'Do not try to edit Venue/Vendor': 'Venue/Vendor'}} <span class="text-danger">*</span></label>
                                    <select class="form-control" name="product_for" id="product_for" required {{$route == "review.edit" ? 'Disabled': ''}}>
                                        <option value="" {{ empty($review->product_for) ? 'selected' : '' }}>Select</option>
                                        <option value="venue" {{ $review->product_for === 'venue' ? 'selected' : '' }}>Venue</option>
                                        <option value="vendor" {{ $review->product_for === 'vendor' ? 'selected' : '' }}>Vendor</option>
                                    </select>
                                    @error('product_for')<span class="ml-1 text-sm text-danger">{{ $message }}</span>@enderror
                                </div>
                            </div>
                            <div class="col-sm-4 mb-3" id="dynamicSelectContainer" style="display: none;">
                                <div class="form-group">
                                    <label>Venue or Vendor<span class="text-danger"> *</span></label>
                                    <select class="form-control" name="product_id" id="venue_or_vendor_data" required>
                                        <option value="{{ $review->product_id }}" {{ $route == "review.edit" ? 'selected' : '' }}>{{ $review->product_id }}</option>
                                    </select>
                                    @error('product_id')<span class="ml-1 text-sm text-danger">{{ $message }}</span>@enderror
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-sm-4 mb-3">
                                <div class="form-group">
                                    <label>Users Name <span class="text-danger">*</span></label>
                                    <input type="text" class="form-control" placeholder="Enter Users Name." name="users_name" required value="{{$review->users_name}}">
                                    @error('users_name')<span class="ml-1 text-sm text-danger">{{$message}}</span>@enderror
                                </div>
                            </div>
                            <div class="col-sm-4 mb-3">
                                <div class="form-group">
                                    <label>Users Rating <span class="text-danger">*</span></label>
                                    <select class="form-control" name="rating" id="rating" required>
                                        <option value="" {{ empty($review->rating) ? 'selected' : '' }}>Select</option>
                                        <option value="1" {{ $review->rating == '1' ? 'selected' : '' }}>1 Star</option>
                                        <option value="2" {{ $review->rating == '2' ? 'selected' : '' }}>2 Star</option>
                                        <option value="3" {{ $review->rating == '3' ? 'selected' : '' }}>3 Star</option>
                                        <option value="4" {{ $review->rating == '4' ? 'selected' : '' }}>4 Star</option>
                                        <option value="5" {{ $review->rating == '5' ? 'selected' : '' }}>5 Star</option>
                                    </select>
                                    @error('rating')<span class="ml-1 text-sm text-danger">{{$message}}</span>@enderror
                                </div>
                            </div>
                            <div class="col-sm-4 mb-3">
                                <div class="form-group">
                                    <label>Status<span class="text-danger"> *</span></label>
                                    <select class="form-control" name="status" id="status" required>
                                        <option value="1" {{ $review->status == '0' ? 'selected' : '' }}>Off</option>
                                        <option value="2" {{ $review->status == '1' ? 'selected' : '' }}>On</option>
                                    </select>
                                    @error('status')<span class="ml-1 text-sm text-danger">{{$message}}</span>@enderror
                                </div>
                            </div>
                        </div>
                            <div class="col-sm-12 mb-3">
                                <div class="form-group">
                                    <label>Comment <span class="text-danger">*</span></label>
                                    <textarea type="text" class="form-control" placeholder="Enter Comment" name="comment" required>{{$review->comment}}</textarea>
                                    @error('comment')<span class="ml-1 text-sm text-danger">{{$message}}</span>@enderror
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-sm-4 mb-3">
                                    <div class="form-group">
                                        <label>Phone Number <span class="text-danger">*</span></label>
                                        <input type="number" class="form-control" maxlength="10" placeholder="+917754966128" name='c_number' required value="{{$review->c_number}}">
                                        @error('c_number')<span class="ml-1 text-sm text-danger">{{ $message }}</span>@enderror
                                    </div>
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
   $(document).ready(function() {
        $('#venue_or_vendor_data').select2();
        $('#product_for').on('change', function() {
            var selectedValue = $(this).val();
            $('#dynamicSelectContainer').hide();
            if (selectedValue === 'venue' || selectedValue === 'vendor') {
                var url = (selectedValue === 'venue') ? '{{ route('review.getvenues') }}' : '{{ route('review.getvendors') }}';
                $.get(url, function(data) {
                    var dynamicSelect = $('#venue_or_vendor_data');
                    dynamicSelect.empty();
                    $.each(data, function(index, item) {
                        var optionText = (selectedValue === 'vendor') ? item.brand_name : item.name;
                        dynamicSelect.append('<option value="' + item.id + '">' + optionText + '</option>');
                    });
                    $('#dynamicSelectContainer').show();
                    dynamicSelect.trigger('change.select2');
                });
            }
        });
    });
    </script>
@endsection
