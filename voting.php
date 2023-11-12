<?php
    session_start();
    include 'conn.php';
    if(empty($_SESSION['voter_name']))
    {
        header("Location:index.php");
    }
    $voter_id = $_SESSION['voter_id'];
    $check_vote = "SELECT * FROM votes WHERE voter_id='$voter_id'";
    $check_vote_result = mysqli_query($con,$check_vote);
    if($check_vote_result->num_rows > 0)
    {
        header("Location:voting_done.php");
    }
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.0/css/bootstrap.min.css">
    <title>EVoting</title>
    <style>
        fieldset {
        background-color: #eeeeee;
        }

        legend {
        background-color: gray;
        color: white;
        padding: 5px 10px;
        }

        input {
        margin: 5px;
        }
</style>
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
                <a class="nav-link" href="#">Home <span class="sr-only">(current)</span></a>
            </li>
            <li class="nav-item">
                <a class="nav-link" href="<?php echo $base_url?>vlogout.php">Logout</a>
            </li>
        </ul>
    </div>
    </nav>
    
    <div class="container mt-3 border rounded">
        <h3><u><b>Terms and Conditions:</u></b></h3>
        <p class="text-justify" >Corruption is a cancer that eats away at the fabric of society. Let us join hands and stand united against this menace, for a future built on integrity, transparency, and justice.</p>
        <input type="checkbox" name="checkbox" id="checkbox"><b>I agree to the Terms and Conditions</b>
        <div class="text-center">
            <a href="voting_page.php"><button class="btn btn-primary mb-3" id="agree">I agree</button></a>
        </div>
    </div>

    <script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/popper.js@1.16.0/dist/umd/popper.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.0/js/bootstrap.min.js"></script>
    <script>
        $(document).ready(function () {
            $('#agree').attr('disabled', true);
            $('#checkbox').click(function(){
            if($(this).prop("checked") == true){
                $('#agree').attr('disabled', false);
            }
            else if($(this).prop("checked") == false){
                $('#agree').attr('disabled', true);
            }
            });
        });
            
    </script>
</body>
</html>