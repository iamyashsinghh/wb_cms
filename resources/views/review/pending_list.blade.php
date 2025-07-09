@extends('layouts.app')
@section('title', 'Pending Reviews')

@section('header-css')
    <link rel="stylesheet" href="//cdn.datatables.net/1.13.1/css/jquery.dataTables.min.css">
@endsection

@section('main')
    <div class="content-wrapper pb-5">
        <section class="content-header">
            <div class="container-fluid">
                <div class="row mb-2">
                    <div class="col-sm-6">
                        <h1 class="m-0">Pending Reviews</h1>
                    </div>
                </div>
            </div>
        </section>

        <section class="content">
            <div class="container-fluid">
                <div class="table-responsive">
                    <table id="pendingReviewsTable" class="table text-sm">
                        <thead>
                            <tr>
                                <th>ID</th>
                                <th>Reviewers Name</th>
                                <th>Rating</th>
                                <th>Venue Or Vendor</th>
                                <th>Place Name</th>
                                <th>Status</th>
                                <th class="text-center no-sort">Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($pendingReviews as $review)
                                <tr>
                                    <td>{{ $review->id }}</td>
                                    <td>{{ $review->users_name }}</td>
                                    <td>{{ $review->rating }}</td>
                                    <td>{{ $review->product_for }}</td>
                                    <td>
                                        @if ($review->product_for === 'vendor')
                                            {{ optional($vendors->firstWhere('id', $review->product_id))->brand_name }}
                                        @else
                                            {{ optional($venues->firstWhere('id', $review->product_id))->name }}
                                        @endif
                                    </td>
                                    <td>
                                        @if ($review->status == 1)
                                            <a data-id="{{ $review->id }}" data-status="0"
                                                data-submit-url="{{ route('review.update_review_status') }}"
                                                href="javascript:void(0);" onclick="handle_update_status(this)">
                                                <i class="fa fa-toggle-on text-success" style="font-size: 22px;"></i>
                                            </a>
                                        @else
                                            <a data-id="{{ $review->id }}" data-status="1"
                                                data-submit-url="{{ route('review.update_review_status') }}"
                                                href="javascript:void(0);" onclick="handle_update_status(this)">
                                                <i class="fa fa-toggle-off text-danger" style="font-size: 22px;"></i>
                                            </a>
                                        @endif
                                    </td>
                                    <td class="text-center">
                                        <a href="{{ route('review.edit', $review->id) }}" class="text-success mx-2"
                                            title="Edit">
                                            <i class="fa fa-edit" style="font-size: 15px;"></i>
                                        </a>
                                        <a href="javascript:void(0);" onclick="handle_delete_review({{ $review->id }})"
                                            class="text-danger mx-2" title="Delete">
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
    </div>

    @include('includes.delete_vendor_venue_modal')
@endsection

@section('footer-script')
    <script src="//cdn.datatables.net/1.13.1/js/jquery.dataTables.min.js"></script>
    <script>
        $(document).ready(function() {
            $('#pendingReviewsTable').DataTable({
                pageLength: 10,
                ordering: true,
                language: {
                    search: "_INPUT_",
                    searchPlaceholder: "Type here to search...",
                },
                columnDefs: [{
                    targets: 'no-sort',
                    orderable: false
                }]
            });
        });

        function handle_update_status(elem) {
            if (confirm("Are you sure want to update the status?")) {
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
                            toastr[data.alert_type](data.message);
                        }
                    })
                    .catch(error => {
                        console.error('Error updating status:', error);
                    });
            }
        }

        function handle_delete_review(review_id) {
            const deleteReviewModal = new bootstrap.Modal(document.getElementById('deleteVendorVenueModal'));
            const deleteReviewForm = document.getElementById('deleteVendorVenueForm');
            const actionUrl = `{{ route('review.destroy', ':id') }}`.replace(':id', review_id);
            deleteReviewForm.action = actionUrl;
            deleteReviewModal.show();
        }
    </script>
@endsection
