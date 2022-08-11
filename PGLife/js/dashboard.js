window.addEventListener("load", function () {
    var is_interested_images = document.getElementsByClassName("is-interested-image");
    Array.from(is_interested_images).forEach(element => {
        element.addEventListener("click", function (event) {
            var XHR = new XMLHttpRequest();
            var property_id = event.target.getAttribute("property_id");
            // console.log(typeof(property_id));
            // console.log(property_id);

            // On success
            XHR.addEventListener("load", remove_interested_success);

            // On error
            XHR.addEventListener("error", on_error);

            // Set up request
            XHR.open("GET", "api/handle_interested_dashboard.php?property_id=" + property_id);

            // Initiate the request
            XHR.send();

            document.getElementById("loading").style.display = 'block';
            event.preventDefault();
        });
    });
});

var remove_interested_success = function (event) {
    document.getElementById("loading").style.display = 'none';

    var response = JSON.parse(event.target.responseText);
    if (response.success) {
        var property_id = response.property_id;

        var is_interested_image = document.querySelectorAll(".property-id-" + property_id + " .is-interested-image")[0];
        var interested_user_count = document.querySelectorAll(".property-id-" + property_id + " .interested-user-count")[0];

        if ( !response.is_interested ) {
          // Upon Click, the specific interested property will become not-interested, and this block will run...
          // We will hide that property that is being marked uninterested, by .hide class

            var card_box = document.getElementById(`card-${property_id}`);
            card_box.classList.add("hide");
            interested_user_count.innerHTML = parseFloat(interested_user_count.innerHTML) - 1;
        }
    }
    else if (!response.success && !response.is_logged_in) {
        window.$("#login-modal").modal("show");
    }
};

var on_error = function (event) {
    document.getElementById("loading").style.display = 'none';
    // alert('Oops! Something went wrong! (on_error)');
    alert('Connection to server could not be established!');
};
