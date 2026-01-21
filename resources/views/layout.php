<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.0.0/dist/tailwind.min.css" rel="stylesheet">
    <title>Layout</title>
</head>
<body class="bg-gray-100">
    <div class="flex flex-col min-h-screen">
        <!-- Navbar -->
        <nav class="bg-white shadow">
            <div class="container mx-auto px-4 py-3 flex justify-between items-center">
                <div class="text-lg font-semibold">ITAMS</div>
                <div>
                    <span class="mr-4">Logged in as: knowelljr</span>
                    <a href="#" class="text-blue-500">Logout</a>
                </div>
            </div>
        </nav>

        <div class="flex flex-1">
            <!-- Sidebar -->
            <aside class="w-64 bg-gray-200 p-4">
                <h2 class="font-bold mb-2">Menu</h2>
                <ul>
                    <li><a href="#" class="block p-2 rounded hover:bg-gray-300">Home</a></li>
                    <li><a href="#" class="block p-2 rounded hover:bg-gray-300">Profile</a></li>
                    <li><a href="#" class="block p-2 rounded hover:bg-gray-300">Settings</a></li>
                    <li><a href="#" class="block p-2 rounded hover:bg-gray-300">Help</a></li>
                </ul>
                <ul class="mt-4">
                    <li class="font-bold">Requester</li>
                    <li><a href="#" class="block p-2 rounded hover:bg-gray-300">Create Request</a></li>
                    <li><a href="#" class="block p-2 rounded hover:bg-gray-300">View Requests</a></li>
                    <li class="font-bold">IT Staff</li>
                    <li><a href="#" class="block p-2 rounded hover:bg-gray-300">Manage Requests</a></li>
                    <li class="font-bold">IT Manager</li>
                    <li><a href="#" class="block p-2 rounded hover:bg-gray-300">Approve Requests</a></li>
                    <li class="font-bold">Admin</li>
                    <li><a href="#" class="block p-2 rounded hover:bg-gray-300">User Management</a></li>
                </ul>
            </aside>
            <!-- Main Content Area -->
            <main class="flex-1 p-4">
                <div class="bg-white p-4 rounded shadow">
                    <h1 class="text-2xl font-bold mb-4">Main Content Area</h1>
                    <!-- Success and Error Messages -->
                    <div class="mb-4">
                        <div class="text-green-600">
                            <!-- Success message goes here -->
                        </div>
                        <div class="text-red-600">
                            <!-- Error message goes here -->
                        </div>
                    </div>
                    <!-- Main content goes here -->
                </div>
            </main>
        </div>
        <!-- Footer -->
        <footer class="bg-white text-center py-4 mt-4">
            <p>&copy; 2026 ITAMS. All Rights Reserved.</p>
        </footer>
    </div>
</body>
</html>