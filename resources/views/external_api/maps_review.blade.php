@extends('layouts.app')
@section('title', 'Review Api')
@section('header-css')
@endsection
@section('main')
<div class="content-wrapper pb-5">
    <section class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6">
                    <h1 class="m-0">Review Api</h1>
                </div>
            </div>
            <form action="{{route('api.maps_review_fetch')}}" method="post">
                @csrf

                <div class="card-body">
                    <div class="row">
                        <div class="col-sm-12 mb-3">
                            <div class="form-group">
                                <label for="" class="">Place Id Api Key</label>
                                <input class="form-control" id="" placeholder="Enter Place Id Api Key" name="place_id_api_key" value="">
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
                            <button type="submit" class="btn btn-sm m-1 text-light" style="background-color: var(--wb-dark-red);">Submit</button>
                        </div>
                    </div>
                </div>
            </form>
        </div>
    </section>
    <section class="content">

    </section>
</div>
@endsection
