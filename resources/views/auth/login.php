<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - ITAMS</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-100">
    <div class="flex items-center justify-center min-h-screen">
        <div class="w-full max-w-md bg-white rounded-lg shadow-md p-8">
            <h1 class="text-3xl font-bold text-center text-blue-600 mb-6">ITAMS</h1>
            <h2 class="text-xl font-semibold text-gray-800 mb-6">Login</h2>
            
            <?php if (isset($_SESSION['error'])): ?>
                <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded mb-4">
                    <?php echo $_SESSION['error']; unset($_SESSION['error']); ?>
                </div>
            <?php endif; ?>
            
            <form method="POST" action="/login">
                <div class="mb-4">
                    <label class="block text-gray-700 font-semibold mb-2" for="email">Email</label>
                    <input class="w-full px-4 py-2 border border-gray-300 rounded focus:outline-none focus:ring-2 focus:ring-blue-500" type="email" id="email" name="email" required>
                </div>
                
                <div class="mb-6">
                    <label class="block text-gray-700 font-semibold mb-2" for="password">Password</label>
                    <input class="w-full px-4 py-2 border border-gray-300 rounded focus:outline-none focus:ring-2 focus:ring-blue-500" type="password" id="password" name="password" required>
                </div>
                
                <button class="w-full bg-blue-600 text-white font-semibold py-2 rounded hover:bg-blue-700 transition" type="submit">Login</button>
            </form>
            
            <p class="text-center text-gray-600 mt-4">
                Don't have an account? <a href="/register" class="text-blue-600 hover:underline">Register here</a>
            </p>
        </div>
    </div>
</body>
</html>