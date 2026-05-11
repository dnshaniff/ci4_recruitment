<?= $this->extend('layouts/auth') ?>

<?= $this->section('content') ?>

<div class="auth-wrapper">

    <div class="auth-left d-none d-lg-flex">

        <div class="overlay"></div>

        <div class="auth-left-content">

            <div>
                <div class="brand-logo mb-4">
                    HR
                </div>

                <h1 class="fw-bold display-5 mb-4">
                    Human Resource Management System
                </h1>

                <p class="fs-5 text-light opacity-75">
                    Streamline recruitment, employee management,
                    and company operations in one place.
                </p>
            </div>

            <div class="auth-footer">
                Recruitment Test Project • CodeIgniter 4
            </div>

        </div>

    </div>

    <div class="auth-right">

        <div class="login-card">

            <div class="text-center mb-5">

                <h2 class="fw-bold mb-2">
                    Welcome Back
                </h2>

                <p class="text-muted mb-0">
                    Please sign in to continue
                </p>

            </div>

            <form
                action="<?= base_url('login') ?>"
                method="POST">

                <?= csrf_field() ?>

                <div class="mb-4">

                    <label class="form-label fw-semibold">
                        Email Address
                    </label>

                    <input
                        type="email"
                        name="email"
                        class="custom-form-control form-control form-control-lg"
                        placeholder="Enter your email"
                        value="<?= old('email') ?>">

                </div>

                <div class="mb-4">

                    <label class="form-label fw-semibold">
                        Password
                    </label>

                    <div class="password-wrapper">

                        <input
                            type="password"
                            id="password"
                            name="password"
                            class="custom-form-control form-control form-control-lg pe-5"
                            placeholder="Enter your password">

                        <button
                            type="button"
                            class="password-toggle"
                            id="togglePassword">

                            <i class='bx bx-show'></i>

                        </button>

                    </div>

                </div>

                <button
                    type="submit"
                    class="btn btn-dark btn-lg w-100">

                    Sign In

                </button>

            </form>

            <div class="demo-account mt-4">

                <div class="fw-semibold mb-2">
                    Demo Account
                </div>

                <div class="small text-muted">
                    Email: admin@mail.com
                </div>

                <div class="small text-muted">
                    Password: P@ssw0rd
                </div>

            </div>

        </div>

    </div>

</div>

<?= $this->include('partials/flash') ?>

<?= $this->endSection() ?>