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
                        <button class="issue-btn">Issue Asset</button>
                        <button class="approve-btn">Approve</button>
                        <button class="reject-btn">Reject</button>
                        <button class="details-btn">Details</button>
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