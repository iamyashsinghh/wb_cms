@extends('layouts.app')
@section('title', "Web Analytics")

@section('header-css')
    <link rel="stylesheet" href="//cdn.datatables.net/1.13.1/css/jquery.dataTables.min.css">
@endsection
@section('main')
<div class="content-wrapper pb-5">
    <section class="content-header">
        <div class="container-fluid">
            <h1 class="m-0">Web Analytics</h1>
            <form action="" method="get">
                <div class="row">
                    <div class="col-sm">
                        <div class="form-group">
                            <label for="task_created_from_date_inp">From</label>
                            <input type="date" class="form-control" id="" name="from" value="{{$_GET['from'] ?? ''}}">
                        </div>
                    </div>
                    <div class="col-sm">
                        <div class="form-group">
                            <label for="task_created_from_date_inp">To</label>
                            <input type="date" class="form-control" id="" name="to" value="{{$_GET['to'] ?? ''}}">
                        </div>
                    </div>
                    <div class="col-sm pt-2 m-auto">
                        <button type="submit" class="btn btn-sm text-light" style="background-color: var(--wb-renosand)">Search</button>
                        <a href="{{route('analytics.list')}}" class="btn btn-sm btn-dark">Reset</a>
                    </div>
                </div>
            </form>
        </div>
    </section>
    <section class="content">
        <div class="container-fluid">
            <div class="table-responsive">
                <table id="serverTable" class="table text-sm">
                    <thead>
                        <th>ID</th>
                        <th>Date</th>
                        <th>URL Slug</th>
                        <th>Venue Name</th>
                        <th>Type</th>
                        <th>Request From</th>
                    </thead>
                </table>
            </div>
        </div>
    </section>
</div>
@endsection
@section('footer-script')
@php
    $filter = "";
    if(isset($_GET['from'])){
        $filter = "from=" . $_GET['from'] . "&to=" . $_GET['to'];
    }
    echo $filter;
@endphp
<script src="{{asset('plugins/moment/moment.min.js')}}"></script>
<script src="//cdn.datatables.net/1.13.1/js/jquery.dataTables.min.js"></script>
<script>
     $(document).ready(function() {
        $('#serverTable').DataTable({
            pageLength: 10,
            processing: true,
            searchable: true,
            ordering: true,
            language: {
                "search": "_INPUT_", // Removes the 'Search' field label
                "searchPlaceholder": "Type here to search..", // Placeholder for the search box
            },
            serverSide: true,
            ajax: `{{route('analytics.ajax_list')}}?{!!$filter!!}`,
            order: [
                [0, 'desc']
            ],
            rowCallback: function(row, data, index) {
                const td_elements = row.querySelectorAll('td');
                td_elements[1].innerText = moment(data[1]).format("DD-MMM-YYYY hh:mm a");
                td_elements[3].innerHTML = `${data[3]} <span class="badge badge-info ml-2 text-sm">${data[6]}</span>`

            }
        });
    });
</script>
    
@endsection