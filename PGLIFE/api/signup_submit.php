<?php
  // If user is already signed in, and this signup_submit.php is requested for, the user will automatically get logged out first.
  session_start();
  session_destroy();

  require "../includes/database_connect_hide_error.php";

  if( !$con )
  {
    // echo mysqli_connect_error();
    //  exit();
    $response = array("success" => false, "message" => "Database Connectivity Error!");
    echo json_encode($response);
    return;
  }
  else {
    $full_name = $_POST["full_name"];
    $phone = $_POST["phone"];
    $email = $_POST["email"];

    $password = $_POST["password"];
    // If password is to be stored in hashed format.
    $password = sha1($password);

    $college_name = $_POST["college_name"];
    if( isset($_POST["gender"]) )
      {$gender = $_POST["gender"];}

    $sql_query1 = "SELECT * FROM users where email='$email';";
    $result1 = mysqli_query($con,$sql_query1);

    if ( !$result1 ) {
      $response = array("success" => false, "message" => "Something went wrong!");
      echo json_encode($response);
      return;
    }

    //When already the email is registered by another user previosly
    $row_count = mysqli_num_rows($result1);
    if ($row_count != 0) {
      $response = array("success" => false, "message" => "This email id is already registered with us!");
      echo json_encode($response);
      return;
    }

    $result2 = FALSE;
    if( mysqli_num_rows($result1)==0 ){
      $sql_query2 = "INSERT INTO users (full_name,phone,email,password,college_name,gender) VALUES ('$full_name','$phone','$email','$password','$college_name','$gender');";
      $result2 = mysqli_query($con,$sql_query2);

      if ( !$result2 ) {
        $response = array("success" => false, "message" => "Something went wrong! Registration Unsuccessfull!");
        echo json_encode($response);
        return;
      }
      else {
        $response = array("success" => true, "message" => "User is successfully registered!");
        echo json_encode($response);
        return;
      }
    }
  }

?>

<!--
<!DOCTYPE html>
<html lang="en">

<head>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>
      <?php
        // if( $result2 ){
        //   echo "Signup Successfull";
        // }
        // else{
        //   echo "Signup Error";
        // }
      ?>
    </title>

    <link href="../css/bootstrap.min.css" rel="stylesheet" />
    <link href="https://use.fontawesome.com/releases/v5.11.2/css/all.css" rel="stylesheet" />
    <link href="https://fonts.googleapis.com/css2?family=Open+Sans:ital,wght@0,300;0,400;0,600;0,700;0,800;1,300;1,400;1,600;1,700;1,800&display=swap" rel="stylesheet" />
    <link href="../css/common.css" rel="stylesheet" />
</head>

-->

<!-- <body> -->
  <!-- Header Section -->
  <?php // require "../includes/header.php"; ?>

  <!-- <div class="signup-content mx-4 my-5"> -->
    <?php
    /*
      if( !$con ){
        echo "
          <h2>Database Connectivity Error!</h2>
          <hr />
          <p>Account Creation Unsuccessfull!</p>
        ";
        echo "<h5> ".mysqli_connect_error()." </h5>";
      }
    */
    ?>

    <?php
    // When email entered by user during signup is already registered in the database
    /*
      if( mysqli_num_rows($result1)!=0 ){
    ?>
      <div class="">
        <h2>Email "<?php echo $email; ?>" is already registered with us!</h2>
        <hr />
        <p>Please sign in, or try registering with a different email account...</p>
      </div>
    <?php
      }
    */
    ?>

    <?php
    // Upon Successfull Registration...
    // PHP page to be rendered if sync request come, i.e, non-AJAX request
      // if( $result2 )
      // {
    ?>
    <!--
      <div class="">
        <h2>Dear <?php // echo $full_name ?>, your account is successfully created!</h2>
        <p>Please login to your account to continue searching for your favourite PGs!</p>
        <div class="text-center mt-3 mb-5">

          <a class="" href="#" data-toggle="modal" data-target="#login-modal">
              <i class="fas fa-sign-in-alt"></i>Login
          </a>
        </div>
      </div>
    -->

    <?php
      // }
      // When email entered is uniques but due to some reason $sql_query2 cannot be successfully executed, i.e, user details cannot be added to database
      // elseif( !$result2 && mysqli_num_rows($result1)==0 )
      // {
      //   echo "
      //     <h2>Error in Data Entry!</h2>
      //     <hr />
      //     <p>Account Creation Unsuccessfull! Try with some different data in the fields...</p>
      //   ";
      //   echo "<h5> ".mysqli_error($con)." </h5>";
      // }
    ?>
  <!-- </div> -->


  <!-- Modal Pages -->
  <?php // require "../includes/signup_modal.php"; ?>
  <?php // require "../includes/login_modal.php" ?>

  <!-- Footer -->
  <?php // require "../includes/footer.php" ?>

  <!-- <script type="text/javascript" src="../js/jquery.js"></script>
  <script type="text/javascript" src="../js/bootstrap.min.js"></script>
  <script type="text/javascript" src="../js/common.js"></script>
</body>

</html> -->

<?php mysqli_close($con); ?>
