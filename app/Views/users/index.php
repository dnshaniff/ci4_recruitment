<?= $this->extend('layouts/app') ?>

<?= $this->section('content') ?>

<div class="page-header mb-4">
    <div>
        <h3 class="fw-bold mb-1">
            User Management
        </h3>
    </div>
</div>

<div class="card border-0 shadow-sm rounded-4">
    <div class="card-body">
        <table class="table align-middle datatables-users">
            <thead>
                <tr>
                    <th width="1px">#</th>
                    <th>Name</th>
                    <th>Email</th>
                    <th width="13%">Created At</th>
                    <th width="13%">Updated At</th>
                    <th width="1px">Action</th>
                </tr>
            </thead>
        </table>
    </div>
</div>

<div class="modal fade" id="userModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 rounded-4">
            <div class="modal-header border-0 p-4 pb-0">
                <div>
                    <h4 class="fw-bold mb-1 modal-title">User</h4>
                </div>
                <button type="button" class="btn-close shadow-none" data-bs-dismiss="modal"></button>
            </div>
            <form id="userForm">
                <div class="modal-body p-4">
                    <div class="mb-4">
                        <label class="form-label fw-semibold">Name</label>
                        <input type="text" name="name" id="name" class="form-control" placeholder="Enter name">
                        <div class="invalid-feedback error-name"></div>
                    </div>
                    <div class="mb-4">
                        <label class="form-label fw-semibold">Email</label>
                        <input type="email" name="email" id="email" class="form-control" placeholder="Enter email">
                        <div class="invalid-feedback error-email"></div>
                    </div>
                    <div class="mb-0">
                        <label class="form-label fw-semibold">Password</label>
                        <div class="password-wrapper">
                            <input type="password" name="password" id="password" class="form-control pe-5" placeholder="Enter password">
                            <button type="button" class="password-toggle" id="togglePassword">
                                <i class='bx bx-show'></i>
                            </button>
                        </div>
                        <div class="invalid-feedback d-block error-password"></div>
                    </div>
                </div>
                <div class="modal-footer border-0 p-4 pt-0">
                    <button type="button" class="btn btn-light" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-dark">Save User</button>
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
    src="<?= base_url('assets/' . $manifest['resources/js/users.js']['file']) ?>">
</script>

<?= $this->endSection() ?>