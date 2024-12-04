<?php

// ---------------------- UTIL
$location = htmlspecialchars($_GET['location']);
$locationUnformatted = $location;
$location = str_replace(' ', '%20', $location);
$date = htmlspecialchars($_GET['date']);

$dateParts = explode('-', $date);
$month = $dateParts[1];

// ---------------------- OPEN WEATHER API
$openWeatherApiUrl = /*URL*/; 
$openWeatherResponse = file_get_contents($openWeatherApiUrl);
$openWeatherData = json_decode($openWeatherResponse, true);
$weatherDescription = $openWeatherData['weather'][0]['description'];
$temperature = $openWeatherData['main']['temp'];

// ---------------------- WEATHER API
$weatherApiKey = /*ApiKey*/;
$weatherApiUrl = /*URL*/;
$weatherResponse = file_get_contents($weatherApiUrl);
$weatherData = json_decode($weatherResponse, true);

if (isset($weatherData['forecast']['forecastday'][0]['day']['avgtemp_c'])) {
    $avgTemperature = $weatherData['forecast']['forecastday'][0]['day']['avgtemp_c'];
} else {
    $avgTemperature = null;
}

// ---------------------- GEONAMES API
$geoNamesApiUrl = /*URL*/;
$geoNamesResponse = file_get_contents($geoNamesApiUrl);
$geoNamesData = json_decode($geoNamesResponse, true);

$countryName = $geoNamesData['geonames'][0]['countryName'];
$countryCode = $geoNamesData['geonames'][0]['countryCode'];

// ---------------------- FLAGS API
$countryFlag = /*URL*/;

// ---------------------- REST Countries API
$countryNameFormatted = $countryName;
$countryNameFormatted = str_replace(' ', '%20', $countryNameFormatted);

$restCountriesApiUrl = /*URL*/;
$restCountriesResponse = file_get_contents($restCountriesApiUrl);
$restCountriesData = json_decode($restCountriesResponse, true);

$countryCapital = $restCountriesData[0]['capital'][0];
$countryCurrencyCode = key($restCountriesData[0]['currencies']);

// ---------------------- CONVERSION API
$convertedCurrency = null;

if ($countryCurrencyCode !== 'EUR') {
    $exchangeRateApiUrl = /*URL*/;
    $exchangeRateResponse = file_get_contents($exchangeRateApiUrl);
    $exchangeRateData = json_decode($exchangeRateResponse, true);

    $conversionRate = $exchangeRateData['rates'][$countryCurrencyCode];
    $convertedCurrency = 1 / $conversionRate;
}

// ---------------------- RESPONSE
$responseData = array(
    'description' => $weatherDescription,
    'temperature' => $temperature,
    'avg' => $avgTemperature,
    'country' => $countryName,
    'flag' => $countryFlag,
    'capital' => $countryCapital,
    'currency' => $countryCurrencyCode,
    'converted_currency' => $convertedCurrency
);
echo json_encode($responseData);

require_once 'config.php';

$existing_search = $db->query("SELECT * FROM searched_destinations WHERE destination_name = '$locationUnformatted' AND country = '$countryName'");
if ($existing_search->num_rows > 0) {
    $db->query("UPDATE searched_destinations SET search_count = search_count + 1 WHERE destination_name = '$locationUnformatted' AND country = '$countryName'");
} else {
    $db->query("INSERT INTO searched_destinations (destination_name, country) VALUES ('$locationUnformatted', '$countryName')");
}
?>
