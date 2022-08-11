<?php
  session_start();

  // If user is logged in, only then user can view the dashboard.php page, else will get redirected to homepage.
  if( isset($_SESSION["user_id"]) )
  {
    require "includes/database_connect.php";
    if( !$con )
    {
      echo "Couldn't connect to database!\n";
      echo mysqli_connect_error();
    }
    else
    {
      $user_id = $_SESSION["user_id"];
      $sql_query = "SELECT * FROM users WHERE id='$user_id';";
      $result = mysqli_query($con,$sql_query);
      if( !$result )
      {
        echo "Couldn't Authenticate User!";
        echo mysqli_error($con);
      }
      else
      {
        $row = mysqli_fetch_assoc($result);
        $full_name = $_SESSION["full_name"];
        $email = $row["email"];
        $phone = $row["phone"];
        $college = $row["college_name"];

        //Fetching liked properties of the logged in user...
        $sql_liked_property = "SELECT * FROM interested_users_properties INNER JOIN properties ON interested_users_properties.property_id = properties.id WHERE user_id=$user_id; ";
        $liked_property_result =  mysqli_query($con, $sql_liked_property);

        if( !$liked_property_result ){
          echo mysqli_error($con);
        }
      }
    }
  }
  else
  {
    header("location: index.php");
    exit();
  }

?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Best PG's in Mumbai | PG Life</title>

    <link href="css/bootstrap.min.css" rel="stylesheet" />
    <link href="https://use.fontawesome.com/releases/v5.11.2/css/all.css" rel="stylesheet" />
    <link href="https://fonts.googleapis.com/css2?family=Open+Sans:ital,wght@0,300;0,400;0,600;0,700;0,800;1,300;1,400;1,600;1,700;1,800&display=swap" rel="stylesheet" />
    <link href="css/common.css" rel="stylesheet" />
    <link href="css/property_list.css" rel="stylesheet" />
    <link href="css/dashboard.css" rel="stylesheet" />
</head>

<body>
  <!-- Header Section -->
  <?php require "./includes/header.php"; ?>

  <div id="loading">
  </div>

  <nav aria-label="breadcrumb">
      <ol class="breadcrumb py-2">
          <li class="breadcrumb-item">
              <a href="index.php">Home</a>
          </li>
          <li class="breadcrumb-item active" aria-current="page">
              Dashboard
          </li>
      </ol>
  </nav>


  <div class="container my-5">
    <div class="row mt-5">
      <div class="col-sm-5 offset-sm-2">
        <h1>My Profile</h1>
      </div>
    </div>
    <div class="row justify-content-sm-center">
      <div class="col-sm-3 img-row ">
        <img src="./img/user.png" alt="User Image" class="usr-img">
      </div>
      <div class="col-sm-5 usr-details">
        <p><?php echo $full_name; ?></p>
        <p><?php echo $email; ?></p>
        <p><?php echo $phone; ?></p>
        <p><?php echo $college; ?></p>
      </div>
    </div>
    <div class="row justify-content-sm-end">
      <div class="col-3 offset-9">
        <a href="#" class="delete-profile-link" id="del-link">Delete Profile</a>
      </div>
    </div>
  </div>


  <div class="page-container">

    <div class="property-heading ">
      <h2>My Interested Properties:</h2>
      <hr>
    </div>
      <!-- List of city cards -->
      <?php
        if( $liked_property_result && mysqli_num_rows($liked_property_result)>0 )   //When the user has some liked properties...
        {
      ?>

          <?php

          //Loop to show each property one-by-one in the page
          while( $property_row = mysqli_fetch_assoc($liked_property_result) )
          {
            $pg_id = $property_row["id"];
            $pg_name = $property_row["name"];
            $pg_address = $property_row["address"];
            $gender_allowed = $property_row["gender"];
            $pg_rent = $property_row["rent"];
            $pg_rating_overall = round(( $property_row["rating_clean"] + $property_row["rating_food"] + $property_row["rating_safety"] )/3 , 1 );

            //Fetching the number of person that marked the property interested...
            $sql_interest = "SELECT * FROM interested_users_properties WHERE property_id = $pg_id; ";
            $interest_result = mysqli_query($con,$sql_interest);
            if( $interest_result ){
              $num_interested = mysqli_num_rows($interest_result);
            }
      ?>
            <!-- Property Card containing details of single PG -->
            <div class="property-card row" id="card-<?php echo $pg_id; ?>">
                <div class="image-container col-md-4">
                    <img src="img/properties/1/1d4f0757fdb86d5f.jpg" />
                </div>
                <div class="content-container col-md-8">
                    <div class="row no-gutters justify-content-between">
                        <div class="star-container" title="<?php echo $pg_rating_overall; ?>">
                            <!--
                            <i class="fas fa-star"></i>             - full coloured star
                            <i class="fas fa-star-half-alt"></i>    - half coloured star
                            <i class="far fa-star"></i>             - full empty start
                          -->
                          <?php
                            $star_full = floor($pg_rating_overall);
                            $star_empty = 5-1-$star_full;
                            //Full coloured star loop
                            for( $i=1 ; $i<=$star_full ; $i++ ){
                              ?> <i class="fas fa-star"></i> <?php
                            }

                            // Half coloured star placeholder
                            if( ($pg_rating_overall - $star_full)>0.2 && ($pg_rating_overall - $star_full)<0.8 ){
                              ?> <i class="fas fa-star-half-alt"></i> <?php
                            }
                            elseif( ($pg_rating_overall - $star_full)>=0.8 ){
                              ?> <i class="fas fa-star"></i> <?php
                            }
                            elseif( ($pg_rating_overall - $star_full)<=0.2 ){
                              ?> <i class="far fa-star"></i> <?php
                            }

                            //Empty coloured star loop
                            for( $i=1 ; $i<=$star_empty ; $i++ ){
                              ?> <i class="far fa-star"></i> <?php
                            }

                          ?>
                        </div>



                        <div class="interested-container <?php echo "property-id-" . $pg_id; ?>">
                          <?php
                          //During 1st time loading of page, checking if property is already marked interested by the logged in user, or not.
                          //This check is done to

                          if( isset($_SESSION["user_id"]) ){
                            //User is logged in...
                            $user_id = $_SESSION["user_id"];
                            $sql_isLiked = "SELECT * FROM interested_users_properties WHERE user_id = $user_id AND property_id = $pg_id";
                            $isLiked_result = mysqli_query($con, $sql_isLiked);
                          }
                          ?>
                          <?php
                            if( isset($_SESSION["user_id"]) && mysqli_num_rows($isLiked_result)==1 ){
                          ?>
                              <i class="is-interested-image fas fa-heart" property_id=<?php echo $pg_id; ?> ></i>
                          <?php
                            }
                            else{
                          ?>
                              <i class="is-interested-image far fa-heart" property_id=<?php echo $pg_id; ?> ></i>
                          <?php
                            } ?>

                            <div class="interested-text">
                              <?php if( $interest_result ){ ?>
                                <span class="interested-user-count"><?php echo $num_interested; ?></span> interested
                              <?php } ?>
                            </div>
                        </div>


                    </div>
                    <div class="detail-container">
                        <div class="property-name"><?php echo $pg_name; ?></div>
                        <div class="property-address"><?php echo $pg_address; ?></div>
                        <div class="property-gender">

                            <?php
                              if( $gender_allowed=='male' ){
                                ?> <img src="img/male.png" /> <?php
                              }
                              elseif( $gender_allowed=='female' ){
                                ?> <img src="img/female.png" /> <?php
                              }
                              else{
                                ?> <img src="img/unisex.png" /> <?php
                              }
                            ?>

                        </div>
                    </div>
                    <div class="row no-gutters">
                        <div class="rent-container col-6">
                            <div class="rent">Rs <?php echo $pg_rent; ?>/-</div>
                            <div class="rent-unit">per month</div>
                        </div>
                        <div class="button-container col-6">
                            <a href="property_detail.php?property_id=<?php echo $pg_id ;?>" class="btn btn-primary">View</a>
                        </div>
                    </div>
                </div>
            </div>

      <?php
          }
        }
        elseif( mysqli_num_rows($liked_property_result)==0 )
        {
          //Code for user still not having any property marked as interested....
          ?>
          <div class="no-pg mx-4 text-center">
            <p><span style="font-size: 2rem; ">🙅</span></p>
            <p>You haven't marked any PG as interested yet...</p>
          </div>
          <?php
        }

      ?>



  </div>


  <!-- Modal Pages -->
  <?// php require "./includes/signup_modal.php"; ?>
  <?// php require "./includes/login_modal.php" ?>


  <!-- Footer -->
  <?php require "./includes/footer.php" ?>

  <script type="text/javascript" src="js/jquery.js"></script>
  <script type="text/javascript" src="js/bootstrap.min.js"></script>
  <!-- <script type="text/javascript" src="js/common.js"></script> -->
  <script type="text/javascript" src="js/dashboard.js"></script>
</body>

</html>

<?php mysqli_close($con); ?>
