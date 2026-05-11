<nav class="top-navbar">

    <div>

        <h4 class="fw-bold mb-0">
            Dashboard
        </h4>

    </div>

    <div class="d-flex align-items-center gap-3">

        <div class="user-avatar">

            <?= strtoupper(substr(auth_user()['name'], 0, 1)) ?>

        </div>

        <div>

            <div class="fw-semibold">
                <?= auth_user()['name'] ?>
            </div>

            <small class="text-muted">
                Administrator
            </small>

        </div>

    </div>

</nav>