<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Registration</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" integrity="sha384-T3c6CoIi6uLrA9TneNEoa7RxnatzjcDSCmG1MXxSR1GAsXEV/Dwwykc2MPK8M2HN" crossorigin="anonymous">
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <div class="container">
        <?php
        include "database.php";

        if ($_SERVER["REQUEST_METHOD"] == "POST") {
            $fullname = $_POST["fullname"];
            $email = $_POST["email"];
            $password = $_POST["password"];
            $repeat_password = $_POST["repeat_password"];
            $country = $_POST["country"];
            $phone = $_POST["phone"]; // Updated phone field
            $state = $_POST["state"];
            $city = $_POST["city"];
            $barangay = $_POST["barangay"];

            // Check if passwords match
            if ($password !== $repeat_password) {
                echo "<div class='alert alert-danger'>Passwords do not match.</div>";
            } else {
                // Hash the password
                $passwordHash = password_hash($password, PASSWORD_DEFAULT);

                // Prepare the SQL statement
                $stmt = $conn->prepare("INSERT INTO users (full_name, email, password, country, phone, state, city, barangay) VALUES (?, ?, ?, ?, ?, ?, ?, ?)");

                // Bind parameters and execute the statement
                $stmt->bind_param("ssssssss", $fullname, $email, $passwordHash, $country, $phone, $state, $city, $barangay);
                if ($stmt->execute()) {
                    echo "<div class='alert alert-success'>You are registered successfully.</div>";
                } else {
                    echo "<div class='alert alert-danger'>Error: " . $stmt->error . "</div>";
                }

                // Close the statement
                $stmt->close();
            }
        }

        // Close the connection
        $conn->close();
        ?>
        <form action="registration.php" method="post">
            <div class="form-group">
                <input type="text" class="form-control" name="fullname" placeholder="Full Name:" required>
            </div>
            <div class="form-group">
                <input type="email" class="form-control" name="email" placeholder="Email:" required>
            </div>
            <div class="form-group">
                <input type="password" class="form-control" name="password" placeholder="Password:" required>
            </div>
            <div class="form-group">
                <input type="password" class="form-control" name="repeat_password" placeholder="Repeat Password:" required>
            </div>
            <div class="form-group">
                <select id="country" class="form-control" name="country" required></select>
            </div>
            <div class="form-group">
                <label for="phone">Phone Number:</label>
                <div class="input-group">
                    <div class="input-group-prepend">
                        <span id="phonecode" class="input-group-text"></span>
                    </div>
                    <input type="tel" class="form-control" name="phone" placeholder="Phone Number (with country code):" required>
                </div>
            </div>
            <div class="form-group">
                <select id="state" class="form-control" name="state" required></select>
            </div>
            <div class="form-group">
                <select id="city" class="form-control" name="city" required></select>
            </div>
            <div class="form-group">
                <input type="text" class="form-control" name="barangay" placeholder="Barangay:" required>
            </div>
            <div class="form-btn">
                <input type="submit" class="btn btn-primary" value="Register" name="submit">
                <a href="index.php" class="btn btn-secondary">Back</a>
            </div>
        </form>
        <div><p>Already Registered?<a href="login.php">Login Here</a></p></div>
    </div>

    <script>
        let data = [];

        document.addEventListener('DOMContentLoaded', function() {
            fetch('https://raw.githubusercontent.com/dr5hn/countries-states-cities-database/master/countries%2Bstates%2Bcities.json')
                .then(response => response.json())
                .then(jsonData => {
                    data = jsonData;
                    const countries = data.map(country => country.name);
                    populateDropdown('country', countries);
                })
                .catch(error => console.error('Error fetching countries:', error));
        });

        function populateDropdown(dropdownId, data) {
            const dropdown = document.getElementById(dropdownId);
            dropdown.innerHTML = '';
            data.forEach(item => {
                const option = document.createElement('option');
                option.value = item;
                option.text = item;
                dropdown.add(option);
            });
        }

        document.getElementById('country').addEventListener('change', function() {
            const selectedCountry = this.value;
            const countryData = data.find(country => country.name === selectedCountry);
            if (countryData && countryData.phone_code) {
                document.getElementById('phonecode').textContent = '+' + countryData.phone_code;
            }
            if (countryData && countryData.states) {
                const states = countryData.states.map(state => state.name);
                populateDropdown('state', states);
            }
        });

        document.getElementById('state').addEventListener('change', function() {
            const selectedCountry = document.getElementById('country').value;
            const selectedState = this.value;
            const countryData = data.find(country => country.name === selectedCountry);
            if (countryData) {
                const stateData = countryData.states.find(state => state.name === selectedState);
                if (stateData && stateData.cities) {
                    const cities = stateData.cities.map(city => city.name);
                    populateDropdown('city', cities);
                }
            }
        });
    </script>
</body>
</html>
