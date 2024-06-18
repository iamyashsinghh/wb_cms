@extends('layouts.app')

@section('title', 'Edit Author')

@section('main')
    <div class="content-wrapper pb-5">
        <section class="content-header">
            <div class="container-fluid">
                <div class="row mb-2">
                    <div class="col-sm-6">
                        <h1 class="m-0">Edit Author</h1>
                    </div>
                </div>
            </div>
        </section>
        <section class="content">
            <div class="container-fluid">
                <div class="card text-sm">
                    <div class="card-header text-light" style="background-color: var(--wb-renosand)">
                        <h3 class="card-title">Author Details</h3>
                    </div>
                    <form action="{{ route('author.update', $author->id) }}" method="post" enctype="multipart/form-data">
                        <div class="modal-body text-sm">
                            @csrf
                            <div class="row">
                                <div class="col-sm-4">
                                    <div class="form-group">
                                        <label for="author_name">Author Name <span class="text-danger">*</span></label>
                                        <input type="text" class="form-control @error('name') is-invalid @enderror"
                                            placeholder="Enter Author Name" id="author_name" name="name"
                                            value="{{ old('name', $author->name) }}" required>
                                        @error('name')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>
                                <div class="col-sm-4">
                                    <div class="form-group">
                                        <label for="author_image">Author Image</label>
                                        <input type="file" class="form-control @error('image') is-invalid @enderror"
                                            id="author_image" name="image" onchange="setImagePreview(event)">
                                        @if($author->image)
                                            <img id="image_preview" src="{{ $author->image }}" alt="Image Preview" style="max-width: 100%; height: auto;">
                                        @endif
                                        @error('image')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>
                                <div class="col-sm-4">
                                    <div class="form-group">
                                        <label for="author_bio">Author Bio</label>
                                        <textarea class="form-control @error('bio') is-invalid @enderror" placeholder="Enter Author Bio"
                                            id="author_bio" name="bio" rows="3">{{ old('bio', $author->bio) }}</textarea>
                                        @error('bio')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="modal-footer text-sm">
                            <a href="{{ route('author.list') }}" class="btn btn-sm bg-secondary m-1">Back</a>
                            <button type="submit" class="btn btn-sm text-light m-1"
                                style="background-color: var(--wb-dark-red);">Submit</button>
                        </div>
                    </form>
                </div>
            </div>
        </section>
    </div>

    <script>
        function setImagePreview(event) {
            const reader = new FileReader();
            reader.onload = function() {
                const output = document.getElementById('image_preview');
                output.src = reader.result;
            }
            reader.readAsDataURL(event.target.files[0]);
        }
    </script>
@endsection
