<?php $vite = vite_assets(); ?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title><?= $title ?? 'Authentication' ?></title>

    <link rel="stylesheet" href="<?= $vite['css'] ?>">
</head>

<body>

    <?= $this->renderSection('content') ?>

    <script type="module" src="<?= $vite['js'] ?>"></script>

</body>

</html>