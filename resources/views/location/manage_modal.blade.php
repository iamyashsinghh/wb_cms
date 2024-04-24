<div class="modal fade" id="manageLocationModal" tabindex="-1">
    <div class="modal-dialog modal-md">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title">Add Location</h4>
                <button type="button" class="btn text-secondary" data-bs-dismiss="modal" aria-label="Close"><i class="fa fa-times"></i></button>
            </div>
            <form action="{{route('location.manage_process')}}" method="post">
                <div class="modal-body text-sm">
                    @csrf
                    <div class="form-group">
                        <label>City <span class="text-danger">*</span></label>
                        <select class="form-control" name="city" required>
                            @foreach($cities as $list)
                            <option value="{{$list->id}}">{{$list->name}}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="form-group">
                        <label for="name_inp">Location Name <span class="text-danger">*</span></label>
                        <input type="text" class="form-control" id="name_inp" placeholder="Enter location name" name="location_name" required>
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