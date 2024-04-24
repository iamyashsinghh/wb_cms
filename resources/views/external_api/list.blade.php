@extends('layouts.app')
@section('title', "Requests List")

@section('header-css')
    <link rel="stylesheet" href="//cdn.datatables.net/1.13.1/css/jquery.dataTables.min.css">
@endsection
@section('main')
<div class="content-wrapper pb-5">
    <section class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6">
                    <h1 class="m-0">Reuests List</h1>
                </div>
            </div>
        </div>
    </section>
    <section class="content">
        <div class="container-fluid">
            <div class="container">
                <div class="table-responsive">
                    <table id="serverTable" class="table text-sm">
                        <thead>
                            <th>ID</th>
                            <th>Requests</th>
                            <th>Time</th>
                        </thead>
                    </table>
                </div>
            </div>
        </div>
    </section>
</div>
@endsection
@section('footer-script')
<script src="//cdn.datatables.net/1.13.1/js/jquery.dataTables.min.js"></script>
<script>
    //  $(document).ready(function() {
    //     $('#serverTable').DataTable({
    //         pageLength: 10,
    //         processing: true,
    //         searchable: true,
    //         ordering: true,
    //         language: {
    //             "search": "_INPUT_", // Removes the 'Search' field label
    //             "searchPlaceholder": "Type here to search..", // Placeholder for the search box
    //         },
    //         serverSide: true,
    //         ajax: `{{route('venue.ajax_list')}}`,
    //         order: [
    //             [0, 'desc']
    //         ],
    //         rowCallback: function(row, data, index) {
    //             const td_elements = row.querySelectorAll('td');
    //             if(data[5] == 1){
    //                 wb_assured = `<a data-id="${data[0]}" data-status="0" data-submit-url="{{route('venue.update_wb_assured_status')}}" href="javascript:void(0);" style="font-size: 22px;" onclick="handle_update_status(this)"><i class="fa fa-toggle-on text-success"></i></a>`;
    //             }else{
    //                 wb_assured = `<a data-id="${data[0]}" data-status="1" data-submit-url="{{route('venue.update_wb_assured_status')}}" href="javascript:void(0);" style="font-size: 22px;" onclick="handle_update_status(this)"><i class="fa fa-toggle-off text-danger"></i></a>`;
    //             }
    //             if(data[6] == 1){
    //                 popular_elem = `<a data-id="${data[0]}" data-status="0" data-submit-url="{{route('venue.update_popular_status')}}" href="javascript:void(0);" style="font-size: 22px;" onclick="handle_update_status(this)"><i class="fa fa-toggle-on text-success"></i></a>`;
    //             }else{
    //                 popular_elem = `<a data-id="${data[0]}" data-status="1" data-submit-url="{{route('venue.update_popular_status')}}" href="javascript:void(0);" style="font-size: 22px;" onclick="handle_update_status(this)"><i class="fa fa-toggle-off text-danger"></i></a>`;
    //             }

    //             if(data[7] == 1){
    //                 status_elem = `<a data-id="${data[0]}" data-status="0" data-submit-url="{{route('venue.update_status')}}" href="javascript:void(0);" style="font-size: 22px;" onclick="handle_update_status(this)"><i class="fa fa-toggle-on text-success"></i></a>`;
    //             }else{
    //                 status_elem = `<a data-id="${data[0]}" data-status="1" data-submit-url="{{route('venue.update_status')}}" href="javascript:void(0);" style="font-size: 22px;" onclick="handle_update_status(this)"><i class="fa fa-toggle-off text-danger"></i></a>`;
    //             }

    //             td_elements[5].innerHTML = wb_assured;
    //             td_elements[6].innerHTML = popular_elem;
    //             td_elements[7].innerHTML = status_elem;

    //             if(data[8] == null){
    //                 td_elements[8].innerHTML = `<span class="badge badge-danger">Not Available</span>`;
    //             }else{
    //                 td_elements[8].innerHTML = `<span class="badge badge-success">Available</span>`;
    //             }

    //             td_elements[9].classList = 'text-center text-nowrap';
    //             td_elements[9].innerHTML = `<a href="{{route('venue.edit')}}/${data[0]}" class="text-success mx-2" title="Edit">
    //                 <i class="fa fa-edit" style="font-size: 15px;"></i>
    //             </a>
    //             <a onclick="handle_delete_venue(${data[0]})" class="text-danger mx-2" title="Delete">
    //                 <i class="fa fa-trash-alt" style="font-size: 15px;"></i>
    //             </a>
    //             <div class="dropdown d-inline-block mx-2">
    //                 <a href="javascript:void(0);" data-bs-toggle="dropdown" aria-expanded="false">
    //                 <i class="fa fa-caret-down text-dark"></i>
    //                 </a>
    //                 <ul class="dropdown-menu">
    //                     <li><a class="dropdown-item" href="{{route('venue.manage_images')}}/${data[0]}">Update Images</a></li>
    //                     <li><a class="dropdown-item" href="javascript:void(0);" onclick="handle_update_phone_no('venue', ${data[0]})">Update Phone Number</a></li>
    //                     <li><a class="dropdown-item" href="javascript:void(0);" onclick="handle_update_meta('venue', ${data[0]})">Update Meta</a></li>
    //                     <li><a class="dropdown-item" href="javascript:void(0);" onclick="handle_update_faq(${data[0]})">Update FAQ</a></li>
    //                 </ul>
    //             </div>`;
    //         }
    //     });
    // });

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
            // serverSide: true,
            ajax: `{{route('api.ajax')}}`,
            order: [
                [0, 'desc']
            ],
            rowCallback: function(row, data, index) {}
        });
    });


</script>

@endsection
