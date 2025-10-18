// form validation //
var item_code_valid = false;
var lot_no_valid = false;
var sloc_valid = false;
var item_code_valid = false;
var pallet_valid = false;
var rack_valid = false;
var counter_valid = false;

// - on page load completed - //
$(document).ready(function(){
    
    // auto focus to plant entry //
    $('#input_plant').focus();
    $('#input_plant').select();


    // $(".mini-loading").show();
    
    // - get current STO period - //
    $.ajax({
        url: baseUrl + '/api', 
        type: 'GET',
        data: {
            cmd: "get_sto_period",
            plant: "ID1B"
        },
        dataType: 'json', 
        success: function(response) {
            // console.log(response.status);
            // console.log('Success:', response);
            var sto_period = response.data;
            $('#input_period').val(sto_period);
            // $(".mini-loading").hide();
        },
        error: function(xhr, status, error) {
            // if server send invalid JSON reply //
            console.log('Error:', error); 
            console.log('Invalid server response:', xhr.responseText);
            display_error("Failed obtaining current STO period! <hr/> Invalid server response!. " + xhr.responseText)
        },
        beforeSend: function () {
            $("#period_loading").show(); // Show loading animation
            loadingStart();
        },
        complete: function () {
            $("#period_loading").hide(); // Hide loading animation
            loadingEnd();
        }
    });

    // -- on plant entry -- //
    $('#input_plant').keyup(function(event){
        // convert to uppercase //
        $('#input_plant').val($('#input_plant').val().toUpperCase());
        // - on [Enter] key press - //
        if(event.key == "Enter"){
            // alert("[Enter] key pressed");
            submitPlant();
        }
        if(event.which > 47 || event.which == 8){  // except backspace //
 
        }else{
            
        }
    })

    // - plant list - //
    var plant_list = ['', 'ID1A', 'ID1B', 'ID1C', 'ID1D'];
    $("#input_plant").autocomplete({
        source: plant_list,
        // minLength: 0 // Allow search with zero characters
    }).on("focus", function() {
        $(this).autocomplete("search", ""); // Trigger search with an empty string
    });

    // get current plant from user's cookies //
    let current_plant = Cookies.get('sts_plant');
    $("#input_plant").val(current_plant);

    // get current user plant from server //
    get_current_plant();

    // -- on item code entry -- //
    $('#input_item_code').keyup(function(event){
        // - on [Enter] key press - //
        if(event.key == "Enter"){
            // get item name from item code //
            var item_code_val = $('#input_item_code').val();
            // do not process empty item code //
            if(item_code_val.length > 0){
                // remove special character //
                // $('#input_item_code').val(item_code_val.replace(/[^a-zA-Z0-9]/g, ""));
                // choose item code //
                submitItemCode();
            }
        }
    })

    // - autocomplete item code - //
    $("#input_item_code").autocomplete({
        source: function (request, response) {
            $.ajax({
                url: baseUrl + "/api",
                dataType: "json",
                data: {
                    cmd: "search_item",
                    plant: $("#input_plant").val(),
                    search_text: $("#input_item_code").val() // Search term from the input field
                },
                success: function (data) {
                    // response(data); // Pass the data to the autocomplete widget
                    response(data.data); // Pass the data to the autocomplete widget
                },
                error: function () {
                    console.error("Failed to fetch data");
                },
                beforeSend: function () {
                    $("#item_loading").css("display", "inline-block"); // Show loading animation
                },
                complete: function () {
                    $("#item_loading").hide(); // Hide loading animation
                }
            });
        },
        minLength: 1, // Minimum number of characters before searching
        select: function (event, ui) {
            // console.log("Selected Value: " + ui.item.value);
            // console.log("Selected Label: " + ui.item.label);
        }
    })
    .autocomplete("instance")._renderItem = function (ul, item) {
        // Customize the display of each item
        return $("<li>")
            .append(`<div>${item.value} <br/> <strong>${item.description}</strong></div>`)
            .appendTo(ul);
    };

    // auto choose item code on leaving input //
    $('#input_item_code').blur(function(){
        submitItemCode();
    })
        

    // - on lot number entry - //
    $('#input_lot_no').keyup(function(event){
        if(event.key == "Enter"){
            submitLotNo();
            // $('#input_qty').focus();
            // validate lot no //
            // validateLotNo();
        }
    });

    // autocomplete lot no //
    $("#input_lot_no").autocomplete({
        source: function (request, response) {
            $.ajax({
                url: baseUrl + "/api",
                dataType: "json",
                data: {
                    cmd: "search_lot",
                    plant: $("#input_plant").val(),
                    item_code: $("#input_item_code").val(),
                    search: $("#input_lot_no").val(),
                },
                success: function (data) {
                    response(data.data); // Pass the data to the autocomplete widget
                },
                error: function (err) {
                    console.error("Failed to fetch data");
                    console.error(err);
                },
                beforeSend: function () {
                    $("#lotno_loading").show().css("display", "inline-block"); // Show loading animation
                },
                complete: function () {
                    $("#lotno_loading").hide(); // Hide loading animation
                }
            });
        },
        minLength: 0, // Minimum number of characters before searching
        select: function (event, ui) {
            // console.log("Selected Value: " + ui.item.value);
            // console.log("Selected Label: " + ui.item.label);
        }
    })
    .on("focus", function() {
        $(this).autocomplete("search", ""); // Trigger search with an empty string
    })
    .autocomplete("instance")._renderItem = function (ul, item) {
        // Customize the display of each item
        return $("<li>")
            .append(`<div><strong>${item.description}</strong></div>`)
            .appendTo(ul);
    };

    // on leaving lot no entry //
    $('#input_lot_no').blur(function(){
        // validate lot no //
        // validateLotNo();
    })

    
    // on qty data entry //
    $('#input_qty').keyup(function(event){
        if(event.key == "Enter"){
            // focus to sloc entry //
            $('#input_book_no').focus();
        }
    });

    
    // on sloc data entry //
    $('#input_sloc').keyup(function(event){
        var sloc = $('#input_sloc').val();
        // remove special character //
        $('#input_sloc').val(sloc.replace(/[^a-zA-Z0-9]/g, ""));
        // convert to uppercase //
        $('#input_sloc').val(sloc.toUpperCase());
        if(event.key == "Enter"){
            submitSloc();
            // $('#input_pallet').focus();
            // validate sloc entry //
            // validateSloc();
        }
    });

    // jquery autocomplete //
    $("#input_sloc").autocomplete({
        source: function (request, response) {
            $.ajax({
                // url: "./api/sloc_list_api.php", // Replace with your endpoint URL
                url: baseUrl + "/api", // Replace with your endpoint URL
                dataType: "json",
                data: {
                    cmd: "search_sloc",
                    plant: $("#input_plant").val(),
                    search: $("#input_sloc").val(),
                },
                success: function (data) {
                    response(data.data); // Pass the data to the autocomplete widget
                },
                error: function () {
                    console.error("Failed to fetch data");
                },
                beforeSend: function () {
                    $("#sloc_loading").show(); // Show loading animation
                },
                complete: function () {
                    $("#sloc_loading").hide(); // Hide loading animation
                }
            });
        },
        minLength: 0, // Minimum number of characters before searching
        select: function (event, ui) {
            // console.log("Selected Value: " + ui.item.value);
            // console.log("Selected Label: " + ui.item.label);
        }
    })
    .on("focus", function() {
        $(this).autocomplete("search", ""); // Trigger search with an empty string
    })
    .autocomplete("instance")._renderItem = function (ul, item) {
        // Customize the display of each item
        return $("<li>")
            .append(`<div><strong>${item.description}</strong></div>`)
            .appendTo(ul);
    };

    // -- on leaving sloc entry -- //
    $('#input_sloc').blur(function(){
        // validate sloc entry //
        // validateSloc();
    })
    

    // -- on pallet code entry -- //
    $('#input_pallet').keyup(function(event){
        if(event.which > 47){
            // on any key - except for special key //
            // autocompletePalletCode();
        }

        // on [Enter] key //
        if(event.key == "Enter"){
            $('#input_rack').focus();
            submitPallet();
            // validate pallet code //
            // validatePallet();
        }
        
    });

    // jquery autocomplete //
    $("#input_pallet").autocomplete({
        source: function (request, response) {
            $.ajax({
                // url: "./api/pallet_list_api.php", // Replace with your endpoint URL
                url: "./api/entry-api.php?cmd=search_pallet", // Replace with your endpoint URL
                dataType: "json",
                data: {
                    plant: $("#input_plant").val(),
                    search: $("#input_pallet").val(),
                },
                success: function (data) {
                    // console.log(data);
                    response(data.data); // Pass the data to the autocomplete widget
                },
                error: function () {
                    console.error("Failed to fetch data");
                },
                beforeSend: function () {
                    $("#pallet_loading").show(); // Show loading animation
                },
                complete: function () {
                    $("#pallet_loading").hide(); // Hide loading animation
                }
            });
        },
        minLength: 1, // Minimum number of characters before searching
        select: function (event, ui) {
            // console.log("Selected Value: " + ui.item.value);
            // console.log("Selected Label: " + ui.item.label);
        }
    })/*
    .on("focus", function() {
        $(this).autocomplete("search", ""); // Trigger search with an empty string
    })*/
    .autocomplete("instance")._renderItem = function (ul, item) {
        // Customize the display of each item
        return $("<li>")
            .append(`<div><strong>${item.description}</strong></div>`)
            .appendTo(ul);
    };

    // - on leaving pallet code entry - //
    $('#input_pallet').blur(function(){
        // validatePallet();
    })

    // - on rack entry - //
    $('#input_rack').keyup(function(event){
        if(event.which > 47){
            // on any key - except for special key //
            // autocompleteRack();
        }

        // on [Enter] key //
        if(event.key == "Enter"){
            // validate rack entry //
            submitRack();
            // focus to book no entry //
            // $('#input_book_no').focus();
        }

        var rack = $('#input_rack').val();
        // convert to uppercase //
        $('#input_rack').val(rack.toUpperCase());

    });

    // jquery autocomplete //
    $("#input_rack").autocomplete({
        source: function (request, response) {
            $.ajax({
                // url: "./api/rack_list_api.php", // Replace with your endpoint URL
                url: "./api/entry-api.php?cmd=search_rack", // Replace with your endpoint URL
                dataType: "json",
                data: {
                    plant: $("#input_plant").val(),
                    search: $("#input_rack").val(),
                },
                success: function (data) {
                    // console.log(data);
                    response(data.data); // Pass the data to the autocomplete widget
                },
                error: function () {
                    console.error("Failed to fetch data");
                },
                beforeSend: function () {
                    $("#rack_loading").show(); // Show loading animation
                },
                complete: function () {
                    $("#rack_loading").hide(); // Hide loading animation
                }
            });
        },
        minLength: 1, // Minimum number of characters before searching
        select: function (event, ui) {
            // console.log("Selected Value: " + ui.item.value);
            // console.log("Selected Label: " + ui.item.label);
        }
    })/*
    .on("focus", function() {
        $(this).autocomplete("search", ""); // Trigger search with an empty string
    })*/
    .autocomplete("instance")._renderItem = function (ul, item) {
        // Customize the display of each item
        return $("<li>")
            .append(`<div><strong>${item.description}</strong></div>`)
            .appendTo(ul);
    };

    // - on leaving rack entry - //
    $('#input_rack').blur(function(){
        // validate rack entry //
        // validateRack();
    })

    // on book no data entry //
    $('#input_book_no').keyup(function(event){
        if(event.key == "Enter"){
            // focus to counter entry //
            $('#input_counter').focus();
        }
    });
    
    // on counter data entry //
    $('#input_counter').keyup(function(event){
        if(event.key == "Enter"){
            // validate counter entry //
            // validateCounter();
            $('#input_notes').focus();
        }
    });

    // - on leaving counter entry - //
    $('#input_counter').blur(function(){
        // validate counter entry //
        // validateCounter();
    })
    
    // on notes data entry //
    $('#input_notes').keyup(function(event){
        if(event.key == "Enter"){
            // focus to save button //
            // $('#btn_save').focus();
            checkEntry();
        }
    });

    // validate form entry //
    $('#input_item_code, #input_lot_no, #input_sloc, #input_pallet, #input_rack, #input_counter').blur(function(){
        // validateForm();
    })

 

    
})


// - SUBMIT PLANT - //
function submitPlant(){
    var plant_val = $('#input_plant').val();
    // if input plant entered //
    if(plant_val.length > 0){
        // verify current user plant //
        $.ajax({
            url: baseUrl + '/api', 
            type: 'GET',
            data: {
                cmd: "check_user_plant",
                plant: plant_val
            },
            dataType: 'json', 
            success: function(response) {
                // console.log(response.status);
                // console.log('Success:', response);
                // console.log(response.status);
                if(response.status == "ok"){
                    // user session ok //
                    $('#input_plant').disable();
                    $('#btn_submit_plant').disable();
                    $('#input_item_code').enable();
                    $('#btn_cancel_item_code').enable();
                    $('#btn_submit_item_code').enable();
                    $('#input_item_code').focus();
                    $('#warning_invalid_plant').hide();
                    // get sto period for selected plant //
                    var plant = $('#input_plant').val();
                    get_sto_period(plant);
                    // save current user selected plant //
                    set_current_plant(plant);
                    // set default rack and pallet //
                    $('#input_pallet').val("CONVERTING");
                    $('#input_rack').val("CONVERTING");
                }else{
                    // display error  //
                    $('#warning_invalid_plant_message').html(response.message);
                    $('#warning_invalid_plant').show();
                    // focus on plant input //
                    $('#input_plant').focus();
                }
            },
            beforeSend: function () {
                loadingStart();
            },
            complete: function () {
                loadingEnd();
            },
            error: function(xhr, status, error) {
                // if server send invalid JSON reply //
                console.log('Error:', error); 
                console.log('Invalid server response:', xhr.responseText); 
            }
        });        
    }else{
        // display error  //
        $('#warning_invalid_plant_message').html("Please enter PLANT");
        $('#warning_invalid_plant').show();
    }

}


// - submit item code - //
function submitItemCode(){
    var plant_val = $('#input_plant').val();
    var item_code_val = $('#input_item_code').val();
    // if input item code entered //
    if(item_code_val.length > 0){
        // verify input item code //
        $.ajax({
            url: baseUrl + '/api', 
            type: 'GET',
            data: {
                cmd: "get_item",
                plant: plant_val,
                item_code: item_code_val
            },
            dataType: 'json', 
            success: function(response) {
                // console.log(response.status);
                // console.log('Success:', response);
                // console.log(response.status);
                if(response.status == "ok"){
                    // item code ok //
                    $('#warning_invalid_item').hide();
                    // set item name //
                    $('#input_item_name').val(response.item_name);
                    $('#btn_submit_item_code').disable();
                    $('#input_item_code').disable();
                    $('#input_lot_no').enable();
                    $('#btn_cancel_lot_no').enable();
                    $('#btn_submit_lot_no').enable();
                    $('#input_lot_no').focus();
                }else{
                    // invalid item code  //
                    $('#warning_invalid_item_message').html(response.message);
                    $('#warning_invalid_item').show();
                    // focus on input_item_code //
                    // $('#input_item_code').focus();
                    $("#input_item_code").autocomplete('close');
                }
            },
            error: function(xhr, status, error) {
                // if server send invalid JSON reply //
                console.log('Error:', error); 
                console.log('Invalid server response:', xhr.responseText); 
            },
            beforeSend: function () {
                $("#item_loading").show().css("display", "inline-block"); // Show loading animation
                loadingStart();
            },
            complete: function () {
                $("#item_loading").hide(); // Hide loading animation
                loadingEnd();
            }
        });        
    }else{
        // display error  //
        $('#warning_invalid_item_message').html("Please enter ITEM CODE");
        $('#warning_invalid_item').show();
    }
}

// - submit lot no - //
function submitLotNo(){
    // var period_val = $('#input_period').val();
    var plant_val = $('#input_plant').val();
    var item_code_val = $('#input_item_code').val();
    var lot_no_val = $('#input_lot_no').val().toUpperCase();
    $('#input_lot_no').val(lot_no_val);

    // if lot no entered //
    if(lot_no_val.length > 0){
        // verify input item code //
        $.ajax({
            url: baseUrl + '/api', 
            type: 'GET',
            data: {
                cmd: "check_lot",
                plant: plant_val,
                item_code: item_code_val,
                lot_no: lot_no_val,
            },
            dataType: 'json', 
            success: function(response) {
                // console.log(response.status);
                // console.log('Success:', response);
                // console.log(response.status);
                if(response.status == "ok"){
                    // lot number ok //
                    // $('#input_lot_no').disable();
                    $('#warning_invalid_lot_no').hide();
                    
                    $('#input_sloc').enable();
                    $('#input_sloc').focus();
                    $('#btn_submit_lot_no').enable();
                    $('#btn_submit_sloc').enable();
                    focusAndCenterInput('input_sloc');
                    // $("#input_sloc").autocomplete('close');
                }else{
                    // invalid item code  //
                    $('#warning_invalid_lot_no_message').html(response.message);
                    $('#warning_invalid_lot_no').show();
                    // focus on input_item_code //
                    // $('#input_item_code').focus();
                    $("#input_lot_no").autocomplete('close');
                }
            },
            error: function(xhr, status, error) {
                // if server send invalid JSON reply //
                console.error('Error:', error); 
                console.error('Invalid server response:', xhr.responseText); 
                display_error('Invalid server response: <hr/>' + xhr.responseText);
            }
        });        
    }else{
        // display error  //
        $('#warning_invalid_lot_no_message').html("Please enter LOT NO.!");
        $('#warning_invalid_lot_no').show();
        // display_error('Invalid server response: <hr/>' + xhr.responseText);
    }
    
}

// - submit SLOC - //
function submitSloc(){
    var plant_val = $('#input_plant').val();
    var sloc_val = $('#input_sloc').val();
    // if input sloc entered //
    if(sloc_val.length > 0){
        // verify input item code //
        $.ajax({
            url: baseUrl + '/api', 
            type: 'GET',
            data: {
                cmd: "check_sloc",
                plant: plant_val,
                sloc: sloc_val
            },
            dataType: 'json', 
            success: function(response) {
                // console.log(response.status);
                // console.log('Success:', response);
                // console.log(response.status);
                if(response.status == "ok"){
                    // sloc ok //
                    // $('#input_sloc').disable();
                    $('#input_pallet').enable();
                    $('#input_pallet').focus();
                    $('#warning_invalid_sloc').hide();
                }else{
                    // invalid sloc //
                    $('#warning_invalid_sloc_message').html(response.message);
                    $('#warning_invalid_sloc').show();
                    // focus on input_item_code //
                    $('#input_sloc').focus();
                }
            },
            error: function(xhr, status, error) {
                // if server send invalid JSON reply //
                console.log('Error:', error); 
                console.log('Invalid server response:', xhr.responseText);
                display_error('Invalid server response: <hr/>' + xhr.responseText); 
            }
        });        
    }else{
        // display error  //
        $('#warning_invalid_sloc_message').html("Please enter SLOC");
        $('#warning_invalid_sloc').show();
    }
}


// - submit PALLET - //
function submitPallet(){
    var plant_val = $('#input_plant').val();
    var pallet_val = $('#input_pallet').val();
    // if input pallet entered //
    if(pallet_val.length > 0){
        // verify input item code //
        $.ajax({
            url: baseUrl + '/api', 
            type: 'GET',
            data: {
                cmd: "validate_pallet",
                plant: plant_val,
                pallet: pallet_val
            },
            dataType: 'json', 
            success: function(response) {
                // console.log(response.status);
                // console.log('Success:', response);
                // console.log(response.status);
                if(response.status == "ok"){
                    // pallet ok //
                    $('#warning_invalid_pallet').hide();
                    // $('#input_pallet').disable();
                    // $('#btn_pallet').disable();
                    $('#input_rack').enable();
                    $('#input_rack').focus();
                    focusAndCenterInput('input_rack');
                }else{
                    // invalid pallet //
                    $('#warning_invalid_pallet_message').html(response.message);
                    $('#warning_invalid_pallet').show();
                    $('#input_pallet').focus();
                }
            },
            error: function(xhr, status, error) {
                // if server send invalid JSON reply //
                console.log('Error:', error); 
                console.log('Invalid server response:', xhr.responseText); 
                display_error('Invalid server response: <hr/>' + xhr.responseText); 
            },
            beforeSend: function () {
                $("#pallet_loading").show(); // Show loading animation
            },
            complete: function () {
                $("#pallet_loading").hide(); // Hide loading animation
            }
        });        
    }else{
        // display error  //
        $('#warning_invalid_pallet_message').html("Please enter pallet");
        $('#warning_invalid_pallet').show();
    }
}



// - submit RACK - //
function submitRack(){
    var plant_val = $('#input_plant').val();
    var rack_val = $('#input_rack').val();
    // if input rack entered //
    if(rack_val.length > 0){
        // verify input item code //
        $.ajax({
            url: baseUrl + '/api', 
            type: 'GET',
            data: {
                cmd: "validate_rack",
                plant: plant_val,
                rack: rack_val
            },
            dataType: 'json', 
            success: function(response) {
                // console.log(response.status);
                // console.log('Success:', response);
                // console.log(response.status);
                if(response.status == "ok"){
                    // rack ok //
                    $('#warning_invalid_rack').hide();
                    // $('#input_rack').disable();
                    // $('#btn_rack').disable();
                    $('#input_qty').enable();
                    $('#input_qty').focus();
                    focusAndCenterInput('input_qty');
                }else{
                    // invalid rack //
                    $('#warning_invalid_rack_message').html(response.message);
                    $('#warning_invalid_rack').show();
                    $('#input_rack').focus();
                }
            },
            error: function(xhr, status, error) {
                // if server send invalid JSON reply //
                console.log('Error:', error); 
                console.log('Invalid server response:', xhr.responseText); 
                display_error('Invalid server response: <hr/>' + xhr.responseText); 
            },
            beforeSend: function () {
                $("#rack_loading").show(); // Show loading animation
            },
            complete: function () {
                $("#rack_loading").hide(); // Hide loading animation
            }
        });        
    }else{
        // display error  //
        $('#warning_invalid_rack_message').html("Please enter rack");
        $('#warning_invalid_rack').show();
    }
}


// - submit Qty - //
function submitQty(){
    $('#input_book_no').enable();
    $('#input_book_no').focus();
    $('#input_counter').enable();
    $('#input_notes').enable();
}

    
// -- validate lot number -- //
function validateLotNo(){
    // if lot no not empty //
    var lot_no_val = $('#input_lot_no').val();
    if(lot_no_val.length > 0){
        $('#warning_invalid_lot_no').hide();
        // focus to qty entry //
        $('#input_qty').focus();
        lot_no_valid = true;
    }else{
        $('#warning_invalid_lot_no_message').html("Lot number cannot be empty!");
        $('#warning_invalid_lot_no').show();
        lot_no_valid = false;
    }
}

// -- validate sloc -- //
function validateSloc(){
    // if sloc not empty //
    var sloc_val = $('#input_sloc').val();
    if(sloc_val.length > 0){
        $('#warning_invalid_sloc').hide();
        // focus to pallet entry //
        $('#input_pallet').focus();
        sloc_valid = true;
    }else{
        $('#warning_invalid_sloc_message').html("Storage location cannot be empty!");
        $('#warning_invalid_sloc').show();
        sloc_valid = false;
    }
}

// - autocomplete pallet code - //
function autocompletePalletCode(){
    var plant_val = $('#input_plant').val();
    var pallet_val = $('#input_pallet').val();
    if(pallet_val.length > 0){
        var ajax = $.ajax({
            url: "./entry/pallet_list_api.php",
            method: "GET",
            data: {
                plant: plant_val,
                pallet : pallet_val
            }
        });
        ajax.done(function(data){
            // console.log(data);
            var response = JSON.parse(data);
            if(response['status'] == 'ok'){
                // put lot number list into datalist //
                $('#pallet_list').html(response['data']);
            }else{
                // clear datalist //
                $('#pallet_list').html("");
            }
        });
        ajax.fail(function(err){
            alert(err);
            console.error(err);
        });
    }
}

// -- validate pallet code function //
function validatePallet(){
    // validate pallet code entry //
    var pallet_val = $('#input_pallet').val();
    // if pallet code not empty //
    if(pallet_val.length > 0){
        $('#warning_invalid_pallet').hide();
        var jqxhr = $.ajax({
            url: "./api/validate_pallet_api.php",
            method: "GET",
            data: {
                    cmd: "check_pallet",
                    pallet_code: pallet_val
                }
        });
        
        jqxhr.done(function(msg) {
            var response = JSON.parse(msg);
            if(response['status'] == 'error'){
                // invalid pallet code - display error  //
                $('#warning_invalid_pallet_message').html(response['message']);
                $('#warning_invalid_pallet').show();
                // $('#btn_save').prop("disabled", true);
                // $('#input_pallet').focus();
                // $('#input_pallet').select();
                pallet_valid = false;
            }else{
                // pallet code valid //
                $('#warning_invalid_pallet').hide();
                $('#btn_save').prop("disabled", false);
                // $('#input_rack').focus();
                pallet_valid = true;
            }
        });
        jqxhr.fail(function(err) {
            console.error(err);
            alert(err);
        });        
    }else{
        $('#warning_invalid_pallet_message').html("Pallet cannot be empty!");
        $('#warning_invalid_pallet').show();
    }

}

// -- autocomplete rack entry -- //
function autocompleteRack(){
    var plant_val = $('#input_plant').val();
    var rack_val = $('#input_rack').val();
    // convert to uppercase //
    $('#input_rack').val(rack_val.toUpperCase());
    // if not empty //
    if(rack_val.length > 0){
        var ajax = $.ajax({
            url: "./entry/rack_list_api.php",
            method: "GET",
            data: {
                plant: plant_val,
                rack: rack_val
            }
        });

        ajax.done(function(data){
            var response = JSON.parse(data);
            // console.log(response);
            if(response['status'] == 'ok'){
                // put rack list into datalist //
                $('#rack_list').html(response['data']);
            }else{
                // clear datalist //
                $('#rack_list').html("");
            }
        });

        ajax.fail(function(err){
            console.log(err);
            alert(err);
        });
    
    }
}

// -- validate rack -- //
function validateRack(){
    // if rack not empty //
    var rack_val = $('#input_rack').val();
    if(rack_val.length > 0){
        $('#warning_invalid_rack').hide();
        // focus to book number entry //
        $('#input_book_no').focus();
        rack_valid = true;
    }else{
        $('#warning_invalid_rack_message').html("Rack cannot be empty!");
        $('#warning_invalid_rack').show();
        rack_valid = false;
    }
}

// - autofill sloc list - //
function autofillSloc(){
    // get sloc list for current plant //
    var plant_val = $('#input_plant').val();
    var ajax = $.ajax({
        url: "./entry/sloc_list_api.php",
        method: "GET",
        data: {
            plant : plant_val
        }
    });

    ajax.done(function(data){
        var response = JSON.parse(data);
        // console.log(response);
        if(response['status'] == 'ok'){
            // put lot number list into datalist //
            $('#sloc_list').html(response['data']);
        }else{
            // clear datalist //
            $('#sloc_list').html("");
        }
    });

    ajax.fail(function(err){
        alert(err);
        console.log(err);
    });        
}


// -- validate counter name -- //
function validateCounter(){
    // if counter not empty //
    var counter_val = $('#input_counter').val();
    if(counter_val.length > 0){
        $('#warning_invalid_counter').hide();
        // focus to notes entry //
        $('#input_notes').focus();
        counter_valid = true;
    }else{
        $('#warning_invalid_counter_message').html("Counter name cannot be empty!");
        $('#warning_invalid_counter').show();
        counter_valid = false;
    }
}


// -- validate form entry -- //
function validateForm(){
    var form_valid = false;
    // make sure every entry is valid //
    form_valid = item_code_valid && lot_no_valid && sloc_valid && pallet_valid && rack_valid && counter_valid;
    // if entry valid //
    if(form_valid){
        // enable save button //
        // $('#btn_save').prop('disabled', false);
    }else{
        // if some entry invalid //

        // disable save button //
        // $('#btn_save').prop('disabled', true);
    }

    console.log(form_valid);
}

// cancel entry //
function cancelEntry(){
    clear_form();
}

// clear form //
function clear_form(){
    $("#input_item_code").val("");
    $("#input_item_name").val("");
    $("#input_sloc").val("");
    $("#input_lot_no").val("");
    $("#input_qty").val("");
    $("#input_rack").val("");
    $("#input_pallet").val("");
    $("#input_book_no").val("");
    $("#input_counter").val("");
    $("#input_notes").val("");

    $("#input_plant").focus();
    // disable save button //
    // $('#btn_save').prop("disabled", true);
}



// -- check entry before saving -- /
function checkEntry(ignore_warning){

    console.log(" > checking manual entry");

    // $('#btn_save').prop("disabled", true);
    // $("#btn_save").text("Saving");

    // $('.loading').show();
    $('#main_form, #item_warning_window').hide();
    $('#loading_message').html('Checking...');
    $('#loading_window').show();
    
    // get form data //
    var val_username = $("#username").val();            // user name //
    var val_period = $("#input_period").val();          // period //
    var val_plant = $("#input_plant").val();            // plant //
    var val_item_code = $("#input_item_code").val();    // item code //
    var val_item_name = $("#input_item_name").val();    // item name //
    var val_lot_no = $("#input_lot_no").val();          // lot no //
    var val_qty = $("#input_qty").val();                // qty //
    var val_sloc = $("#input_sloc").val();              // sloc //
    var val_rack = $("#input_rack").val();              // rack //
    var val_pallet = $("#input_pallet").val();          // pallet //
    var val_book_no = $("#input_book_no").val();        // book number //
    var val_counter = $("#input_counter").val();        // counter/checker //
    var val_notes = $("#input_notes").val();            // notes //

    // autosave counter name //
    localStorage.setItem('counter', val_counter);

    // prepare entry items list //
    var entry_items = Array();

    // generate json data //
    var json_data = {};
    json_data['username'] = val_username;
    json_data['period'] = val_period;
    json_data['plant'] = val_plant;
    json_data['tag'] = "";
    json_data['item_code'] = val_item_code;
    json_data['item_name'] = val_item_name;
    json_data['lot_no'] = val_lot_no;
    json_data['sloc'] = val_sloc;
    json_data['qty'] = val_qty;
    json_data['rack'] = val_rack;
    json_data['pallet'] = val_pallet;
    json_data['book_no'] = val_book_no;
    json_data['counter'] = val_counter;
    json_data['notes'] = val_notes;
    json_data['source'] = "manual";
    json_data['id'] = "1";

    entry_items.push(JSON.stringify(json_data));
    
    console.log(entry_items);

    // - send entry data into server - //
    $.ajax({
        url: baseUrl + '/api?cmd=check_entry',
        type: 'POST',
        data: {
            entry_data: JSON.stringify(entry_items),
        },
        dataType: 'json', 
        success: function(response) {
            console.log(response);
            if(response.status == "ok"){
                // - response ok - //
                // entry valid //
                saveEntry();
                
            }else if(response.status == "warning"){
                // - warning found - //
                console.log("warning found")
                console.log(response);
                var warnings = response['messages'];
                // alert(warning_messages);

                $('#main_form').hide();
                $('#loading_window').hide();
                $('#entry_warning_window').show();
                $('#entry_warning_message').html(warnings);
                
            }else if(response.status == "error"){
                // - errors found - //
                // console.error('Error:', error);
                // console.error('Invalid server response:', response); 
                // display_error('Invalid server response: <hr/>' + response); 
            
                // console.log("error found");

                $('#loading').hide();

                console.log(response['message']);

                var errors = response['messages'];
                console.log(errors);
                $('#main_form').hide();
                $('#loading_window').hide();
                $('#entry_error_window').show();
                $('#entry_error_message').html(errors);
                
            }
        },
        error: function(xhr, status, error) {
            // if server send invalid JSON reply //
            console.error('Error:', error); 
            console.error('Invalid server response:', xhr.responseText); 
            display_error('Invalid server response: <hr/>' + xhr.responseText); 
        },
        beforeSend: function () {
            $("#pallet_loading").show().css("display", "inline-block"); // Show loading animation
        },
        complete: function () {
            $("#pallet_loading").hide(); // Hide loading animation
        }
    });

}


// -- save entry into database -- /
function saveEntry(ignore_warning){

    console.log(" > saving entry manual");

    $('#main_form, #entry_warning_window').hide();
    $('#loading_message').html('Saving...');
    $('#loading_window').show();
    
    // get form data //
    var val_username = $("#username").val();            // user name //
    var val_period = $("#input_period").val();          // period //
    var val_plant = $("#input_plant").val();            // plant //
    var val_item_code = $("#input_item_code").val();    // item code //
    var val_item_name = $("#input_item_name").val();    // item name //
    var val_lot_no = $("#input_lot_no").val();          // lot no //
    var val_qty = $("#input_qty").val();                // qty //
    var val_sloc = $("#input_sloc").val();              // sloc //
    var val_rack = $("#input_rack").val();              // rack //
    var val_pallet = $("#input_pallet").val();          // pallet //
    var val_book_no = $("#input_book_no").val();        // book number //
    var val_counter = $("#input_counter").val();        // counter/checker //
    var val_notes = $("#input_notes").val();            // notes //

    // autosave counter name //
    localStorage.setItem('counter', val_counter);

    // prepare entry items list //
    var entry_items = Array();

    // generate json data //
    var json_data = {};
    json_data['username'] = val_username;
    json_data['period'] = val_period;
    json_data['plant'] = val_plant;
    json_data['tag'] = "";
    json_data['item_code'] = val_item_code;
    json_data['item_name'] = val_item_name;
    json_data['lot_no'] = val_lot_no;
    json_data['sloc'] = val_sloc;
    json_data['qty'] = val_qty;
    json_data['rack'] = val_rack;
    json_data['pallet'] = val_pallet;
    json_data['book_no'] = val_book_no;
    json_data['counter'] = val_counter;
    json_data['notes'] = val_notes;
    json_data['source'] = "manual";
    json_data['id'] = "1";

    entry_items.push(JSON.stringify(json_data));
    
    console.log(entry_items);

    // - send entry data into server - //
    var jqxhr = $.ajax({
        // url: "./api/sto_entry_api.php",
        url: "./api/entry-api.php?cmd=save_entry",
        method: "POST",
        data: {
            entry_data: JSON.stringify(entry_items),
        }
    });

    jqxhr.done(function(msg) {
        // var response = JSON.parse(msg);
        response = msg;
        console.log(response);
        // if response ok //
        if(response.status == 'ok'){
            // alert('Entry Saved');
            $('#loading').hide();
            // $("#btn_save").text("Save");
            toast("Entry saved");

            // reset form //
            resetForm();
            // location.reload();

            // $('#main_form').show();
            $('#loading_window').hide();

        // -- display error message -- //
        }else if(response.status == 'error'){
            console.log("error found");

            $('#loading').hide();
            // $('#btn_save').prop("disabled", false);
            $("#btn_save").text("Save");
            
            // display error window //
            console.log(response['message']);

            var errors = response['message'];
            console.log(errors);
            var error_list = "<b>Mohon dicek kembali.</b><br/>";
            for (const err_msg of errors) {
                error_list = error_list + "<li>" + err_msg + "</li>";
            }

            console.log(error_list);
            // display_error(error_list);
            display_error(errors);

            $('#main_form').show();
            $('#loading_window').hide();
            

        // -- display warning message -- //
        }else if(response.status == 'warning'){
            console.log("warning found")
        }

        // $('#main_form').show();
        // $('#loading_window').hide();
        
    });
    
    // on server request error //
    jqxhr.fail(function(err) {
        console.error(err.responseText);
        $('#loading').hide();

        alert(err);
        // $('#btn_save').prop("disabled", false);
        $("#btn_save").text("Save");
        $('#loading').hide();

        display_error("<b>Invalid server response!</b> <hr/>" + err.responseText);

        $('#main_form').show();
        $('#loading_window').hide();
    });    

}



/*
// -- CHECK ENTRY -- //
function checkEntry(){

    loadingMessage("Checking");
    $('#loading').show();
    // $('#btn_save').prop("disabled", true);
    $("#btn_save").text("Checking");

    // get form data //
    var val_username = $("#username").val();            // user name //
    var val_plant = $("#input_plant").val();            // plant //
    var val_item_code = $("#input_item_code").val();    // item code //
    var val_item_name = $("#input_item_name").val();    // item name //
    var val_lot_no = $("#input_lot_no").val();          // lot no //
    var val_qty = $("#input_qty").val();                // qty //
    var val_sloc = $("#input_sloc").val();              // sloc //
    var val_rack = $("#input_rack").val();              // rack //
    var val_pallet = $("#input_pallet").val();          // pallet //
    var val_book_no = $("#input_book_no").val();        // book number //
    var val_counter = $("#input_counter").val();        // counter/checker //
    var val_notes = $("#input_notes").val();            // notes //
    
    // generate json data //
    var json_data = {};
    json_data['username'] = val_username;
    json_data['plant'] = val_plant;
    json_data['tag'] = "";
    json_data['item_code'] = val_item_code;
    json_data['item_name'] = val_item_name;
    json_data['lot_no'] = val_lot_no;
    json_data['sloc'] = val_sloc;
    json_data['qty'] = val_qty;
    json_data['rack'] = val_rack;
    json_data['pallet'] = val_pallet;
    json_data['book_no'] = val_book_no;
    json_data['counter'] = val_counter;
    json_data['notes'] = val_notes;
    json_data['source'] = "manual";
    json_data['id'] = "1";

    // send request to server //
    $.ajax({
        url: './api/entry-api.php?cmd=check_entry', 
        type: 'POST',
        data: {
            cmd: "load",
            name: "sto_period"
        },
        dataType: 'json', 
        success: function(response) {
            console.log(response.status);
            console.log('Success:', response);
            var sto_period = response.data;
            $('#input_period').val(sto_period);
        },
        error: function(xhr, status, error) {
            // if server send invalid JSON reply //
            console.log('Error:', error); 
            console.log('Invalid server response:', xhr.responseText); 
        }
    });


    // - check entry data - //
    var jqxhr = $.ajax({
        url: "./api/sto_entry_api.php",
        method: "POST",
        data: {
            cmd: "check_entry",
            entry_data: JSON.stringify(entry_items),
            ignore_warning: ignore_warning_val
        }
    });

    jqxhr.done(function(msg) {
        // try parsing JSON response //
        try{
            var response = JSON.parse(msg);
            console.log(response);
            // if response ok //
            if(response['status'] == 'ok'){
                // no error or warning found //
                console.log(msg);
                console.log('> saving entry');

                // - save entry data - //
                var jqxhr1 = $.ajax({
                    url: "./api/sto_entry_api.php",
                    method: "POST",
                    data: {
                        cmd: "save_entry",
                        entry_data: JSON.stringify(entry_items),
                        ignore_warning: ignore_warning_val
                    }
                });

                jqxhr1.done(function(msg) {
                    // try parsing JSON response //
                    try{
                        var response = JSON.parse(msg);
                        console.log(response);
                        // if response ok //
                        if(response['status'] == 'ok'){
                            // no error or warning found //
                            console.log(msg);
                            console.log('> entry saved');
                            alert('Entry Saved');

                            // clear form //
                            clear_form();


                        // -- display error message -- //
                        }else if(response['status'] == 'error'){
                            console.log("error found");

                            $('#loading').hide();
                            // $('#btn_save').prop("disabled", false);
                            $("#btn_save").text("Save");
                            
                            // display error window //
                            console.log(response['message']);

                            var errors = response['message'];
                            console.log(errors);
                            var error_list = "<b>Mohon dicek kembali.</b><br/>";
                            for (const err_msg of errors) {
                                error_list = error_list + "<li>" + err_msg + "</li>";
                            }

                            console.log(error_list);
                            display_error(error_list);
                        

                        // -- display warning message -- //
                        }else if(response['status'] == 'warning'){
                            console.log("warning found")

                        }

                    // on JSON response parse error //
                    }catch(err){
                        
                        // alert(msg);
                        $('#loading').hide();
                        console.error(err);
                        // alert(err);
                        console.error(msg);
                        display_error("<b>Error parsing JSON response!</b> <hr/>" + err + "<br/>" + msg);
                        
                    }
                    
                });
                
                // on server request error //
                jqxhr1.fail(function(err) {
                    $('#loading').hide();

                    alert(err);
                    // $('#btn_save').prop("disabled", false);
                    $("#btn_save").text("Save");
                    $('#loading').hide();
                });  



            // -- display error message -- //
            }else if(response['status'] == 'error'){
                console.log("error found");

                $('#loading').hide();
                // $('#btn_save').prop("disabled", false);
                $("#btn_save").text("Save");
                
                // display error window //
                console.log(response['message']);

                var errors = response['message'];
                console.log(errors);
                var error_list = "<b>Mohon dicek kembali.</b><br/>";
                for (const err_msg of errors) {
                    error_list = error_list + "<li>" + err_msg + "</li>";
                }

                console.log(error_list);
                // display_error(error_list);
                display_error(errors);
             

            // -- display warning message -- //
            }else if(response['status'] == 'warning'){
                console.log("warning found")

            }

        // on JSON response parse error //
        }catch(err){
            
            // alert(msg);
            $('#loading').hide();
            console.error(err);
            // alert(err);
            console.error(msg);
            display_error("<b>Error parsing JSON response!</b> <hr/>" + err + "<br/>" + msg);
            
        }
        
    });
    
    // on server request error //
    jqxhr.fail(function(err) {
        $('#loading').hide();

        alert(err);
        // $('#btn_save').prop("disabled", false);
        $("#btn_save").text("Save");
        $('#loading').hide();
    });    
    
}
*/


/*
// -- SAVE ENTRY -- //
function saveEntry(){
    $('#loading').show();

    // - save entry data - //
    loadingMessage("Saving");
    $('#loading').show();
    $('#loading-text').html("Saving...");
    // $('#btn_save').prop("disabled", true);
    $("#btn_save").text("Saving");

    // get form data //
    var val_username = $("#username").val();            // user name //
    var val_period = $("#input_period").val();          // period //
    var val_plant = $("#input_plant").val();            // plant //
    var val_item_code = $("#input_item_code").val();    // item code //
    var val_item_name = $("#input_item_name").val();    // item name //
    var val_lot_no = $("#input_lot_no").val();          // lot no //
    var val_qty = $("#input_qty").val();                // qty //
    var val_sloc = $("#input_sloc").val();              // sloc //
    var val_rack = $("#input_rack").val();              // rack //
    var val_pallet = $("#input_pallet").val();          // pallet //
    var val_book_no = $("#input_book_no").val();        // book number //
    var val_counter = $("#input_counter").val();        // counter/checker //
    var val_notes = $("#input_notes").val();            // notes //
    
    // generate json data //
    var json_data = {};
    json_data['username'] = val_username;
    json_data['period'] = val_period;
    json_data['plant'] = val_plant;
    json_data['tag'] = "";
    json_data['item_code'] = val_item_code;
    json_data['item_name'] = val_item_name;
    json_data['lot_no'] = val_lot_no;
    json_data['sloc'] = val_sloc;
    json_data['qty'] = val_qty;
    json_data['rack'] = val_rack;
    json_data['pallet'] = val_pallet;
    json_data['book_no'] = val_book_no;
    json_data['counter'] = val_counter;
    json_data['notes'] = val_notes;
    json_data['source'] = "manual";
    json_data['id'] = "1";
    
    
    // create entry item list //
    var sto_array = Array();
    // insert entry item into sto entry list //
    sto_array.push(JSON.stringify(json_data))
    entry_items = sto_array;
    // console.log(entry_items);
    
    // - check entry data - //
    var jqxhr = $.ajax({
        // url: "./api/sto_entry_api.php",
        url: "./api/entry-api.php?cmd=save_entry",
        method: "POST",
        data: {
            entry_data: JSON.stringify(entry_items),
        }
    });

    jqxhr.done(function(msg) {
        // var response = JSON.parse(msg);
        response = msg;
        console.log(response);
        // if response ok //
        if(response.status == 'ok'){
            // alert('Entry Saved');
            $('#loading').hide();
            $("#btn_save").text("Save");
            toast("Entry saved");

            // clear form //
            clear_form();

        // -- display error message -- //
        }else if(response.status == 'error'){
            console.log("error found");

            $('#loading').hide();
            // $('#btn_save').prop("disabled", false);
            $("#btn_save").text("Save");
            
            // display error window //
            console.log(response['message']);

            var errors = response['message'];
            console.log(errors);
            var error_list = "<b>Mohon dicek kembali.</b><br/>";
            for (const err_msg of errors) {
                error_list = error_list + "<li>" + err_msg + "</li>";
            }

            console.log(error_list);
            // display_error(error_list);
            display_error(errors);
            

        // -- display warning message -- //
        }else if(response.status == 'warning'){
            console.log("warning found")
        }
        
    });
    
    // on server request error //
    jqxhr.fail(function(err) {
        console.error(err.responseText);
        $('#loading').hide();

        alert(err);
        // $('#btn_save').prop("disabled", false);
        $("#btn_save").text("Save");
        $('#loading').hide();

        display_error("<b>Invalid server response!</b> <hr/>" + err.responseText);
    });    

}
*/


// -- reset form -- //
function resetForm(){
    location.reload();

    /*
    $('#main_form').show();

    $("#input_item_code").val("");
    $("#input_item_name").val("");
    $("#input_lot_no").val("");
    $("#input_sloc").val("");
    $("#input_qty").val("");
    $("#input_pallet").val("");
    $("#input_rack").val("");
    $("#input_book_no").val("");
    $("#input_counter").val("");
    $("#input_notes").val("");

    $("#input_item_code").disable();
    $("#input_lot_no").disable();
    $("#input_sloc").disable();
    $("#input_qty").disable();
    $("#input_pallet").disable();
    $("#input_rack").disable();
    $("#input_book_no").disable();
    $("#input_counter").disable();
    $("#input_notes").disable();

    // $("#input_plant, #btn_submit_plant, #input_item_code, #btn_submit_item_code").prop("disabled", false);
    $("#input_plant").enable();
    $("#input_plant").select();
    $("#input_plant").focus();

    // hide items list / editor //
    $('#items_list_container, #item_editor').hide();

    // clear items list //
    // localStorage.setItem('items_list', "[]");
    // update items list //
    // updateItemsList();

    // disable save button //
    // $('#btn_save').prop("disabled", true);

    // autoload counter name from localstorage //
    $('#input_counter').val(localStorage.getItem('counter'));
    
    
    $('#pallet_warning_window, #entry_warning_window').hide();

    // get current user plant from server //
    get_current_plant();
    */
}


// - dismiss warning window - //
function dismissWarning(){
    $('#main_form').show();
    $('#pallet_warning_window, #entry_warning_window').hide();
}

// - dismiss error window - //
function dismissError(){
    $('#main_form').show();
    $('#entry_error_window').hide();
}


// - cancel item code - //
function cancelItemCode(){
    $('#input_item_code').val('');
    $('#input_lot_no').val('');
}


// - get sto period for plant - //
function get_sto_period(plant){
    $.ajax({
        // url: './api/config-api.php', 
        url: baseUrl + '/api', 
        type: 'GET',
        data: {
            cmd: "get_sto_period",
            plant: plant,
        },
        dataType: 'json', 
        success: function(response) {
            console.log(response.status);
            console.log('Success:', response);
            var sto_period = response.data;
            $('#input_period').val(sto_period);
        },
        error: function(xhr, status, error) {
            // if server send invalid JSON reply //
            console.log('Error:', error); 
            console.log('Invalid server response:', xhr.responseText);
            display_error("Failed obtaining current STO period! <hr/> Invalid server response!. " + xhr.responseText) 
        },
        beforeSend: function () {
            $("#period_loading").show().css("display", "inline-block"); // Show loading animation
        },
        complete: function () {
            $("#period_loading").hide(); // Hide loading animation
        }
    });
}


// - get current user plant - //
function get_current_plant(){
    $.ajax({
        // url: './api/config-api.php', 
        url: baseUrl + '/api', 
        type: 'GET',
        data: {
            cmd: "get_current_plant"
        },
        dataType: 'json', 
        success: function(response) {
            console.log(response.status);
            if(response.status == "ok"){
                console.log('Success:', response);
                var current_plant = response.data;
                $('#input_plant').val(current_plant);
                // set default plant to ID1B //
                $('#input_plant').val("ID1B");
            }else{
                display_error("Failed obtaining current user plant! <hr/>" + response.responseText);
            }
            
        },
        error: function(xhr, status, error) {
            // if server send invalid JSON reply //
            console.error('Error:', error); 
            console.error('Invalid server response:', xhr.responseText); 
            display_error('Invalid server response: <hr/>' + xhr.responseText);
        },
        beforeSend: function () {
            $("#plant_loading").show().css("display", "inline-block"); // Show loading animation
        },
        complete: function () {
            $("#plant_loading").hide(); // Hide loading animation
        }
    });
}


// - set current user plant - //
function set_current_plant(plant){
    $.ajax({
        // url: './api/config-api.php', 
        url: baseUrl + '/api', 
        type: 'GET',
        data: {
            cmd: "set_current_plant",
            plant: plant
        },
        dataType: 'json', 
        success: function(response) {
            console.log(response.status);
            if(response.status == "ok"){
                console.log('Success:', response);
            }else{
                display_error("Failed saving current user plant! <hr/>" + response.responseText);
            }
            
        },
        error: function(xhr, status, error) {
            // if server send invalid JSON reply //
            console.error('Error:', error); 
            console.error('Invalid server response:', xhr.responseText); 
            display_error('Invalid server response: <hr/>' + xhr.responseText); 
        },
        beforeSend: function () {
            $("#plant_loading").show().css("display", "inline-block"); // Show loading animation
        },
        complete: function () {
            $("#plant_loading").hide(); // Hide loading animation
        }
    });
}
