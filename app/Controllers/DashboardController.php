<?php

namespace App\Controllers;

use App\Models\DashboardModel;

class DashboardController {
    private $dashboardModel;

    public function __construct() {
        $this->dashboardModel = new DashboardModel();
    }

    public function getDashboardData($role) {
        switch ($role) {
            case 'REQUESTER':
                return $this->getRequesterData();
            case 'IT_STAFF':
                return $this->getItStaffData();
            case 'IT_MANAGER':
                return $this->getItManagerData();
            default:
                throw new \Exception('Invalid role');
        }
    }

    private function getRequesterData() {
        // Fetch statistics and charts data for REQUESTER
        return [
            'requests' => $this->dashboardModel->getRequesterRequests(),
            'chartData' => $this->dashboardModel->getRequesterChartData()
        ];
    }

    private function getItStaffData() {
        // Fetch statistics and charts data for IT_STAFF
        return [
            'resolvedRequests' => $this->dashboardModel->getResolvedRequests(),
            'openRequests' => $this->dashboardModel->getOpenRequests(),
            'chartData' => $this->dashboardModel->getItStaffChartData()
        ];
    }

    private function getItManagerData() {
        // Fetch statistics and charts data for IT_MANAGER
        return [
            'overallStatistics' => $this->dashboardModel->getOverallStatistics(),
            'chartData' => $this->dashboardModel->getItManagerChartData()
        ];
    }
}