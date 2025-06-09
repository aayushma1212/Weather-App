<?php

$serverName = "localhost";
$userName= "root";
$password = "";


$conn = mysqli_connect($serverName, $userName, $password);
if($conn){
    echo "Connection Successful.";
}
else{
    echo "Failed to connect".mysqli_connect_error();
}

$createDatabase = "CREATE DATABASE IF NOT EXISTS databasee_weather";
if (mysqli_query($conn, $createDatabase)) {
    echo "Database Created.";
} else {
    echo "Failed to create database " . mysqli_connect_error();
}

// Select the created database

mysqli_select_db($conn, 'databasee_weather');

$createTable = "CREATE TABLE IF NOT EXISTS `weather`(
    // Encoding fetched data to JSON and sending as response
      
       `humidity` FLOAT NOT NULL,
       `wind` FLOAT NOT NULL,
       `pressure` FLOAT NOT NULL,
       `dayDate` INT(255),
       `city` VARCHAR(255),
       `weatherIcon` VARCHAR(255),
       `temp` FLOAT,
       `weather_condition` VARCHAR(255)

   )";

   if (mysqli_query($conn, $createTable)) {
       echo "Table Created or already Exists ";
   } else {
       echo "Failed to create database <br>" . mysqli_connect_error();
   }
   

if(isset($_GET['q'])){
    $cityName = $_GET['q'];
    echo $cityName;
}else{
    $cityName ="Lancaster";
}
$selectAllData = "SELECT * FROM weather WHERE city= '$cityName'";
$result = mysqli_query($conn, $selectAllData);

if (mysqli_num_rows($result) > 0) {
    // Checking if existing data is less than 2 hours old
    $row = mysqli_fetch_assoc($result);
    $lastUpdateTime = $row['dayDate'];
    $currentTime = time();
    $dataAgeInSeconds = $currentTime - $lastUpdateTime;
    $validDataThreshold = 7200;

    if ($dataAgeInSeconds <= $validDataThreshold) {
    
        $rows[] = $row;
    }
}
if (mysqli_num_rows($result) == 0) {
    $apiKey="e7416bde22a62c4dcf3f8bb31ddc1b8f";
    $url ='https://api.openweathermap.org/data/2.5/weather?q=Lancaster&appid=e7416bde22a62c4dcf3f8bb31ddc1b8f';
    $response = file_get_contents($url);
    $data = json_decode($response, true); 
    $humidity = $data['main']['humidity'];
    $wind = $data['wind']['speed'];
    $pressure = $data['main']['pressure'];
    $dayDate = time(); 
    $city= $data['name'];
    $weatherIcon = $data['weather'][0]['icon'];
    $temp = $data['main']['temp'];
    $weather_condition = $data['weather'][0]['description'];


$insertData= "INSERT INTO weather (humidity, wind, pressure, dayDate, city, weatherIcon, temp, weather_condition)
        VALUES ('$humidity', '$wind', '$pressure', '$dayDate', '$city', '$weatherIcon', '$temp','$weather_condition')
        ON DUPLICATE KEY UPDATE humidity = '$humidity', wind = '$wind', pressure = '$pressure', dayDate = '$dayDate',
            weatherIcon = '$weatherIcon', temp = '$temp', weather_condition = '$weather_condition'";
 

    if (mysqli_query($conn, $insertData)) {
        // echo "Data inserted Successfully";
    } else {
        // echo "Failed to insert data" . mysqli_error($conn);
    }
}

// Fetching data from weather table based on city name again after insertion

$result = mysqli_query($conn, $selectAllData);
while ($row = mysqli_fetch_assoc($result)) {
    $rows[] = $row;
}

// Encoding fetched data to JSON and sending as response

$json_data = json_encode($rows);
echo $json_data;

header('Content-Type: application/json');


?>

