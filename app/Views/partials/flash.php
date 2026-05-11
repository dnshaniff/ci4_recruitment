<?php if (session()->getFlashdata('error')) : ?>

    <script>
        document.addEventListener('DOMContentLoaded', function() {

            const notyf = new Notyf({
                duration: 4000,
                position: {
                    x: 'right',
                    y: 'top',
                }
            });

            notyf.error("<?= session()->getFlashdata('error') ?>");

        });
    </script>

<?php endif; ?>

<?php if (session()->getFlashdata('success')) : ?>

    <script>
        document.addEventListener('DOMContentLoaded', function() {

            const notyf = new Notyf({
                duration: 4000,
                position: {
                    x: 'right',
                    y: 'top',
                }
            });

            notyf.success("<?= session()->getFlashdata('success') ?>");

        });
    </script>

<?php endif; ?>