<?php include 'includes/header.php'; ?>

<style>
    .banner-preview {
        width: 100%;
        max-width: 600px;
        border: 2px dashed #ddd;
        border-radius: 10px;
        padding: 20px;
        margin: 20px auto;
        text-align: center;
        background: #f9f9f9;
    }

    .banner-preview img {
        max-width: 100%;
        height: auto;
        border-radius: 8px;
        box-shadow: 0 4px 10px rgba(0, 0, 0, 0.1);
    }

    .banner-preview.empty {
        min-height: 200px;
        display: flex;
        align-items: center;
        justify-content: center;
        color: #999;
    }

    .upload-btn {
        position: relative;
        overflow: hidden;
        display: inline-block;
    }

    .upload-btn input[type=file] {
        position: absolute;
        left: 0;
        top: 0;
        opacity: 0;
        cursor: pointer;
        width: 100%;
        height: 100%;
    }
</style>

<div class="container-fluid">
    <div class="row mb-4">
        <div class="col">
            <h2 class="fw-bold text-dark">Banner Management</h2>
            <p class="text-muted">Upload banners with geo-targeting support</p>
        </div>
    </div>

    <div class="row">
        <!-- Whitelisted Banner -->
        <div class="col-md-6">
            <div class="card">
                <div class="card-header bg-warning bg-opacity-10">
                    <h5 class="mb-0 text-warning">
                        <i class="fas fa-globe-asia me-2"></i>Whitelisted Countries Banner
                    </h5>
                </div>
                <div class="card-body">
                    <form id="whitelistBannerForm" enctype="multipart/form-data">
                        <div class="mb-3">
                            <label class="form-label fw-bold">Banner Image</label>
                            <div class="banner-preview" id="whitelist_preview">
                                <div class="empty">No banner uploaded</div>
                            </div>
                            <div class="upload-btn">
                                <button type="button" class="btn btn-primary">
                                    <i class="fas fa-upload me-2"></i>Choose Image
                                </button>
                                <input type="file" id="whitelist_image" name="whitelist_image" accept="image/*"
                                    onchange="previewImage(this, 'whitelist')">
                            </div>
                            <div class="form-text">Recommended: 1080x400px, Max: 2MB</div>
                        </div>

                        <div class="mb-3">
                            <label class="form-label fw-bold">Click URL</label>
                            <input type="url" class="form-control" id="whitelist_banner_url" name="whitelist_banner_url"
                                placeholder="https://example.com/offer">
                            <div class="form-text">Where users will be redirected when they click the banner</div>
                        </div>

                        <div class="mb-3">
                            <label class="form-label fw-bold">Current Banner URL</label>
                            <input type="text" class="form-control" id="whitelist_banner_image_current" readonly>
                        </div>

                        <button type="submit" class="btn btn-warning w-100">
                            <i class="fas fa-save me-2"></i>Save Whitelisted Banner
                        </button>
                    </form>
                </div>
            </div>
        </div>

        <!-- Alternative Banner -->
        <div class="col-md-6">
            <div class="card">
                <div class="card-header bg-danger bg-opacity-10">
                    <h5 class="mb-0 text-danger">
                        <i class="fas fa-globe me-2"></i>Non-Whitelisted Countries Banner
                    </h5>
                </div>
                <div class="card-body">
                    <form id="altBannerForm" enctype="multipart/form-data">
                        <div class="mb-3">
                            <label class="form-label fw-bold">Banner Image</label>
                            <div class="banner-preview" id="alt_preview">
                                <div class="empty">No banner uploaded</div>
                            </div>
                            <div class="upload-btn">
                                <button type="button" class="btn btn-primary">
                                    <i class="fas fa-upload me-2"></i>Choose Image
                                </button>
                                <input type="file" id="alt_image" name="alt_image" accept="image/*"
                                    onchange="previewImage(this, 'alt')">
                            </div>
                            <div class="form-text">Recommended: 1080x400px, Max: 2MB</div>
                        </div>

                        <div class="mb-3">
                            <label class="form-label fw-bold">Click URL</label>
                            <input type="url" class="form-control" id="alt_banner_url" name="alt_banner_url"
                                placeholder="https://example.com/offer">
                            <div class="form-text">Where users will be redirected when they click the banner</div>
                        </div>

                        <div class="mb-3">
                            <label class="form-label fw-bold">Current Banner URL</label>
                            <input type="text" class="form-control" id="alt_banner_image_current" readonly>
                        </div>

                        <button type="submit" class="btn btn-danger w-100">
                            <i class="fas fa-save me-2"></i>Save Alternative Banner
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
    // Preview image before upload
    function previewImage(input, type) {
        if (input.files && input.files[0]) {
            const reader = new FileReader();
            reader.onload = function (e) {
                const preview = document.getElementById(type + '_preview');
                preview.innerHTML = `<img src="${e.target.result}" alt="Banner Preview">`;
            }
            reader.readAsDataURL(input.files[0]);
        }
    }

    // Load existing banners
    function loadBanners() {
        fetch('../api/admin/get_settings.php')
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    data.data.forEach(setting => {
                        // Whitelisted banner
                        if (setting.setting_key === 'whitelist_banner_url') {
                            document.getElementById('whitelist_banner_url').value = setting.setting_value;
                        }
                        if (setting.setting_key === 'whitelist_banner_image') {
                            document.getElementById('whitelist_banner_image_current').value = setting.setting_value;
                            if (setting.setting_value) {
                                document.getElementById('whitelist_preview').innerHTML =
                                    `<img src="../${setting.setting_value}" alt="Whitelisted Banner">`;
                            }
                        }

                        // Alternative banner
                        if (setting.setting_key === 'alt_banner_url') {
                            document.getElementById('alt_banner_url').value = setting.setting_value;
                        }
                        if (setting.setting_key === 'alt_banner_image') {
                            document.getElementById('alt_banner_image_current').value = setting.setting_value;
                            if (setting.setting_value) {
                                document.getElementById('alt_preview').innerHTML =
                                    `<img src="../${setting.setting_value}" alt="Alternative Banner">`;
                            }
                        }
                    });
                }
            });
    }

    // Save whitelisted banner
    document.getElementById('whitelistBannerForm').addEventListener('submit', function (e) {
        e.preventDefault();
        saveBanner('whitelist');
    });

    // Save alternative banner
    document.getElementById('altBannerForm').addEventListener('submit', function (e) {
        e.preventDefault();
        saveBanner('alt');
    });

    function saveBanner(type) {
        const formData = new FormData();
        const imageInput = document.getElementById(type + '_image');
        const urlInput = document.getElementById(type + '_banner_url');

        // Add image if selected
        if (imageInput.files.length > 0) {
            formData.append('banner_image', imageInput.files[0]);
        }

        // Add URL
        formData.append('banner_url', urlInput.value);
        formData.append('banner_type', type);

        // Show loading
        const button = event.target.querySelector('button[type=submit]');
        const originalText = button.innerHTML;
        button.innerHTML = '<i class="fas fa-spinner fa-spin me-2"></i>Uploading...';
        button.disabled = true;

        fetch('../api/admin/upload_banner.php', {
            method: 'POST',
            body: formData
        })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    alert('Banner saved successfully!');
                    loadBanners();
                } else {
                    alert('Error: ' + data.message);
                }
            })
            .catch(error => {
                alert('Upload failed: ' + error);
            })
            .finally(() => {
                button.innerHTML = originalText;
                button.disabled = false;
            });
    }

    // Load banners on page load
    document.addEventListener('DOMContentLoaded', loadBanners);
</script>

<?php include 'includes/footer.php'; ?>