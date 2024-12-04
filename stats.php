<?php
require_once 'config.php';

//------------------------ IN THE LAST HOUR
$ip_address = $_SERVER['REMOTE_ADDR'];
$hashed_ip = hash('sha256', $ip_address);

$sql = "SELECT * FROM hashed_visits WHERE hashed_ip = '$hashed_ip' AND visit_time > DATE_SUB(NOW(), INTERVAL 1 HOUR)";
$result = $db->query($sql);

if ($result->num_rows == 0) {
    $insert_sql = "INSERT INTO hashed_visits (hashed_ip) VALUES ('$hashed_ip')";
    $db->query($insert_sql);
}

$sql = "SELECT COUNT(DISTINCT hashed_ip) AS unique_visits FROM hashed_visits WHERE visit_time > DATE_SUB(NOW(), INTERVAL 1 HOUR)";
$result = $db->query($sql);

$unique_visitors = 0;
if ($result->num_rows > 0) {
    $row = $result->fetch_assoc();
    $unique_visitors = $row['unique_visits'];
}

//------------------------ IN THE INTERVALS
$time_intervals = [
    '06:00-15:00' => "SELECT COUNT(*) AS count FROM hashed_visits WHERE HOUR(visit_time) >= 6 AND HOUR(visit_time) < 15",
    '15:00-21:00' => "SELECT COUNT(*) AS count FROM hashed_visits WHERE HOUR(visit_time) >= 15 AND HOUR(visit_time) < 21",
    '21:00-24:00' => "SELECT COUNT(*) AS count FROM hashed_visits WHERE HOUR(visit_time) >= 21 AND HOUR(visit_time) < 24",
    '00:00-06:00' => "SELECT COUNT(*) AS count FROM hashed_visits WHERE HOUR(visit_time) >= 0 AND HOUR(visit_time) < 6"
];

$visit_counts = [];

foreach ($time_intervals as $interval => $sql) {
    $result = $db->query($sql);
    if ($result) {
        $row = $result->fetch_assoc();
        $visit_counts[$interval] = $row['count'];
    } else {
        $visit_counts[$interval] = 0;
    }
}

//------------------------ DESTINATIONS SEARCH
$sql = "SELECT destination_name, country, search_count FROM searched_destinations";
$result = $db->query($sql);

$searched_destinations = [];

if ($result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $searched_destinations[] = $row;
    }
}
?>

<!DOCTYPE html>
<html lang="sk">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Vacation finder</title>
    <link rel="stylesheet" type="text/css" href="CSS/styles.css">
    <link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/1.11.5/css/jquery.dataTables.css">
    <link rel="shortcut icon" href="#">
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.datatables.net/1.11.5/js/jquery.dataTables.min.js"></script>
</head>
<body>
<header>
    <h1>Stats</h1>
    <div class="header-right">
        <a href="index.php">Weather</a>
        <a href="stats.php">Stats</a>
    </div>
</header>

<div class="stats">
    <h2 class="stats-title">Statistics</h2>
    <div class="visit-count">
        Number of unique visits in the last hour: <strong><?php echo $unique_visitors; ?></strong>
    </div>
    <table>
        <tr>
            <?php foreach (array_keys($visit_counts) as $interval) { echo "<th>$interval</th>"; } ?>
        </tr>
        <tr>
            <?php foreach ($visit_counts as $count) { echo "<td>$count</td>"; } ?>
        </tr>
    </table>
    <table id="searched-destinations-table">
        <thead>
        <tr>
            <th>Destination</th>
            <th>Country</th>
            <th>Search Count</th>
        </tr>
        </thead>
        <tbody>
        <?php foreach ($searched_destinations as $destination) : ?>
            <tr>
                <td><?php echo $destination['destination_name']; ?></td>
                <td><?php echo $destination['country']; ?></td>
                <td><?php echo $destination['search_count']; ?></td>
            </tr>
        <?php endforeach; ?>
        </tbody>
    </table>
</div>

<script>
    $(document).ready(function() {
        $('#searched-destinations-table').DataTable({
            "order": [[1, 'asc'], [0, 'asc']]
        });
    });
</script>

</body>
</html>

