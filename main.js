document.getElementById('weather-form').addEventListener('submit', function(event) {
    document.getElementById('location-info').style.display = 'none';
    document.getElementById('currency-converted').style.display = 'none';
    var flagContainer = document.getElementById('flag');
    while (flagContainer.firstChild) {
        flagContainer.removeChild(flagContainer.firstChild);
    }

    event.preventDefault();

    var location = document.getElementById('location').value;
    var date = document.getElementById('date').value;

    fetch('informationGet.php?location=' + location + '&date=' + date)
        .then(response => response.json())
        .then(data => {
            document.getElementById('location-name').textContent = location;

            if(data !== null) {
                document.getElementById('weather-description').textContent = 'Weather: ' + data.description;
                document.getElementById('weather-temperature').textContent = 'Temperature: ' + data.temperature + '°C';
                document.getElementById('avg-temperature').textContent = 'Avg temperature: ' + data.avg + '°C';
                document.getElementById('country').textContent = 'Country: ' + data.country;
                document.getElementById('capital').textContent = 'Capital: ' + data.capital;
                document.getElementById('currency').textContent = 'Currency: ' + data.currency;

                if (data.flag) {
                    var flagImg = document.createElement('img');
                    flagImg.src = data.flag;
                    flagImg.alt = 'Flag';
                    flagContainer.appendChild(flagImg);
                }

                if (data.converted_currency) {
                    document.getElementById('currency-converted').textContent = 'Converted Currency: ' + data.converted_currency.toFixed(2) + ' EUR';
                    document.getElementById('currency-converted').style.display = 'block';
                }
                document.getElementById('location-info').style.display = 'block';
            }

        })
        .catch(error => {
            console.error('Error:', error);
        });
});
