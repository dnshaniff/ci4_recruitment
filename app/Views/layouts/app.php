<?php $vite = vite_assets(); ?>

<!DOCTYPE html>
<html lang="en">

<head>

    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="<?= csrf_token() ?>" content="<?= csrf_hash() ?>">

    <title><?= $title ?? 'Dashboard' ?></title>

    <link
        rel="stylesheet"
        href="<?= $vite['css'] ?>">

</head>

<body>

    <div class="app-layout">

        <!-- SIDEBAR -->
        <?= $this->include('partials/sidebar') ?>

        <!-- MAIN -->
        <main class="main-content">

            <!-- NAVBAR -->
            <?= $this->include('partials/navbar') ?>

            <!-- CONTENT -->
            <div class="content-wrapper">

                <?= $this->renderSection('content') ?>

            </div>

            <!-- FOOTER -->
            <?= $this->include('partials/footer') ?>

        </main>

    </div>

    <?= $this->include('partials/flash') ?>

    <script type="module" src="<?= $vite['js'] ?>"></script>

    <?= $this->include('partials/scripts') ?>

    <?= $this->renderSection('scripts') ?>

</body>

</html>