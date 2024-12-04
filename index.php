<!DOCTYPE html>
<html lang="sk">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Vacation finder</title>
    <link rel="stylesheet" type="text/css" href="CSS/styles.css">
    <link rel="shortcut icon" href="#">
</head>
<body>
<header>
    <h1>Location info</h1>
    <div class="header-right">
        <a href="index.php">Weather</a>
        <a href="stats.php">Stats</a>
    </div>
</header>
<main>
    <form id="weather-form" method="get">
        <div class="form-group">
            <label for="location">Location:</label>
            <input type="text" id="location" name="location" required>
        </div>
        <div class="form-group">
            <label for="date">Date:</label>
            <input type="date" id="date" name="date" required value="<?php echo date('Y-m-d'); ?>">
        </div>
        <div class="form-group">
            <button type="submit">Search</button>
        </div>
    </form>

    <div id="location-info" style="display: none;">
        <p id="location-name"></p>
        <p id="weather-description"></p>
        <p id="weather-temperature"></p>
        <p id="avg-temperature"></p>
        <p id="country"></p>
        <p id="flag"></p>
        <p id="capital"></p>
        <p id="currency"></p>
        <p id="currency-converted" style="display: none;"></p>
    </div>
</main>

<script src="main.js"></script>
</body>
</html>

