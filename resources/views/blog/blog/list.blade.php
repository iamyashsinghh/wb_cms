@extends('layouts.app')
@section('title', 'Blogs')

@section('header-css')
    <link rel="stylesheet" href="//cdn.datatables.net/1.13.1/css/jquery.dataTables.min.css">
    <script src="https://cdnjs.cloudflare.com/ajax/libs/moment.js/2.29.4/moment.min.js"></script>
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
                                <th>Action</th>
                                <th class="text-center">Action</th>
                            </tr>
                        </thead>
                        <tbody></tbody>
                    </table>
                </div>
            </div>
        </section>
        @include('includes.update_faq_modal')
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
                serverSide: false,
                ajax: `{{ route('blog.ajax_list') }}`,
                columns: [{
                        data: 0
                    },
                    {
                        data: 1
                    },
                    {
                        data: 2
                    },
                    {
                        data: 3
                    },
                    {
                        data: 4
                    },
                    {
                        data: 5
                    },
                    {
                        data: 6
                    },
                    {
                        data: 7,
                        orderable: false,
                        searchable: false
                    }
                ],
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
                    td_elements[5].innerText = moment(data[5]).format("DD-MMM-YYYY hh:mm a");
                    let shedule =
                        `<input type="datetime-local" data-id="${data[0]}" value="${data[7]}" class="form-control schedule-input" placeholder="Schedule Date & Time">`;
                    td_elements[6].innerHTML = shedule;
                    td_elements[7].innerHTML = `
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
                         <div class="dropdown d-inline-block mx-2" style=>
                            <a href="javascript:void(0);" data-bs-toggle="dropdown" aria-expanded="false">
                                <i class="fa fa-caret-down text-dark"></i>
                            </a>
                            <ul class="dropdown-menu">
                                <li><a class="dropdown-item" href="javascript:void(0);" onclick="handle_update_faq(${data[0]})">Update FAQ</a></li>
                            </ul>
                        </div>
                    `;
                }
            });

            $(document).on('change', '.schedule-input', function() {
                const blogId = $(this).data('id');
                const scheduleDate = $(this).val();

                updateSchedule(blogId, scheduleDate);
            });

            function updateSchedule(blogId, scheduleDate) {
                $.ajax({
                    url: `{{ route('blog.update_schedule') }}`,
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': '{{ csrf_token() }}'
                    },
                    data: {
                        id: blogId,
                        schedule_date: scheduleDate
                    },
                    success: function(response) {
                        if (response.success) {
                            toastr.success(response.message);
                        } else {
                            toastr.error(response.message);
                        }
                    },
                    error: function() {
                        toastr.error('An error occurred while updating the schedule.');
                    }
                });
            }
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

        function handle_update_faq(blog_id) {
            fetch(`{{ route('blog.fetch_faq') }}/${blog_id}`).then(response => response.json()).then(data => {
                const faqs = JSON.parse(data.faq);
                const updateFaqModal = document.getElementById("updateFaqModal");
                const modal = new bootstrap.Modal(updateFaqModal);

                updateFaqModal.querySelector('form').action = `{{ route('blog.update_faq') }}/${blog_id}`;
                updateFaqModal.querySelector('#faq_modal_body').innerHTML = "";

                if (faqs != null && faqs.length > 0) {
                    for (let faq of faqs) {
                        const faqElem = `<div class="row">
                        <div class="col-sm-5">
                            <div class="form-group">
                                <label for="desc_text">FAQ Question <span class="text-danger">*</span></label>
                                <textarea class="form-control" id="desc_text" placeholder="Enter faq question" name="faq_question[]" required rows="1">${faq.question}</textarea>
                            </div>
                        </div>
                        <div class="col-sm-6">
                            <div class="form-group">
                                <label for="desc_text">FAQ Answer <span class="text-danger">*</span></label>
                                <textarea class="form-control" id="desc_text" placeholder="Enter meta description" name="faq_answer[]" required rows="1">${faq.answer}</textarea>
                            </div>
                        </div>
                        <div class="col m-auto">
                            <button type="button" class="btn btn-sm text-danger" onclick="handle_remove_faq(this)"><i class="fa fa-times"></i></button>
                        </div>
                    </div>`;
                        updateFaqModal.querySelector('#faq_modal_body').innerHTML += faqElem;
                    }
                } else {
                    const faqElem = `<div class="row">
                    <div class="col-sm-5">
                        <div class="form-group">
                            <label for="desc_text">FAQ Question <span class="text-danger">*</span></label>
                            <textarea class="form-control" id="desc_text" placeholder="Enter faq question" name="faq_question[]" required rows="1"></textarea>
                        </div>
                    </div>
                    <div class="col-sm-6">
                        <div class="form-group">
                            <label for="desc_text">FAQ Answer <span class="text-danger">*</span></label>
                            <textarea class="form-control" id="desc_text" placeholder="Enter meta description" name="faq_answer[]" required rows="1"></textarea>
                        </div>
                    </div>
                    <div class="col m-auto">
                        <button type="button" class="btn btn-sm text-danger" onclick="handle_remove_faq(this)"><i class="fa fa-times"></i></button>
                    </div>
                </div>`;
                    updateFaqModal.querySelector('#faq_modal_body').innerHTML = faqElem;
                }

                modal.show();
            });
        }
    </script>
@endsection
