<div class="modal fade" id="updatePhoneNoModal" tabindex="-1" data-bs-backdrop="static">
    <div class="modal-dialog modal-md">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title">Change Phone Number</h4>
                <button type="button" class="btn text-secondary" data-bs-dismiss="modal" aria-label="Close"><i class="fa fa-times"></i></button>
            </div>
            <form action="" method="post">
                @csrf
                <div class="modal-body text-sm">
                    <div class="form-group">
                        <label for="phone_inp">Phone No. <span class="text-danger">*</span></label>
                        <input type="number" class="form-control" id="phone_inp" placeholder="Enter phone number" name="phone_number" required minlength="11">
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