@extends('layouts.app')
@section('title', 'Cities')

@section('header-css')
    <link rel="stylesheet" href="//cdn.datatables.net/1.13.1/css/jquery.dataTables.min.css">
@endsection
@section('main')
    <div class="content-wrapper pb-5">
        <section class="content-header">
            <div class="container-fluid">
                <div class="row mb-2">
                    <div class="col-sm-6">
                        <h1 class="m-0">Cities</h1>
                    </div>
                </div>
                <div class="button-group my-4">
                    <a href="javascript:void(0);" data-bs-toggle="modal" data-bs-target="#manageCityModal"
                        class="btn btn-sm text-light buttons-print" style="background-color: var(--wb-renosand)"><i
                            class="fa fa-plus mr-1"></i>Add New</a>
                </div>
            </div>
        </section>
        <section class="content">
            <div class="container-fluid">
                <div class="table-responsive">
                    <table id="clientTable" class="table">
                        <thead>
                            <tr>
                                <th>ID</th>
                                <th>Name</th>
                                <th>Slug</th>
                                <th>Status</th>
                                <th class="text-center">Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($cities as $key => $list)
                                <tr>
                                    <td>{{ $list->id }}</td>
                                    <td>{{ $list->name }}</td>
                                    <td>{{ $list->slug }}</td>

                                    <td>
                                        @canany(['publish city', 'super power'])
                                            @if ($list->status == 1)
                                                <a href="javascript:void(0);" data-id="{{ $list->status }}" data-status="0"
                                                    data-submit-url="{{ route('city.update_status') }}"
                                                    onclick="handle_update_status(this)" style="font-size: 22px;">
                                                    <i class="fa fa-toggle-on text-success"></i>
                                                </a>
                                            @else
                                                <a href="javascript:void(0);" data-id="{{ $list->status }}" data-status="1"
                                                    data-submit-url="{{ route('city.update_status') }}"
                                                    onclick="handle_update_status(this)" style="font-size: 22px;">
                                                    <i class="fa fa-toggle-off text-danger"></i>
                                                </a>
                                            @endif
                                        @else
                                            @if ($list->status == 1)
                                                <i class="fa fa-toggle-on text-success" style="font-size: 22px;"></i>
                                            @else
                                                <i class="fa fa-toggle-off text-danger" style="font-size: 22px;"></i>
                                            @endif
                                        @endcanany
                                    </td>

                                    <td class="text-center"><a class="text-danger"
                                            onclick="return confirm('Are you sure want to delete?')"
                                            href="{{ route('city.delete', $list->id) }}"><i class="fa fa-trash"></i></a>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </section>
        @include('city.manage_modal')
    </div>
@endsection
@section('footer-script')
    <script src="//cdn.datatables.net/1.13.1/js/jquery.dataTables.min.js"></script>
    <script>
        init_client_datatables("clientTable");

          function handle_update_status(elem) {
            if (confirm("Are you sure want to update the status")) {
                const submit_url = elem.getAttribute('data-submit-url');
                const data_id = elem.getAttribute('data-id');
                const data_status = elem.getAttribute('data-status');
                fetch(`${submit_url}/${data_id}/${data_status}`).then(response => response.json()).then(data => {
                    if (data.success === true) {
                        const icon = elem.firstChild;
                        if (data_status == 0) {
                            icon.classList = `fa fa-toggle-off text-danger`;
                            elem.setAttribute('data-status', 1);
                        } else {
                            icon.classList = `fa fa-toggle-on text-success`;
                            elem.setAttribute('data-status', 0);
                        }
                    }
                    toastr[data.alert_type](data.message);
                });
            }
        }
    </script>
@endsection
