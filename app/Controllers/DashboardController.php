<?php

namespace App\Controllers;

use App\Models\Request;
use App\Models\Issuance;
use App\Models\Statistics;

class DashboardController {
    protected $userRole;

    public function __construct($userRole) {
        $this->userRole = $userRole;
    }

    public function show() {
        switch ($this->userRole) {
            case 'REQUESTER':
                return $this->showRequestStatus();
            case 'IT_STAFF':
                return $this->showPendingRequestsAndIssuances();
            case 'IT_MANAGER':
                return $this->showApprovalQueueAndStatistics();
            default:
                throw new \Exception('Invalid user role');
        }
    }

    protected function showRequestStatus() {
        // Logic to fetch and return request status for requester
        return Request::getStatus();
    }

    protected function showPendingRequestsAndIssuances() {
        // Logic to fetch and return pending requests and issuances for IT staff
        return [
            'pendingRequests' => Request::getPending(),
            'pendingIssuances' => Issuance::getPending(),
        ];
    }

    protected function showApprovalQueueAndStatistics() {
        // Logic to fetch and return approval queue and statistics for IT manager
        return [
            'approvalQueue' => Request::getApprovalQueue(),
            'statistics' => Statistics::getOverview(),
        ];
    }
}