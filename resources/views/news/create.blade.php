@extends('layouts.app')

@section('title', 'Create News')

@section('content')
    <h1>Create News</h1>

    <div class="card">
        <form method="POST" action="{{ route('news.store') }}" enctype="multipart/form-data" id="newsForm">
            @csrf

            <div class="form-group">
                <label for="title">Title *</label>
                <input type="text" id="title" name="title" value="{{ old('title') }}" required>
            </div>

            <div class="form-group">
                <label for="excerpt">Excerpt (Preview Text for Feed)</label>
                <textarea id="excerpt" name="excerpt" rows="3" maxlength="500" placeholder="Write a compelling preview text that will appear on the news feed to entice users to click and read the full article...">{{ old('excerpt') }}</textarea>
                <div style="font-size: 0.85rem; color: var(--gray); margin-top: 0.25rem;">
                    <span id="excerpt-count">0</span> / 500 characters
                </div>
            </div>

            <div class="form-group">
                <label for="image">Image</label>
                <input type="file" id="imageInput" accept="image/*">
                <input type="hidden" id="image" name="image" value="">
                
                <div id="imageEditor" style="display: none; margin-top: 1rem;">
                    <div style="margin-bottom: 1rem;">
                        <button type="button" id="cropBtn" class="btn btn-secondary" style="margin-right: 0.5rem;">Crop</button>
                        <button type="button" id="resetBtn" class="btn btn-secondary" style="margin-right: 0.5rem;">Reset</button>
                        <button type="button" id="removeImageBtn" class="btn btn-secondary">Remove Image</button>
                    </div>
                    <div style="max-width: 100%; overflow: hidden;">
                        <img id="imagePreview" style="max-width: 100%; display: block;">
                    </div>
                    <div style="margin-top: 1rem;">
                        <label>Width: <input type="number" id="widthInput" value="800" min="100" max="2000" style="width: 100px; margin-left: 0.5rem;"></label>
                        <label style="margin-left: 1rem;">Height: <input type="number" id="heightInput" value="600" min="100" max="2000" style="width: 100px; margin-left: 0.5rem;"></label>
                        <button type="button" id="resizeBtn" class="btn btn-secondary" style="margin-left: 1rem;">Resize</button>
                    </div>
                </div>
            </div>

            <div class="form-group">
                <label for="content">Content *</label>
                <textarea id="content" name="content" required style="min-height: 200px;">{{ old('content') }}</textarea>
            </div>

            <div class="form-group">
                <label for="publication_date">Publication Date</label>
                <input type="datetime-local" id="publication_date" name="publication_date" value="{{ old('publication_date') }}">
            </div>

            <div>
                <button type="submit" class="btn btn-primary">Create News</button>
                <a href="{{ route('news.index') }}" class="btn btn-secondary">Cancel</a>
            </div>
        </form>
    </div>

    <!-- Cropper.js CSS -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/cropperjs/1.5.13/cropper.min.css">
    
    <!-- Cropper.js JS -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/cropperjs/1.5.13/cropper.min.js"></script>
    
    <script>
        let cropper;
        let originalImageData = null;
        
        const imageInput = document.getElementById('imageInput');
        const imagePreview = document.getElementById('imagePreview');
        const imageEditor = document.getElementById('imageEditor');
        const imageHidden = document.getElementById('image');
        const cropBtn = document.getElementById('cropBtn');
        const resetBtn = document.getElementById('resetBtn');
        const removeImageBtn = document.getElementById('removeImageBtn');
        const widthInput = document.getElementById('widthInput');
        const heightInput = document.getElementById('heightInput');
        const resizeBtn = document.getElementById('resizeBtn');
        const newsForm = document.getElementById('newsForm');
        
        imageInput.addEventListener('change', function(e) {
            const file = e.target.files[0];
            if (file) {
                const reader = new FileReader();
                reader.onload = function(e) {
                    imagePreview.src = e.target.result;
                    originalImageData = e.target.result;
                    imageEditor.style.display = 'block';
                    
                    // Initialize cropper
                    if (cropper) {
                        cropper.destroy();
                    }
                    cropper = new Cropper(imagePreview, {
                        aspectRatio: NaN, // Free aspect ratio
                        viewMode: 1,
                        autoCropArea: 0.8,
                    });
                };
                reader.readAsDataURL(file);
            }
        });
        
        cropBtn.addEventListener('click', function() {
            if (cropper) {
                const canvas = cropper.getCroppedCanvas({
                    width: parseInt(widthInput.value) || 800,
                    height: parseInt(heightInput.value) || 600,
                });
                const croppedDataUrl = canvas.toDataURL('image/jpeg', 0.9);
                imagePreview.src = croppedDataUrl;
                originalImageData = croppedDataUrl;
                cropper.destroy();
                cropper = null;
            }
        });
        
        resetBtn.addEventListener('click', function() {
            if (originalImageData) {
                imagePreview.src = originalImageData;
                if (cropper) {
                    cropper.destroy();
                }
                cropper = new Cropper(imagePreview, {
                    aspectRatio: NaN,
                    viewMode: 1,
                    autoCropArea: 0.8,
                });
            }
        });
        
        removeImageBtn.addEventListener('click', function() {
            imageInput.value = '';
            imageEditor.style.display = 'none';
            imageHidden.value = '';
            if (cropper) {
                cropper.destroy();
                cropper = null;
            }
        });
        
        resizeBtn.addEventListener('click', function() {
            if (imagePreview.src && !cropper) {
                const img = new Image();
                img.onload = function() {
                    const canvas = document.createElement('canvas');
                    const ctx = canvas.getContext('2d');
                    const width = parseInt(widthInput.value) || 800;
                    const height = parseInt(heightInput.value) || 600;
                    
                    canvas.width = width;
                    canvas.height = height;
                    ctx.drawImage(img, 0, 0, width, height);
                    
                    const resizedDataUrl = canvas.toDataURL('image/jpeg', 0.9);
                    imagePreview.src = resizedDataUrl;
                    originalImageData = resizedDataUrl;
                };
                img.src = imagePreview.src;
            }
        });
        
        // Convert image to blob before form submission
        newsForm.addEventListener('submit', function(e) {
            if (imagePreview.src && imagePreview.src.startsWith('data:')) {
                e.preventDefault();
                
                // Convert data URL to blob
                fetch(imagePreview.src)
                    .then(res => res.blob())
                    .then(blob => {
                        const formData = new FormData(newsForm);
                        formData.delete('image');
                        formData.append('image', blob, 'news-image.jpg');
                        
                        // Get CSRF token
                        const csrfToken = document.querySelector('input[name="_token"]').value;
                        
                        // Submit form with blob using XMLHttpRequest for better redirect handling
                        const xhr = new XMLHttpRequest();
                        xhr.open('POST', newsForm.action, true);
                        xhr.setRequestHeader('X-Requested-With', 'XMLHttpRequest');
                        xhr.setRequestHeader('X-CSRF-TOKEN', csrfToken);
                        
                        xhr.onload = function() {
                            if (xhr.status >= 200 && xhr.status < 300) {
                                // Check if response is a redirect
                                const responseUrl = xhr.responseURL || xhr.getResponseHeader('Location');
                                if (responseUrl && responseUrl !== newsForm.action) {
                                    window.location.href = responseUrl;
                                } else {
                                    // Parse response to check for redirect in HTML
                                    const parser = new DOMParser();
                                    const doc = parser.parseFromString(xhr.responseText, 'text/html');
                                    const metaRefresh = doc.querySelector('meta[http-equiv="refresh"]');
                                    if (metaRefresh) {
                                        const content = metaRefresh.getAttribute('content');
                                        const urlMatch = content.match(/url=(.+)/i);
                                        if (urlMatch) {
                                            window.location.href = urlMatch[1];
                                            return;
                                        }
                                    }
                                    // If no redirect found, reload the page
                                    window.location.href = '{{ route("news.index") }}';
                                }
                            } else {
                                // Show error page
                                document.open();
                                document.write(xhr.responseText);
                                document.close();
                            }
                        };
                        
                        xhr.onerror = function() {
                            alert('An error occurred while submitting the form. Please try again.');
                        };
                        
                        xhr.send(formData);
                        .catch(error => {
                            console.error('Error:', error);
                            alert('An error occurred while submitting the form. Please try again.');
                        });
                    });
            }
        });

        // Character counter for excerpt
        const excerptTextarea = document.getElementById('excerpt');
        const excerptCount = document.getElementById('excerpt-count');
        if (excerptTextarea && excerptCount) {
            excerptTextarea.addEventListener('input', function() {
                excerptCount.textContent = this.value.length;
            });
        }
    </script>
    
    <style>
        #imageEditor {
            border: 2px dashed var(--gray);
            padding: 1rem;
            border-radius: 8px;
            background-color: var(--gray-light);
        }
        #imagePreview {
            max-height: 500px;
        }
    </style>
@endsection
