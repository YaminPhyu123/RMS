<?php
header('Content-Type: application/json');
include('config/config.php');
include('config/checklogin.php');

$con = mysqli_connect("localhost", "root", "", "project");
if (!$con) {
    die(json_encode(["error" => "Problem in database connection! Contact administrator!"]));
}

$action = isset($_GET['action']) ? $_GET['action'] : '';
$type = isset($_GET['type']) ? $_GET['type'] : '';
$year = isset($_GET['year']) ? intval($_GET['year']) : '';
$month = isset($_GET['month']) ? intval($_GET['month']) : '';

if ($action == 'years') {
    $sql = "SELECT DISTINCT YEAR(date) AS year FROM daily_record ORDER BY year DESC";
    $result = mysqli_query($con, $sql);
    $years = [];
    while ($row = mysqli_fetch_assoc($result)) {
        $years[] = $row['year'];
    }
    echo json_encode($years);
} elseif ($type == 'daily') {
    $sql = "SELECT * FROM daily_record WHERE YEAR(date) = ? AND MONTH(date) = ? ORDER BY date DESC";
    $stmt = mysqli_prepare($con, $sql);
    mysqli_stmt_bind_param($stmt, 'ii', $year, $month);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);

    $data = [
        'labels' => [],
        'expenses' => [],
        'incomes' => [],
        'profits' => [],
        'table' => ''
    ];
    while ($row = mysqli_fetch_assoc($result)) {
        $data['labels'][] = $row['date'];
        $data['expenses'][] = $row['expense'];
        $data['incomes'][] = $row['income'];
        $data['profits'][] = $row['profit'];
        $data['table'] .= "<tr>
                            <td>{$row['date']}</td>
                            <td>{$row['expense']}</td>
                            <td>{$row['income']}</td>
                            <td>{$row['profit']}</td>
                         </tr>";
    }
    echo json_encode($data);
} elseif ($type == 'monthly') {
    $sql = "SELECT DATE_FORMAT(date, '%Y-%m') AS month, SUM(expense) AS purchaset, SUM(income) AS salet, SUM(profit) AS profitt
            FROM daily_record
            WHERE YEAR(date) = ?
            GROUP BY DATE_FORMAT(date, '%Y-%m')
            ORDER BY month DESC";
    $stmt = mysqli_prepare($con, $sql);
    mysqli_stmt_bind_param($stmt, 'i', $year);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);

    $data = [
        'labels' => [],
        'expenses' => [],
        'incomes' => [],
        'profits' => [],
        'table' => ''
    ];
    while ($row = mysqli_fetch_assoc($result)) {
        $data['labels'][] = $row['month'];
        $data['expenses'][] = $row['purchaset'];
        $data['incomes'][] = $row['salet'];
        $data['profits'][] = $row['profitt'];
        $data['table'] .= "<tr>
                            <td>{$row['month']}</td>
                            <td>{$row['purchaset']}</td>
                            <td>{$row['salet']}</td>
                            <td>{$row['profitt']}</td>
                         </tr>";
    }
    echo json_encode($data);
}

mysqli_close($con);

?>
