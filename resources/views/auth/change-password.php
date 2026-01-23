<?php
if (!isset($_SESSION['user_id'])) {
    header('Location: /login');
    exit;
}

$error = $_SESSION['error'] ?? '';
$info = $_SESSION['info'] ?? '';
unset($_SESSION['error'], $_SESSION['info']);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
    <title>Change Password - ITAMS</title>
</head>
<body class="bg-gradient-to-r from-blue-500 to-blue-700 flex items-center justify-center min-h-screen">
    <div class="w-full max-w-md">
        <div class="bg-white rounded-lg shadow-lg p-8">
            <div class="text-center mb-8">
                <h1 class="text-4xl font-bold text-blue-600">ITAMS</h1>
                <p class="text-gray-600 text-sm mt-2">Change Your Password</p>
            </div>

            <?php if ($error): ?>
                <div class="mb-4 p-4 bg-red-100 border border-red-400 text-red-700 rounded">
                    <?php echo htmlspecialchars($error); ?>
                </div>
            <?php endif; ?>

            <?php if ($info): ?>
                <div class="mb-4 p-4 bg-blue-100 border border-blue-400 text-blue-700 rounded">
                    <?php echo htmlspecialchars($info); ?>
                </div>
            <?php endif; ?>

            <form action="/change-password" method="POST" class="space-y-4">
                <div>
                    <label for="current_password" class="block text-sm font-medium text-gray-700 mb-2">
                        Current Password <span class="text-red-500">*</span>
                    </label>
                    <input 
                        type="password" 
                        id="current_password" 
                        name="current_password" 
                        required 
                        class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500"
                        placeholder="Enter current password"
                    >
                </div>

                <div>
                    <label for="new_password" class="block text-sm font-medium text-gray-700 mb-2">
                        New Password <span class="text-red-500">*</span>
                    </label>
                    <input 
                        type="password" 
                        id="new_password" 
                        name="new_password" 
                        required 
                        class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500"
                        placeholder="Enter new password (min 6 characters)"
                    >
                    <p class="text-xs text-gray-500 mt-1">Must be at least 6 characters</p>
                </div>

                <div>
                    <label for="confirm_password" class="block text-sm font-medium text-gray-700 mb-2">
                        Confirm New Password <span class="text-red-500">*</span>
                    </label>
                    <input 
                        type="password" 
                        id="confirm_password" 
                        name="confirm_password" 
                        required 
                        class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500"
                        placeholder="Confirm new password"
                    >
                </div>

                <button 
                    type="submit" 
                    class="w-full bg-blue-600 text-white py-3 px-4 rounded-lg hover:bg-blue-700 transition duration-300 font-medium"
                >
                    Change Password
                </button>

                <div class="text-center mt-4">
                    <a href="/logout" class="text-sm text-gray-600 hover:underline">Logout</a>
                </div>
            </form>
        </div>
    </div>
</body>
</html>
