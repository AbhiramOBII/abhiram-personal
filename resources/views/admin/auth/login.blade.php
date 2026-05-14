<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Login — Abhiram Chandramohan</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&family=Space+Grotesk:wght@400;500;600;700&display=swap" rel="stylesheet">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <style>
        body { font-family: 'Inter', ui-sans-serif, system-ui, sans-serif; }
        .login-card {
            background: #ffffff;
            border: 1px solid #e5e7eb;
            box-shadow: 0 4px 24px rgba(0, 0, 0, 0.06);
        }
        .login-input {
            background: #f8f9fb;
            border: 1px solid #e5e7eb;
            color: #1e293b;
            font-size: 14px;
            padding: 12px 16px;
            border-radius: 10px;
            width: 100%;
            outline: none;
            transition: border-color 0.2s, box-shadow 0.2s;
        }
        .login-input::placeholder { color: #94a3b8; }
        .login-input:focus {
            border-color: #d0ad5d;
            box-shadow: 0 0 0 3px rgba(208, 173, 93, 0.12);
        }
        .login-btn {
            background: #d0ad5d;
            color: #ffffff;
            font-weight: 600;
            font-size: 14px;
            padding: 14px;
            border-radius: 10px;
            width: 100%;
            border: none;
            cursor: pointer;
            transition: all 0.2s;
        }
        .login-btn:hover {
            background: #b8952e;
            box-shadow: 0 4px 16px rgba(208, 173, 93, 0.25);
        }
    </style>
</head>
<body style="background: #f8f9fb; color: #1e293b; margin: 0; min-height: 100vh; display: flex; align-items: center; justify-content: center;">

    <div style="width: 100%; max-width: 420px; padding: 0 24px;">

        <!-- Logo / Brand -->
        <div style="text-align: center; margin-bottom: 40px;">
            <a href="/" style="text-decoration: none; display: inline-block;">
                <span style="font-family: 'Space Grotesk', sans-serif; font-size: 28px; font-weight: 700; color: #1e293b;">Abhiram</span>
                <span style="font-family: 'Space Grotesk', sans-serif; font-size: 28px; font-weight: 700; color: #d0ad5d;">.</span>
            </a>
            <p style="margin-top: 12px; font-size: 10px; text-transform: uppercase; letter-spacing: 0.25em; color: #94a3b8;">Admin Panel</p>
        </div>

        <!-- Login Card -->
        <div class="login-card" style="border-radius: 16px; padding: 40px;">

            <div style="margin-bottom: 32px;">
                <h1 style="font-family: 'Space Grotesk', sans-serif; font-size: 24px; font-weight: 700; color: #1e293b; margin: 0;">Welcome back</h1>
                <p style="margin-top: 8px; font-size: 14px; color: #94a3b8;">Sign in to access the admin dashboard.</p>
            </div>

            @if ($errors->any())
                <div style="margin-bottom: 24px; padding: 14px 16px; border-radius: 10px; background: #fef2f2; border: 1px solid #fecaca;">
                    @foreach ($errors->all() as $error)
                        <p style="font-size: 13px; color: #dc2626; margin: 0;">{{ $error }}</p>
                    @endforeach
                </div>
            @endif

            <form method="POST" action="{{ route('admin.login') }}">
                @csrf

                <!-- Email -->
                <div style="margin-bottom: 20px;">
                    <label for="email" style="display: block; font-size: 11px; font-weight: 500; text-transform: uppercase; letter-spacing: 0.15em; color: #64748b; margin-bottom: 8px;">Email</label>
                    <input
                        type="email"
                        id="email"
                        name="email"
                        value="{{ old('email') }}"
                        required
                        autofocus
                        class="login-input"
                        placeholder="you@example.com"
                    >
                </div>

                <!-- Password -->
                <div style="margin-bottom: 24px;">
                    <label for="password" style="display: block; font-size: 11px; font-weight: 500; text-transform: uppercase; letter-spacing: 0.15em; color: #64748b; margin-bottom: 8px;">Password</label>
                    <input
                        type="password"
                        id="password"
                        name="password"
                        required
                        class="login-input"
                        placeholder="••••••••"
                    >
                </div>

                <!-- Remember Me -->
                <div style="margin-bottom: 28px; display: flex; align-items: center;">
                    <label style="display: flex; align-items: center; gap: 8px; cursor: pointer;">
                        <input type="checkbox" name="remember" style="width: 16px; height: 16px; accent-color: #d0ad5d; border-radius: 4px;">
                        <span style="font-size: 13px; color: #64748b;">Remember me</span>
                    </label>
                </div>

                <!-- Submit -->
                <button type="submit" class="login-btn">Sign In</button>
            </form>
        </div>

        <!-- Back link -->
        <div style="margin-top: 32px; text-align: center;">
            <a href="/" style="font-size: 12px; color: #94a3b8; text-decoration: none; transition: color 0.2s;">
                ← Back to website
            </a>
        </div>
    </div>
</body>
</html>
