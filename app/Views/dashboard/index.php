<?= $this->extend('layouts/app') ?>

<?= $this->section('content') ?>

<div class="row g-4">
    <div class="col-md-4">
        <div class="dashboard-card">
            <div class="card-icon bg-primary-subtle text-primary">
                <i class='bx bx-user'></i>
            </div>
            <div>
                <h3 class="fw-bold mb-1">
                    <?= $totalUsers ?>
                </h3>
                <div class="text-muted">
                    Total Users
                </div>
            </div>
        </div>
    </div>

    <div class="col-md-4">
        <div class="dashboard-card">
            <div class="card-icon bg-success-subtle text-success">
                <i class='bx bx-id-card'></i>
            </div>
            <div>
                <h3 class="fw-bold mb-1">
                    <?= $totalEmployees ?>
                </h3>
                <div class="text-muted">
                    Total Employees
                </div>
            </div>
        </div>
    </div>

</div>

<?= $this->include('partials/flash') ?>

<?= $this->endSection() ?>