@extends('layouts.app')
@section('title', 'Review List')
@php
    $data = [
        'venue' => $venue,
        'vendor' => $vendor,
    ];
@endphp
@section('header-css')
    <link rel="stylesheet" href="//cdn.datatables.net/1.13.1/css/jquery.dataTables.min.css">
@endsection
@section('main')
    <div class="content-wrapper pb-5">
        <section class="content-header">
            <div class="container-fluid">
                <div class="row mb-4">
                    <div class="col-sm-6">
                        <h1 class="m-0">Review List</h1>
                    </div>
                </div>
                <div class="row">
                    <div class="col-lg-3 col-6">
                        <!-- small box -->
                        <div class="small-box text-sm text-light" style="background: var(--wb-dark-red);">
                            <div class="inner">
                                <h3>{{ $disabledReviewsCount }}</h3>
                                <p>Pending Reviews</p>
                            </div>
                            <a href="{{ route('review.pending') }}" class="small-box-footer">
                                More info <i class="fas fa-arrow-circle-right"></i>
                            </a>
                        </div>
                    </div>
                </div>
                <div class="button-group my-2">
                    @canany(['create review', 'super power'])
                        <a href="{{ route('review.add') }}" class="btn btn-sm text-light buttons-print"
                            style="background-color: var(--wb-renosand)"><i class="fa fa-plus mr-1"></i>Add New</a>
                    @endcanany
                </div>
            </div>
        </section>
        <section class="content">
            <div class="container-fluid">
                <div class="table-responsive">
                    <table id="serverTable" class="table text-sm">
                        <thead>
                            <th>ID</th>
                            <th>Reviewers Name</th>
                            <th>Rating</th>
                            <th>Venue Or Vendor</th>
                            <th>Place Name</th>
                            <th>Status</th>
                            <th class="text-center no-sort">Action</th>
                        </thead>
                    </table>
                </div>
            </div>
        </section>
    </div>
    @include('includes.delete_vendor_venue_modal');
@endsection
@section('footer-script')
    <script src="//cdn.datatables.net/1.13.1/js/jquery.dataTables.min.js"></script>
    <script>
        function handle_delete_review(review_id) {
            const deleteReviewModal = new bootstrap.Modal(document.getElementById('deleteVendorVenueModal'));
            const deleteReviewForm = document.getElementById('deleteVendorVenueForm');
            const actionUrl = `{{ route('review.destroy', ':id') }}`.replace(':id', review_id);
            deleteReviewForm.action = actionUrl;
            deleteReviewModal.show();
        }

        $(document).ready(function() {
            let venue = @json($data['venue']);
            let vendor = @json($data['vendor']);
            $('#serverTable').DataTable({
                pageLength: 10,
                processing: true,
                searchable: true,
                ordering: true,
                language: {
                    "search": "_INPUT_",
                    "searchPlaceholder": "Type here to search..",
                },
                serverSide: true,
                ajax: "{{ route('review.ajax_list') }}",
                dataSrc: function(json) {
                    console.log(json);
                    return json;
                },
                order: [
                    [0, 'desc']
                ],
                columnDefs: [{
                    targets: 'no-sort',
                    "defaultContent": "-"
                }],
                rowCallback: function(row, data, index) {
                    const td_elements = row.querySelectorAll('td');

                    @canany(['publish review', 'super power'])
                        const status = (data[5] == 1) ?
                            `<a data-id="${data[0]}" data-status="0" data-submit-url="{{ route('review.update_review_status') }}" href="javascript:void(0);" style="font-size: 22px;" onclick="handle_update_status(this)">
                                <i class="fa fa-toggle-on text-success"></i>
                            </a>` :
                            `<a data-id="${data[0]}" data-status="1" data-submit-url="{{ route('review.update_review_status') }}" href="javascript:void(0);" style="font-size: 22px;" onclick="handle_update_status(this)">
                                <i class="fa fa-toggle-off text-danger"></i>
                            </a>`;
                    @else
                        const status = (data[5] == 1) ?
                            `<a href="javascript:void(0);" style="font-size: 22px;"">
                                <i class="fa fa-toggle-on text-success"></i>
                            </a>` :
                            `<a href="javascript:void(0);" style="font-size: 22px;">
                                <i class="fa fa-toggle-off text-danger"></i>
                            </a>`;
                    @endcanany
                    let result;

                    if (data[3] == 'vendor') {
                        result = getNameByIdV(data[4], vendor);
                    } else {
                        result = getNameById(data[4], venue);
                    }

                    td_elements[4].innerHTML = result;
                    td_elements[5].innerHTML = status;
                    td_elements[6].innerHTML = `
                    @canany(['edit review', 'super power'])
                            <a href="{{ route('review.edit') }}/${data[0]}" class="text-success mx-2" title="Edit">
                                <i class="fa fa-edit" style="font-size: 15px;"></i>
                            </a>
                        @endcanany

                        @canany(['delete review', 'super power'])
                        <a href="javascript:void(0);" onclick="handle_delete_review(${data[0]})" class="text-danger mx-2" title="Delete">
                            <i class="fa fa-trash-alt" style="font-size: 15px;"></i>
                        </a>
                        @endcanany
                        `;
                }
            });

            function getNameById(id, array) {
                var result = array.find(function(item) {
                    return item.id === id;
                });
                return result ? result.name : null;
            }

            function getNameByIdV(id, array) {
                var result = array.find(function(item) {
                    return item.id === id;
                });
                return result ? result.brand_name : null;
            }
        });

        function handle_update_status(elem) {
            if (confirm("Are you sure want to update the status")) {

                const submit_url = elem.getAttribute('data-submit-url');
                const data_id = elem.getAttribute('data-id');
                const data_status = elem.getAttribute('data-status');

                fetch(`${submit_url}/${data_id}/${data_status}`)
                    .then(response => response.json())
                    .then(data => {
                        if (data.success === true) {
                            const icon = elem.querySelector('i');

                            if (data_status == 0) {
                                icon.classList.remove('fa-toggle-on', 'text-success');
                                icon.classList.add('fa-toggle-off', 'text-danger');
                                elem.setAttribute('data-status', 1);
                            } else {
                                icon.classList.remove('fa-toggle-off', 'text-danger');
                                icon.classList.add('fa-toggle-on', 'text-success');
                                elem.setAttribute('data-status', 0);
                            }

                        }
                        toastr[data.alert_type](data.message);
                    })
                    .catch(error => {
                        console.error('Error updating status:', error);
                    });
            }
        }
    </script>
@endsection
