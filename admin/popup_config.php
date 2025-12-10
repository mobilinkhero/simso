<?php include 'includes/header.php'; ?>

<style>
    /* Page specific styles */
    .preview-dialog {
        background: white;
        border-radius: 20px;
        padding: 30px;
        max-width: 350px;
        margin: 0 auto;
        box-shadow: 0 10px 40px rgba(0, 0, 0, 0.2);
        text-align: center;
    }

    .preview-dialog .dialog-icon {
        width: 60px;
        height: 60px;
        background: #f44336;
        border-radius: 50%;
        margin: 0 auto 20px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 30px;
        color: white;
    }

    .preview-dialog.whitelist .dialog-icon {
        background: #ff9800;
    }

    .preview-dialog h4 {
        font-weight: bold;
        margin-bottom: 15px;
        color: #333;
    }

    .preview-dialog p {
        color: #666;
        margin-bottom: 20px;
        line-height: 1.6;
    }

    .preview-dialog .action-btn {
        background: #6B4CE6;
        color: white;
        border: none;
        padding: 12px 30px;
        border-radius: 10px;
        font-weight: bold;
        cursor: pointer;
        margin-top: 10px;
    }
</style>

<div class="container-fluid">
    <div class="row mb-4">
        <div class="col">
            <h2 class="fw-bold text-dark">Popup Configuration</h2>
            <p class="text-muted">Customize error dialogs with Geo-Targeting</p>
        </div>
    </div>

    <div class="row">
        <!-- Form Column -->
        <div class="col-md-7">
            <div class="card">
                <div class="card-header">
                    <ul class="nav nav-tabs card-header-tabs" id="popupTabs" role="tablist">
                        <li class="nav-item">
                            <button class="nav-link active" data-bs-toggle="tab" data-bs-target="#whitelist-popup"
                                type="button">Whitelisted Error</button>
                        </li>
                        <li class="nav-item">
                            <button class="nav-link" data-bs-toggle="tab" data-bs-target="#alt-popup"
                                type="button">Alternative Error</button>
                        </li>
                    </ul>
                </div>
                <div class="card-body p-4">
                    <form id="popupForm">
                        <div class="tab-content">
                            <!-- Whitelisted Error Popup -->
                            <div class="tab-pane fade show active" id="whitelist-popup">
                                <h6 class="mb-3 text-warning fw-bold">Shown to Whitelisted Countries</h6>
                                <div class="mb-3">
                                    <label class="form-label">Error Title</label>
                                    <input type="text" class="form-control" id="whitelist_error_title"
                                        name="whitelist_error_title" placeholder="e.g. Record Not Found">
                                </div>
                                <div class="mb-3">
                                    <label class="form-label">Error Message</label>
                                    <textarea class="form-control" id="whitelist_error_msg" name="whitelist_error_msg"
                                        rows="3" placeholder="Enter the error message to display..."></textarea>
                                </div>
                                <div class="mb-3">
                                    <label class="form-label">Action Button Text (Optional)</label>
                                    <input type="text" class="form-control" id="whitelist_action_text"
                                        name="whitelist_action_text" placeholder="e.g. Contact Support">
                                    <div class="form-text">Leave empty to hide button</div>
                                </div>
                                <div class="mb-3">
                                    <label class="form-label">Action Button URL (Optional)</label>
                                    <input type="url" class="form-control" id="whitelist_action_url"
                                        name="whitelist_action_url" placeholder="https://example.com/support">
                                </div>
                            </div>

                            <!-- Alternative Error Popup -->
                            <div class="tab-pane fade" id="alt-popup">
                                <h6 class="mb-3 text-danger fw-bold">Shown to Everyone Else</h6>
                                <div class="mb-3">
                                    <label class="form-label">Error Title</label>
                                    <input type="text" class="form-control" id="alt_error_title" name="alt_error_title"
                                        placeholder="e.g. No Network Data">
                                </div>
                                <div class="mb-3">
                                    <label class="form-label">Error Message</label>
                                    <textarea class="form-control" id="alt_error_msg" name="alt_error_msg" rows="3"
                                        placeholder="Enter the error message to display..."></textarea>
                                </div>
                            </div>
                        </div>

                        <div class="mt-4">
                            <button type="submit" class="btn btn-primary w-100">
                                <i class="fas fa-save me-2"></i> Save Popup Configuration
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <!-- Preview Column -->
        <div class="col-md-5">
            <div class="card bg-transparent shadow-none">
                <div class="card-body text-center">
                    <h5 class="mb-4">Live Preview</h5>
                    <div class="d-flex justify-content-center mb-3">
                        <div class="btn-group" role="group">
                            <button type="button" class="btn btn-outline-warning active"
                                onclick="setPreviewMode('whitelist')">Whitelisted</button>
                            <button type="button" class="btn btn-outline-danger"
                                onclick="setPreviewMode('alt')">Alternative</button>
                        </div>
                    </div>

                    <!-- Whitelisted Preview -->
                    <div class="preview-dialog whitelist" id="preview-whitelist">
                        <div class="dialog-icon">
                            <i class="fas fa-exclamation-triangle"></i>
                        </div>
                        <h4 id="preview_whitelist_title">Record Not Found</h4>
                        <p id="preview_whitelist_msg">We could not find any details for this number.</p>
                        <button class="action-btn" id="preview_whitelist_btn" style="display: block; margin: 0 auto;">
                            Contact Support
                        </button>
                    </div>

                    <!-- Alternative Preview -->
                    <div class="preview-dialog" id="preview-alt" style="display: none;">
                        <div class="dialog-icon">
                            <i class="fas fa-times-circle"></i>
                        </div>
                        <h4 id="preview_alt_title">No Network Data</h4>
                        <p id="preview_alt_msg">Network information is currently unavailable for this number.</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
    let currentPreviewMode = 'whitelist';

    document.addEventListener('DOMContentLoaded', function () {
        loadSettings();

        // Real-time preview updates
        const inputs = [
            'whitelist_error_title', 'whitelist_error_msg', 'whitelist_action_text', 'whitelist_action_url',
            'alt_error_title', 'alt_error_msg'
        ];

        inputs.forEach(id => {
            const el = document.getElementById(id);
            if (el) {
                el.addEventListener('input', function (e) {
                    updatePreview();
                });
            }
        });

        document.getElementById('popupForm').addEventListener('submit', function (e) {
            e.preventDefault();
            saveSettings();
        });
    });

    function setPreviewMode(mode) {
        currentPreviewMode = mode;
        document.querySelectorAll('.btn-group .btn').forEach(btn => btn.classList.remove('active'));
        event.target.classList.add('active');

        if (mode === 'whitelist') {
            document.getElementById('preview-whitelist').style.display = 'block';
            document.getElementById('preview-alt').style.display = 'none';
        } else {
            document.getElementById('preview-whitelist').style.display = 'none';
            document.getElementById('preview-alt').style.display = 'block';
        }
    }

    function updatePreview() {
        // Update Whitelisted Preview
        const whitelistTitle = document.getElementById('whitelist_error_title').value;
        const whitelistMsg = document.getElementById('whitelist_error_msg').value;
        const whitelistBtnText = document.getElementById('whitelist_action_text').value;

        document.getElementById('preview_whitelist_title').innerText = whitelistTitle || 'Record Not Found';
        document.getElementById('preview_whitelist_msg').innerText = whitelistMsg || 'We could not find any details for this number.';

        const btnElement = document.getElementById('preview_whitelist_btn');
        if (whitelistBtnText) {
            btnElement.innerText = whitelistBtnText;
            btnElement.style.display = 'block';
        } else {
            btnElement.style.display = 'none';
        }

        // Update Alternative Preview
        const altTitle = document.getElementById('alt_error_title').value;
        const altMsg = document.getElementById('alt_error_msg').value;

        document.getElementById('preview_alt_title').innerText = altTitle || 'No Network Data';
        document.getElementById('preview_alt_msg').innerText = altMsg || 'Network information is currently unavailable for this number.';
    }

    function loadSettings() {
        fetch('../api/admin/get_settings.php')
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    data.data.forEach(setting => {
                        const input = document.getElementById(setting.setting_key);
                        if (input) {
                            input.value = setting.setting_value;
                        }
                    });
                    updatePreview();
                }
            });
    }

    function saveSettings() {
        const formData = new FormData(document.getElementById('popupForm'));
        const promises = [];

        for (let [key, value] of formData.entries()) {
            promises.push(fetch('../api/admin/save_settings.php', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                },
                body: JSON.stringify({
                    setting_key: key,
                    setting_value: value
                })
            }));
        }

        Promise.all(promises)
            .then(() => {
                alert('Popup configuration saved successfully!');
            })
            .catch(error => {
                console.error('Error:', error);
                alert('Error saving configuration');
            });
    }
</script>

<?php include 'includes/footer.php'; ?>