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

?>

<!DOCTYPE html>
<html>
<head>
    <title>EVoting - Login</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.0/css/bootstrap.min.css">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <style>
        body {
            font-family: Arial, Helvetica, sans-serif;
            background-image: url(profile/election.jpg);
            background-size: cover;
            background-position: center;
        }
        form {border: 3px solid #f1f1f1;}

        input[type=text], input[type=password] {
            width: 100%;
            padding: 12px 20px;
            margin: 8px 0;
            display: inline-block;
            border: 1px solid #ccc;
            box-sizing: border-box;
        }

        #button {
            background-color: #4CAF50;
            color: white;
            padding: 14px 20px;
            margin: 8px 0;
            border: none;
            cursor: pointer;
            width: 100%;
        }

        #button:hover {
            opacity: 0.8;
        }

        span.psw {
            float: right;
            padding-top: 16px;
        }

        /* Change styles for span and cancel button on extra small screens */
        @media screen and (max-width: 300px) {
            span.psw {
                display: block;
                float: none;
            }
        }
        h2, label[for="voter_id"], label[for="password"] {
            color: white; /* Set the color of the h2, Voting Id, and Password labels to white */
        }
    </style>
</head>
<body>

    <h2 class="text-center my-3">Voter's Login</h2>

    <div class="container">
        <?php
        if(isset($_POST['login']))
        {
            $voter_id = $_POST['voter_id'];
            $password = $_POST['password'];
            $selectedCounty = $_POST['county'];
            $selectedConstituency = $_POST['constituency'];

            $sql = "SELECT * FROM voters WHERE voters_id='$voter_id' AND password='$password' AND county='$selectedCounty' AND constituency='$selectedConstituency'";
            $q = mysqli_query($con, $sql);
            if(mysqli_num_rows($q) > 0)
            {
                $get_voters_details = mysqli_fetch_array($q);
                $get_voters_name = $get_voters_details['name'];
                $_SESSION['voter_id'] = $voter_id;
                $_SESSION['voter_name'] = $get_voters_name;
                header('Location:voting.php');
            }
            else
            {
                echo '<div class="alert alert-warning alert-dismissible fade show" role="alert"><strong>Login Failed</strong><button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button></div>';
            }
            
        }
        ?>
        <div class="row">
            <div class="col-md-3"></div>
            <div class="col-md-6">
                <form action="" method="post">
                    <label for="voter_id"><b>Voting Id</b></label>
                    <input type="text" placeholder="Enter Username" name="voter_id" id="voter_id" autofocus required>

                    <label for="password"><b>Password</b></label>
                    <input type="password" placeholder="Enter Password" name="password" id="password" required>

                    <!-- Add dropdown for Counties -->
                    <div class="dropdown-wrapper">
                        <select name="county" id="county" required onchange="this.form.submit()">
                            <option value="">Select County</option>
                            <?php foreach ($counties as $county): ?>
                                <option value="<?php echo $county['id']; ?>" <?php if ($county['id'] == $selectedCounty) echo 'selected'; ?>>
                                    <?php echo $county['county']; ?>
                                </option>
                            <?php endforeach; ?>
                        </select>

                    <!-- Add dropdown for Constituencies -->
                    <select name="constituency" id="constituency" required onchange="">
                            <option value="">Select Constituency</option>
                            <?php foreach ($constituencies as $constituency): ?>
                                <option value="<?php echo $constituency['constituency']; ?>" <?php if ($constituency['constituency'] == $selectedConstituency) echo 'selected'; ?>>
                                    <?php echo $constituency['constituency']; ?>
                                </option>
                            <?php endforeach; ?>
                        </select>

                    <button type="submit" id="button" name="login">Login</button>
                </form>
                <p class="text-center">Not registered? <a href="voter_registration.php">Click here to register.</a></p>
            </div>
            <div class="col-md-3"></div>
        </div>
    </div>

    <script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/popper.js@1.16.0/dist/umd/popper.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.0/js/bootstrap.min.js"></script>
</body>
</html>
