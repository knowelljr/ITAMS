<?php
$activePage = 'asset-requests';

$error = $_SESSION['error'] ?? '';
$success = $_SESSION['success'] ?? '';
unset($_SESSION['error'], $_SESSION['success']);

$currentSort = $_GET['sort'] ?? 'date';
$currentSearch = $_GET['search'] ?? '';
$currentPage = max(1, $_GET['page'] ?? 1);

$sortDateSelected = ($currentSort === 'date') ? 'selected' : '';
$sortDeptSelected = ($currentSort === 'department') ? 'selected' : '';
$sortPrioritySelected = ($currentSort === 'priority') ? 'selected' : '';

$content = <<<HTML
<div class="w-full max-w-screen-xl mx-auto px-2">
<div class="mb-3">
    <div class="flex justify-between items-center mb-4">
        <div>
            <h1 class="text-3xl font-bold">Manage Asset Requests</h1>
            <p class="text-gray-600">Update priority, attach quotation, set PO number, cancel or view details.</p>
        </div>
        <a href="/asset-requests/create" class="bg-blue-600 text-white px-4 py-2 rounded hover:bg-blue-700">+ New Request</a>
    </div>
</div>
HTML;

if ($error) {
    $content .= "<div class='mb-4 p-4 bg-red-100 border border-red-400 text-red-700 rounded'>$error</div>";
}

function badge($text, $type) {
    $map = [
        'yellow' => 'bg-yellow-100 text-yellow-900 border border-yellow-300',
        'green' => 'bg-green-100 text-green-900 border border-green-300',
        'red' => 'bg-red-100 text-red-900 border border-red-300',
        'gray' => 'bg-gray-100 text-gray-900 border border-gray-300',
        'blue' => 'bg-blue-100 text-blue-900 border border-blue-300'
    ];
    $cls = $map[$type] ?? $map['blue'];
    return "<span class='inline-flex items-center px-3 py-1.5 text-xs font-bold rounded-full $cls shadow-sm'>$text</span>";
}

$content .= <<<HTML
<!-- Search and Sort Controls -->
<div class="bg-white rounded-lg shadow-md p-4 mb-2">
    <div class="flex gap-2 items-center">
        <form method="GET" action="/asset-requests/manage" class="flex gap-2 flex-1">
            <input type="text" name="search" value="$currentSearch" placeholder="Search by request #, name, department, asset..." class="flex-1 px-4 py-2 border border-gray-300 rounded focus:ring-2 focus:ring-blue-500">
            <input type="hidden" name="sort" value="$currentSort">
            <button type="submit" class="px-3 py-2 bg-blue-600 text-white rounded hover:bg-blue-700">Search</button>
            <a href="/asset-requests/manage" class="px-3 py-2 border border-gray-300 rounded hover:bg-gray-50">Clear</a>
        </form>
        <div class="flex items-center gap-2">
            <label class="text-sm font-medium text-gray-700">Sort by:</label>
            <select onchange="window.location.href='/asset-requests/manage?sort='+this.value+'&search=$currentSearch'" class="px-3 py-2 border border-gray-300 rounded focus:ring-2 focus:ring-blue-500">
                <option value="date" $sortDateSelected>Request Date</option>
                <option value="department" $sortDeptSelected>Department</option>
                <option value="priority" $sortPrioritySelected>Priority</option>
            </select>
        </div>
    </div>
</div>

<div class="bg-white rounded-lg shadow overflow-hidden" data-page-size="10" data-export-name="asset-requests">
  <table class="min-w-full table-auto data-table">
    <thead class="bg-gray-50">
      <tr>
        <th class="px-6 py-2 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Ref #</th>
        <th class="px-6 py-2 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Requester</th>
        <th class="px-6 py-2 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Asset Details</th>
        <th class="px-6 py-2 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Priority</th>
        <th class="px-6 py-2 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">Approvals</th>
        
        <th class="px-6 py-2 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
      </tr>
    </thead>
    <tbody class="bg-white divide-y divide-gray-200">
HTML;

if (empty($requests)) {
    $content .= "<tr><td colspan='8' class='px-6 py-12 text-center text-gray-500 text-base'>
        <svg class='mx-auto h-12 w-12 text-gray-400 mb-4' xmlns='http://www.w3.org/2000/svg' fill='none' viewBox='0 0 24 24' stroke='currentColor'>
            <path stroke-linecap='round' stroke-linejoin='round' stroke-width='2' d='M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z' />
        </svg>
        <p class='font-medium'>No requests found</p>
        <p class='text-sm text-gray-400 mt-1'>Try adjusting your search or filter criteria</p>
    </td></tr>";
} else {
    foreach ($requests as $r) {
        $deptBadge = match($r['department_manager_approval_status'] ?? 'PENDING') {
            'APPROVED' => badge('✓ Dept Mgr', 'green'),
            'REJECTED' => badge('✗ Dept Mgr', 'red'),
            default => badge('⏳ Dept Mgr', 'yellow')
        };
        $itBadge = match($r['it_manager_approval_status'] ?? 'PENDING') {
            'APPROVED' => badge('✓ IT Mgr', 'green'),
            'REJECTED' => badge('✗ IT Mgr', 'red'),
            default => badge('⏳ IT Mgr', 'yellow')
        };
        $statusClass = match($r['status']) {
            'PENDING' => 'yellow',
            'DEPT_APPROVED' => 'blue',
            'FULLY_APPROVED' => 'green',
            'REJECTED' => 'red',
            'CANCELLED' => 'gray',
            default => 'blue'
        };
        $statusBadge = badge($r['status'], $statusClass);

        $priority = htmlspecialchars($r['priority'] ?? 'normal');
        $po = htmlspecialchars($r['po_number'] ?? '');
        $quotationLink = !empty($r['quotation_file']) ? "<a href='{$r['quotation_file']}' target='_blank' class='text-blue-600 hover:text-blue-800 font-medium hover:underline'>View File</a>" : '<span class="text-gray-400 text-sm">No file</span>';

        // Build priority options with color coding
        $priorityOptions = '';
        foreach (['low'=>'Low','fair'=>'Fair','high'=>'High'] as $opt => $label) {
            $sel = ($opt === strtolower($priority)) ? 'selected' : '';
            $priorityOptions .= "<option value='$opt' $sel>$label</option>";
        }

        $priorityColor = match(strtolower($priority)) {
            'high' => 'border-red-300 bg-red-50 focus:ring-red-500 text-red-800 font-semibold',
            'fair' => 'border-yellow-300 bg-yellow-50 focus:ring-yellow-500 text-yellow-800 font-medium',
            'low' => 'border-green-300 bg-green-50 focus:ring-green-500 text-green-800',
            default => 'border-gray-300 bg-gray-50 focus:ring-blue-500'
        };

        $content .= "<tr>";
        $content .= "<td class='px-6 py-2 whitespace-nowrap text-sm text-gray-900'>{$r['request_number']}</td>";
        $content .= "<td class='px-6 py-2 whitespace-nowrap'><div class='text-sm font-medium text-gray-900'>{$r['requester_name']}</div><div class='text-xs text-gray-600 mt-1'><span class='font-mono'>{$r['employee_number']}</span> • {$r['department_name']}</div></td>";
        $content .= "<td class='px-6 py-2 whitespace-nowrap'><div class='text-sm font-medium text-gray-900'>{$r['asset_name']}</div><div class='text-xs text-gray-600 mt-1'><span class='bg-gray-100 px-2 py-1 rounded text-xs font-medium'>{$r['asset_category']}</span> • Qty: <span class='font-semibold'>{$r['quantity_requested']}</span></div><div class='mt-2'>$statusBadge</div></td>";
        $formId = "manage-{$r['id']}";
        $content .= "<td class='px-6 py-2 whitespace-nowrap'><select name='priority' form='$formId' class='w-full px-3 py-2 border rounded-md text-xs $priorityColor'>$priorityOptions</select></td>";
        $content .= "<td class='px-6 py-2 whitespace-nowrap'><div class='flex flex-col gap-2'>$deptBadge $itBadge</div></td>";
        
        $content .= "<td class='px-6 py-2 whitespace-nowrap'><div class='flex flex-wrap justify-center gap-1.5'>";
        $content .= "<form id='$formId' action='/asset-requests/manage/process' method='POST' enctype='multipart/form-data'>";
        $content .= "<input type='hidden' name='action' value='update'>";
        $content .= "<input type='hidden' name='request_id' value='{$r['id']}'>";
        $content .= "<button type='submit' class='p-2 bg-blue-600 text-white rounded-md hover:bg-blue-700 transition-colors shadow-sm' title='Save Changes'><svg xmlns='http://www.w3.org/2000/svg' class='h-5 w-5' fill='currentColor' viewBox='0 0 20 20'><path d='M4 3a2 2 0 00-2 2v10a2 2 0 002 2h12a1 1 0 00.7-.3l1-1a1 1 0 00.3-.7V6.4a1 1 0 00-.3-.7l-3.4-3.4A1 1 0 0013.6 2H4zm0 2h9v3H4V5zm0 5h12v5H4v-5z'/></svg></button>";
        $content .= "</form>";
        $content .= "<form action='/asset-requests/manage/process' method='POST' onsubmit=\"return confirm('Cancel this request?');\">";
        $content .= "<input type='hidden' name='action' value='cancel'>";
        $content .= "<input type='hidden' name='request_id' value='{$r['id']}'>";
        $content .= "<button type='submit' class='p-2 bg-red-600 text-white rounded-md hover:bg-red-700 transition-colors shadow-sm' title='Cancel Request'><svg xmlns='http://www.w3.org/2000/svg' class='h-5 w-5' fill='currentColor' viewBox='0 0 20 20'><path d='M4.22 4.22a.75.75 0 011.06 0L10 8.94l4.72-4.72a.75.75 0 111.06 1.06L11.06 10l4.72 4.72a.75.75 0 11-1.06 1.06L10 11.06l-4.72 4.72a.75.75 0 11-1.06-1.06L8.94 10 4.22 5.28a.75.75 0 010-1.06z'/></svg></button>";
        $content .= "</form>";
        $content .= "<button onclick=\"showDetails({$r['id']})\" class='p-2 bg-gray-700 text-white rounded-md hover:bg-gray-800 transition-colors shadow-sm' title='View Details'><svg xmlns='http://www.w3.org/2000/svg' class='h-5 w-5' fill='currentColor' viewBox='0 0 20 20'><path d='M10 2a8 8 0 100 16 8 8 0 000-16zm-.75 5a.75.75 0 111.5 0v.5a.75.75 0 01-1.5 0V7zm0 3a.75.75 0 011.5 0v3.5a.75.75 0 01-1.5 0V10z'/></svg></button>";
        $content .= "<button onclick=\"showIssueModal({$r['id']}, '{$r['request_number']}')\" class='p-2 bg-green-600 text-white rounded-md hover:bg-green-700 transition-colors shadow-sm' title='Issue Asset'><svg xmlns='http://www.w3.org/2000/svg' class='h-5 w-5' fill='currentColor' viewBox='0 0 20 20'><path d='M3 5a2 2 0 012-2h5a2 2 0 011.6.8l3.4 4.53c.26.35.4.77.4 1.2V14a2 2 0 01-2 2h-1.28a2.5 2.5 0 10-4.84 0H7a2 2 0 01-2-2V5zm5.25 10a1 1 0 112 0 1 1 0 01-2 0zM5.5 6.5a.75.75 0 000 1.5h3a.75.75 0 000-1.5h-3z'/></svg></button>";
        $content .= "</div></td>";
        $content .= "</tr>";
    }
}

 $content .= <<<'HTML'
    </tbody>
  </table>
</div>
</div>

<!-- Pagination -->
HTML;

if ($totalPages > 1) {
    $content .= "<div class='mt-4 flex justify-center items-center gap-2'>";
    
    // Previous button
    if ($currentPage > 1) {
        $prevPage = $currentPage - 1;
        $content .= "<a href='/asset-requests/manage?page=$prevPage&sort=$currentSort&search=$currentSearch' class='px-3 py-2 border border-gray-300 rounded hover:bg-gray-50'>Previous</a>";
    }
    
    // Page numbers
    $startPage = max(1, $currentPage - 2);
    $endPage = min($totalPages, $currentPage + 2);
    
    if ($startPage > 1) {
        $content .= "<a href='/asset-requests/manage?page=1&sort=$currentSort&search=$currentSearch' class='px-3 py-2 border border-gray-300 rounded hover:bg-gray-50'>1</a>";
        if ($startPage > 2) {
            $content .= "<span class='px-3 py-2'>...</span>";
        }
    }
    
    for ($i = $startPage; $i <= $endPage; $i++) {
        if ($i == $currentPage) {
            $content .= "<span class='px-3 py-2 bg-blue-600 text-white rounded'>$i</span>";
        } else {
            $content .= "<a href='/asset-requests/manage?page=$i&sort=$currentSort&search=$currentSearch' class='px-3 py-2 border border-gray-300 rounded hover:bg-gray-50'>$i</a>";
        }
    }
    
    if ($endPage < $totalPages) {
        if ($endPage < $totalPages - 1) {
            $content .= "<span class='px-3 py-2'>...</span>";
        }
        $content .= "<a href='/asset-requests/manage?page=$totalPages&sort=$currentSort&search=$currentSearch' class='px-3 py-2 border border-gray-300 rounded hover:bg-gray-50'>$totalPages</a>";
    }
    
    // Next button
    if ($currentPage < $totalPages) {
        $nextPage = $currentPage + 1;
        $content .= "<a href='/asset-requests/manage?page=$nextPage&sort=$currentSort&search=$currentSearch' class='px-3 py-2 border border-gray-300 rounded hover:bg-gray-50'>Next</a>";
    }
    
    $content .= "</div>";
    $content .= "<div class='mt-2 text-center text-sm text-gray-600'>Page $currentPage of $totalPages (Total: $total requests)</div>";
}

$content .= <<<'HTML'

  </div> <!-- container -->

<!-- Details Modal -->
<div id="detailsModal" class="hidden fixed z-10 inset-0 overflow-y-auto">
  <div class="flex items-center justify-center min-h-screen px-4">
    <div class="fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity" onclick="closeDetails()"></div>
    <div class="relative bg-white rounded-lg max-w-2xl w-full p-6">
      <h3 class="text-lg font-medium mb-4">Request Details</h3>
      <div id="detailsBody" class="text-sm text-gray-700 space-y-2"></div>
      <div class="mt-4 flex justify-end">
        <button onclick="closeDetails()" class="px-4 py-2 border border-gray-300 rounded hover:bg-gray-50">Close</button>
      </div>
    </div>
  </div>
</div>

<!-- Issue Modal -->
<div id="issueModal" class="hidden fixed z-10 inset-0 overflow-y-auto">
  <div class="flex items-center justify-center min-h-screen px-4">
    <div class="fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity" onclick="closeIssueModal()"></div>
    <div class="relative bg-white rounded-lg max-w-2xl w-full p-6">
      <h3 class="text-lg font-medium mb-4">Issue Asset for Request: <span id="issueRequestNumber" class="font-mono text-blue-600"></span></h3>
      <form id="issueForm" action="/assets/issue/process" method="POST">
        <input type="hidden" id="issueRequestId" name="request_id">
        
        <div class="grid grid-cols-1 gap-4">
          <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">Asset <span class="text-red-500">*</span></label>
            <select name="asset_id" required class="w-full px-3 py-2 border border-gray-300 rounded focus:ring-2 focus:ring-blue-500">
              <option value="">Select Asset</option>
              <!-- Assets will be populated dynamically or from server -->
            </select>
          </div>
          
          <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">Issued To <span class="text-red-500">*</span></label>
            <input type="text" name="issued_to" required class="w-full px-3 py-2 border border-gray-300 rounded focus:ring-2 focus:ring-blue-500" placeholder="Employee name">
          </div>
          
          <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">Department</label>
            <input type="text" name="department" class="w-full px-3 py-2 border border-gray-300 rounded focus:ring-2 focus:ring-blue-500" placeholder="Department">
          </div>
          
          <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">Issuance Type <span class="text-red-500">*</span></label>
            <select name="issuance_type" required class="w-full px-3 py-2 border border-gray-300 rounded focus:ring-2 focus:ring-blue-500">
              <option value="">Select Type</option>
              <option value="permanent">Permanent</option>
              <option value="temporary">Temporary</option>
            </select>
          </div>
          
          <div id="returnDateField" class="hidden">
            <label class="block text-sm font-medium text-gray-700 mb-1">Expected Return Date</label>
            <input type="date" name="expected_return_date" class="w-full px-3 py-2 border border-gray-300 rounded focus:ring-2 focus:ring-blue-500">
          </div>
          
          <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">Purpose <span class="text-red-500">*</span></label>
            <textarea name="purpose" required rows="3" class="w-full px-3 py-2 border border-gray-300 rounded focus:ring-2 focus:ring-blue-500" placeholder="Purpose of issuance"></textarea>
          </div>
          
          <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">Notes</label>
            <textarea name="notes" rows="2" class="w-full px-3 py-2 border border-gray-300 rounded focus:ring-2 focus:ring-blue-500" placeholder="Additional notes"></textarea>
          </div>
        </div>
        
        <div class="mt-6 flex justify-end gap-2">
          <button type="button" onclick="closeIssueModal()" class="px-4 py-2 border border-gray-300 rounded hover:bg-gray-50">Cancel</button>
          <button type="submit" class="px-4 py-2 bg-green-600 text-white rounded hover:bg-green-700">Issue Asset</button>
        </div>
      </form>
    </div>
  </div>
</div>

<script>
// Show/hide return date based on issuance type
document.addEventListener('DOMContentLoaded', function() {
  const issueTypeSelect = document.querySelector('select[name="issuance_type"]');
  const returnDateField = document.getElementById('returnDateField');
  
  if (issueTypeSelect) {
    issueTypeSelect.addEventListener('change', function() {
      if (this.value === 'temporary') {
        returnDateField.classList.remove('hidden');
        returnDateField.querySelector('input').required = true;
      } else {
        returnDateField.classList.add('hidden');
        returnDateField.querySelector('input').required = false;
      }
    });
  }
});

async function showDetails(id) {
  const rows = Array.from(document.querySelectorAll('tbody tr'));
  const row = rows.find(tr => {
    const btn = tr.querySelector('button[onclick^="showDetails("]');
    return btn && btn.getAttribute('onclick') === `showDetails(${id})`;
  });
  const ref = row ? row.querySelector('td:nth-child(1)').textContent.trim() : '';
  try {
    const res = await fetch(`/api/asset-requests/get-by-number?request_number=${encodeURIComponent(ref)}`);
    const data = await res.json();
    const body = document.getElementById('detailsBody');
    if (!data.success) { body.innerHTML = `<div class='text-red-600'>${data.error || 'Failed to load details'}</div>`; }
    else {
      const r = data.request;
      body.innerHTML = `
        <div><strong>Ref #:</strong> ${r.request_number}</div>
        <div><strong>Requester:</strong> ${r.requester_name} (${r.employee_number})</div>
        <div><strong>Department:</strong> ${r.department_name || 'N/A'}</div>
        <div><strong>Asset:</strong> ${r.asset_name} (${r.asset_category})</div>
        <div><strong>Quantity:</strong> ${r.quantity_requested}</div>
        <div><strong>Date Needed:</strong> ${r.date_needed || 'N/A'}</div>
        <div><strong>Status:</strong> ${r.status}</div>
        <div><strong>Priority:</strong> ${r.priority || 'normal'}</div>
        <div><strong>PO #:</strong> ${r.po_number || 'N/A'}</div>
        <div><strong>Quotation:</strong> ${r.quotation_file ? `<a href='${r.quotation_file}' target='_blank' class='text-blue-600'>View</a>` : 'None'}</div>
        <div><strong>Dept Approval:</strong> ${r.department_manager_approval_status}</div>
        <div><strong>IT Approval:</strong> ${r.it_manager_approval_status}</div>
        <div><strong>Reason:</strong> ${r.reason}</div>
      `;
    }
    document.getElementById('detailsModal').classList.remove('hidden');
  } catch (e) {
    document.getElementById('detailsBody').innerHTML = `<div class='text-red-600'>Error: ${e.message}</div>`;
    document.getElementById('detailsModal').classList.remove('hidden');
  }
}
function closeDetails(){ document.getElementById('detailsModal').classList.add('hidden'); }

// Issue Modal Functions
function showIssueModal(requestId, requestNumber) {
  document.getElementById('issueRequestId').value = requestId;
  document.getElementById('issueRequestNumber').textContent = requestNumber;
  document.getElementById('issueModal').classList.remove('hidden');
}

function closeIssueModal() {
  document.getElementById('issueModal').classList.add('hidden');
  document.getElementById('issueForm').reset();
}
</script>
HTML;

include __DIR__ . '/../layout.php';
