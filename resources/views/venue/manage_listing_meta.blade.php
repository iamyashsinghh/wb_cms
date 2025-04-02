@extends('layouts.app')
@section('title', $page_heading)
@section('header-css')
<link rel="stylesheet" href="{{asset('plugins/select2/css/select2.min.css')}}">
<link rel="stylesheet" href="{{ asset('plugins/summernote/summernote-lite.min.css') }}">
@endsection
@section('main')
<div class="content-wrapper pb-5">
    <section class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6">
                    <h1 class="m-0">{{$page_heading}}</h1>
                </div>
            </div>
        </div>
    </section>
    <section class="content">
        <div class="container-fluid">
            <div class="card text-sm">
                <div class="card-header text-light" style="background-color: var(--wb-renosand)">
                    <h3 class="card-title">Listing Meta Details</h3>
                </div>
                <form action="{{route('venue.listing_meta.manage_process', $meta->id)}}" method="post">
                    <div class="modal-body text-sm">
                        @csrf
                        <div class="row">
                            <div class="col-sm-4">
                                <div class="form-group">
                                    <label>Category <span class="text-danger">*</span></label>
                                    <select class="form-control select2" name="category" required>
                                        <option value="" disabled selected>Select Category</option>
                                        @foreach($categories as $list)
                                        <option value="{{$list->id}}" {{$meta->category_id == $list->id ? 'selected' : ''}}>{{$list->name}}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                            <div class="col-sm-4">
                                <div class="form-group">
                                    <label>City <span class="text-danger">*</span></label>
                                    <select class="form-control select2" name="city" onchange="fetch_locations(this.value)" required>
                                        <option value="" disabled selected>Select City</option>
                                        @foreach($cities as $list)
                                        <option value="{{$list->id}}" {{$meta->city_id == $list->id ? 'selected' : ''}}>{{$list->name}}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                            <div class="col-sm-4">
                                <div class="form-group">
                                    <label>Location</label>
                                    <select class="form-control select2" id="location_select" name="location">
                                        <option value="">All</option>
                                        @foreach ($locations as $list)
                                            <option value="{{$list->id}}" {{$meta->location_id == $list->id ? 'selected' : ''}}>{{$list->name}}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                            <div class="col-sm-4">
                                <div class="form-group">
                                    <label for="title_text">Meta Title <span class="text-danger">*</span></label>
                                    <textarea class="form-control" placeholder="Enter meta title" name="meta_title" id="meta_title_count"  rows="3" required>{{$meta->meta_title}}</textarea>
                                                                        <div id="charCountmeta_title_count">0 Characters</div>

                                </div>
                            </div>
                            <div class="col-sm-4">
                                <div class="form-group">
                                    <label for="desc_text">Meta Description</label>
                                    <textarea class="form-control" placeholder="Enter meta description" id="meta_description_count" name="meta_description" rows="3">{{$meta->meta_description}}</textarea>
                                                                        <div id="charCountmeta_description_count">0 Characters</div>

                                </div>
                            </div>
                            <div class="col-sm-4">
                                <div class="form-group">
                                    <label for="desc_text">Meta Keywords</label>
                                    <textarea class="form-control" placeholder="Enter meta keywords" name="meta_keywords" rows="3">{{$meta->meta_keywords}}</textarea>
                                </div>
                            </div>
                            <div class="col-sm-12">
                                <div class="form-group">
                                    <label for="desc_text">Header Script</label>
                                    <textarea class="form-control" placeholder="Enter header script" name="header_script" rows="5">{{$meta->header_script}}</textarea>
                                </div>
                            </div>
                            <div class="col-sm-12">
                                <div class="form-group">
                                    <label for="desc_text">Footer <span id="current-tag">None</span></label>
                                    <textarea id="summernote" class="form-control" placeholder="Enter footer caption" name="footer_caption" rows="3">{{$meta->caption}}</textarea>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer text-sm">
                        <a href="{{route('venue.listing_meta.ajax_list')}}" class="btn btn-sm bg-secondary m-1">Back</a>
                        <button type="submit" class="btn btn-sm text-light m-1" style="background-color: var(--wb-dark-red);">Submit</button>
                    </div>
                </form>
            </div>
        </div>
    </section>
</div>
@endsection
@section('footer-script')
<script src="{{asset('plugins/select2/js/select2.min.js')}}"></script>
<script src="{{ asset('plugins/summernote/summernote-lite.min.js') }}"></script>
<script>
    $('.select2').select2({
        placeholder: 'Select an option'
    });
    $(document).on('click', function () {
    setTimeout(updateTag, 200);
});
    $('#summernote').summernote({
            placeholder: 'Type here content',
            tabsize: 2,
            callbacks: {
        onKeyup: function () {
            setTimeout(updateTag, 200);
        },
        onMouseUp: function () {
            setTimeout(updateTag, 200);
        }
    }
        });

        function updateTag() {

    var selection = document.getSelection(); // Get current selection
    if (selection.rangeCount > 0) {
        var node = selection.getRangeAt(0).commonAncestorContainer; // Find the parent node
        var tagName = node.nodeType === 3 ? node.parentNode.nodeName : node.nodeName; // Handle text nodes
        $('#current-tag').text(tagName); // Update the tag display
    }
}

    function fetch_locations(city_id, selected_id = null){
        fetch(`{{route('location.get_locations')}}/${city_id}`).then(response => response.json()).then(data => {
            let elem = `<option value="">All</option>`;
            for(let loc of data.locations){
                elem += `<option value="${loc.id}" ${loc.id == selected_id ? 'selected': ''}>${loc.name}</option>`
            }
            location_select.innerHTML = elem;
        })
    }
</script>
@endsection
