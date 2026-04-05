?<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Changement de mot de passe - ISTAM Gestion EDT</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.0/font/bootstrap-icons.css" rel="stylesheet">
    <link rel="icon" type="image/png" href="<?php echo e(asset('OFPPT_Logo.png')); ?>">
    <style>
        :root {
            --ofppt-blue: #003366;
            --ofppt-accent: #2e86de;
        }
        body {
            background: linear-gradient(135deg, var(--ofppt-blue) 0%, #001a33 100%);
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
        }
        .login-card {
            background: white;
            border-radius: 20px;
            box-shadow: 0 20px 60px rgba(0,0,0,0.3);
            overflow: hidden;
            max-width: 420px;
            width: 100%;
            margin: 0 auto;
        }
        .login-header {
            background: linear-gradient(135deg, var(--ofppt-blue) 0%, #004080 100%);
            color: white;
            padding: 40px 30px;
            text-align: center;
        }
        .login-header h3 {
            color: white;
            margin-bottom: 5px;
            font-weight: 700;
        }
        .login-header .icon-circle {
            width: 80px;
            height: 80px;
            background: rgba(255,255,255,0.1);
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto 20px;
        }
        .login-header .icon-circle i {
            font-size: 35px;
            color: white;
        }
        .login-body {
            padding: 40px 30px;
        }
        .form-control {
            border-radius: 10px;
            padding: 12px 15px;
            border: 2px solid #e9ecef;
        }
        .form-control:focus {
            border-color: var(--ofppt-accent);
            box-shadow: 0 0 0 0.2rem rgba(46, 134, 222, 0.15);
        }
        .btn-login {
            background: linear-gradient(135deg, var(--ofppt-blue) 0%, #004080 100%);
            border: none;
            border-radius: 10px;
            padding: 12px;
            font-weight: 600;
            transition: transform 0.2s, box-shadow 0.2s;
            color: white;
        }
        .btn-login:hover {
            transform: translateY(-2px);
            box-shadow: 0 5px 20px rgba(0, 51, 102, 0.4);
            color: white;
        }
        .input-group-text {
            background: #f8f9fa;
            border: 2px solid #e9ecef;
            border-right: none;
            border-radius: 10px 0 0 10px;
        }
        .input-group .form-control {
            border-left: none;
            border-radius: 0 10px 10px 0;
        }
        
        /* Logout Button Style */
        .logout-btn {
            background: none;
            border: none;
            color: #6c757d;
            font-size: 0.9rem;
            padding: 0;
            text-decoration: none;
        }
        .logout-btn:hover {
            color: #dc3545;
            text-decoration: underline;
        }
    </style>
</head>
<body>
    <div class="login-card">
        <div class="login-header">
            <div class="icon-circle bg-white p-2">
                <img src="<?php echo e(asset('OFPPT_Logo.png')); ?>" alt="OFPPT Logo" class="img-fluid" style="height: 50px; width: auto; object-fit: contain;">
            </div>
            <h3>Sécurité</h3>
            <p class="mb-0 opacity-75">Mise à jour du mot de passe requise</p>
        </div>
        
        <div class="login-body">
            <h6 class="text-center mb-4 text-muted" style="line-height: 1.5; font-size: 0.95rem;">
                Vous utilisez actuellement le mot de passe par défaut. Pour protéger votre compte, veuillez le modifier.
            </h6>

            <?php if($errors->any()): ?>
                <div class="alert alert-danger py-2">
                    <?php $__currentLoopData = $errors->all(); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $error): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <small><i class="bi bi-exclamation-circle me-1"></i><?php echo e($error); ?></small><br>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                </div>
            <?php endif; ?>

            <form method="POST" action="<?php echo e(route('password.change.update')); ?>">
                <?php echo csrf_field(); ?>

                <div class="mb-3">
                    <label for="password" class="form-label fw-semibold" style="font-size: 0.9rem;">
                        <i class="bi bi-key me-1 text-muted"></i> Nouveau mot de passe
                    </label>
                    <div class="input-group">
                        <span class="input-group-text"><i class="bi bi-lock"></i></span>
                        <input type="password"
                               class="form-control <?php $__errorArgs = ['password'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>"
                               id="password"
                               name="password"
                               placeholder="Min. 8 caractères"
                               required
                               autofocus>
                    </div>
                </div>

                <div class="mb-4">
                    <label for="password-confirm" class="form-label fw-semibold" style="font-size: 0.9rem;">
                        <i class="bi bi-check-circle me-1 text-muted"></i> Confirmer le mot de passe
                    </label>
                    <div class="input-group">
                        <span class="input-group-text"><i class="bi bi-shield-check"></i></span>
                        <input type="password"
                               class="form-control"
                               id="password-confirm"
                               name="password_confirmation"
                               placeholder="Ressaisir le mot de passe"
                               required>
                    </div>
                </div>

                <button type="submit" class="btn btn-primary btn-login w-100 mb-3">
                    <i class="bi bi-check2-circle me-2"></i> Mettre à jour
                </button>
            </form>

            <div class="text-center mt-3 pt-3 border-top">
                <form method="POST" action="<?php echo e(route('logout')); ?>">
                    <?php echo csrf_field(); ?>
                    <button type="submit" class="logout-btn">
                        <i class="bi bi-box-arrow-left me-1"></i> Annuler et se déconnecter
                    </button>
                </form>
            </div>
            
            <div class="text-center mt-4">
                <small class="text-muted">
                    <i class="bi bi-shield-check me-1"></i>
                    Connexion sécurisée OFPPT
                </small>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>


<?php /**PATH C:\XAMPP1\htdocs\emploi-du-temps\resources\views/auth/force-password-change.blade.php ENDPATH**/ ?>