<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Register - HK ISP Billing</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <style>
        :root {
            --primary: #3b82f6;
            --primary-hover: #2563eb;
            --bg: #f8fafc;
            --text-main: #1e293b;
            --text-sub: #64748b;
        }

        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Inter', sans-serif;
            background: var(--bg);
            color: var(--text-main);
            display: flex;
            align-items: center;
            justify-content: center;
            min-height: 100vh;
            padding: 20px;
        }

        .auth-card {
            background: white;
            width: 100%;
            max-width: 450px;
            padding: 40px;
            border-radius: 16px;
            box-shadow: 0 10px 25px -5px rgba(0, 0, 0, 0.1);
        }

        .logo {
            text-align: center;
            margin-bottom: 25px;
        }

        .logo i {
            font-size: 2.5rem;
            color: var(--primary);
            margin-bottom: 10px;
        }

        .logo h2 {
            font-size: 1.5rem;
            font-weight: 700;
            color: #0f172a;
        }

        .form-grid {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 15px;
        }

        .full-width {
            grid-column: span 2;
        }

        .form-group {
            margin-bottom: 15px;
        }

        .form-group label {
            display: block;
            font-size: 0.875rem;
            font-weight: 600;
            margin-bottom: 8px;
            color: var(--text-main);
        }

        .input-wrapper {
            position: relative;
        }

        .input-wrapper i {
            position: absolute;
            left: 14px;
            top: 12px;
            color: var(--text-sub);
        }

        .input-wrapper input {
            width: 100%;
            padding: 10px 14px 10px 40px;
            border: 1px solid #e2e8f0;
            border-radius: 8px;
            font-size: 0.95rem;
            outline: none;
            transition: border-color 0.2s;
        }

        .input-wrapper input:focus {
            border-color: var(--primary);
            box-shadow: 0 0 0 3px rgba(59, 130, 246, 0.1);
        }

        .btn-auth {
            width: 100%;
            background: var(--primary);
            color: white;
            border: none;
            padding: 12px;
            border-radius: 8px;
            font-weight: 600;
            cursor: pointer;
            transition: background 0.2s;
            margin-top: 10px;
        }

        .btn-auth:hover {
            background: var(--primary-hover);
        }

        .alert {
            padding: 12px;
            border-radius: 8px;
            font-size: 0.875rem;
            margin-bottom: 20px;
        }

        .alert-error {
            background: #fee2e2;
            color: #dc2626;
            border: 1px solid #fecaca;
        }

        .footer-links {
            text-align: center;
            margin-top: 25px;
            font-size: 0.875rem;
            color: var(--text-sub);
        }

        .footer-links a {
            color: var(--primary);
            text-decoration: none;
            font-weight: 600;
        }

        .footer-links a:hover {
            text-decoration: underline;
        }

        @media (max-width: 400px) {
            .form-grid {
                grid-template-columns: 1fr;
            }

            .full-width {
                grid-column: span 1;
            }
        }
    </style>
</head>

<body>

    <div class="auth-card">
        <div class="logo">
            <i class="fas fa-id-card"></i>
            <h2>Create Account</h2>
            <p style="color: var(--text-sub); font-size: 0.875rem; margin-top: 5px;">Join the ISP Management Team</p>
        </div>

        <?php if (isset($_SESSION['error'])): ?>
            <div class="alert alert-error">
                <?= $_SESSION['error'];
                unset($_SESSION['error']); ?>
            </div>
        <?php endif; ?>

        <form action="<?= url('auth/storeRegistration') ?>" method="POST">
            <div class="form-grid">
                <div class="form-group full-width">
                    <label>Full Name</label>
                    <div class="input-wrapper">
                        <i class="fas fa-signature"></i>
                        <input type="text" name="display_name" placeholder="John Doe" required>
                    </div>
                </div>

                <div class="form-group">
                    <label>Username</label>
                    <div class="input-wrapper">
                        <i class="fas fa-user"></i>
                        <input type="text" name="username" placeholder="johndoe" required>
                    </div>
                </div>

                <div class="form-group">
                    <label>Email Address</label>
                    <div class="input-wrapper">
                        <i class="fas fa-envelope"></i>
                        <input type="email" name="email" placeholder="john@example.com" required>
                    </div>
                </div>

                <div class="form-group">
                    <label>Password</label>
                    <div class="input-wrapper">
                        <i class="fas fa-lock"></i>
                        <input type="password" name="password" placeholder="••••••••" required>
                    </div>
                </div>

                <div class="form-group">
                    <label>Confirm Password</label>
                    <div class="input-wrapper">
                        <i class="fas fa-check-double"></i>
                        <input type="password" name="confirm_password" placeholder="••••••••" required>
                    </div>
                </div>
            </div>

            <button type="submit" class="btn-auth">Register Now</button>
        </form>

        <div class="footer-links">
            Already have an account? <a href="<?= url('auth/login') ?>">Sign In</a>
        </div>
    </div>

</body>

</html>