<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
    <title>Login Page</title>
    <style>
        body {
            background: linear-gradient(to right, #4facfe 0%, #00f2fe 100%);
        }
    </style>
</head>
<body class="flex items-center justify-center h-screen">
    <div class="bg-white p-8 rounded-lg shadow-lg w-96">
        <h2 class="text-2xl font-bold mb-6 text-center">Login</h2>
        <form action="/login" method="POST">
            <div class="mb-4">
                <label class="block text-gray-700" for="email">Email</label>
                <input class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md" type="email" id="email" name="email" required>
            </div>
            <div class="mb-4">
                <label class="block text-gray-700" for="password">Password</label>
                <input class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md" type="password" id="password" name="password" required>
            </div>
            <div class="flex items-center mb-4">
                <input class="mr-2 leading-tight" type="checkbox" id="remember" name="remember">
                <label class="text-sm text-gray-600" for="remember">Remember me</label>
            </div>
            <button class="w-full bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded" type="submit">Login</button>
        </form>
        <div class="text-center mt-4">
            <a class="text-blue-500 hover:underline" href="/register">Create an account</a>
        </div>
    </div>
</body>
</html>