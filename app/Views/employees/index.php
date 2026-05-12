<?= $this->extend('layouts/app') ?>

<?= $this->section('content') ?>

<div class="page-header mb-4">
    <div>
        <h3 class="fw-bold mb-1">
            Employee Management
        </h3>
    </div>
</div>

<div class="card border-0 shadow-sm rounded-4">
    <div class="card-body">
        <table class="table align-middle datatables-employees">
            <thead>
                <tr>
                    <th width="1px">#</th>
                    <th>Name</th>
                    <th>Email</th>
                    <th>Position</th>
                    <th>Gender</th>
                    <th width="13%">Created At</th>
                    <th width="13%">Updated At</th>
                    <th width="1px">Action</th>
                </tr>
            </thead>
        </table>
    </div>
</div>

<div class="modal fade" id="employeeModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content border-0 rounded-4">
            <div class="modal-header border-0 p-4 pb-0">
                <div>
                    <h4 class="fw-bold mb-1 modal-title">Employee</h4>
                </div>
                <button type="button" class="btn-close shadow-none" data-bs-dismiss="modal"></button>
            </div>
            <form id="employeeForm" enctype="multipart/form-data">
                <div class="modal-body p-4">
                    <div class="row">
                        <div class="col-md-6 mb-4">
                            <label class="form-label fw-semibold">Employee Code</label>
                            <input type="text" name="employee_code" id="employee_code" class="form-control" placeholder="EMP-0001">
                            <div class="invalid-feedback error-employee_code"></div>
                        </div>
                        <div class="col-md-6 mb-4">
                            <label class="form-label fw-semibold">Name</label>
                            <input type="text" name="name" id="name" class="form-control" placeholder="Enter employee name">
                            <div class="invalid-feedback error-name"></div>
                        </div>
                        <div class="col-md-6 mb-4">
                            <label class="form-label fw-semibold">Email</label>
                            <input type="email" name="email" id="email" class="form-control" placeholder="Enter employee email">
                            <div class="invalid-feedback error-email"></div>
                        </div>
                        <div class="col-md-6 mb-4">
                            <label class="form-label fw-semibold">Gender</label>
                            <select name="gender" id="gender" class="form-select">
                                <option value="">Select Gender</option>
                                <option value="Male">Male</option>
                                <option value="Female">Female</option>
                            </select>
                            <div class="invalid-feedback error-gender"></div>
                        </div>
                        <div class="col-md-12 mb-4">
                            <label class="form-label fw-semibold">Position</label>
                            <input type="text" name="position" id="position" class="form-control" placeholder="Enter employee position">
                            <div class="invalid-feedback error-position"></div>
                        </div>
                        <div class="col-md-12">
                            <label class="form-label fw-semibold">Photo</label>
                            <input type="file" name="photo" id="photo" class="form-control" accept=".jpg,.jpeg">
                            <small class="text-muted">JPG/JPEG only, max 300KB</small>
                            <div class="invalid-feedback error-photo"></div>
                        </div>
                        <div class="col-md-12 mt-4">
                            <div class="border rounded-4 p-3 text-center bg-light" id="photoPreviewWrapper">
                                <img src="https://placehold.co/600x400?text=No+Image" id="photoPreview" class="img-fluid rounded-3" style="max-height: 240px; object-fit: cover;">
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer border-0 p-4 pt-0">
                    <button type="button" class="btn btn-light" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-dark">Save Employee</button>
                </div>
            </form>
        </div>
    </div>
</div>

<?= $this->endSection() ?>

<?= $this->section('scripts') ?>

<?php
$manifest = json_decode(
    file_get_contents(FCPATH . 'assets/.vite/manifest.json'),
    true
);
?>

<script
    type="module"
    src="<?= base_url('assets/' . $manifest['resources/js/employees.js']['file']) ?>">
</script>

<?= $this->endSection() ?>