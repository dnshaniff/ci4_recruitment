<aside class="sidebar">

    <div>

        <!-- BRAND -->
        <div class="sidebar-brand">

            <div class="brand-logo">
                HR
            </div>

            <div>
                <h5 class="mb-0 fw-bold">
                    HR System
                </h5>

                <small class="text-muted">
                    Admin Panel
                </small>
            </div>

        </div>

        <!-- MENU -->
        <ul class="sidebar-menu">
            <li>
                <a href="<?= base_url('/') ?>" class="<?= is_active('') ?>">
                    <i class='bx bx-grid-alt'></i>
                    Dashboard
                </a>
            </li>
            <li>
                <a href="<?= base_url('users') ?>" class="<?= is_active('users') ?>">
                    <i class='bx bx-user'></i>
                    Users
                </a>
            </li>
            <li>
                <a href="<?= base_url('employees') ?>" class="<?= is_active('employees') ?>">
                    <i class='bx bx-id-card'></i>
                    Employees
                </a>
            </li>
        </ul>
    </div>

    <!-- LOGOUT -->
    <div class="sidebar-footer">
        <a href="<?= base_url('logout') ?>" class="sidebar-logout">
            <i class='bx bx-log-out'></i>
            <span>
                Logout
            </span>
        </a>
    </div>

</aside>