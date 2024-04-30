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
<script src="{{asset('plugins/jquery/jquery.min.js')}}"></script>
<link href="https://cdn.jsdelivr.net/npm/quill@2.0.0/dist/quill.snow.css" rel="stylesheet" />
<script src="https://cdn.jsdelivr.net/npm/quill@2.0.0/dist/quill.js"></script>

<script>
    $(document).ready(function() {
        function initQuillEditorsWithinModal() {
            $('#updateFaqModal textarea').each(function() {
                if (!$(this).next().hasClass('ql-container')) { // Check if Quill has not been initialized
                    var quillEditor = new Quill(this, { theme: 'snow' });
                    $(this).data('quill', quillEditor);
                }
            });
        }
        $('#updateFaqModal').on('shown.bs.modal', function() {
            // initQuillEditorsWithinModal();
        });
        const firstQuill = new Quill('#desc_text', { theme: 'snow' });
        $('#desc_text').closest('.form-group').find('.quill-editor').data('quill', firstQuill);
        $('form').on('submit', function(e) {
            e.preventDefault();
            $('.quill-editor').each(function() {
                const quillInstance = $(this).data('quill');
                $(this).next('textarea').val(quillInstance.root.innerHTML);
            });
            this.submit();
        });

        window.handle_add_more_faq = function(elem) {
            const modalBody = document.getElementById('faq_modal_body');
            const randomId = 'quill-' + Math.random().toString(36).substring(7); // Unique ID for new Quill editor
            const textareaId = 'textarea-' + Math.random().toString(36).substring(7); // Unique ID for new textarea

            const row = document.createElement('div');
            row.classList.add('row');
            const faqElems = `
                <div class="col-sm-5">
                    <div class="form-group">
                        <label>FAQ Question <span class="text-danger">*</span></label>
                        <textarea class="form-control" placeholder="Enter FAQ question" name="faq_question[]" required></textarea>
                    </div>
                </div>
                <div class="col-sm-6">
                    <div class="form-group">
                        <label>FAQ Answer <span class="text-danger">*</span></label>
                        <div id="${randomId}" class="quill-editor"></div>
                        <textarea class="form-control d-none" id="${textareaId}" placeholder="Enter FAQ answer" name="faq_answer[]"></textarea>
                    </div>
                </div>
                <div class="col m-auto">
                    <button type="button" class="btn btn-sm text-danger" onclick="handle_remove_faq(this)"><i class="fa fa-times"></i></button>
                </div>
            `;
            row.innerHTML = faqElems;
            modalBody.appendChild(row);

            const newQuillContainer = document.querySelector(`#${randomId}`);
            const newQuill = new Quill(newQuillContainer, { theme: 'snow' });
            $(newQuillContainer).data('quill', newQuill);
        };
        window.handle_remove_faq = function(elem) {
            elem.closest('.row').remove();
        };
    });
</script>
