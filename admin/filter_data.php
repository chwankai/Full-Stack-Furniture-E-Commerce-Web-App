<?php
require_once __DIR__ . '/include/admin_session.php';
include ('include/config.php');

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $input = json_decode(file_get_contents('php://input'), true);
    $year = $input['year'];
    $month = $input['month'];

    $dateFilter = "$year-";
    if ($month !== 'all') {
        $dateFilter .= str_pad($month, 2, "0", STR_PAD_LEFT) . "-%";
    } else {
        $dateFilter .= "%";
    }

    // Fetch filtered data for daily chart
    $dailyQuery = "SELECT DATE(orderDate) as date, SUM(orders.grandtotal) as total_price, orderStatus
                   FROM orders 
                   WHERE orderDate LIKE '$dateFilter' AND orderStatus is NOT null
                   GROUP BY date 
                   ORDER BY date ASC";
    $dailyResult = mysqli_query($con, $dailyQuery);

    $dates = [];
    $dailyTotalPrices = [];

    while ($row = mysqli_fetch_assoc($dailyResult)) {
        $dates[] = $row['date'];
        $dailyTotalPrices[] = $row['total_price'];
    }

    // Fetch filtered data for monthly chart
    $monthlyQuery = "SELECT DATE_FORMAT(orderDate, '%Y-%m') as month, SUM(orders.grandtotal) as total_price, orderStatus
                     FROM orders 
                     WHERE orderDate LIKE '$year-%' AND orderStatus is NOT null
                     GROUP BY month 
                     ORDER BY month ASC";
    $monthlyResult = mysqli_query($con, $monthlyQuery);

    $months = [];
    $monthlyTotalPrices = [];

    while ($row = mysqli_fetch_assoc($monthlyResult)) {
        $months[] = $row['month'];
        $monthlyTotalPrices[] = $row['total_price'];
    }

    echo json_encode([
        'daily' => [
            'labels' => $dates,
            'data' => $dailyTotalPrices
        ],
        'monthly' => [
            'labels' => $months,
            'data' => $monthlyTotalPrices
        ]
    ]);
}
?>
