@extends('layouts.app')
@section('title', $page_heading)
@section('header-css')
    <link rel="stylesheet" href="{{ asset('plugins/select2/css/select2.min.css') }}">
    <link rel="stylesheet" href="{{ asset('plugins/summernote/summernote-lite.min.css') }}">
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
                        <h3 class="card-title">Listing Meta Details</h3>
                    </div>
                    <form action="{{ route('venue.listing_meta.manage_process', $meta->id) }}" method="post">
                        <div class="modal-body text-sm">
                            @csrf
                            <div class="row">
                                <div class="col-sm-4">
                                    <div class="form-group">
                                        <label>Category <span class="text-danger">*</span></label>
                                        <select class="form-control select2" name="category" required>
                                            <option value="" disabled selected>Select Category</option>
                                            @foreach ($categories as $list)
                                                <option value="{{ $list->id }}"
                                                    {{ $meta->category_id == $list->id ? 'selected' : '' }}>
                                                    {{ $list->name }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>
                                <div class="col-sm-4">
                                    <div class="form-group">
                                        <label>City <span class="text-danger">*</span></label>
                                        <select class="form-control select2" name="city"
                                            onchange="fetch_locations(this.value)" required>
                                            <option value="" disabled selected>Select City</option>
                                            @foreach ($cities as $list)
                                                <option value="{{ $list->id }}"
                                                    {{ $meta->city_id == $list->id ? 'selected' : '' }}>{{ $list->name }}
                                                </option>
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
                                                <option value="{{ $list->id }}"
                                                    {{ $meta->location_id == $list->id ? 'selected' : '' }}>
                                                    {{ $list->name }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>
                                <div class="col-sm-4">
                                    <div class="form-group">
                                        <label for="title_text">Meta Title <span class="text-danger">*</span></label>
                                        <textarea class="form-control" placeholder="Enter meta title" name="meta_title" id="meta_title_count" rows="3"
                                            required>{{ $meta->meta_title }}</textarea>
                                        <div id="charCountmeta_title_count">0 Characters</div>

                                    </div>
                                </div>
                                <div class="col-sm-4">
                                    <div class="form-group">
                                        <label for="desc_text">Meta Description</label>
                                        <textarea class="form-control" placeholder="Enter meta description" id="meta_description_count" name="meta_description"
                                            rows="3">{{ $meta->meta_description }}</textarea>
                                        <div id="charCountmeta_description_count">0 Characters</div>

                                    </div>
                                </div>
                                <div class="col-sm-4">
                                    <div class="form-group">
                                        <label for="desc_text">Meta Keywords</label>
                                        <textarea class="form-control" placeholder="Enter meta keywords" name="meta_keywords" rows="3">{{ $meta->meta_keywords }}</textarea>
                                    </div>
                                </div>
                                <div class="col-sm-12">
                                    <div class="form-group">
                                        <label for="desc_text">Header Script</label>
                                        <textarea class="form-control" placeholder="Enter header script" name="header_script" rows="5">{{ $meta->header_script }}</textarea>
                                    </div>
                                </div>
                                <div class="col-sm-12">
                                    <div class="form-group">
                                        <label for="desc_text">Footer <span id="current-tag">None</span></label>
                                        <textarea id="summernote" class="form-control summernote" placeholder="Enter footer caption" name="footer_caption"
                                            rows="3">{{ $meta->caption }}</textarea>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="modal-footer text-sm">
                            <a href="{{ route('venue.listing_meta.ajax_list') }}" class="btn bg-secondary m-1">Back</a>
                            <button type="button" class="btn btn-warning m-1" id="revertButton">Revert</button>
                            <button type="button" class="btn btn-info" id="draftButton">Draft Saved</button>
                            <button type="submit" class="btn text-light m-1"
                                style="background-color: var(--wb-dark-red);">Submit</button>
                        </div>
                    </form>
                </div>
            </div>
        </section>
    </div>
@endsection
@section('footer-script')
    <script src="{{ asset('plugins/select2/js/select2.min.js') }}"></script>
    <script src="{{ asset('plugins/summernote/summernote-lite.min.js') }}"></script>
    <script>
        $(document).ready(function() {
            $('.select2').select2({
                placeholder: 'Select an option'
            });
            $('#summernote').summernote({
                placeholder: 'Type here content',
                tabsize: 2,
                height: 200,
                callbacks: {
                    onKeyup: function() {
                        triggerDraftSave();
                        setTimeout(updateTag, 200);
                    },
                    onMouseUp: function() {
                        setTimeout(updateTag, 200);
                    }
                }
            });

            // --- CAPTURE ORIGINAL FORM DATA ---
            let originalData = getFormData($('form'));

            // --- LOAD DRAFT DATA IF EXISTS ---
            @if ($meta->draft_data)
                try {
                    let draft = JSON.parse(@json($meta->draft_data));
                    for (let key in draft) {
                        let $el = $('[name="' + key + '"]');
                        if ($el.length) {
                            if ($el.attr('type') === 'checkbox' || $el.attr('type') === 'radio') {
                                $el.each(function() {
                                    if ($(this).val() == draft[key]) $(this).prop('checked', true);
                                });
                            } else if ($el.is('select[multiple]')) {
                                $el.val(draft[key]).trigger('change');
                            } else {
                                $el.val(draft[key]);
                            }
                        }
                    }
                    // For summernote
                    if (draft['footer_caption']) {
                        $('#summernote').summernote('code', draft['footer_caption']);
                    }
                } catch (e) {}
            @endif

            // --- AUTO-SAVE ON CHANGE ---
            let autoSaveTimer;
            $('form').on('input change', 'input, select, textarea', function() {
                clearTimeout(autoSaveTimer);
                autoSaveTimer = setTimeout(() => {
                    triggerDraftSave();
                }, 1000);
            });

            function getFormData($form) {
                // Sync Summernote editors into textareas
                $('#summernote').val($('#summernote').summernote('code'));
                const formData = $form.serializeArray();
                let data = {};
                formData.forEach(function(item) {
                    if (data[item.name]) {
                        if (Array.isArray(data[item.name])) {
                            data[item.name].push(item.value);
                        } else {
                            data[item.name] = [data[item.name], item.value];
                        }
                    } else {
                        data[item.name] = item.value;
                    }
                });
                return data;
            }

            function triggerDraftSave() {
                let data = getFormData($('form'));
                $.ajax({
                    url: "{{ route('venue.listing_meta.saveDraft', $meta->id) }}",
                    method: 'POST',
                    data: {
                        _token: '{{ csrf_token() }}',
                        draft_data: JSON.stringify(data)
                    }
                });
            }
            // --- REVERT BUTTON FUNCTION ---
            $('#revertButton').on('click', function() {
                for (let key in originalData) {
                    let $el = $('[name="' + key + '"]');
                    if ($el.length) {
                        if ($el.attr('type') === 'checkbox' || $el.attr('type') === 'radio') {
                            $el.each(function() {
                                if ($(this).val() == originalData[key]) $(this).prop('checked',
                                    true);
                                else $(this).prop('checked', false);
                            });
                        } else if ($el.is('select[multiple]')) {
                            $el.val(originalData[key]).trigger('change');
                        } else {
                            $el.val(originalData[key]);
                        }
                    }
                }
                // For summernote
                if (originalData['summary']) {
                    $('.summernote').summernote('code', originalData['summary']);
                }
                // Remove draft from DB
                $.ajax({
                    url: "{{ route('venue.listing_meta.saveDraft', $meta->id) }}",
                    method: 'POST',
                    data: {
                        _token: '{{ csrf_token() }}',
                        draft_data: ''
                    },
                    success: function() {
                        toastr.success('Form reverted to original values.');
                        $('#draftButton').text('Draft Cleared');
                    }
                });
            });

            function updateTag() {
                var selection = document.getSelection();
                if (selection.rangeCount > 0) {
                    var node = selection.getRangeAt(0).commonAncestorContainer;
                    var tagName = node.nodeType === 3 ? node.parentNode.nodeName : node.nodeName;
                    $('#current-tag').text(tagName);
                }
            }

            window.fetch_locations = function(city_id, selected_id = null) {
                fetch(`{{ route('location.get_locations') }}/${city_id}`)
                    .then(response => response.json())
                    .then(data => {
                        let elem = `<option value="">All</option>`;
                        for (let loc of data.locations) {
                            elem +=
                                `<option value="${loc.id}" ${loc.id == selected_id ? 'selected' : ''}>${loc.name}</option>`;
                        }
                        location_select.innerHTML = elem;
                    });
            }
        });
    </script>
@endsection
