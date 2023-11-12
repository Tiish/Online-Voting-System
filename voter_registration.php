<?php
session_start();
include 'conn.php';

// Fetch counties from the database
$countySql = "SELECT * FROM counties";
$countyResult = mysqli_query($con, $countySql);
$counties = mysqli_fetch_all($countyResult, MYSQLI_ASSOC);

// Set the selected county and constituency if the form is submitted
$selectedCounty = isset($_POST['county']) ? $_POST['county'] : '';
$selectedConstituency = isset($_POST['constituency']) ? $_POST['constituency'] : '';

// Fetch constituencies based on the selected county
$constituencySql = "SELECT * FROM constituencies WHERE county_id = '$selectedCounty'";
$constituencyResult = mysqli_query($con, $constituencySql);
$constituencies = mysqli_fetch_all($constituencyResult, MYSQLI_ASSOC);

// Set the session variables
$_SESSION['selected_county'] = $selectedCounty; // Updated variable name
$_SESSION['selected_constituency'] = $selectedConstituency; // Updated variable name

// Handle form submission
if (isset($_POST['register'])) {
    $voter_id = $_POST['voter_id'];
    $name = $_POST['name'];
    $password = $_POST['password'];
    $county_id = $_POST['county'];
    $constituency_id = $_POST['constituency'];

    // Perform validation, e.g., check if voter_id is unique, etc.

    // Insert the voter data into the database
    $insertSql = "INSERT INTO voters (voters_id, name, password, county, constituency) 
                  VALUES ('$voter_id', '$name', '$password', '$county_id', '$constituency_id')";

    if (mysqli_query($con, $insertSql)) {
        // Registration successful
        $_SESSION['registration_success'] = true;
    } else {
        // Registration failed
        $_SESSION['registration_success'] = false;
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Voter Registration</title>
    <style>
        body {
			font-family: Arial, Helvetica, sans-serif;
			background-image: url(profile/election.jpg);
			background-size: cover;
			background-position: center;
		}

        .container {
            max-width: 400px;
            margin: 50px auto;
            padding: 20px;
            background-color: #fff;
            border: 1px solid #ccc;
            border-radius: 5px;
        }

        h2 {
            text-align: center;
            margin-bottom: 20px;
        }

        .form-group {
            margin-bottom: 15px;
        }

        label {
            display: block;
            font-weight: bold;
        }

        input[type="text"],
        input[type="password"],
        select {
            width: 100%;
            padding: 10px;
            border: 1px solid #ccc;
            border-radius: 5px;
        }

        input[type="submit"] {
            background-color: #4CAF50;
            color: #fff;
            padding: 10px 20px;
            border: none;
            border-radius: 5px;
            cursor: pointer;
        }

        input[type="submit"]:hover {
            background-color: #45a049;
        }

        .error-message {
            color: red;
            margin-top: 10px;
        }
    </style>
</head>
<body>
    <div class="container">
        <h2>Voter Registration</h2>
        <form method="post">
            <div class="form-group">
                <label for="voter_id">Voter ID:</label>
                <input type="text" id="voter_id" name="voter_id" required>
            </div>
            <div class="form-group">
                <label for="name">Name:</label>
                <input type="text" id="name" name="name" required>
            </div>
            <div class="form-group">
                <label for="password">Password:</label>
                <input type="password" id="password" name="password" required>
            </div>
            <div class="form-group">
                <label for="county">County:</label>
                <select id="county" name="county" required onchange="this.form.submit()">
                    <option value="">Select County</option>
                    <?php foreach ($counties as $county): ?>
                        <option value="<?php echo $county['id']; ?>" <?php if ($county['id'] == $selectedCounty) echo 'selected'; ?>>
                            <?php echo $county['county']; ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="form-group">
                <label for="constituency">Constituency:</label>
                <select id="constituency" name="constituency" required onchange="">
                    <option value="">Select Constituency</option>
                    <?php foreach ($constituencies as $constituency): ?>
                        <option value="<?php echo $constituency['constituency']; ?>" <?php if ($constituency['constituency'] == $selectedConstituency) echo 'selected'; ?>>
                            <?php echo $constituency['constituency']; ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="form-group">
                <input type="submit" value="Register" name="register">
            </div>
            <div class="form-group">
                <a href="index.php"><button type="button">Go to Login</button></a>
            </div>
        </form>
    </div>

    <script>
        const countySelect = document.getElementById("county");
        const constituencySelect = document.getElementById("constituency");

        countySelect.addEventListener("change", () => {
            const selectedCountyId = countySelect.value;
            constituencySelect.innerHTML = "<option value=''>Select Constituency</option>";

            if (selectedCountyId !== "") {
                fetch(`/get_constituencies.php?county_id=${selectedCountyId}`)
                    .then(response => response.json())
                    .then(data => {
                        data.forEach(constituency => {
                            const option = document.createElement("option");
                            option.value = constituency.id;
                            option.textContent = constituency.constituency;
                            constituencySelect.appendChild(option);
                        });
                    })
                    .catch(error => {
                        console.error("Error fetching constituencies:", error);
                    });
            }
        });

        // Check if registration was successful and show the popup message
        <?php
        if (isset($_SESSION['registration_success']) && $_SESSION['registration_success']) {
            echo 'alert("Registration successful!");';
            unset($_SESSION['registration_success']); // Clear the message after displaying
        }
        ?>
    </script>
</body>
</html>
