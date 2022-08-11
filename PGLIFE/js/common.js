window.addEventListener("load", function () {

  //code corresponding to signup form...
  // --------------------------------------------------------
    var signup_form = document.getElementById("signup-form");
    signup_form.addEventListener("submit", function (event) {
        // event.preventDefault();
        var XHR = new XMLHttpRequest();
        var form_data = new FormData(signup_form);

        // Set up request
        XHR.open("POST", "api/signup_submit.php");

        // On success
        XHR.addEventListener("load", function(event) {
            document.getElementById("loading").style.display = 'none';

            if( XHR.status === 200 ){
              // Use this console.log code lines to check the response and debug the code
              // console.log( "Response: " + XHR.response );
              // console.log( "Response Text: " + XHR.responseText );

              var response = JSON.parse(XHR.responseText);
              if ( response.success ) {
                  alert(response.message);
                  window.location.href = "index.php";
              }
              else {
                  alert(response.message);
              }
            }
            else{
              alert("Something Went wrong!");
            }
        });

        // On error
        XHR.addEventListener("error", on_error);

        // Form data is sent with request
        XHR.send(form_data);

        document.getElementById("loading").style.display = 'block';
        event.preventDefault();
    });


   //code corresponding to login form...
   // ----------------------------------------------------
   var login_form = document.getElementById("login-form");
   login_form.addEventListener("submit", function (event) {
       // event.preventDefault();
       var XHR = new XMLHttpRequest();
       var form_data = new FormData(login_form);

       // Set up request
       XHR.open("POST", "api/login_submit.php");

       // On success
       XHR.addEventListener("load", function(event) {
           document.getElementById("loading").style.display = 'none';

           if( XHR.status === 200 ){
             // Use this console.log code lines to check the response and debug the code
             console.log( "Response: " + XHR.response );
             console.log( "Response Text: " + XHR.responseText );

             var response = JSON.parse(XHR.responseText);
             if ( response.success ) {
                 alert(response.message);
                 window.location.href = "index.php";
             }
             else {
                 alert(response.message);
             }
           }
           else{
             alert("Something Went wrong!");
           }
       });

       // On error
       XHR.addEventListener("error", on_error);

       // Form data is sent with request
       XHR.send(form_data);

       document.getElementById("loading").style.display = 'block';
       event.preventDefault();
   });
});


var on_error = function (event) {
    document.getElementById("loading").style.display = 'none';
    // alert('Oops! Something went wrong! (on_error)');
    alert('Connection to server could not be established!');
};
