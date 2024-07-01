@extends('layouts.app')
@section('title', 'Blogs')

@section('header-css')
    <link rel="stylesheet" href="//cdn.datatables.net/1.13.1/css/jquery.dataTables.min.css">
@endsection
@section('main')
    <div class="content-wrapper pb-5">
        <section class="content-header">
            <div class="container-fluid">
                <div class="row mb-2">
                    <div class="col-sm-6">
                        <h1 class="m-0">Blogs</h1>
                    </div>
                </div>
                <div class="button-group my-4">
                    @canany(['create blog', 'super power'])
                        <a href="{{ route('blog.manage') }}/0" class="btn btn-sm text-light buttons-print"
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
                            <tr>
                                <th>ID</th>
                                <th>Blog Heading</th>
                                <th>Author</th>
                                <th>Status</th>
                                <th>Popular</th>
                                <th>Last Modified At</th>
                                <th class="text-center">Action</th>
                            </tr>
                        </thead>
                        <tbody></tbody>
                    </table>
                </div>
            </div>
        </section>
    </div>
@endsection
@section('footer-script')
    <script src="//cdn.datatables.net/1.13.1/js/jquery.dataTables.min.js"></script>
    <script>
        $(document).ready(function() {
            $('#serverTable').DataTable({
                pageLength: 10,
                processing: true,
                language: {
                    "search": "_INPUT_",
                    "searchPlaceholder": "Type here to search..",
                },
                serverSide: true,
                ajax: `{{ route('blog.ajax_list') }}`,
                order: [
                    [0, 'desc']
                ],
                rowCallback: function(row, data, index) {
                    const td_elements = row.querySelectorAll('td');

                    @canany(['publish blog', 'super power'])
                        let status = data[3] == 1 ?
                            `<a data-id="${data[0]}" data-status="0" data-submit-url="{{ route('blog.status') }}" href="javascript:void(0);" style="font-size: 22px;" onclick="handle_update_status(this)"><i class="fa fa-toggle-on text-success"></i></a>` :
                            `<a data-id="${data[0]}" data-status="1" data-submit-url="{{ route('blog.status') }}" href="javascript:void(0);" style="font-size: 22px;" onclick="handle_update_status(this)"><i class="fa fa-toggle-off text-danger"></i></a>`;
                    @else
                        let status = data[3] == 1 ?
                            `<a data-id="${data[0]}" href="javascript:void(0);" style="font-size: 22px;"><i class="fa fa-toggle-on text-success"></i></a>` :
                            `<a data-id="${data[0]}" href="javascript:void(0);" style="font-size: 22px;"><i class="fa fa-toggle-off text-danger"></i></a>`;
                    @endcanany

                    let popular = data[6] == 1 ?
                        `<a data-id="${data[0]}" data-status="0" data-submit-url="{{ route('blog.popular') }}" href="javascript:void(0);" style="font-size: 22px;" onclick="handle_update_status(this)"><i class="fa fa-toggle-on text-success"></i></a>` :
                        `<a data-id="${data[0]}" data-status="1" data-submit-url="{{ route('blog.popular') }}" href="javascript:void(0);" style="font-size: 22px;" onclick="handle_update_status(this)"><i class="fa fa-toggle-off text-danger"></i></a>`;
                    td_elements[3].innerHTML = status;
                    td_elements[4].innerHTML = popular;
                    td_elements[5].innerText = data[5];
                    td_elements[6].innerHTML = `
                        @canany(['edit blog', 'super power'])
                            <a href="{{ route('blog.manage') }}/${data[0]}" class="text-success mx-2" title="Edit">
                                <i class="fa fa-edit" style="font-size: 15px;"></i>
                            </a>
                        @endcanany
                        @canany(['delete blog', 'super power'])
                            <a onclick="handle_delete_blog(${data[0]})" class="text-danger mx-2" title="Delete">
                                <i class="fa fa-trash-alt" style="font-size: 15px;"></i>
                            </a>
                        @endcanany
                    `;
                }
            });
        });

        function handle_update_status(elem) {
            if (confirm("Are you sure want to update the status?")) {
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
                        toastr[data.alert_type](data.message);
                    }
                });
            }
        }

        function handle_delete_blog(blogId) {
            if (confirm("Are you sure you want to delete this blog?")) {
                fetch(`{{ route('blog.destroy', '') }}/${blogId}`, {
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': '{{ csrf_token() }}'
                    }
                }).then(response => response.json()).then(data => {
                    if (data.success === true) {
                        toastr[data.alert_type](data.message);
                        setTimeout(() => {
                            window.location.reload();
                        }, 1000);
                    }
                });
            }
        }
    </script>
@endsection
