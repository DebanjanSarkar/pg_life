<?php
  session_start();
  require "includes/database_connect.php";

  // If http://domain:port/PGLIFE/property_list.php link is directly opened without any "city" parameter passed, the user
  // will be redirected to the index.php page
  if( !isset($_GET["city"]) )
  {
    header("location: index.php");
    exit();
  }

  //If connected to database successfully...
  if( $con )
  {
    $city = strtolower($_GET["city"]);
    //$city = strtolower($city);          //Converts whole string to lower case
    $city = ucwords($city);             //Converts the first letter of each word to uppercase
    $sql_query1 = "SELECT * FROM cities WHERE name='$city';";
    $city_result  = mysqli_query($con,$sql_query1);          //Getting the city with matching name.
    $properties_result = FALSE;
    if( $city_result && mysqli_num_rows($city_result)==1 ) //When $result1 is not false, and it contains single row.
    {
      $city_row = mysqli_fetch_assoc($city_result);
      $city_id = $city_row["id"];
      if( isset($_GET["filter"]) )
      {
        if( $_GET["filter"]=="rent_desc" )
        {
          $sql_query2 = "SELECT * FROM properties where city_id='$city_id' ORDER BY rent DESC;";
        }
        elseif( $_GET["filter"]=="rent_asc" )
        {
          $sql_query2 = "SELECT * FROM properties where city_id='$city_id' ORDER BY rent ASC;";
        }
        elseif( $_GET["filter"]=="rating_desc" )
        {
          $sql_query2 = "SELECT * FROM properties where city_id='$city_id' ORDER BY rating_food+rating_clean+rating_safety DESC;";
        }
      }
      else
      {
        $sql_query2 = "SELECT * FROM properties where city_id='$city_id';";
      }
      $properties_result = mysqli_query($con,$sql_query2);
      if( $properties_result && mysqli_num_rows($properties_result)>0 )   //When entered city has more than one properties.
      {
        //Code for displaying the properties details in selected city..
      }
      elseif( mysqli_num_rows($properties_result)==0 )
      {
        //Code for selected city having no pgs in our records.
      }
      elseif( !$properties_result )
      {
        //Code to execute when $sql_query2 cannot be successfully executed.
        echo mysqli_error($con);
      }

    }
    elseif( mysqli_num_rows($city_result)==0 )
    {
      //Code for selected city does not exist in our records.
    }
    elseif( !$city_result )
    {
      //Code for $sql_query1 failed to execute, i.e, city record cannot be found by executing select query, query failed to execute.
      echo mysqli_error($con);
    }
  }
  else{
    echo "Database Connectivity Error!";
    echo mysqli_connect_error();
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

    <style media="screen">
      .filter-link, .filter-link:hover{
        text-decoration: none;
        color: inherit;
      }
    </style>
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
                <?php echo $city; ?>
            </li>
        </ol>
    </nav>


    <div class="page-container">

        <!-- List of city cards -->
        <?php
          if( $properties_result && mysqli_num_rows($properties_result)>0 )   //When entered city has more than one properties.
          {
            //Filter bar code here
            ?>
              <!-- Filter bar containing options to apply filters and sort. -->
              <div class="filter-bar row justify-content-around">
                  <!-- Filter Based on Gender -->
                  <!-- <div class="col-auto" data-toggle="modal" data-target="#filter-modal">
                      <img src="img/filter.png" alt="filter" />
                      <span>Filter</span>
                  </div> -->


                  <div class="col-auto">
                      <a class="filter-link" href="property_list.php?city=<?php echo $city;?>&filter=rent_desc">
                        <img src="img/desc.png" alt="sort-rent-desc" />
                        <span>Highest rent first</span>
                      </a>
                  </div>
                  <div class="col-auto">
                      <a class="filter-link" href="property_list.php?city=<?php echo $city;?>&filter=rent_asc">
                        <img src="img/asc.png" alt="sort-rent-asc" />
                        <span>Lowest rent first</span>
                      </a>
                  </div>
                  <div class="col-auto">
                      <a class="filter-link" href="property_list.php?city=<?php echo $city;?>&filter=rating_desc">
                        <img src="img/desc.png" alt="sort-rating-desc" />
                        <span>Rating</span>
                      </a>
                  </div>
              </div>

            <?php

            //Loop to show each property one-by-one in the page
            while( $property_row = mysqli_fetch_assoc($properties_result) )
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
              <div class="property-card row">
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
          elseif( !$city_result )
          {
            //Code for $sql_query1 failed to execute, i.e, city record cannot be found by executing select query, query failed to execute.
            echo mysqli_error($con);
          }
          elseif( mysqli_num_rows($city_result)==0 )
          {
            //Code for selected city does not exist in our records.
            ?>
            <div class="no-city mx-4 my-5">
              <h2><?php echo $city; ?> does not exist in our records still...</h2>
              <hr>
              <p>
                Our network is growing at fast pace, and soon we will have our leads at <?php echo $city; ?>.
              </p>
            </div>

            <?php
          }
          elseif( !$properties_result )
          {
            //Code to execute when $sql_query2 cannot be successfully executed.
            echo mysqli_error($con);
          }
          elseif( mysqli_num_rows($properties_result)==0 )
          {
            //Code for selected city having no pgs in our records.
            ?>
            <div class="no-pg mx-4 my-5">
              <h2>Currently, there are no PGs in <?php echo $city; ?> listed on our Platform, but soon there will be!</h2>
              <hr>
              <p>
                Try searching for PGs in some other nearby cities for now. Our network is growing day-by-day and soon we will come up with PGs listed in more cities nationwide.
              </p>
              <br>
              <p>
                Soon, we will have registered PGs at <?php echo $city; ?>.
              </p>
            </div>
            <?php
          }

        ?>


        <!-- <div class="property-card row">
            <div class="image-container col-md-4">
                <img src="img/properties/1/eace7b9114fd6046.jpg" />
            </div>
            <div class="content-container col-md-8">
                <div class="row no-gutters justify-content-between">
                    <div class="star-container" title="4.8">
                        <i class="fas fa-star"></i>
                        <i class="fas fa-star"></i>
                        <i class="fas fa-star"></i>
                        <i class="fas fa-star"></i>
                        <i class="fas fa-star"></i>
                    </div>
                    <div class="interested-container">
                        <i class="far fa-heart"></i>
                        <div class="interested-text">6 interested</div>
                    </div>
                </div>
                <div class="detail-container">
                    <div class="property-name">Ganpati Paying Guest</div>
                    <div class="property-address">Police Beat, Sainath Complex, Besides, SV Rd, Daulat Nagar, Borivali East, Mumbai - 400066</div>
                    <div class="property-gender">
                        <img src="img/unisex.png" />
                    </div>
                </div>
                <div class="row no-gutters">
                    <div class="rent-container col-6">
                        <div class="rent">Rs 8,500/-</div>
                        <div class="rent-unit">per month</div>
                    </div>
                    <div class="button-container col-6">
                        <a href="property_detail.php" class="btn btn-primary">View</a>
                    </div>
                </div>
            </div>
        </div>

        <div class="property-card row">
            <div class="image-container col-md-4">
                <img src="img/properties/1/46ebbb537aa9fb0a.jpg" />
            </div>
            <div class="content-container col-md-8">
                <div class="row no-gutters justify-content-between">
                    <div class="star-container" title="3.5">
                        <i class="fas fa-star"></i>
                        <i class="fas fa-star"></i>
                        <i class="fas fa-star"></i>
                        <i class="fas fa-star-half-alt"></i>
                        <i class="far fa-star"></i>
                    </div>
                    <div class="interested-container">
                        <i class="far fa-heart"></i>
                        <div class="interested-text">2 interested</div>
                    </div>
                </div>
                <div class="detail-container">
                    <div class="property-name">PG for Girls Borivali West</div>
                    <div class="property-address">Plot no.258/D4, Gorai no.2, Borivali West, Mumbai, Maharashtra 400092</div>
                    <div class="property-gender">
                        <img src="img/female.png" />
                    </div>
                </div>
                <div class="row no-gutters">
                    <div class="rent-container col-6">
                        <div class="rent">Rs 8,000/-</div>
                        <div class="rent-unit">per month</div>
                    </div>
                    <div class="button-container col-6">
                        <a href="property_detail.php" class="btn btn-primary">View</a>
                    </div>
                </div>
            </div>
        </div> -->



    </div>

    <!-- Filter Modal -->
    <div class="modal fade" id="filter-modal" tabindex="-1" role="dialog" aria-labelledby="filter-heading" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h3 class="modal-title" id="filter-heading">Filters</h3>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>

                <div class="modal-body">
                    <h5>Gender</h5>
                    <hr />
                    <div>
                        <button class="btn btn-outline-dark btn-active">
                            No Filter
                        </button>
                        <button class="btn btn-outline-dark">
                            <i class="fas fa-venus-mars"></i>Unisex
                        </button>
                        <button class="btn btn-outline-dark">
                            <i class="fas fa-mars"></i>Male
                        </button>
                        <button class="btn btn-outline-dark">
                            <i class="fas fa-venus"></i>Female
                        </button>
                    </div>
                </div>

                <div class="modal-footer">
                    <button data-dismiss="modal" class="btn btn-success">Apply</button>
                </div>
            </div>
        </div>
    </div>

    <!-- Signup and Login Modal Pages -->
    <?php require "./includes/signup_modal.php"; ?>
    <?php require "./includes/login_modal.php" ?>

    <!-- Footer -->
    <?php require "./includes/footer.php" ?>

    <script type="text/javascript" src="js/jquery.js"></script>
    <script type="text/javascript" src="js/bootstrap.min.js"></script>
    <script type="text/javascript" src="js/common.js"></script>
    <script type="text/javascript" src="js/property_list.js"></script>

</body>

</html>

<?php mysqli_close($con); ?>
