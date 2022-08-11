<?php
session_start();
require "includes/database_connect.php";

// If http://domain:port/PGLIFE/property_detail.php link is directly opened without any "property_id" parameter passed, the user
// will be redirected to the index.php page
if( !isset($_GET["property_id"]) )
{
  header("location: index.php");
  exit();
}

//If connected to database successfully...
if( $con )
{
  $pg_id = $_GET["property_id"];
  $sql_query1 = "SELECT * FROM properties WHERE id=$pg_id; ";
  $property_result = mysqli_query($con,$sql_query1);
  if( !$property_result )
  {
    echo mysqli_error($con);
    exit();
  }
  elseif( mysqli_num_rows($property_result)==0 )
  {
    //Code when SQL query gives empty set, i.e., no PG corresponding to provided PG id exists.
    //This might happen when user manually tampers the URL parameters.
    //In such case, user is redirected to index page
    header("location: index.php");
    exit();
  }
  elseif( mysqli_num_rows($property_result)==1 )    //PG id corresponds to correct single PG records
  {
    $property_row = mysqli_fetch_assoc($property_result);

    $pg_city_id = $property_row["city_id"];
    $pg_name = $property_row["name"];
    $pg_address = $property_row["address"];
    $pg_description = $property_row["description"];
    $gender_allowed = $property_row["gender"];
    $pg_rent = $property_row["rent"];
    $pg_rating_clean = $property_row["rating_clean"];
    $pg_rating_food = $property_row["rating_food"];
    $pg_rating_safety = $property_row["rating_safety"];
    $pg_rating_overall = round(( $property_row["rating_clean"] + $property_row["rating_food"] + $property_row["rating_safety"] )/3 , 1 );

    $sql_query2 = "SELECT * FROM cities WHERE id=$pg_city_id; ";
    $city_result = mysqli_query($con,$sql_query2);
    if( !$city_result )
    {
      echo mysqli_error($con);
      exit();
    }
    else{
      $city_row = mysqli_fetch_assoc($city_result);
      $city_name = $city_row["name"];
    }

    //Fetching the number of person that marked the property interested...
    $sql_interest = "SELECT * FROM interested_users_properties WHERE property_id = $pg_id; ";
    $interest_result = mysqli_query($con,$sql_interest);
    if( $interest_result ){
      $num_interested = mysqli_num_rows($interest_result);
    }

  }

}
else{
  echo "Database Connectivity Error!";
  echo mysqli_connect_error();
}

?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Ganpati Paying Guest | PG Life</title>

    <link href="css/bootstrap.min.css" rel="stylesheet" />
    <link href="https://use.fontawesome.com/releases/v5.11.2/css/all.css" rel="stylesheet" />
    <link href="https://fonts.googleapis.com/css2?family=Open+Sans:ital,wght@0,300;0,400;0,600;0,700;0,800;1,300;1,400;1,600;1,700;1,800&display=swap" rel="stylesheet" />
    <link href="css/common.css" rel="stylesheet" />
    <link href="css/property_detail.css" rel="stylesheet" />
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
        <?php if($city_result){ ?>
            <li class="breadcrumb-item">
                <a href="property_list.php?city=<?php echo $city_name; ?>"><?php echo $city_name; ?></a>
            </li>
            <li class="breadcrumb-item active" aria-current="page">
                <?php echo $pg_name; ?>
            </li>
        <?php } ?>
        </ol>
    </nav>

<?php
// Details of property when correct single property / pg is fetched from database
  if( $property_result && mysqli_num_rows($property_result)==1 )
  {
?>

    <div id="property-images" class="carousel slide" data-ride="carousel">
      <ol class="carousel-indicators">
          <li data-target="#property-images" data-slide-to="0" class="active"></li>
          <li data-target="#property-images" data-slide-to="1" class=""></li>
          <li data-target="#property-images" data-slide-to="2" class=""></li>
      </ol>
      <div class="carousel-inner">
          <div class="carousel-item active">
              <img class="d-block w-100" src="img/properties/1/1d4f0757fdb86d5f.jpg" alt="slide">
          </div>
          <div class="carousel-item">
              <img class="d-block w-100" src="img/properties/1/46ebbb537aa9fb0a.jpg" alt="slide">
          </div>
          <div class="carousel-item">
              <img class="d-block w-100" src="img/properties/1/eace7b9114fd6046.jpg" alt="slide">
          </div>
      </div>
      <a class="carousel-control-prev" href="#property-images" role="button" data-slide="prev">
          <span class="carousel-control-prev-icon" aria-hidden="true"></span>
          <span class="sr-only">Previous</span>
      </a>
      <a class="carousel-control-next" href="#property-images" role="button" data-slide="next">
          <span class="carousel-control-next-icon" aria-hidden="true"></span>
          <span class="sr-only">Next</span>
      </a>
    </div>

    <div class="property-summary page-container">
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

          <div class="interested-container">
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
                <i class="is-interested-image fas fa-heart"></i>
            <?php
              }
              else{
            ?>
                <i class="is-interested-image far fa-heart"></i>
            <?php
              } ?>

              <div class="interested-text">
                <?php if( $interest_result )
                { ?>
                  <span class="interested-user-count">
                    <?php
                    //Printing the Number of users interested
                      if($num_interested>=0)
                        {echo $num_interested;}
                      else
                        {echo 0;}
                    ?>
                  </span> interested
                <?php
                } ?>
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
              <a href="#" class="btn btn-primary">Book Now</a>
          </div>
      </div>
    </div>

    <div class="property-amenities">
      <div class="page-container">
          <h1>Amenities</h1>
          <div class="row justify-content-between">
              <div class="col-md-auto">
                  <h5>Building</h5>
              <?php
                //Getting amenities of type=Building in corresponding PG / property
                $query_building = "SELECT properties_amenities.id, property_id, amenity_id, amenities.name, amenities.type, amenities.icon FROM properties_amenities JOIN amenities ON properties_amenities.amenity_id=amenities.id WHERE amenities.type='Building' AND properties_amenities.property_id=$pg_id; ";
                $result_building = mysqli_query($con,$query_building);

                if( $result_building && mysqli_num_rows($result_building)>0 )
                {
                  while( $building_row = mysqli_fetch_assoc($result_building) )
                  {
                    $amenity_name = $building_row["name"];
                    $amenity_icon = $building_row["icon"];

              ?>
                  <div class="amenity-container">
                      <img src="img/amenities/<?php echo $amenity_icon; ?>.svg">
                      <span> <?php echo $amenity_name; ?> </span>
                  </div>
              <?php
                  }
                }
              ?>

              </div>

              <div class="col-md-auto">
                  <h5>Common Area</h5>
              <?php
              //Getting amenities of type=Common Area in corresponding PG / property
                $query_common_area = "SELECT properties_amenities.id, property_id, amenity_id, amenities.name, amenities.type, amenities.icon FROM properties_amenities JOIN amenities ON properties_amenities.amenity_id=amenities.id WHERE amenities.type='Common Area' AND properties_amenities.property_id=$pg_id; ";
                $result_common_area = mysqli_query($con,$query_common_area);

                if( $result_common_area && mysqli_num_rows($result_common_area)>0 )
                {
                  while( $common_area_row = mysqli_fetch_assoc($result_common_area) )
                  {
                    $amenity_name = $common_area_row["name"];
                    $amenity_icon = $common_area_row["icon"];

              ?>
                  <div class="amenity-container">
                      <img src="img/amenities/<?php echo $amenity_icon; ?>.svg">
                      <span> <?php echo $amenity_name; ?> </span>
                  </div>
              <?php
                  }
                }
              ?>

              </div>

              <div class="col-md-auto">
                  <h5>Bedroom</h5>
              <?php
              //Getting amenities of type=Bedroom in corresponding PG / property
                $query_bedroom = "SELECT properties_amenities.id, property_id, amenity_id, amenities.name, amenities.type, amenities.icon FROM properties_amenities JOIN amenities ON properties_amenities.amenity_id=amenities.id WHERE amenities.type='Bedroom' AND properties_amenities.property_id=$pg_id; ";
                $result_bedroom = mysqli_query($con,$query_bedroom);

                if( $result_bedroom && mysqli_num_rows($result_bedroom)>0 )
                {
                  while( $bedroom_row = mysqli_fetch_assoc($result_bedroom) )
                  {
                    $amenity_name = $bedroom_row["name"];
                    $amenity_icon = $bedroom_row["icon"];

              ?>
                  <div class="amenity-container">
                      <img src="img/amenities/<?php echo $amenity_icon; ?>.svg">
                      <span> <?php echo $amenity_name; ?> </span>
                  </div>
              <?php
                  }
                }
              ?>

              </div>

              <div class="col-md-auto">
                  <h5>Washroom</h5>
              <?php
              //Getting amenities of type=Washroom in corresponding PG / property
                $query_washroom = "SELECT properties_amenities.id, property_id, amenity_id, amenities.name, amenities.type, amenities.icon FROM properties_amenities JOIN amenities ON properties_amenities.amenity_id=amenities.id WHERE amenities.type='Washroom' AND properties_amenities.property_id=$pg_id; ";
                $result_washroom = mysqli_query($con,$query_washroom);

                if( $result_washroom && mysqli_num_rows($result_washroom)>0 )
                {
                  while( $washroom_row = mysqli_fetch_assoc($result_washroom) )
                  {
                    $amenity_name = $washroom_row["name"];
                    $amenity_icon = $washroom_row["icon"];

              ?>
                  <div class="amenity-container">
                      <img src="img/amenities/<?php echo $amenity_icon; ?>.svg">
                      <span> <?php echo $amenity_name; ?> </span>
                  </div>
              <?php
                  }
                }
              ?>

              </div>
          </div>
      </div>
    </div>

    <div class="property-about page-container">
      <h1>About the PG</h1>
      <p><?php echo $pg_description; ?></p>
    </div>

    <div class="property-rating">
      <div class="page-container">
          <h1>Property Rating</h1>
          <div class="row align-items-center justify-content-between">
              <div class="col-md-6">
                  <div class="rating-criteria row">
                      <div class="col-6">
                          <i class="rating-criteria-icon fas fa-broom"></i>
                          <span class="rating-criteria-text">Cleanliness</span>
                      </div>
                      <div class="rating-criteria-star-container col-6" title="<?php echo $pg_rating_clean; ?>">
                        <?php
                          $star_full = floor($pg_rating_clean);
                          $star_empty = 5-1-$star_full;
                          //Full coloured star loop
                          for( $i=1 ; $i<=$star_full ; $i++ ){
                            ?> <i class="fas fa-star"></i> <?php
                          }

                          // Half coloured star placeholder
                          if( ($pg_rating_clean - $star_full)>0.2 && ($pg_rating_clean - $star_full)<0.8 ){
                            ?> <i class="fas fa-star-half-alt"></i> <?php
                          }
                          elseif( ($pg_rating_clean - $star_full)>=0.8 ){
                            ?> <i class="fas fa-star"></i> <?php
                          }
                          elseif( ($pg_rating_clean - $star_full)<=0.2 ){
                            ?> <i class="far fa-star"></i> <?php
                          }

                          //Empty coloured star loop
                          for( $i=1 ; $i<=$star_empty ; $i++ ){
                            ?> <i class="far fa-star"></i> <?php
                          }

                        ?>
                      </div>
                  </div>

                  <div class="rating-criteria row">
                      <div class="col-6">
                          <i class="rating-criteria-icon fas fa-utensils"></i>
                          <span class="rating-criteria-text">Food Quality</span>
                      </div>
                      <div class="rating-criteria-star-container col-6" title="<?php echo $pg_rating_food; ?>">
                        <?php
                          $star_full = floor($pg_rating_food);
                          $star_empty = 5-1-$star_full;
                          //Full coloured star loop
                          for( $i=1 ; $i<=$star_full ; $i++ ){
                            ?> <i class="fas fa-star"></i> <?php
                          }

                          // Half coloured star placeholder
                          if( ($pg_rating_food - $star_full)>0.2 && ($pg_rating_food - $star_full)<0.8 ){
                            ?> <i class="fas fa-star-half-alt"></i> <?php
                          }
                          elseif( ($pg_rating_food - $star_full)>=0.8 ){
                            ?> <i class="fas fa-star"></i> <?php
                          }
                          elseif( ($pg_rating_food - $star_full)<=0.2 ){
                            ?> <i class="far fa-star"></i> <?php
                          }

                          //Empty coloured star loop
                          for( $i=1 ; $i<=$star_empty ; $i++ ){
                            ?> <i class="far fa-star"></i> <?php
                          }

                        ?>
                      </div>
                  </div>

                  <div class="rating-criteria row">
                      <div class="col-6">
                          <i class="rating-criteria-icon fa fa-lock"></i>
                          <span class="rating-criteria-text">Safety</span>
                      </div>
                      <div class="rating-criteria-star-container col-6" title="<?php echo $pg_rating_safety; ?>">
                        <?php
                          $star_full = floor($pg_rating_safety);
                          $star_empty = 5-1-$star_full;
                          //Full coloured star loop
                          for( $i=1 ; $i<=$star_full ; $i++ ){
                            ?> <i class="fas fa-star"></i> <?php
                          }

                          // Half coloured star placeholder
                          if( ($pg_rating_safety - $star_full)>0.2 && ($pg_rating_safety - $star_full)<0.8 ){
                            ?> <i class="fas fa-star-half-alt"></i> <?php
                          }
                          elseif( ($pg_rating_safety - $star_full)>=0.8 ){
                            ?> <i class="fas fa-star"></i> <?php
                          }
                          elseif( ($pg_rating_safety - $star_full)<=0.2 ){
                            ?> <i class="far fa-star"></i> <?php
                          }

                          //Empty coloured star loop
                          for( $i=1 ; $i<=$star_empty ; $i++ ){
                            ?> <i class="far fa-star"></i> <?php
                          }

                        ?>
                      </div>
                  </div>
              </div>

              <div class="col-md-4">
                  <div class="rating-circle">
                      <div class="total-rating"><?php echo $pg_rating_overall; ?></div>
                      <div class="rating-circle-star-container">
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
                  </div>
              </div>
          </div>
      </div>
    </div>

    <div class="property-testimonials page-container">

    <?php
      $testimonial_query = "SELECT * FROM testimonials WHERE property_id=$pg_id; ";
      $testimonial_result = mysqli_query($con,$testimonial_query);
      if( !$testimonial_result )
      {
        //Code when testimonial_query failed to execute...
        echo mysqli_error($con);
        exit();
      }
      elseif( $testimonial_result && mysqli_num_rows($testimonial_result)>0 )
      {
        echo "<h1>What People Say</h1><hr />";
        //Code to display the testimonials posted by different users
        while( $testimonial_row = mysqli_fetch_assoc($testimonial_result) )
        {
          $testimonial_text = $testimonial_row["content"];
          $testimonial_user = $testimonial_row["user_name"];
    ?>
          <div class="testimonial-block">
              <div class="testimonial-image-container">
                  <img class="testimonial-img" src="img/man.png">
              </div>
              <div class="testimonial-text">
                  <i class="fa fa-quote-left" aria-hidden="true"></i>
                  <p><?php echo $testimonial_text; ?></p>
              </div>
              <div class="testimonial-name">- <?php echo $testimonial_user; ?></div>
          </div>
    <?php
        }
      }
      elseif( $testimonial_result && mysqli_num_rows($testimonial_result)==0 )
      {
        echo "<h1>Yet to be reviewed...</h1>";
      }

    ?>

  </div>

<?php
  }
?>

    <!-- Modal Pages -->
    <?php require "./includes/signup_modal.php"; ?>
    <?php require "./includes/login_modal.php" ?>

    <!-- Footer -->
    <?php require "./includes/footer.php" ?>

    <script type="text/javascript" src="js/jquery.js"></script>
    <script type="text/javascript" src="js/bootstrap.min.js"></script>
    <script type="text/javascript" src="js/common.js"></script>
    <script type="text/javascript" src="js/property_detail.js"></script>
</body>

</html>

<?php mysqli_close($con); ?>
