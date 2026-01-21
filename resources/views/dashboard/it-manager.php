<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>IT Manager Dashboard</title>
    <script src="https://cdn.jsdelivr.net/npm/apexcharts"></script>
</head>
<body>
    <h1>IT Manager Dashboard</h1>

    <div id="approvalQueueChart"></div>
    <div id="approvalStatsChart"></div>
    <div id="requestStatusChart"></div>
    <div id="pendingApprovalsCount"></div>
    <div id="fastMovingAssetsChart"></div>
    <div id="assetStockLevelChart"></div>

    <script>
        // Sample data, replace it with actual data
        var approvalQueueData = [/* data */];
        var approvalStatsData = [/* data */];
        var requestStatusData = [/* data */];
        var pendingApprovals = /* pending count */;
        var fastMovingAssetsData = [/* data */];
        var assetStockLevelData = [/* data */];

        // Approval Queue Chart
        var options1 = {
            series: [
                {
                    name: 'Requests',
                    data: approvalQueueData
                }
            ],
            chart: {
                height: 350,
                type: 'line',
            },
            title: {
                text: 'Approval Queue',
            },
            xaxis: {
                categories: [/* categories */]
            }
        };
        var chart1 = new ApexCharts(document.querySelector("#approvalQueueChart"), options1);
        chart1.render();

        // Approval Statistics Chart
        var options2 = {
            series: [
                {
                    name: 'Approved',
                    data: approvalStatsData.approved
                },
                {
                    name: 'Rejected',
                    data: approvalStatsData.rejected
                }
            ],
            chart: {
                type: 'pie',
            },
            title: {
                text: 'Approval Statistics',
            }
        };
        var chart2 = new ApexCharts(document.querySelector("#approvalStatsChart"), options2);
        chart2.render();

        // Request Status Distribution
        var options3 = {
            series: requestStatusData,
            chart: {
                height: 350,
                type: 'bar',
            },
            title: {
                text: 'Request Status Distribution',
            },
            xaxis: {
                labels: { show: true }
            }
        };
        var chart3 = new ApexCharts(document.querySelector("#requestStatusChart"), options3);
        chart3.render();

        // Pending Approvals Count
        document.querySelector('#pendingApprovalsCount').innerText = 'Pending Approvals: ' + pendingApprovals;

        // Fast Moving Assets Chart
        var options4 = {
            series: [{
                name: 'Assets',
                data: fastMovingAssetsData
            }],
            chart: {
                type: 'line',
                height: 350
            },
            title: {
                text: 'Fast Moving Assets',
            }
        };
        var chart4 = new ApexCharts(document.querySelector("#fastMovingAssetsChart"), options4);
        chart4.render();

        // Asset Stock Level Monitoring
        var options5 = {
            series: [{
                name: 'Stock Level',
                data: assetStockLevelData
            }],
            chart: {
                height: 350,
                type: 'bar'
            },
            title: {
                text: 'Asset Stock Level Monitoring',
            }
        };
        var chart5 = new ApexCharts(document.querySelector("#assetStockLevelChart"), options5);
        chart5.render();
    </script>
</body>
</html>