document.addEventListener('DOMContentLoaded', function () {
    loadStats();
    loadApiLogs();
    loadSettings();
});

// Tab switching is handled by Bootstrap data-bs-toggle attributes in HTML
function showTab(tabId) {
    if (tabId === 'api-logs') loadApiLogs();
    if (tabId === 'settings') loadSettings();
}

function loadStats() {
    fetch('../api/admin/stats.php')
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                // We might need to update stats.php to return relevant stats since we removed local SIMs
                // For now, let's just show total requests if available, or keep existing logic
                if (document.getElementById('totalRecords')) document.getElementById('totalRecords').innerText = data.data.total_records;
                if (document.getElementById('todayRequests')) document.getElementById('todayRequests').innerText = data.data.today_requests;
                if (document.getElementById('successRate')) document.getElementById('successRate').innerText = data.data.success_rate + '%';
            }
        });
}

function loadApiLogs() {
    fetch('../api/admin/get_logs.php')
        .then(response => response.json())
        .then(data => {
            const container = document.getElementById('apiLogsTable');
            if (data.success && data.data.length > 0) {
                let html = '<table class="table table-hover"><thead><tr><th>Time</th><th>Phone</th><th>Status</th><th>IP</th><th>Duration</th></tr></thead><tbody>';
                data.data.forEach(log => {
                    let statusClass = log.response_status === 'success' ? 'text-success' : 'text-danger';
                    html += `<tr>
                        <td>${log.created_at}</td>
                        <td>${log.phone_number}</td>
                        <td class="${statusClass} fw-bold">${log.response_status}</td>
                        <td>${log.ip_address}</td>
                        <td>${log.response_time_ms}ms</td>
                    </tr>`;
                });
                html += '</tbody></table>';
                container.innerHTML = html;
            } else {
                container.innerHTML = '<div class="text-center p-4 text-muted">No logs found</div>';
            }
        });
}

function loadSettings() {
    fetch('../api/admin/get_settings.php')
        .then(response => response.json())
        .then(data => {
            const container = document.getElementById('settingsForm');
            if (data.success) {
                let html = '<form onsubmit="saveSettings(event)">';
                data.data.forEach(setting => {
                    // Skip config items that are handled in the dedicated Config page
                    const excludedSettings = [
                        // App Config page settings
                        'home_title', 'home_subtitle', 'input_label', 'input_hint', 'button_text',
                        'alt_home_title', 'alt_home_subtitle', 'alt_input_label', 'alt_input_hint', 'alt_button_text',
                        'whitelisted_countries',
                        // Popup Config page settings
                        'whitelist_error_title', 'whitelist_error_msg', 'whitelist_action_text', 'whitelist_action_url',
                        'alt_error_title', 'alt_error_msg'
                    ];

                    if (excludedSettings.includes(setting.setting_key)) return;

                    html += `<div class="mb-3">
                        <label class="form-label text-capitalize">${setting.setting_key.replace(/_/g, ' ')}</label>
                        <input type="text" class="form-control" name="${setting.setting_key}" value="${setting.setting_value}">
                        <div class="form-text text-muted">${setting.description || ''}</div>
                    </div>`;
                });
                html += '<button type="submit" class="btn btn-primary"><i class="fas fa-save me-2"></i>Save General Settings</button></form>';
                container.innerHTML = html;
            }
        });
}

function saveSettings(e) {
    e.preventDefault();
    const formData = new FormData(e.target);
    const promises = [];

    for (let [key, value] of formData.entries()) {
        promises.push(fetch('../api/admin/save_settings.php', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify({ setting_key: key, setting_value: value })
        }));
    }

    Promise.all(promises)
        .then(() => alert('Settings saved successfully'))
        .catch(() => alert('Error saving settings'));
}
