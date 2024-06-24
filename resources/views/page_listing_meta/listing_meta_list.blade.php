@extends('layouts.app')
@section('title', "Page Listing Meta")
@section('header-css')
    <link rel="stylesheet" href="//cdn.datatables.net/1.13.1/css/jquery.dataTables.min.css">
@endsection
@section('main')
<div class="content-wrapper pb-5">
    <section class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6">
                    <h1 class="m-0">Page Listing Meta</h1>
                </div>
            </div>
            <div class="button-group my-4">
                @canany(['super power', 'create page_listing_meta'])
                <a href="{{route('page_listing_meta.listing_meta.manage')}}" class="btn btn-sm text-light buttons-print" style="background-color: var(--wb-renosand)"><i class="fa fa-plus mr-1"></i>Add New</a>
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
                        <th>Slug</th>
                        <th>City</th>
                        <th>Status</th>
                        <th class="text-center">Action</th>
                    </thead>
                </table>
            </div>
        </div>
    </section>
    <div class="modal fade" id="manageMetaModal" tabindex="-1">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title"></h4>
                    <button type="button" class="btn text-secondary" data-bs-dismiss="modal" aria-label="Close"><i class="fa fa-times"></i></button>
                </div>
                <form action="" method="post">
                    <div class="modal-body text-sm">
                        @csrf
                        <div class="row">
                            <div class="col-sm-4">
                                <div class="form-group">
                                    <label>Category <span class="text-danger">*</span></label>
                                    <select class="form-control" name="category" required>
                                        <option disabled selected>Select Category</option>

                                    </select>
                                </div>
                            </div>
                            <div class="col-sm-4">
                                <div class="form-group">
                                    <label>City <span class="text-danger">*</span></label>
                                    <select class="form-control" name="city" onchange="fetch_locations(this.value)" required>
                                        <option disabled selected>Select City</option>

                                    </select>
                                </div>
                            </div>
                            <div class="col-sm-4">
                                <div class="form-group">
                                    <label>Location</label>
                                    <select class="form-control" id="location_select" name="location" required>
                                        <option value="" selected>All</option>
                                    </select>
                                </div>
                            </div>
                            <div class="col-sm-6">
                                <div class="form-group">
                                    <label for="title_text">Meta Title <span class="text-danger">*</span></label>
                                    <textarea class="form-control" placeholder="Enter meta description" name="meta_title" rows="3" required></textarea>
                                </div>
                            </div>
                            <div class="col-sm-6">
                                <div class="form-group">
                                    <label for="title_text">Meta Keywords</label>
                                    <textarea class="form-control" placeholder="Enter meta description" name="meta_keywords" rows="3"></textarea>
                                </div>
                            </div>
                            <div class="col-sm-12">
                                <div class="form-group">
                                    <label for="desc_text">Meta Description <span class="text-danger">*</span></label>
                                    <textarea class="form-control" placeholder="Enter meta description" name="meta_description" rows="3" required></textarea>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer text-sm">
                        <a href="javascript:void(0);" class="btn btn-sm bg-secondary m-1" data-bs-dismiss="modal">Close</a>
                        <button type="submit" class="btn btn-sm text-light m-1" style="background-color: var(--wb-dark-red);">Submit</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
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
            searchable: true,
            ordering: true,
            language: {
                "search": "_INPUT_",
                "searchPlaceholder": "Type here to search..",
            },
            serverSide: true,
            ajax: `{{route('page_listing_meta.listing_meta.ajax_list')}}`,
            order: [
                [0, 'desc']
            ],
            rowCallback: function(row, data, index) {
                const td_elements = row.querySelectorAll('td');


                @canany(['publish page_listing_meta', 'super power'])
                if(data[5] == 1){
                    status_elem = `<a data-id="${data[0]}" data-status="0" href="javascript:void(0);" style="font-size: 22px;" onclick="update_status(this)"><i class="fa fa-toggle-on text-success"></i></a>`;
                }else{
                    status_elem = `<a data-id="${data[0]}" data-status="1" href="javascript:void(0);" style="font-size: 22px;" onclick="update_status(this)"><i class="fa fa-toggle-off text-danger"></i></a>`;
                }
                @else
                if(data[5] == 1){
                    status_elem = `<a data-id="${data[0]}" data-status="0" style="font-size: 22px;" ><i class="fa fa-toggle-on text-success"></i></a>`;
                }else{
                    status_elem = `<a data-id="${data[0]}" data-status="1" style="font-size: 22px;"><i class="fa fa-toggle-off text-danger"></i></a>`;
                }
                @endcanany
                td_elements[3].innerHTML = status_elem;

                td_elements[4].classList.add('text-center');
                td_elements[4].innerHTML = `
                    @canany(['edit page_listing_meta', 'super power'])
                <a href="{{route('page_listing_meta.listing_meta.manage')}}/${data[0]}" class="text-success mx-2" title="Edit">
                    <i class="fa fa-edit" style="font-size: 15px;"></i>
                </a>
                @endcanany

                @canany(['delete page_listing_meta', 'super power'])
                <a href="{{route('page_listing_meta.listing_meta.delete')}}/${data[0]}" onclick="return confirm('Are you sure want to delete?')" class="text-danger mx-2" title="Delete">
                    <i class="fa fa-trash-alt" style="font-size: 15px;"></i>
                </a>
                @endcanany

                @canany(['edit page_listing_meta', 'super power'])
                <div class="dropdown d-inline-block mx-2">
                    <a href="javascript:void(0);" data-bs-toggle="dropdown" aria-expanded="false">
                    <i class="fa fa-caret-down text-dark"></i>
                    </a>
                    <ul class="dropdown-menu">
                        <li><a class="dropdown-item" href="javascript:void(0);" onclick="update_faq(${data[0]})">Update FAQ</a></li>
                    </ul>
                </div>
                @endcanany
                `;
            }
        });
    });

    function update_status(elem){
        if(confirm("Are you sure want to update the status")){
            const data_id = elem.getAttribute('data-id');
            const data_status = elem.getAttribute('data-status');
            fetch(`{{route('page_listing_meta.listing_meta.update_status')}}/${data_id}/${data_status}`).then(response => response.json()).then(data => {
                if(data.success === true){
                    const icon = elem.firstChild;
                    if(data_status == 0){
                        icon.classList = `fa fa-toggle-off text-danger`;
                        elem.setAttribute('data-status', 1);
                    }else{
                        icon.classList = `fa fa-toggle-on text-success`;
                        elem.setAttribute('data-status', 0);
                    }
                }
                toastr[data.alert_type](data.message);
            })
        }
    }

    function update_faq(faq_id){
        fetch(`{{route('page_listing_meta.listing_meta.fetch_faq')}}/${faq_id}`).then(response => response.json()).then(data => {
            const faqs = JSON.parse(data.faq);
            const updateFaqModal = document.getElementById("updateFaqModal");
            const modal = new bootstrap.Modal(updateFaqModal);

            updateFaqModal.querySelector('form').action = `{{route('page_listing_meta.listing_meta.update_faq')}}/${faq_id}`;
            updateFaqModal.querySelector('#faq_modal_body').innerHTML = "";

            if(faqs != null && faqs.length > 0){
                for(let faq of faqs){
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
            }else{
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
        })
    }
</script>

@endsection
