<div class="modal fade" id="updateMetaModal" tabindex="-1" data-bs-backdrop="static">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title">Update Meta</h4>
                <button type="button" class="btn text-secondary" data-bs-dismiss="modal" aria-label="Close"><i class="fa fa-times"></i></button>
            </div>
            <form action="" method="post">
                <div class="modal-body text-sm">
                    @csrf
                    <div class="form-group">
                        <label for="title_inp">Meta Title <span class="text-danger">*</span></label>
                        <input type="text" class="form-control" id="title_inp" placeholder="Enter meta title" name="meta_title" required>
                    </div>
                    <div class="form-group">
                        <label for="desc_text">Meta Description <span class="text-danger">*</span></label>
                        <textarea class="form-control" id="desc_text" placeholder="Enter meta description" name="meta_description" rows="3" required></textarea>
                    </div>
                    <div class="form-group">
                        <label for="desc_text">Meta Keywords <span class="text-danger">*</span></label>
                        <textarea class="form-control" id="keyword_text" placeholder="Enter meta Keyword" name="meta_keywords" rows="3" required></textarea>
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