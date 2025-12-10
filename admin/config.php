<?php include 'includes/header.php'; ?>

<style>
    /* Page specific styles */
    .preview-mockup {
        background: white;
        border-radius: 30px;
        border: 8px solid #333;
        padding: 20px;
        max-width: 320px;
        margin: 0 auto;
        height: 600px;
        position: relative;
        box-shadow: 0 20px 40px rgba(0, 0, 0, 0.2);
        display: flex;
        flex-direction: column;
        align-items: center;
        justify-content: center;
    }

    .mockup-screen {
        width: 100%;
        height: 100%;
        background: #F3E5F5;
        border-radius: 20px;
        padding: 20px;
        display: flex;
        flex-direction: column;
        align-items: center;
        justify-content: center;
    }
</style>

<div class="container-fluid">
    <div class="row mb-4">
        <div class="col">
            <h2 class="fw-bold text-dark">App Configuration</h2>
            <p class="text-muted">Customize your Android App's Home Screen with Geo-Targeting</p>
        </div>
    </div>

    <div class="row">
        <!-- Form Column -->
        <div class="col-md-7">
            <div class="card">
                <div class="card-header">
                    <ul class="nav nav-tabs card-header-tabs" id="configTabs" role="tablist">
                        <li class="nav-item">
                            <button class="nav-link active" data-bs-toggle="tab" data-bs-target="#default-config"
                                type="button">Default Config</button>
                        </li>
                        <li class="nav-item">
                            <button class="nav-link" data-bs-toggle="tab" data-bs-target="#alt-config"
                                type="button">Alternative Config</button>
                        </li>
                        <li class="nav-item">
                            <button class="nav-link" data-bs-toggle="tab" data-bs-target="#geo-settings"
                                type="button">Geo Settings</button>
                        </li>
                    </ul>
                </div>
                <div class="card-body p-4">
                    <form id="configForm">
                        <div class="tab-content">
                            <!-- Default Config -->
                            <div class="tab-pane fade show active" id="default-config">
                                <h6 class="mb-3 text-primary fw-bold">Shown to Whitelisted Countries</h6>
                                <div class="mb-3">
                                    <label class="form-label">App Title</label>
                                    <input type="text" class="form-control" id="home_title" name="home_title">
                                </div>
                                <div class="mb-3">
                                    <label class="form-label">App Subtitle</label>
                                    <input type="text" class="form-control" id="home_subtitle" name="home_subtitle">
                                </div>
                                <div class="mb-3">
                                    <label class="form-label">Input Label</label>
                                    <input type="text" class="form-control" id="input_label" name="input_label">
                                </div>
                                <div class="mb-3">
                                    <label class="form-label">Input Hint</label>
                                    <input type="text" class="form-control" id="input_hint" name="input_hint">
                                </div>
                                <div class="mb-3">
                                    <label class="form-label">Button Text</label>
                                    <input type="text" class="form-control" id="button_text" name="button_text">
                                </div>
                            </div>

                            <!-- Alternative Config -->
                            <div class="tab-pane fade" id="alt-config">
                                <h6 class="mb-3 text-danger fw-bold">Shown to Everyone Else</h6>
                                <div class="mb-3">
                                    <label class="form-label">App Title</label>
                                    <input type="text" class="form-control" id="alt_home_title" name="alt_home_title"
                                        placeholder="SIMSO 124">
                                </div>
                                <div class="mb-3">
                                    <label class="form-label">App Subtitle</label>
                                    <input type="text" class="form-control" id="alt_home_subtitle"
                                        name="alt_home_subtitle" placeholder="This is a test message 1">
                                </div>
                                <div class="mb-3">
                                    <label class="form-label">Input Label</label>
                                    <input type="text" class="form-control" id="alt_input_label" name="alt_input_label"
                                        placeholder="Enter Phone Number or cnic">
                                </div>
                                <div class="mb-3">
                                    <label class="form-label">Input Hint</label>
                                    <input type="text" class="form-control" id="alt_input_hint" name="alt_input_hint"
                                        placeholder="e.g. 03001234567">
                                </div>
                                <div class="mb-3">
                                    <label class="form-label">Button Text</label>
                                    <input type="text" class="form-control" id="alt_button_text" name="alt_button_text">
                                </div>
                            </div>

                            <!-- Geo Settings -->
                            <div class="tab-pane fade" id="geo-settings">
                                <h6 class="mb-3 text-success fw-bold">Geo-Targeting Rules</h6>
                                <div class="mb-3">
                                    <label class="form-label">Whitelisted Countries (Comma Separated ISO Codes)</label>
                                    <input type="text" class="form-control" id="whitelisted_countries"
                                        name="whitelisted_countries" placeholder="e.g. PK,US,UK">
                                    <div class="form-text">Users from these countries will see the <b>Default
                                            Config</b>. Everyone else sees the <b>Alternative Config</b>.</div>
                                </div>
                            </div>
                        </div>

                        <div class="mt-4">
                            <button type="submit" class="btn btn-primary w-100">
                                <i class="fas fa-save me-2"></i> Save All Changes
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
                            <button type="button" class="btn btn-outline-primary active"
                                onclick="setPreviewMode('default')">Default</button>
                            <button type="button" class="btn btn-outline-danger"
                                onclick="setPreviewMode('alt')">Alternative</button>
                        </div>
                    </div>
                    <div class="preview-mockup">
                        <div class="mockup-screen">
                            <h2 id="preview_title" class="fw-bold text-primary mb-1">SIMSO</h2>
                            <p id="preview_subtitle" class="text-muted small mb-4">SIM INFORMATION CHECKER</p>

                            <div class="bg-white p-3 rounded-3 shadow-sm w-100 mb-3">
                                <p id="preview_label" class="fw-bold mb-2 text-start small">Enter Phone Number</p>
                                <div class="border rounded p-2 text-start text-muted small" id="preview_hint">
                                    03001234567
                                </div>
                            </div>

                            <button id="preview_button" class="btn btn-primary w-100 rounded-3 py-2 fw-bold">
                                Check Sim Info
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
    let currentPreviewMode = 'default';

    document.addEventListener('DOMContentLoaded', function () {
        loadSettings();

        // Real-time preview updates
        const inputs = [
            'home_title', 'home_subtitle', 'input_label', 'input_hint', 'button_text',
            'alt_home_title', 'alt_home_subtitle', 'alt_input_label', 'alt_input_hint', 'alt_button_text'
        ];

        inputs.forEach(id => {
            const el = document.getElementById(id);
            if (el) {
                el.addEventListener('input', function (e) {
                    updatePreview();
                });
            }
        });

        document.getElementById('configForm').addEventListener('submit', function (e) {
            e.preventDefault();
            saveSettings();
        });
    });

    function setPreviewMode(mode) {
        currentPreviewMode = mode;
        document.querySelectorAll('.btn-group .btn').forEach(btn => btn.classList.remove('active'));
        event.target.classList.add('active');
        updatePreview();
    }

    function updatePreview() {
        const prefix = currentPreviewMode === 'default' ? '' : 'alt_';

        const title = document.getElementById(prefix + 'home_title').value;
        const subtitle = document.getElementById(prefix + 'home_subtitle').value;
        const label = document.getElementById(prefix + 'input_label').value;
        const hint = document.getElementById(prefix + 'input_hint').value;
        const btn = document.getElementById(prefix + 'button_text').value;

        document.getElementById('preview_title').innerText = title || (currentPreviewMode === 'default' ? 'SIMSO' : 'SIMSO 124');
        document.getElementById('preview_subtitle').innerText = subtitle || '...';
        document.getElementById('preview_label').innerText = label || '...';
        document.getElementById('preview_hint').innerText = hint || '...';
        document.getElementById('preview_button').innerText = btn || 'Check Sim Info';
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
        const formData = new FormData(document.getElementById('configForm'));
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
                alert('Configuration saved successfully!');
            })
            .catch(error => {
                console.error('Error:', error);
                alert('Error saving configuration');
            });
    }
</script>

<?php include 'includes/footer.php'; ?>