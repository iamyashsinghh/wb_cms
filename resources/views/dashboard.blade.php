@extends('layouts.app')
@section('title', 'Dashboard')
@section('header-css')
<link rel="stylesheet" href="{{asset('plugins/charts/chart.css')}}">
@endsection
@section('main')
<div class="content-wrapper pb-5">
    <section class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6">
                    <h1 class="m-0">Dashboard</h1>
                </div>
            </div>
        </div>
    </section>
    <section class="content">
        <div class="container-fluid">
            <div class="row">
                <div class="col-lg-4 col-6">
                    <!-- small box -->
                    <div class="small-box text-sm text-light" style="background: var(--wb-renosand);">
                        <div class="inner">
                            <h3>{{$total_vendors}}</h3>
                            <p>Total Vendors</p>
                        </div>
                        <div class="icon">
                            <i class="ion ion-bag"></i>
                        </div>
                        <a href="{{route('vendor.list')}}" class="small-box-footer">More info <i class="fas fa-arrow-circle-right"></i></a>
                    </div>
                </div>
                <div class="col-lg-4 col-6">
                    <!-- small box -->
                    <div class="small-box text-sm text-light" style="background: var(--wb-renosand);">
                        <div class="inner">
                            <h3>{{$total_venues}}</h3>
                            <p>Total Venue</p>
                        </div>
                        <div class="icon">
                            <i class="ion ion-bag"></i>
                        </div>
                        <a href="{{route('venue.list')}}" class="small-box-footer">More info <i class="fas fa-arrow-circle-right"></i></a>
                    </div>
                </div>
                <div class="col-lg-4 col-6">
                    <!-- small box -->
                    <div class="small-box text-sm text-light" style="background: var(--wb-dark-red);">
                        <div class="inner">
                            <h3>{{$totalAnalitics}}</h3>
                            <p>Total Leads</p>
                        </div>
                        <div class="icon">
                            <i class="ion ion-bag"></i>
                        </div>
                        <a href="{{route('analytics.list')}}" class="small-box-footer">More info <i class="fas fa-arrow-circle-right"></i></a>
                    </div>
                </div>
            </div>
        </div>
    </section>
</div>
@endsection
