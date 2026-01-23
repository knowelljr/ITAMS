<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Asset Requests Management</title>
    <link rel="stylesheet" href="styles.css"> <!-- Link your CSS here -->
</head>
<body>
    <div class="container">
        <h1>Asset Requests Management</h1>
        <div class="filters">
            <form id="filterForm">
                <label for="status">Status:</label>
                <select id="status" name="status">
                    <option value="all">All</option>
                    <option value="pending">Pending</option>
                    <option value="approved">Approved</option>
                    <option value="rejected">Rejected</option>
                </select>

                <label for="startDate">Start Date:</label>
                <input type="date" id="startDate" name="startDate">

                <label for="endDate">End Date:</label>
                <input type="date" id="endDate" name="endDate">

                <label for="requesterName">Requester Name:</label>
                <input type="text" id="requesterName" name="requesterName">

                <button type="submit">Filter</button>
            </form>
        </div>

        <table>
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Requester</th>
                    <th>Status</th>
                    <th>Date Submitted</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <!-- Sample data row -->
                <tr>
                    <td>1</td>
                    <td>John Doe</td>
                    <td>Pending</td>
                    <td>2026-01-20</td>
                    <td>
                        <div class="flex items-center gap-2">
                            <button class="px-3 py-1 bg-green-600 text-white rounded hover:bg-green-700" title="Issue Asset" aria-label="Issue Asset">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 inline" fill="currentColor" viewBox="0 0 20 20" aria-hidden="true">
                                    <path d="M3 5a2 2 0 012-2h5a2 2 0 011.6.8l3.4 4.53c.26.35.4.77.4 1.2V14a2 2 0 01-2 2h-1.28a2.5 2.5 0 10-4.84 0H7a2 2 0 01-2-2V5zm5.25 10a1 1 0 112 0 1 1 0 01-2 0zM5.5 6.5a.75.75 0 000 1.5h3a.75.75 0 000-1.5h-3z" />
                                </svg>
                                <span class="sr-only">Issue Asset</span>
                            </button>
                            <button class="px-3 py-1 bg-blue-600 text-white rounded hover:bg-blue-700" title="Approve" aria-label="Approve">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 inline" fill="currentColor" viewBox="0 0 20 20" aria-hidden="true">
                                    <path d="M16.7 5.3a1 1 0 010 1.4l-7 7a1 1 0 01-1.4 0l-3-3a1 1 0 011.4-1.4l2.3 2.3 6.3-6.3a1 1 0 011.4 0z" />
                                </svg>
                                <span class="sr-only">Approve</span>
                            </button>
                            <button class="px-3 py-1 bg-red-600 text-white rounded hover:bg-red-700" title="Reject" aria-label="Reject">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 inline" fill="currentColor" viewBox="0 0 20 20" aria-hidden="true">
                                    <path d="M4.22 4.22a.75.75 0 011.06 0L10 8.94l4.72-4.72a.75.75 0 111.06 1.06L11.06 10l4.72 4.72a.75.75 0 11-1.06 1.06L10 11.06l-4.72 4.72a.75.75 0 11-1.06-1.06L8.94 10 4.22 5.28a.75.75 0 010-1.06z" />
                                </svg>
                                <span class="sr-only">Reject</span>
                            </button>
                            <button class="px-3 py-1 bg-gray-700 text-white rounded hover:bg-gray-800" title="Details" aria-label="Details">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 inline" fill="currentColor" viewBox="0 0 20 20" aria-hidden="true">
                                    <path d="M10 2a8 8 0 100 16 8 8 0 000-16zm-.75 5a.75.75 0 111.5 0v.5a.75.75 0 01-1.5 0V7zm0 3a.75.75 0 011.5 0v3.5a.75.75 0 01-1.5 0V10z" />
                                </svg>
                                <span class="sr-only">Details</span>
                            </button>
                        </div>
                    </td>
                </tr>
                <!-- Repeat this structure for each request -->
            </tbody>
        </table>

        <!-- Modal for request details -->
        <div id="requestDetailsModal" class="modal">
            <div class="modal-content">
                <span class="close-btn">&times;</span>
                <h2>Request Details</h2>
                <div id="requestDetails"></div>
            </div>
        </div>
    </div>

    <script src="script.js"></script> <!-- Link your JS here -->
</body>
</html>