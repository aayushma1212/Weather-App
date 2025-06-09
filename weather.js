document.addEventListener('DOMContentLoaded', function () {
    const apiKey = 'e7416bde22a62c4dcf3f8bb31ddc1b8f'; 
    const defaultCity = 'Lancaster'; 
    const cityInput = document.getElementById('city');
    const searchButton = document.querySelector('.searchButton');
    const weatherIcon = document.querySelector('.weatherIcon');

    cityInput.value = defaultCity;
    fetchWeatherData(defaultCity);

    // GET WEATHER DATA FOR SEARCHED CITY 
    searchButton.addEventListener('click', function () {
        const city = cityInput.value.trim();
        if (city !== '') {
            fetchWeatherData(city);
        } else {
            alert('Please enter a city name.');
        }
    });

    async function fetchWeatherData(city) {
        const apiUrl = `https://api.openweathermap.org/data/2.5/weather?q=${city}&units=metric&appid=${apiKey}`;
        try {
            const response = await fetch(apiUrl);
            if (!response.ok) {
                throw new Error('Response not ok');
            }
            const data = await response.json();
            updateWeather(data);
            // Fetch additional data
            fetchAdditionalData(data.name);
        } catch (error) {
            console.error('Error:', error);
            alert('Error. Please try again.');
        }
    }

    async function fetchAdditionalData(cityName) {
        const response = await fetch(`http://localhost/weatherprototype2/weatherr.php?q=${cityName}`);
    
    }

    // DISPLAY WEATHER DATA
    function updateWeather(data) {
        document.querySelector('.dayDate').textContent = new Date().toLocaleDateString('en-GB', { weekday: 'long', month: 'long', day: 'numeric' });
        document.querySelector('.cityName').textContent = data.name;
        document.querySelector('.temp').textContent = `${Math.round(data.main.temp)}°C`;
        document.querySelector('.weather_condition').textContent = data.weather[0].description;
        document.querySelector('.humidity p').textContent = `${data.main.humidity}%`;
        document.querySelector('.windspeed p').textContent = `${data.wind.speed}m/s`;
        document.querySelector('.pressure p').textContent = `${data.main.pressure}hPa`;

        //  CHANGE WEATHER ICON AS PER THE CONDITION
        const iconCode = data.weather[0].icon;
        const iconUrl = `https://openweathermap.org/img/w/${iconCode}.png`;
        weatherIcon.src = iconUrl;
    }
});
