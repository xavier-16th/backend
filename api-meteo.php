<?php


// Check if the form was submitted
$city = isset($_POST['city']) ? $_POST['city'] : 'Paris';

// Display errors for development
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// Include Composer's autoloader
require 'vendor/autoload.php';

use GuzzleHttp\Client;
use GuzzleHttp\Exception\RequestException;

// OpenWeather API key 
$apiKey = 'aa7bcce72117a2bece7eaed395f5f53c';

// Create Guzzle client
$client = new Client([
    'base_uri' => 'https://api.openweathermap.org/data/2.5/',
    'timeout' => 5.0,
]);

// Variables to store weather data
$weather = null;
$error = null;

try {
    // Send request to the API
    $response = $client->request('GET', 'weather', [
        'query' => [
            'q' => $city,
            'appid' => $apiKey,
            'units' => 'metric', // To get temperatures in Celsius
            'lang' => 'en'       // To get descriptions in English
        ]
    ]);

    // Retrieve and decode JSON data
    $weather = json_decode($response->getBody()->getContents(), true);
} catch (RequestException $e) {
    // Error handling
    $error = "Error while retrieving weather data: ";
    if ($e->hasResponse()) {
        $errorBody = json_decode($e->getResponse()->getBody()->getContents(), true);
        $error .= isset($errorBody['message']) ? $errorBody['message'] : $e->getMessage();
    } else {
        $error .= $e->getMessage();
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Weather API App</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            max-width: 800px;
            margin: 0 auto;
            padding: 20px;
        }
        .weather-card {
            background-color: #f5f5f5;
            border-radius: 10px;
            padding: 20px;
            margin-top: 20px;
            box-shadow: 0 2px 5px rgba(0,0,0,0.1);
        }
        .error {
            color: red;
            padding: 10px;
            background-color: #ffeeee;
            border-radius: 5px;
            margin-top: 20px;
        }
        form {
            margin-bottom: 20px;
        }
        input, button {
            padding: 8px;
            font-size: 16px;
        }
    </style>
</head>
<body>
    <h1>Weather Application</h1>

    <form method="post">
        <label for="city">City:</label>
        <input type="text" id="city" name="city" value="<?php echo htmlspecialchars($city); ?>" required>
        <button type="submit">Get Weather</button>
    </form>

    <?php if ($error): ?>
        <div class="error">
            <?php echo htmlspecialchars($error); ?>
        </div>
    <?php elseif ($weather): ?>
        <div class="weather-card">
            <h2>Weather in <?php echo htmlspecialchars($weather['name']); ?></h2>
            <?php if (isset($weather['weather'][0]['icon'])): ?>
                <img src="https://openweathermap.org/img/wn/<?php echo $weather['weather'][0]['icon']; ?>@2x.png" 
                     alt="<?php echo htmlspecialchars($weather['weather'][0]['description']); ?>">
            <?php endif; ?>
            <p><strong>Temperature:</strong> <?php echo round($weather['main']['temp']); ?>°C</p>
            <p><strong>Feels like:</strong> <?php echo round($weather['main']['feels_like']); ?>°C</p>
            <p><strong>Conditions:</strong> <?php echo htmlspecialchars($weather['weather'][0]['description']); ?></p>
            <p><strong>Humidity:</strong> <?php echo $weather['main']['humidity']; ?>%</p>
            <p><strong>Wind:</strong> <?php echo round($weather['wind']['speed'] * 3.6); ?> km/h</p>
        </div>
    <?php endif; ?>
</body>
</html>