<div class="modal fade" id="updateFaqModal" tabindex="-1" data-bs-backdrop="static">
    <div class="modal-dialog modal-xl">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title">Update FAQ <button type="button" class="btn btn-success btn-xs ml-3" onclick="handle_add_more_faq(this)"><i class="fa fa-add"></i></button></h4>
                <button type="button" class="btn text-secondary" data-bs-dismiss="modal" aria-label="Close"><i class="fa fa-times"></i></button>
            </div>
            <form action="" method="post">
                @csrf
                <div id="faq_modal_body" class="modal-body text-sm">
                    <div class="row">
                        <div class="col-sm-5">
                            <div class="form-group">
                                <label for="desc_text">FAQ Question <span class="text-danger">*</span></label>
                                <textarea class="form-control" id="desc_text" placeholder="Enter faq question" name="faq_question[]" required></textarea>
                            </div>
                        </div>
                        <div class="col-sm-6">
                            <div class="form-group">
                                <label for="desc_text">FAQ Answer <span class="text-danger">*</span></label>
                                <textarea class="form-control" id="desc_text" placeholder="Enter meta description" name="faq_answer[]" required></textarea>
                            </div>
                        </div>
                        <div class="col m-auto">
                            <button type="button" class="btn btn-sm text-danger" onclick="handle_remove_faq(this)"><i class="fa fa-times"></i></button>
                        </div>
                    </div>
                </div>
                <div class="modal-footer text-sm">
                    <a href="javascript:void(0);" class="btn btn-sm bg-secondary m-1" data-bs-dismiss="modal">Close</a>
                    <button type="submit" class="btn btn-sm text-light m-1" style="background-color: var(--wb-dark-red);">Update</button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
    function handle_add_more_faq(elem){
        const modalBody = document.getElementById('faq_modal_body');
        const row = document.createElement('div');
        row.classList.add('row');
        const faqElems = `<div class="col-sm-5">
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
        </div>`;

        row.innerHTML = faqElems;
        modalBody.append(row);
    }

    function handle_remove_faq(elem){
        elem.parentElement.parentElement.remove();
    }
</script>