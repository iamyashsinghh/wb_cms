@extends('layouts.app')
@section('title', $page_heading)
@section('main')
<style>
    .overlay_container {
        position: relative;
    }

    .overlay_container .overlay_image  {
        opacity: 1;
        display: block;
        width: 100%;
        height: auto;
        transition: .5s ease;
        backface-visibility: hidden;
    }

   .overlay_container .overlay_body {
        transition: .5s ease;
        opacity: 0;
        position: absolute;
        top: 50%;
        left: 50%;
        transform: translate(-50%, -50%);
        -ms-transform: translate(-50%, -50%);
        text-align: center;
    }

    .overlay_container:hover .overlay_image {
        opacity: 0.3;
    }

    .overlay_container:hover .overlay_body {
        opacity: 1;
    }
</style>
@php

@endphp
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
            <div class="row">
                <div class="col-sm-6">
                    <div class="card mb-5">
                        <div class="card-header text-light" style="background-color: var(--wb-renosand);">
                            <h3 class="card-title">{{$data->name}}</h3>
                        </div>
                        <div class="card-body">
                            <h3>Images</h3>
                            <form action="{{route("$view_used_for.images.manage_process", $data->id)}}" method="post" enctype="multipart/form-data">
                                @csrf
                                <input type="hidden" name="user_id" value="{{isset($user_id) ? $user_id : 0}}"> {{-- this input elem is only used for vendor users--}}
                                <div class="form-group">
                                    <label for="">Multiple Selection</label>
                                    <div class="custom-file">
                                        <input type="file" class="custom-file-input" id="customFile" name="gallery_images[]" multiple>
                                        <label class="custom-file-label" for="customFile">Choose file</label>
                                    </div>
                                </div>
                                <button type="submit" class="btn btn-sm text-light" style="background: var(--wb-dark-red);">Save</button>
                            </form>
                        </div>
                    </div>
                </div>
                <div class="col-sm-6 text-right">
                    <button class="btn btn-sm btn-secondary" onclick="handle_sorting_process()">Update Sorting</button>
                </div>
            </div>
            <div id="images_gallery" class="row">
                @if ($data->images != null)
                    @foreach (explode(",", $data->images) as $key => $item)
                    <div class="col-sm-3 py-2 text-center">
                        <div class="overlay_container">
                            <img data-name="{{$item}}" src="{{asset("storage/uploads/$item")}}" class="img-thumbnail sortable_content overlay_image" style="width: 300px; height: 200px;">
                            <div class="overlay_body">
                                <a data-id="{{$key}}" href="javascript:void(0);" class="text-danger" onclick="handle_image_delete(this, '{{$item}}')" style="font-size: 20px;"><i class="fa fa-trash"></i></a>
                            </div>
                        </div>
                    </div>
                    @endforeach
                @endif
            </div>
        </div>
    </section>
</div>
@endsection
@section('footer-script')
<script src="{{asset('plugins/jquery-drag-drop-sorting/jquery.sortable.min.js')}}"></script>
<script src="{{asset('plugins/bs-custom-file-input/bs-custom-file-input.min.js')}}"></script>

<script>
    const data_id = "{{$data->id}}";

    $(document).ready(function () {
        bsCustomFileInput.init()
    })

    $("#images_gallery").sortable({
        handle: "img"
    })

    function handle_sorting_process() {
        let confirmation = window.confirm('Sorting confirmation..');
        if (confirmation) {
            const sortable_content = document.querySelectorAll('.sortable_content');
            let sort_images = [];
            for (let item of sortable_content) {
                sort_images.push(item.getAttribute('data-name'));
            }
            common_ajax(`{{route("$view_used_for.images.update_sorting")}}/${data_id}`, 'post', JSON.stringify({
                images: sort_images
            })).then(response => response.json()).then(data => {
                toastr[data.alert_type](data.message);
            })
        }
    }

    function handle_image_delete(elem, image_name){
        let confirm = window.confirm("Are your sure want to delete the current image");
        if(confirm){
            common_ajax(`{{route("$view_used_for.image.delete")}}/${data_id}`, 'post', JSON.stringify({
                image_name: image_name
            })).then(response => response.json()).then(data => {
                if(data.success){
                    const gallery_card = elem.closest('.col-sm-3');
                    gallery_card.remove();
                }
                toastr[data.alert_type](data.message);
            })
        }
    }
</script>
@endsection
