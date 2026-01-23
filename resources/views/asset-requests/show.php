<?php
$error = $_SESSION['error'] ?? '';
$success = $_SESSION['success'] ?? '';
unset($_SESSION['error'], $_SESSION['success']);

ob_start();
?>
<div class="mb-6">
  <h1 class="text-3xl font-bold mb-2">Request Details</h1>
  <a href="/asset-requests/my-requests" class="text-blue-600 hover:text-blue-800">&larr; Back to My Requests</a>
</div>

<?php if ($error): ?>
  <div class="mb-4 p-4 bg-red-100 border border-red-400 text-red-700 rounded"><?= htmlspecialchars($error) ?></div>
<?php endif; ?>

<div class="bg-white rounded-lg shadow overflow-hidden">
  <div class="p-6 grid grid-cols-1 md:grid-cols-2 gap-6">
    <div>
      <h2 class="text-lg font-semibold text-gray-800 mb-2">Request Info</h2>
      <div class="text-sm text-gray-700 space-y-1">
        <p><span class="font-medium">Request #:</span> <?= htmlspecialchars($request['request_number']) ?></p>
        <p><span class="font-medium">Status:</span> <?= htmlspecialchars($request['status']) ?></p>
        <p><span class="font-medium">Priority:</span> <?= htmlspecialchars($request['priority'] ?? 'low') ?></p>
        <p><span class="font-medium">Created At:</span> <?= htmlspecialchars(date('M d, Y', strtotime($request['created_at']))) ?></p>
        <?php if (!empty($request['date_needed'])): ?>
        <p><span class="font-medium">Date Needed:</span> <?= htmlspecialchars(date('M d, Y', strtotime($request['date_needed']))) ?></p>
        <?php endif; ?>
      </div>
    </div>
    <div>
      <h2 class="text-lg font-semibold text-gray-800 mb-2">Requester</h2>
      <div class="text-sm text-gray-700 space-y-1">
        <p><span class="font-medium">Name:</span> <?= htmlspecialchars($request['requester_name']) ?></p>
        <p><span class="font-medium">Email:</span> <?= htmlspecialchars($request['requester_email']) ?></p>
        <p><span class="font-medium">Employee #:</span> <?= htmlspecialchars($request['employee_number']) ?></p>
        <p><span class="font-medium">Department:</span> <?= htmlspecialchars($request['department_name'] ?? '-') ?></p>
      </div>
    </div>
    <div>
      <h2 class="text-lg font-semibold text-gray-800 mb-2">Asset Requested</h2>
      <div class="text-sm text-gray-700 space-y-1">
        <p><span class="font-medium">Name:</span> <?= htmlspecialchars($request['asset_name'] ?? $request['linked_asset_name'] ?? '-') ?></p>
        <p><span class="font-medium">Category:</span> <?= htmlspecialchars($request['asset_category'] ?? '-') ?></p>
        <p><span class="font-medium">Quantity:</span> <?= htmlspecialchars($request['quantity_requested']) ?></p>
        <?php if (!empty($request['linked_asset_code'])): ?>
        <p><span class="font-medium">Linked Asset Code:</span> <?= htmlspecialchars($request['linked_asset_code']) ?></p>
        <?php endif; ?>
      </div>
    </div>
    <div>
      <h2 class="text-lg font-semibold text-gray-800 mb-2">Approvals</h2>
      <div class="text-sm text-gray-700 space-y-1">
        <p><span class="font-medium">Department Manager:</span> <?= htmlspecialchars($request['department_manager_approval_status']) ?><?= $request['dept_mgr_name'] ? ' ('.htmlspecialchars($request['dept_mgr_name']).')' : '' ?></p>
        <p><span class="font-medium">IT Manager:</span> <?= htmlspecialchars($request['it_manager_approval_status']) ?><?= $request['it_mgr_name'] ? ' ('.htmlspecialchars($request['it_mgr_name']).')' : '' ?></p>
      </div>
    </div>
    <div class="md:col-span-2">
      <h2 class="text-lg font-semibold text-gray-800 mb-2">Purpose / Reason</h2>
      <div class="text-sm text-gray-700 bg-gray-50 p-3 rounded border border-gray-200">
        <?= nl2br(htmlspecialchars($request['reason'] ?? '-')) ?>
      </div>
    </div>
  </div>
</div>

<?php
$content = ob_get_clean();
include __DIR__ . '/../layout.php';
?>
