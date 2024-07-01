@extends('layouts.app')

@section('title', $page_heading)

@section('header-css')
    <link rel="stylesheet" href="{{ asset('plugins/select2/css/select2.min.css') }}">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.4.0/css/font-awesome.min.css">
    <link rel="stylesheet" href="{{ asset('plugins/flora/froala_editor.css') }}">
    <link rel="stylesheet" href="{{ asset('plugins/flora/froala_style.css') }}">
    <link rel="stylesheet" href="{{ asset('plugins/flora/code_view.css') }}">
    <link rel="stylesheet" href="{{ asset('plugins/flora/draggable.css') }}">
    <link rel="stylesheet" href="{{ asset('plugins/flora/colors.css') }}">
    <link rel="stylesheet" href="{{ asset('plugins/flora/emoticons.css') }}">
    <link rel="stylesheet" href="{{ asset('plugins/flora/image_manager.css') }}">
    <link rel="stylesheet" href="{{ asset('plugins/flora/image.css') }}">
    <link rel="stylesheet" href="{{ asset('plugins/flora/line_breaker.css') }}">
    <link rel="stylesheet" href="{{ asset('plugins/flora/table.css') }}">
    <link rel="stylesheet" href="{{ asset('plugins/flora/char_counter.css') }}">
    <link rel="stylesheet" href="{{ asset('plugins/flora/video.css') }}">
    <link rel="stylesheet" href="{{ asset('plugins/flora/fullscreen.css') }}">
    <link rel="stylesheet" href="{{ asset('plugins/flora/file.css') }}">
    <link rel="stylesheet" href="{{ asset('plugins/flora/quick_insert.css') }}">
    <link rel="stylesheet" href="{{ asset('plugins/flora/help.css') }}">
    <link rel="stylesheet" href="{{ asset('plugins/flora/third_party/spell_checker.css') }}">
    <link rel="stylesheet" href="{{ asset('plugins/flora/special_characters.css') }}">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/codemirror/6.65.7/codemirror.min.css">
@endsection

@section('main')
    <div class="content-wrapper pb-5">
        <section class="content-header">
            <div class="container-fluid">
                <div class="row mb-2">
                    <div class="col-sm-6">
                        <h1 class="m-0">{{ $page_heading }}</h1>
                    </div>
                </div>
            </div>
        </section>
        <section class="content">
            <div class="container-fluid">
                <div class="card text-sm">
                    <div class="card-header text-light" style="background-color: var(--wb-renosand)">
                        <h3 class="card-title">Blog Details</h3>
                    </div>
                    <form id="blog-form" action="{{ route('blog.manage_process', ['blog_id' => $data->id]) }}"
                        method="post" enctype="multipart/form-data">
                        <div class="modal-body text-sm">
                            @csrf
                            <div class="row">
                                <div class="col-sm-4">
                                    <div class="form-group">
                                        <label for="blog_title">Blog Title <span class="text-danger">*</span></label>
                                        <input type="text" class="form-control @error('heading') is-invalid @enderror"
                                            placeholder="Enter Blog Title" id="blog_title" name="heading"
                                            value="{{ old('heading', $data->heading) }}" required onkeyup="generateSlug()">
                                        @error('heading')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>
                                <div class="col-sm-4">
                                    <div class="form-group">
                                        <label for="blog_slug">Slug <span class="text-danger">*</span></label>
                                        <input type="text" class="form-control @error('slug') is-invalid @enderror"
                                            placeholder="Blog Slug" id="blog_slug" name="slug" required
                                            value="{{ old('slug', $data->slug) }}">
                                        @error('slug')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>
                                <div class="col-sm-4">
                                    <div class="form-group">
                                        <label for="blog_feture_img">Feature Image <span
                                                class="text-danger">*</span></label>
                                        <div class="d-flex">
                                            <div class="row">
                                                <input type="file"
                                                    class="form-control col-7 @error('image') is-invalid @enderror"
                                                    id="blog_feture_img" name="image" onchange="setImagePreview(event)">
                                                <button type="button" class="btn btn-primary col-5" data-toggle="modal"
                                                    data-target="#imagePreviewModal">Preview Image</button>
                                                @error('image')
                                                    <div class="invalid-feedback">{{ $message }}</div>
                                                @enderror
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-sm-4">
                                    <div class="form-group">
                                        <label for="meta_title_count">Meta Title <span
                                                class="text-danger">*</span></label>
                                        <textarea class="form-control @error('meta_title') is-invalid @enderror" placeholder="Enter meta title"
                                            name="meta_title" id="meta_title_count" rows="3" required>{{ old('meta_title', $data->meta_title) }}</textarea>
                                        @error('meta_title')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                        <div id="charCountmeta_title_count">0 Characters</div>
                                    </div>
                                </div>
                                <div class="col-sm-4">
                                    <div class="form-group">
                                        <label for="meta_description_count">Meta Description <span
                                                class="text-danger">*</span></label>
                                        <textarea class="form-control @error('meta_description') is-invalid @enderror" placeholder="Enter meta description"
                                            id="meta_description_count" name="meta_description" rows="3" required>{{ old('meta_description', $data->meta_description) }}</textarea>
                                        @error('meta_description')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                        <div id="charCountmeta_description_count">0 Characters</div>
                                    </div>
                                </div>
                                <div class="col-sm-4">
                                    <div class="form-group">
                                        <label for="meta_keywords">Meta Keywords <span
                                                class="text-danger">*</span></label>
                                        <textarea class="form-control @error('meta_keywords') is-invalid @enderror" placeholder="Enter meta keywords"
                                            name="meta_keywords" rows="3" required>{{ old('meta_keywords', $data->meta_keywords) }}</textarea>
                                        @error('meta_keywords')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>
                                <div class="col-sm-4">
                                    <div class="form-group">
                                        <label for="excerpt">Blog Excerpt</label>
                                        <textarea class="form-control @error('excerpt') is-invalid @enderror" placeholder="Enter Blog Excerpt" name="excerpt"
                                            rows="3">{{ old('excerpt', $data->excerpt) }}</textarea>
                                        @error('excerpt')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>
                                <div class="col-sm-4">
                                    <div class="form-group">
                                        <label for="header_text">Header Custom Tag</label>
                                        <textarea class="form-control @error('header_text') is-invalid @enderror" placeholder="Header Custom Tag"
                                            name="header_text" rows="3">{{ old('header_text', $data->header_text) }}</textarea>
                                        @error('header_text')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>
                                <div class="col-sm-4">
                                    <div class="form-group">
                                        <label for="footer_text">Footer Custom Tag</label>
                                        <textarea class="form-control @error('footer_text') is-invalid @enderror" placeholder="Footer Custom Tag"
                                            name="footer_text" rows="3">{{ old('footer_text', $data->footer_text) }}</textarea>
                                        @error('footer_text')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>
                                <div class="col-sm-4">
                                    <div class="form-group">
                                        <label for="image_alt">Feature Image Alt <span
                                                class="text-danger">*</span></label>
                                        <textarea class="form-control @error('image_alt') is-invalid @enderror" placeholder="Feature Image Alt"
                                            name="image_alt" rows="3" required>{{ old('image_alt', $data->image_alt) }}</textarea>
                                        @error('image_alt')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>
                                <div class="col-sm-4">
                                    <div class="form-group">
                                        <label for="og_title">Og Title</label>
                                        <textarea class="form-control @error('og_title') is-invalid @enderror" placeholder="Og Title" name="og_title"
                                            rows="3">{{ old('og_title', $data->og_title) }}</textarea>
                                        @error('og_title')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>
                                <div class="col-sm-4">
                                    <div class="form-group">
                                        <label for="og_description">Og Description</label>
                                        <textarea class="form-control @error('og_description') is-invalid @enderror" placeholder="Og Description"
                                            name="og_description" rows="3">{{ old('og_description', $data->og_description) }}</textarea>
                                        @error('og_description')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>
                                <div class="col-sm-12">
                                    <div class="form-group">
                                        <label for="blog_summary">Blog <span class="text-danger">*</span></label>
                                        <textarea class="form-control @error('summary') is-invalid @enderror" placeholder="Blog" id="blog_summary"
                                            name="summary" rows="10" required>{{ old('summary', $data->summary) }}</textarea>
                                        @error('summary')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>
                                <div class="col-sm-4">
                                    <div class="form-group">
                                        <label for="author_id">Author <span class="text-danger">*</span></label>
                                        <select class="form-control @error('author_id') is-invalid @enderror"
                                            id="author_id" name="author_id" required>
                                            <option value="">Select Author</option>
                                            @foreach ($authors as $author)
                                                <option value="{{ $author->id }}"
                                                    {{ old('author_id', $data->author_id) == $author->id ? 'selected' : '' }}>
                                                    {{ $author->name }}</option>
                                            @endforeach
                                        </select>
                                        @error('author_id')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="modal-footer text-sm">
                            <a href="{{ route('blog.list') }}" class="btn btn-sm bg-secondary m-1">Back</a>
                            <button type="submit" class="btn btn-sm text-light m-1"
                                style="background-color: var(--wb-dark-red);">Submit</button>
                        </div>
                    </form>
                </div>
            </div>
        </section>
    </div>

    <!-- Modal -->
    <div class="modal fade" id="imagePreviewModal" tabindex="-1" role="dialog"
        aria-labelledby="imagePreviewModalLabel" aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="imagePreviewModalLabel">Image Preview</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <img id="image_preview" src="{{ old('image', $data->image) }}" alt="Image Preview"
                        style="max-width: 100%; height: auto;">
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('footer-script')
    <script src="{{ asset('plugins/select2/js/select2.min.js') }}"></script>
    <script src="https://cdn.ckeditor.com/ckeditor5/40.1.0/classic/ckeditor.js"></script>
    <script type="text/javascript" src="https://cdnjs.cloudflare.com/ajax/libs/codemirror/6.65.7/codemirror.min.js">
    </script>
    <script type="text/javascript" src="https://cdnjs.cloudflare.com/ajax/libs/codemirror/6.65.7/mode/xml/xml.min.js">
    </script>
    <script type="text/javascript" src="https://cdnjs.cloudflare.com/ajax/libs/dompurify/2.2.7/purify.min.js"></script>

    <script type="text/javascript" src="{{ asset('plugins/flora/froala_editor.min.js') }}"></script>
    <script type="text/javascript" src="{{ asset('plugins/flora/align.min.js') }}"></script>
    <script type="text/javascript" src="{{ asset('plugins/flora/char_counter.min.js') }}"></script>
    <script type="text/javascript" src="{{ asset('plugins/flora/code_beautifier.min.js') }}"></script>
    <script type="text/javascript" src="{{ asset('plugins/flora/code_view.min.js') }}"></script>
    <script type="text/javascript" src="{{ asset('plugins/flora/colors.min.js') }}"></script>
    <script type="text/javascript" src="{{ asset('plugins/flora/draggable.min.js') }}"></script>
    <script type="text/javascript" src="{{ asset('plugins/flora/emoticons.min.js') }}"></script>
    <script type="text/javascript" src="{{ asset('plugins/flora/entities.min.js') }}"></script>
    <script type="text/javascript" src="{{ asset('plugins/flora/file.min.js') }}"></script>
    <script type="text/javascript" src="{{ asset('plugins/flora/font_size.min.js') }}"></script>
    <script type="text/javascript" src="{{ asset('plugins/flora/font_family.min.js') }}"></script>
    <script type="text/javascript" src="{{ asset('plugins/flora/fullscreen.min.js') }}"></script>
    <script type="text/javascript" src="{{ asset('plugins/flora/image.min.js') }}"></script>
    <script type="text/javascript" src="{{ asset('plugins/flora/image_manager.min.js') }}"></script>
    <script type="text/javascript" src="{{ asset('plugins/flora/line_breaker.min.js') }}"></script>
    <script type="text/javascript" src="{{ asset('plugins/flora/inline_style.min.js') }}"></script>
    <script type="text/javascript" src="{{ asset('plugins/flora/link.min.js') }}"></script>
    <script type="text/javascript" src="{{ asset('plugins/flora/lists.min.js') }}"></script>
    <script type="text/javascript" src="{{ asset('plugins/flora/paragraph_format.min.js') }}"></script>
    <script type="text/javascript" src="{{ asset('plugins/flora/paragraph_style.min.js') }}"></script>
    <script type="text/javascript" src="{{ asset('plugins/flora/quick_insert.min.js') }}"></script>
    <script type="text/javascript" src="{{ asset('plugins/flora/quote.min.js') }}"></script>
    <script type="text/javascript" src="{{ asset('plugins/flora/table.min.js') }}"></script>
    <script type="text/javascript" src="{{ asset('plugins/flora/save.min.js') }}"></script>
    <script type="text/javascript" src="{{ asset('plugins/flora/url.min.js') }}"></script>
    <script type="text/javascript" src="{{ asset('plugins/flora/video.min.js') }}"></script>
    <script type="text/javascript" src="{{ asset('plugins/flora/help.min.js') }}"></script>
    <script type="text/javascript" src="{{ asset('plugins/flora/print.min.js') }}"></script>
    <script type="text/javascript" src="{{ asset('plugins/flora/third_party/spell_checker.min.js') }}"></script>
    <script type="text/javascript" src="{{ asset('plugins/flora/special_characters.min.js') }}"></script>
    <script type="text/javascript" src="{{ asset('plugins/flora/word_paste.min.js') }}"></script>

    <script>
        function setImagePreview(event) {
            const reader = new FileReader();
            reader.onload = function() {
                const output = document.getElementById('image_preview');
                output.src = reader.result;
            }
            reader.readAsDataURL(event.target.files[0]);
        }

        function generateSlug() {
            const heading = document.getElementById('blog_title').value;
            let slug = heading.toLowerCase()
                .replace(/[^a-z0-9\s-]/g, '')
                .trim()
                .replace(/\s+/g, '-');

            fetch(`{{ route('check-slug') }}/${slug}`)
                .then(response => {
                    if (!response.ok) {
                        throw new Error('Network response was not ok');
                    }
                    return response.json();
                })
                .then(data => {
                    const unique = data.unique;
                    if (unique > 0) {
                        slug += unique;
                    }
                    document.getElementById('blog_slug').value = slug;
                })
                .catch(error => {
                    console.error('Error checking slug uniqueness:', error);
                });
        }

        document.addEventListener('DOMContentLoaded', function () {
            new FroalaEditor('#blog_summary', {
                attribution: false,
                imageUploadURL: '{{ route('froala.upload_image') }}',
                imageUploadParams: {
                    _token: '{{ csrf_token() }}'
                },
                imageManagerLoadURL: '{{ route('froala.load_images') }}',
                imageManagerDeleteURL: '{{ route('froala.delete_image') }}',
                imageManagerDeleteMethod: 'POST',
                imageManagerLoadParams: {
                    _token: '{{ csrf_token() }}'
                },
                imageManagerDeleteParams: {
                    _token: '{{ csrf_token() }}'
                },
                videoUploadURL: '{{ route('froala.upload_video') }}',
                videoUploadParams: {
                    _token: '{{ csrf_token() }}'
                },
                pluginsEnabled: [
                    'image', 'imageManager', 'video', 'align', 'charCounter', 'codeBeautifier',
                    'codeView', 'colors', 'draggable', 'emoticons', 'entities', 'file', 'fontFamily', 'fontSize',
                    'fullscreen', 'inlineStyle', 'lineBreaker', 'link', 'lists', 'paragraphFormat', 'paragraphStyle',
                    'quickInsert', 'quote', 'table', 'url', 'wordPaste'
                ],
                fontSize: ['8', '10', '12', '14', '16', '18', '20', '22', '24', '26', '28', '30', '32', '36', '40', '44', '48', '54', '60', '72', '96'],
                events: {
                    'imageManager.beforeLoad': function () { },
                    'imageManager.loaded': function (data) { },
                    'imageManager.error': function (error) { }
                }
            });
        });
    </script>
    <script src="https://code.jquery.com/jquery-3.3.1.slim.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.14.7/umd/popper.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/js/bootstrap.min.js"></script>
@endsection
