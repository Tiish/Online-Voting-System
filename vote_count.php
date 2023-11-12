<?php
session_start();
include 'conn.php';
?>

<!doctype html>
<html lang="en">

<head>
    <!-- Required meta tags -->
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">

    <!-- Bootstrap CSS -->
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.0/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/font-awesome/4.7.0/css/font-awesome.min.css">

    <title>EVoting - Control</title>
</head>

<body>
    <div class="container">
        <div class="row">
            <div class="col-12 text-right">
                <a href="control.php" class="btn btn-primary mt-2">Back</a>
            </div>
        </div>
    </div>

    <h1 class="text-center mt-2"><u>Candidate List with Votes</u></h1>
    <div class="container mt-3">
        <table class="table table-striped table-hover text-center">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Candidate ID</th>
                    <th>Name</th>
                    <th>Candidate Image</th>
                    <th>Party Symbol</th>
                    <th>Votes Count</th>
                </tr>
            </thead>
            <tbody>
                <?php
                $fetch_candidate_data = "SELECT candidates.*, IFNULL(COUNT(votes.id), 0) as vote_count 
                                        FROM candidates 
                                        LEFT JOIN votes ON candidates.can_id = votes.can_id 
                                        GROUP BY candidates.id";
                $result_candidate_data = mysqli_query($con, $fetch_candidate_data);

                if ($result_candidate_data && mysqli_num_rows($result_candidate_data) > 0) {
                    while ($res = mysqli_fetch_array($result_candidate_data)) {
                        ?>
                        <tr>
                            <td class="pt-4"><h1><?php echo $res['id'] ?></h1></td>
                            <td class="pt-4"><h1><?php echo $res['can_id'] ?></h1></td>
                            <td><h3 class="pt-4"><?php echo $res['can_name'] ?></h3></td>
                            <td><img src="<?php echo $base_url ?>profile/<?php echo $res['can_image'] ?>" alt="image" width="100"></td>
                            <td><img src="<?php echo $base_url ?>profile/<?php echo $res['can_party_symbol'] ?>" alt="party" width="100"></td>
                            <td><h3 class="pt-4"><?php echo $res['vote_count'] ?></h3></td>
                        </tr>
                    <?php
                    }
                } else {
                    ?>
                    <tr>
                        <td colspan="6"><?php echo 'No Data Available' ?></td>
                    </tr>
                <?php
                }
                ?>
            </tbody>
        </table>
    </div>

    <!-- Optional JavaScript -->
    <!-- jQuery first, then Popper.js, then Bootstrap JS -->
    <script src="https://code.jquery.com/jquery-3.5.1.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/popper.js@1.16.0/dist/umd/popper.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.0/js/bootstrap.min.js"></script>
</body>

</html>
