<?php
session_start();
include 'conn.php';

if (empty($_SESSION['voter_name'])) {
    header("Location: index.php");
    exit();
}

// Fetch selected county and constituency from session variables
$selectedCounty = $_SESSION['selected_county'];
$selectedConstituency = $_SESSION['selected_constituency'];

// Fetch candidates from the selected county and constituency, grouped by category
$candidateSql = "
    SELECT * FROM candidates
    WHERE (can_county = '$selectedCounty' OR category = 'President' OR category = 'Governor' OR category = 'Senator')
    AND (can_constituency = '$selectedConstituency' OR category = 'President' OR category = 'Governor' OR category = 'Senator')
    ORDER BY category, can_id
";
$candidateResult = mysqli_query($con, $candidateSql);

// Check for query execution error
if (!$candidateResult) {
    echo "Error: " . mysqli_error($con);
    exit();
}

$candidatesByCategory = array();

while ($candidate = mysqli_fetch_assoc($candidateResult)) {
    $categoryId = getCategoryFromCanId($candidate['can_id']);
    $candidatesByCategory[$categoryId][] = $candidate;
}

function getCategoryFromCanId($canId) {
    $canId = (int) $canId;

    if ($canId >= 1 && $canId <= 9) {
        return 1; // President
    } elseif ($canId >= 10 && $canId <= 99) {
        return 2; // Governor
    } elseif ($canId >= 100 && $canId <= 999) {
        return 3; // Senator
    }

    return 0; // Unknown category
}

// Handle form submission
if (isset($_POST['submit_vote'])) {
    $candidateIds = $_POST['candidate_id'];
    $voterId = $_POST['voter_id'];

    // Ensure only one candidate is selected in each category
    $selectedCategories = array();
    foreach ($candidateIds as $candidateId) {
        $categoryId = getCategoryFromCanId($candidateId);
        if (in_array($categoryId, $selectedCategories)) {
            echo '<script>alert("Please select only one candidate in each category")</script>';
            exit();
        }
        $selectedCategories[] = $categoryId;
    }

    // Check if three candidates are selected
    if (count($selectedCategories) !== 3) {
        echo '<script>alert("Please select three candidates, one from each category")</script>';
        exit();
    }

    // Insert votes into the votes table
    foreach ($candidateIds as $candidateId) {
        $insertVoteSql = "INSERT INTO votes (voter_id, can_id) VALUES ('$voterId', '$candidateId')";
        $insertVoteResult = mysqli_query($con, $insertVoteSql);

        if (!$insertVoteResult) {
            echo '<script>alert("Failed to Vote")</script>';
            exit();
        }
    }

    echo '<script>alert("Voted Successfully")</script>';
    echo '<script>window.location="'.$base_url.'vlogout.php"</script>';
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Voters Dashboard</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.0/css/bootstrap.min.css">
</head>
<body>
    <nav class="navbar navbar-expand-lg navbar-dark bg-dark">
        <a class="navbar-brand" href="#">EVoting</a>
        <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarSupportedContent" aria-controls="navbarSupportedContent" aria-expanded="false" aria-label="Toggle navigation">
            <span class="navbar-toggler-icon"></span>
        </button>

        <div class="collapse navbar-collapse" id="navbarSupportedContent">
            <ul class="navbar-nav mr-auto">
                <li class="nav-item active">
                    <a class="nav-link" href="voting.php">Home <span class="sr-only">(current)</span></a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="<?php echo $base_url ?>vlogout.php">Logout</a>
                </li>
            </ul>
        </div>
    </nav>

    <h1 class="text-center mt-2"><u>Candidate List</u></h1>
    <div class="container mt-3">
        <form action="" method="post">
            <table class="table table-striped table-hover text-center">
                <thead>
                    <tr>
                        <th>Candidate ID</th>
                        <th>Party Symbol</th>
                        <th>Candidate Image</th>
                        <th>Name</th>
                        <th>Vote</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($candidatesByCategory as $categoryId => $categoryCandidates): ?>
                        <?php if ($categoryId === 1): ?>
                            <tr style="border-top: 2px solid blue;">
                                <td colspan="5" class="text-center"><strong>President</strong></td>
                            </tr>
                        <?php elseif ($categoryId === 2): ?>
                            <tr style="border-top: 2px solid blue;">
                                <td colspan="5" class="text-center"><strong>Governor</strong></td>
                            </tr>
                        <?php elseif ($categoryId === 3): ?>
                            <tr style="border-top: 2px solid blue;">
                                <td colspan="5" class="text-center"><strong>Senator</strong></td>
                            </tr>
                        <?php endif; ?>
                        <?php foreach ($categoryCandidates as $candidate): ?>
                            <tr <?php if ($categoryId !== 1) echo 'style="border-top: 2px solid blue;"'; ?>>
                                <td class="pt-4"><h1><?php echo $candidate['can_id']; ?></h1></td>
                                <td><img src="<?php echo $base_url ?>profile/<?php echo $candidate['can_party_symbol']; ?>" alt="Party Symbol" width="100"></td>
                                <td><img src="<?php echo $base_url ?>profile/<?php echo $candidate['can_image']; ?>" alt="Candidate Image" width="100"></td>
                                <td><h3 class="pt-4"><?php echo $candidate['can_name']; ?></h3></td>
                                <td>
                                    <div class="form-check form-check-inline">
                                        <input class="form-check-input" type="checkbox" name="candidate_id[]" value="<?php echo $candidate['can_id']; ?>">
                                    </div>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php endforeach; ?>
                </tbody>
            </table>
            <div class="text-center mt-3">
                <input type="hidden" name="voter_id" value="<?php echo $_SESSION['voter_id']; ?>">
                <button type="submit" name="submit_vote" class="btn btn-info">Submit Vote</button>
            </div>
        </form>
    </div>

    <script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.0/js/bootstrap.min.js"></script>
    <script>
        function resetCheckboxes() {
            document.querySelectorAll('input[type="checkbox"]').forEach(function (checkbox) {
                checkbox.checked = false;
            });
        }
    </script>
</body>
</html>
