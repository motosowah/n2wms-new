// -- loading progress message -- //
function loadingMessage(msg){
    $('#loading_message').html(msg);
    $('#loading').show();
}

// -- dismiss loading message -- //
function loadingDone(){
    $('#loading').hide();

}



// -- display default warning message -- //
function display_warning(message){
    // alert(message);
    // Update the error message before opening the dialog
    $("#warning-msg").html(message);  // Change the error message
    // Open the dialog
    $("#warning-dialog").dialog("open");
}

// Disable an element
$.fn.disable = function() {
    this.prop('disabled', true);
    return this;
};
  
// Enable an element
$.fn.enable = function() {
    this.prop('disabled', false);
    return this;
};

// - session check - //
function session_check(){
    console.log(" > checking current session");
    $.ajax({
        // url: baseUrl + '/session_check', 
        // url: 'sts/session_check', 
        url: '/sts/api2/session_check', 
        type: 'GET',
        data: {
            // cmd: "get_sap_period_list",
        },
        dataType: 'json', 
        timeout: 5000,
        success: function(response) {
            console.log(response.status);
            if(response.status == "ok"){
                console.log(" > session valid (authorized)");
            }else{
                console.log(" > session invalid (unauthorized), please relogin");
                // redirect to login page //
                window.location.href = baseUrl + '/login';
            }
        },
        error: function(xhr, status, error) {
            console.log('AJAX status:', status); // e.g. "timeout", "error", "abort", "parsererror"
            console.log('HTTP status code:', xhr.status); // e.g. 502, 0, 404
            console.log('Server Error:', error);
            console.log('Invalid server response:', xhr.responseText); 

            // create server error message //
            if (xhr.status === 0) {
                display_error('Server is unreachable. Please check your connection.');
                location.reload();
            } else if (xhr.status === 401) {
                display_error('Unauthorized session! <hr/> Please relogin.');
                // redirect to login page //
                window.location.href = baseUrl + '/login';
            } else if (xhr.status === 502) {
                display_error('Server temporarily unavailable (502 Bad Gateway).');
            } else if (xhr.status >= 500) {
                display_error('Internal server error (' + xhr.status + ').');
            }else{
                // if server send invalid JSON reply //
                console.log('Server Error:', error); 
                console.log('Invalid server response:', xhr.responseText); 
                display_error('Invalid server response:<hr/>' + xhr.responseText); 
            }
        },beforeSend: function () {
            loadingStart();
        },
        complete: function () {
            loadingEnd();
        }
    });

}

// - on page load completed - //
$(document).ready(function(){
    // alert("main script loaded");
    console.log("main script loaded");
    // alert(baseUrl);

    // if page active again //
    $(document).on('visibilitychange', function() {
        if (document.visibilityState === 'visible') {
            // console.log('The page is now active.');
            // You can add your code here to handle the page being reactivated
            session_check();
        } else {
            // console.log('The page is now inactive.');
        }
    });
})


// - global check if text empty - //
function ifEmpty(txt){
    if(txt.length > 0){
        return false;
    }else{
        return true;
    }
}


/**
 * Focuses an input element and scrolls it to the vertical center of the screen.
 * @param {string} inputId - The ID of the input element to center.
 */
function focusAndCenterInput(inputId) {
    const $input = $(`#${inputId}`);
    
    if ($input.length === 0) {
      console.warn(`Element with ID "${inputId}" not found.`);
      return; // Exit if element doesn't exist
    }
  
    // Focus the input
    $input.trigger('focus');
  
    // Calculate scroll position to center the input
    const windowHeight = $(window).height();
    const inputTop = $input.offset().top;
    const inputHeight = $input.outerHeight();
  
    const scrollPosition = inputTop - (windowHeight / 2) + (inputHeight / 2);
  
    // Smooth scroll to center the input
    $('html, body').animate(
      { scrollTop: scrollPosition },
      500 // Animation duration (ms)
    );
  }