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

// Handle form submission
if (isset($_POST['add_candidate'])) {
    $candidate_id = $_POST['can_id'];
    $candidate_name = $_POST['can_name'];
    $candidate_party_image = $_FILES['can_party']['name'];
    $candidate_image = $_FILES['can_image']['name'];
    $county_id = $_POST['county'];
    $constituency_id = $_POST['constituency'];

    // Upload candidate images to a directory on the server
    $target_dir = "candidate_images/";
    $target_party_image = $target_dir . basename($candidate_party_image);
    $target_candidate_image = $target_dir . basename($candidate_image);
    
    // Perform validation and other checks, e.g., candidate ID uniqueness, etc.

    // Insert candidate data into the database
    $insertSql = "INSERT INTO candidates (can_id, can_name, can_party_symbol, can_image, can_county, can_constituency) 
                  VALUES ('$candidate_id', '$candidate_name', '$candidate_party_image', '$candidate_image', '$county_id', '$constituency_id')";

    if (mysqli_query($con, $insertSql)) {
        // Candidate addition successful
        $_SESSION['candidate_added'] = true;
        header("Location: control.php"); // Redirect to control.php
        exit();
    } else {
        // Candidate addition failed
        $_SESSION['candidate_added'] = false;
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Add Candidate</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.0/css/bootstrap.min.css">
</head>
<body>

<div class="container mt-5">
    <div class="row justify-content-center">
        <div class="col-md-6">
            <h2 class="mb-4">Add Candidate</h2>
            <form action="add_php.php" method="POST" enctype="multipart/form-data">
                <div class="form-group">
                    <label for="can_id">Candidate ID:</label>
                    <input type="text" name="can_id" id="can_id" class="form-control" required>
                </div>
                <div class="form-group">
                    <label for="can_name">Candidate Name:</label>
                    <input type="text" name="can_name" id="can_name" class="form-control" required>
                </div>
                <div class="form-group">
                    <label for="can_party">Candidate Party Image:</label>
                    <input type="file" name="can_party" id="can_party" class="form-control" required>
                </div>
                <div class="form-group">
                    <label for="can_image">Candidate Image:</label>
                    <input type="file" name="can_image" id="can_image" class="form-control" required>
                </div>
                <!-- County Dropdown -->
                <div class="form-group">
                    <label for="county">County:</label>
                    <select id="county" name="county" class="form-control" required onchange="this.form.submit()">
                        <option value="">Select County</option>
                        <?php foreach ($counties as $county): ?>
                            <option value="<?php echo $county['id']; ?>" <?php if ($county['id'] == $selectedCounty) echo 'selected'; ?>>
                                <?php echo $county['county']; ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <!-- Constituency Dropdown -->
                <div class="form-group">
                    <label for="constituency">Constituency:</label>
                    <select id="constituency" name="constituency" class="form-control" required>
                        <option value="">Select Constituency</option>
                        <?php foreach ($constituencies as $constituency): ?>
                            <option value="<?php echo $constituency['constituency']; ?>" <?php if ($constituency['constituency'] == $selectedConstituency) echo 'selected'; ?>>
                                <?php echo $constituency['constituency']; ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <button type="submit" name="add_candidate" class="btn btn-primary">Add Candidate</button>
				<a href="control.php" class="btn btn-secondary">Back</a> <!-- Added Back button -->
            </form>
        </div>
    </div>
</div>

<script>
    // Check if candidate addition was successful and show the popup message
    <?php
    if (isset($_SESSION['candidate_added']) && $_SESSION['candidate_added']) {
        echo 'alert("Candidate added successfully!");';
        unset($_SESSION['candidate_added']); // Clear the message after displaying
    }
    ?>
</script>

</body>
</html>
