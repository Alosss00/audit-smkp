<?php
$title = 'Login — Audit Internal SMKP Minerba';

ob_start();
?>
<style>
    body {
        background: linear-gradient(135deg, #0f172a 0%, #1e293b 50%, #0f172a 100%);
        min-height: 100vh;
    }
    .login-container {
        max-width: 440px;
        margin: auto;
    }
    .login-card {
        background: rgba(255, 255, 255, 0.96);
        backdrop-filter: blur(12px);
        border: 1px solid rgba(255, 255, 255, 0.2);
        border-radius: 24px;
        box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.35);
    }
    .btn-login {
        background: linear-gradient(135deg, #0284c7, #0369a1);
        border: none;
        font-weight: 700;
        letter-spacing: 0.3px;
        transition: all 0.3s ease;
    }
    .btn-login:hover {
        background: linear-gradient(135deg, #0369a1, #075985);
        transform: translateY(-1px);
        box-shadow: 0 10px 20px -5px rgba(2, 132, 199, 0.4);
    }
    .demo-badge {
        cursor: pointer;
        transition: all 0.2s ease;
    }
    .demo-badge:hover {
        transform: scale(1.03);
    }
</style>
<?php
$styles = ob_get_clean();

ob_start();
?>
<div class="d-flex align-items-center justify-content-center min-vh-100 py-4">
    <div class="login-container w-100">
        
        <!-- Header / Logo -->
        <div class="text-center text-white mb-4">
            <div class="d-inline-flex align-items-center justify-content-center bg-info bg-opacity-25 rounded-circle p-3 mb-3 border border-info border-opacity-25">
                <i class="bi bi-shield-check text-info display-5"></i>
            </div>
            <h3 class="fw-bold tracking-tight mb-1">SMKP MINERBA</h3>
            <p class="text-light opacity-75 small">Sistem Informasi Audit Internal — Kepdirjen 185</p>
        </div>

        <!-- Form Card -->
        <div class="card login-card p-4 p-md-5">
            <h4 class="fw-bold text-slate-800 mb-1 text-center">Masuk ke Akun</h4>
            <p class="text-muted text-center small mb-4">Silakan masukkan username dan password Anda</p>

            <?php if (isset($errors) && $errors->any()): ?>
                <div class="alert alert-danger rounded-3 py-2 px-3 mb-4" role="alert">
                    <div class="d-flex align-items-center gap-2">
                        <i class="bi bi-exclamation-triangle-fill fs-5"></i>
                        <div class="small fw-semibold">
                            <?php echo e($errors->first()); ?>
                        </div>
                    </div>
                </div>
            <?php endif; ?>

            <?php if (session('warning')): ?>
                <div class="alert alert-warning border border-warning border-opacity-50 rounded-3 py-2.5 px-3 mb-4 shadow-xs" role="alert">
                    <div class="d-flex align-items-center gap-2">
                        <i class="bi bi-hourglass-bottom text-warning fs-5"></i>
                        <div class="small fw-semibold text-dark">
                            <?php echo e(session('warning')); ?>
                        </div>
                    </div>
                </div>
            <?php endif; ?>

            <?php if (session('error')): ?>
                <div class="alert alert-danger border border-danger border-opacity-50 rounded-3 py-2.5 px-3 mb-4 shadow-xs" role="alert">
                    <div class="d-flex align-items-center gap-2">
                        <i class="bi bi-exclamation-circle-fill text-danger fs-5"></i>
                        <div class="small fw-semibold text-dark">
                            <?php echo e(session('error')); ?>
                        </div>
                    </div>
                </div>
            <?php endif; ?>

            <?php if (session('info')): ?>
                <div class="alert alert-info rounded-3 py-2 px-3 mb-4" role="alert">
                    <div class="d-flex align-items-center gap-2">
                        <i class="bi bi-info-circle-fill text-info fs-5"></i>
                        <div class="small fw-semibold">
                            <?php echo e(session('info')); ?>
                        </div>
                    </div>
                </div>
            <?php endif; ?>

            <?php if (session('success')): ?>
                <div class="alert alert-success rounded-3 py-2 px-3 mb-4" role="alert">
                    <div class="d-flex align-items-center gap-2">
                        <i class="bi bi-check-circle-fill text-success fs-5"></i>
                        <div class="small fw-semibold">
                            <?php echo e(session('success')); ?>
                        </div>
                    </div>
                </div>
            <?php endif; ?>

            <form action="<?php echo route('login'); ?>" method="POST">
                <input type="hidden" name="_token" value="<?php echo csrf_token(); ?>">

                <!-- Username Field -->
                <div class="mb-3">
                    <label for="username" class="form-label fw-semibold small text-secondary">Username</label>
                    <div class="input-group">
                        <span class="input-group-text bg-light border-end-0"><i class="bi bi-person text-muted"></i></span>
                        <input type="text" name="username" id="username" 
                            class="form-control bg-light border-start-0 <?php echo (isset($errors) && $errors->has('username')) ? 'is-invalid' : ''; ?>" 
                            placeholder="Masukkan username" value="<?php echo e(old('username')); ?>" required autofocus>
                    </div>
                </div>

                <!-- Password Field -->
                <div class="mb-3">
                    <label for="password" class="form-label fw-semibold small text-secondary">Password</label>
                    <div class="input-group">
                        <span class="input-group-text bg-light border-end-0"><i class="bi bi-lock text-muted"></i></span>
                        <input type="password" name="password" id="password" 
                            class="form-control bg-light border-start-0 <?php echo (isset($errors) && $errors->has('password')) ? 'is-invalid' : ''; ?>" 
                            placeholder="Masukkan password" required>
                    </div>
                </div>

                <!-- Remember Me -->
                <div class="d-flex align-items-center justify-content-between mb-4">
                    <div class="form-check">
                        <input class="form-check-input" type="checkbox" name="remember" id="remember">
                        <label class="form-check-label small text-secondary" for="remember">
                            Ingat Saya
                        </label>
                    </div>
                </div>

                <!-- Submit Button -->
                <button type="submit" class="btn btn-primary btn-login w-100 py-3 rounded-3 text-white mb-0">
                    <i class="bi bi-box-arrow-in-right me-2"></i> Masuk Sekarang
                </button>
            </form>

        </div>
    </div>
</div>
<?php
$content = ob_get_clean();

echo view('layouts.app', get_defined_vars())->render();
