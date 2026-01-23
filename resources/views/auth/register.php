<?php
// Display any error messages
$error = $_SESSION['error'] ?? '';

// Clear messages after displaying
if (isset($_SESSION['error'])) {
    unset($_SESSION['error']);
}

// Get departments
$departments = \App\Controllers\DepartmentController::getAllDepartments();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
    <title>Register - ITAMS</title>
</head>
<body class="bg-gradient-to-r from-blue-500 to-blue-700 flex items-center justify-center min-h-screen">
    <div class="w-full max-w-2xl">
        <div class="bg-white rounded-lg shadow-lg p-8">
            <!-- Logo/Title -->
            <div class="text-center mb-8">
                <h1 class="text-4xl font-bold text-blue-600">ITAMS</h1>
                <p class="text-gray-600 text-sm mt-2">Create Your Account</p>
            </div>

            <!-- Error Message -->
            <?php if ($error): ?>
                <div class="mb-4 p-4 bg-red-100 border border-red-400 text-red-700 rounded">
                    <span class="block"><?php echo htmlspecialchars($error); ?></span>
                </div>
            <?php endif; ?>

            <!-- Register Form -->
            <form action="/register" method="POST" class="space-y-4">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <!-- Full Name Field -->
                    <div>
                        <label for="full_name" class="block text-sm font-medium text-gray-700 mb-2">
                            Full Name <span class="text-red-500">*</span>
                        </label>
                        <input 
                            type="text" 
                            id="full_name" 
                            name="full_name" 
                            required 
                            class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                            placeholder="Enter your full name"
                        >
                    </div>

                    <!-- Employee Number Field -->
                    <div>
                        <label for="employee_number" class="block text-sm font-medium text-gray-700 mb-2">
                            Employee Number <span class="text-red-500">*</span>
                        </label>
                        <input 
                            type="text" 
                            id="employee_number" 
                            name="employee_number" 
                            required 
                            class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                            placeholder="Enter employee number"
                        >
                    </div>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <!-- Email Field -->
                    <div>
                        <label for="email" class="block text-sm font-medium text-gray-700 mb-2">
                            Email Address <span class="text-red-500">*</span>
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

                    <!-- Mobile Number Field -->
                    <div>
                        <label for="mobile_number" class="block text-sm font-medium text-gray-700 mb-2">
                            Mobile Number
                        </label>
                        <input 
                            type="text" 
                            id="mobile_number" 
                            name="mobile_number" 
                            class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                            placeholder="Enter mobile number"
                        >
                    </div>
                </div>

                <!-- Department Field -->
                <div>
                    <label for="department_id" class="block text-sm font-medium text-gray-700 mb-2">
                        Department <span class="text-red-500">*</span>
                    </label>
                    <select 
                        id="department_id" 
                        name="department_id" 
                        required 
                        class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                    >
                        <option value="">Select your department</option>
                        <?php foreach ($departments as $dept): ?>
                            <option value="<?php echo $dept['id']; ?>">
                                <?php echo htmlspecialchars($dept['department_code'] . ' - ' . $dept['department_name']); ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <!-- Password Field -->
                    <div>
                        <label for="password" class="block text-sm font-medium text-gray-700 mb-2">
                            Password <span class="text-red-500">*</span>
                        </label>
                        <input 
                            type="password" 
                            id="password" 
                            name="password" 
                            required 
                            class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                            placeholder="Enter password (min 6 characters)"
                        >
                        <p class="text-xs text-gray-500 mt-1">Must be at least 6 characters</p>
                    </div>

                    <!-- Confirm Password Field -->
                    <div>
                        <label for="confirm_password" class="block text-sm font-medium text-gray-700 mb-2">
                            Confirm Password <span class="text-red-500">*</span>
                        </label>
                        <input 
                            type="password" 
                            id="confirm_password" 
                            name="confirm_password" 
                            required 
                            class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                            placeholder="Confirm your password"
                        >
                    </div>
                </div>

                <!-- Terms Agreement -->
                <div class="flex items-start mt-4">
                    <input 
                        type="checkbox" 
                        id="terms" 
                        name="terms" 
                        required 
                        class="mt-1 h-4 w-4 text-blue-600 focus:ring-blue-500 border-gray-300 rounded"
                    >
                    <label for="terms" class="ml-2 block text-sm text-gray-700">
                        I agree to the <a href="#" class="text-blue-600 hover:underline">Terms of Service</a> 
                        and <a href="#" class="text-blue-600 hover:underline">Privacy Policy</a>
                    </label>
                </div>

                <!-- Submit Button -->
                <button 
                    type="submit" 
                    class="w-full bg-blue-600 text-white py-3 px-4 rounded-lg hover:bg-blue-700 transition duration-300 font-medium"
                >
                    Create Account
                </button>

                <!-- Login Link -->
                <div class="text-center mt-4">
                    <p class="text-sm text-gray-600">
                        Already have an account? 
                        <a href="/login" class="text-blue-600 hover:underline font-medium">Login here</a>
                    </p>
                </div>
            </form>
        </div>
    </div>
</body>
</html>
