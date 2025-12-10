<?php include 'includes/header.php'; ?>

<div class="container-fluid">
    <!-- Header -->
    <div class="row mb-4">
        <div class="col">
            <h2 class="fw-bold text-dark">Dashboard</h2>
            <p class="text-muted">Overview of API usage and settings</p>
        </div>
    </div>

    <!-- Stats -->
    <div class="row mb-4">
        <div class="col-md-4">
            <div class="card stat-card">
                <div class="card-body">
                    <div class="stat-icon bg-success bg-opacity-10 text-success">
                        <i class="fas fa-chart-bar"></i>
                    </div>
                    <h6 class="text-muted text-uppercase mb-2" style="font-size: 0.8rem; letter-spacing: 1px;">Today's
                        Requests</h6>
                    <h2 class="fw-bold mb-0" id="todayRequests">0</h2>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card stat-card">
                <div class="card-body">
                    <div class="stat-icon bg-info bg-opacity-10 text-info">
                        <i class="fas fa-signal"></i>
                    </div>
                    <h6 class="text-muted text-uppercase mb-2" style="font-size: 0.8rem; letter-spacing: 1px;">Total
                        Requests</h6>
                    <h2 class="fw-bold mb-0" id="totalRecords">0</h2>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card stat-card">
                <div class="card-body">
                    <div class="stat-icon bg-warning bg-opacity-10 text-warning">
                        <i class="fas fa-check-circle"></i>
                    </div>
                    <h6 class="text-muted text-uppercase mb-2" style="font-size: 0.8rem; letter-spacing: 1px;">Success
                        Rate</h6>
                    <h2 class="fw-bold mb-0" id="successRate">0%</h2>
                </div>
            </div>
        </div>
    </div>

    <!-- Content Tabs -->
    <div class="card">
        <div class="card-header bg-white border-bottom-0 pt-4 px-4">
            <ul class="nav nav-pills card-header-pills" id="adminTabs" role="tablist">
                <li class="nav-item" role="presentation">
                    <button class="nav-link active" id="logs-tab" data-bs-toggle="tab" data-bs-target="#api-logs"
                        type="button" role="tab" onclick="showTab('api-logs')">
                        <i class="fas fa-list me-2"></i> API Logs
                    </button>
                </li>
                <li class="nav-item" role="presentation">
                    <button class="nav-link" id="settings-tab" data-bs-toggle="tab" data-bs-target="#settings"
                        type="button" role="tab" onclick="showTab('settings')">
                        <i class="fas fa-cog me-2"></i> Settings
                    </button>
                </li>
            </ul>
        </div>
        <div class="card-body p-4">
            <div class="tab-content" id="adminTabsContent">

                <!-- API Logs Tab -->
                <div class="tab-pane fade show active" id="api-logs" role="tabpanel">
                    <h5 class="fw-bold mb-4">Recent API Requests</h5>
                    <div class="table-responsive" id="apiLogsTable">
                        <!-- Table will be populated by JS -->
                    </div>
                </div>

                <!-- Settings Tab -->
                <div class="tab-pane fade" id="settings" role="tabpanel">
                    <h5 class="fw-bold mb-4">General Settings</h5>
                    <div id="settingsForm">
                        <!-- Form will be populated by JS -->
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script src="admin.js?v=2.0"></script>

<?php include 'includes/footer.php'; ?>