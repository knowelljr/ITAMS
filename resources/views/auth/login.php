<?php
// Get any error/success messages from session
$error = $_SESSION['error'] ??  '';
$success = $_SESSION['success'] ?? '';

// Clear messages after displaying
if (isset($_SESSION['error'])) {
    unset($_SESSION['error']);
}
if (isset($_SESSION['success'])) {
    unset($_SESSION['success']);
}

// Determine logo path (SVG) that respects app base path and server docroot
$scriptBase = rtrim(str_replace('\\', '/', dirname($_SERVER['SCRIPT_NAME'] ?? '/')), '/');
if ($scriptBase === '/') { $scriptBase = ''; }
$logoPath = $scriptBase . '/images/asset-logo.svg';

// Fallback if served from project root without public as docroot
if (!file_exists(__DIR__ . '/../../../public/images/asset-logo.svg')) {
    $logoPath = $scriptBase . '/public/images/asset-logo.svg';
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
    <style>
        * {
            font-family: 'Inter', -apple-system, BlinkMacSystemFont, 'Segoe UI', 'Roboto', 'Helvetica Neue', Arial, sans-serif;
        }
        html, body { margin: 0; height: 100%; }
        body.login-bg {
            min-height: 100vh;
            background: linear-gradient(135deg, #0f172a 0%, #1e3a8a 50%, #1e40af 100%);
            position: relative;
            overflow: hidden;
        }
        .animated-box {
            position: absolute;
            border-radius: 0.5rem;
            background: rgba(59,130,246,0.10);
            border: 2px solid rgba(59,130,246,0.18);
            box-shadow: 0 2px 16px 0 rgba(30,64,175,0.08);
            z-index: 0;
            animation: floatBox 12s ease-in-out infinite;
        }
        .box1 { width: 180px; height: 180px; top: 8%; left: 10%; animation-delay: 0s; }
        .box2 { width: 120px; height: 120px; top: 60%; left: 18%; animation-delay: 2s; }
        .box3 { width: 90px; height: 90px; top: 30%; left: 70%; animation-delay: 4s; }
        .box4 { width: 140px; height: 140px; top: 75%; left: 60%; animation-delay: 6s; }
        .box5 { width: 100px; height: 100px; top: 15%; left: 80%; animation-delay: 8s; }
        @keyframes floatBox {
            0%, 100% { transform: translateY(0) scale(1); opacity: 0.7; }
            50% { transform: translateY(-30px) scale(1.05); opacity: 1; }
        }
    </style>
    <title>Login - ITAMS</title>
</head>
<body class="login-bg flex items-center justify-center min-h-screen overflow-hidden">
    <!-- Animated background boxes -->
    <div class="animated-box box1"></div>
    <div class="animated-box box2"></div>
    <div class="animated-box box3"></div>
    <div class="animated-box box4"></div>
    <div class="animated-box box5"></div>
    <div class="min-h-screen flex items-center justify-center relative z-10">
        <div class="flex flex-row items-center justify-center max-w-3xl w-full bg-white rounded-lg shadow-lg overflow-hidden">
            <!-- Company Logo (Left Side) -->
            <div class="flex flex-col items-center justify-center bg-white p-12 md:w-2/5 w-1/2 border-r border-gray-200">
                <img src="/images/logo-login.png" alt="Company Logo" loading="lazy" class="h-72 w-auto max-w-lg md:max-w-[400px] drop-shadow-2xl mb-4">
            </div>
            <!-- Login Form Card (Right Side) -->
            <div class="w-full md:w-3/5 w-1/2 flex flex-col justify-center p-12">
                <!-- System Name -->
                <div class="text-center mb-6">
                    <h2 class="text-xl font-semibold text-blue-700">IT Asset Management</h2>
                </div>

                <!-- Error Message -->
                <?php if ($error): ?>
                    <div class="mb-3 p-3 bg-red-100 border border-red-400 text-red-700 rounded text-sm">
                        <span class="block"><?php echo htmlspecialchars($error); ?></span>
                    </div>
                <?php endif; ?>

                <!-- Success Message -->
                <?php if ($success): ?>
                    <div class="mb-3 p-3 bg-green-100 border border-green-400 text-green-700 rounded text-sm">
                        <span class="block"><?php echo htmlspecialchars($success); ?></span>
                    </div>
                <?php endif; ?>

                <!-- Login Form -->
                <form action="/login" method="POST" class="space-y-4">
                    <!-- Email Field -->
                    <div>
                        <label for="email" class="block text-sm font-medium text-gray-700 mb-2">
                            Email Address
                        </label>
                        <input 
                            type="email" 
                            id="email" 
                            name="email" 
                            required 
                            class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                            placeholder="Enter your email"
                        >
                    </div>

                    <!-- Password Field -->
                    <div>
                        <label for="password" class="block text-sm font-medium text-gray-700 mb-2">
                            Password
                        </label>
                        <input 
                            type="password" 
                            id="password" 
                            name="password" 
                            required 
                            class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                            placeholder="Enter your password"
                        >
                    </div>

                    <!-- Submit Button -->
                    <button 
                        type="submit" 
                        class="w-full bg-blue-600 text-white font-semibold py-2 px-4 rounded-lg hover:bg-blue-700 transition duration-200"
                    >
                        Sign In
                    </button>
                </form>

                <!-- Create Account Link -->
                <div class="mt-6 text-center">
                    <p class="text-gray-600 text-sm">
                        Don't have an account?    
                        <a href="/register" class="text-blue-600 hover:text-blue-700 font-semibold">
                            Create one
                        </a>
                    </p>
                </div>

                <!-- Footer -->
                <div class="mt-8 text-center text-xs text-gray-500">
                    <p>&copy; 2026 ITAMS. All rights reserved.</p>
                </div>
            </div>
        </div>
    </div>
</body>
</html>
