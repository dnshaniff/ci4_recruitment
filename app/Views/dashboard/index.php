<?= $this->extend('layouts/app') ?>

<?= $this->section('content') ?>

<div class="row g-4">

    <!-- CARD -->
    <div class="col-md-4">

        <div class="dashboard-card">

            <div class="card-icon bg-primary-subtle text-primary">
                <i class='bx bx-user'></i>
            </div>

            <div>

                <h3 class="fw-bold mb-1">
                    10
                </h3>

                <div class="text-muted">
                    Total Users
                </div>

            </div>

        </div>

    </div>

    <!-- CARD -->
    <div class="col-md-4">

        <div class="dashboard-card">

            <div class="card-icon bg-success-subtle text-success">
                <i class='bx bx-id-card'></i>
            </div>

            <div>

                <h3 class="fw-bold mb-1">
                    25
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