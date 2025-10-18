// on page load completed //
$(document).ready(function(){
    /*
    // - get current STO period - //
    $.ajax({
        // url: './api/config-api.php', 
        url: baseUrl + '/api', 
        type: 'GET',
        data: {
            cmd: "get_sto_period",
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
    */

    // - plant list - //
    var plant_list = ['', 'ID1A', 'ID1B', 'ID1C', 'ID1D'];
    $("#input_plant").autocomplete({
        source: plant_list,
        minLength: 0 // Allow search with zero characters
    }).on("focus", function() {
        $(this).autocomplete("search", ""); // Trigger search with an empty string
    });

    console.log(Cookies);
    // get current plant from user's cookies //
    let current_plant = Cookies.get('sts_plant');
    $("#input_plant").val(current_plant);

    // get current user plant from server //
    get_current_plant();

    // - autocomplete pallet list - //
    var plant_val = $('#input_plant').val();
    // jquery autocomplete //
    $("#input_pallet").autocomplete({
        source: function (request, response) {
            $.ajax({
                // url: "./api/pallet_list_api.php", // Replace with your endpoint URL
                // url: "./api/entry-api.php?cmd=search_pallet", // Replace with your endpoint URL
                // url: baseUrl + '/api?cmd=search_pallet',
                url: '/sts/api2/search_pallet', 
                dataType: "json",
                data: {
                    plant: $("#input_plant").val(),
                    search: $("#input_pallet").val(),
                },
                timeout: 5000,
                success: function (data) {
                    // console.log(data);
                    response(data.data); // Pass the data to the autocomplete widget
                },
                error: function () {
                    console.error("Failed to fetch data");
                },
                beforeSend: function () {
                    $("#pallet_loading").show().css("display", "inline-block"); // Show loading animation
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


    // autofocus on plant on page loaded //
    $('#input_plant').focus();

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

    // -- on pallet code entry -- //
    $('#input_pallet').keyup(function(event){
        if(event.which > 47){
            // on any key - except for special key //
            // autocompletePalletCode();
        }else{
            // on [Enter] key //
            if(event.key == "Enter"){
                // if pallet code not empty //
                if($('#input_pallet').val().length > 0){
                    submitPalletCode();
                }
            }
        }
    });

    // -- on leaving pallet code entry -- //
    $('#input_pallet').blur(function(event){
        if($('#input_pallet').val().length > 0){
            // submitPalletCode();
        }
        
    });

    // -- autocomplete rack list -- //
    $("#input_rack").autocomplete({
        source: function (request, response) {
            $.ajax({
                // url: "./api/entry-api.php", // Replace with your endpoint URL
                url: baseUrl + '/api?cmd=search_rack',
                dataType: "json",
                data: {
                    cmd: "search_rack",
                    plant: plant_val,
                    search: $("#input_rack").val() // Search term from the input field
                },
                success: function (data) {
                    console.log(data);
                    response(data.data); // Pass the data to the autocomplete widget
                },
                error: function (err) {
                    console.error("Failed to fetch data");
                    console.error(err);
                },
                beforeSend: function () {
                    $("#rack_loading").show().css("display", "inline-block"); // Show loading animation
                },
                complete: function () {
                    $("#rack_loading").hide(); // Hide loading animation
                }
            });
        },
        minLength: 1, // Minimum number of characters before searching
        select: function (event, ui) {
            console.log("Selected: " + ui.item.value);
        }
    });
    /*
    $('#input_rack').keyup(function(event){
        // on any key //
        autocompleteRack();

        // on Enter key //
        if(event.key == "Enter"){
            if($('#input_rack').val().length > 0){
                // focus to counter //
                $('#input_counter').focus();
                $('#input_counter').select();
            }else{
                // alert("Please Enter Rack!");
            }
        }
    })
    */


    // ###################### //
    // -- ITEM EDITOR FORM -- //
    // ###################### //
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
                // url: "./api/items_list_api.php", // Replace with your endpoint URL
                // url: "./api/entry-api.php?cmd=search_item", // Replace with your endpoint URL
                url: baseUrl + '/api?cmd=search_item',
                dataType: "json",
                data: {
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
                    $("#item_loading").show().css("display", "inline-block"); // Show loading animation
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
            // $('#input_qty').focus();
            // get current sloc //
            submitLotNo();
        }
    });

    // autocomplete lot no //
    $("#input_lot_no").autocomplete({
        source: function (request, response) {
            $.ajax({
                // url: "./api/batch_list_api.php", // Replace with your endpoint URL
                url: baseUrl + "/api?cmd=search_lot", // Replace with your endpoint URL
                dataType: "json",
                data: {
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


    // - on sloc entry - //
    $('#input_sloc').keyup(function(event){
        if(event.key == "Enter"){
            // $('#input_qty').focus();
            // get current sloc //
            submitSloc();
        }
    });

    // - autocomplete sloc - //
    $("#input_sloc").autocomplete({
        source: function (request, response) {
            $.ajax({
                // url: "./api/sloc_list_api.php", // Replace with your endpoint URL
                // url: "./api/entry-api.php?cmd=search_sloc", // Replace with your endpoint URL
                url: baseUrl + '/api?cmd=search_sloc',
                dataType: "json",
                data: {
                    plant: $("#input_plant").val(),
                    search: $("#input_sloc").val(),
                },
                success: function (data) {
                    response(data.data); // Pass the data to the autocomplete widget
                },
                error: function (err) {
                    console.error("Failed to fetch data");
                    console.error(err);
                },
                beforeSend: function () {
                    $("#sloc_loading").show().css("display", "inline-block"); // Show loading animation
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


    // autoload counter name from localstorage //
    $('#input_counter').val(localStorage.getItem('counter'));

    // -- on counter name entry -- //
    $('#input_counter').keyup(function(event){
        // on Enter key //
        if(event.key == "Enter"){
            validateCounter();
            // focus to notes //
            $('#input_notes').focus();
            $('#input_notes').select();
        }
    });
    
    $('#input_counter').blur(function(event){
        // validateCounter();
    });

    // -- on notes entry -- //
    $('#input_notes').keyup(function(event){
        // on Enter key //
        if(event.key == "Enter"){
            // focus to save button //
            $('#btn_save').focus();
        }
    });

})


// - SUBMIT PLANT - //
function submitPlant(){
    var plant_val = $('#input_plant').val();
    // if input plant entered //
    if(plant_val.length > 0){
        // verify current user plant //
        $.ajax({
            // url: baseUrl + '/api',
            // url: './api/entry-api.php', 
            url: '/sts/api2/check_user_plant',
            type: 'GET',
            data: {
                // cmd: "check_user_plant",
                plant: plant_val
            },
            beforeSend: function () {
                $("#plant_loading").show().css("display", "inline-block"); // Show loading animation
                // $("#table_container").hide(); // hide the table
            },
            complete: function () {
                $("#plant_loading").hide(); // Hide loading animation
                // $("#table_container").show(); // show the table
            },
            dataType: 'json', 
            success: function(response) {
                // console.log(response.status);
                console.log('Success:', response);
                // console.log(response.status);
                if(response.status == "ok"){
                    // user session ok //
                    $('#input_plant').disable();
                    $('#btn_submit_plant').disable();
                    $('#input_pallet').focus();
                    focusAndCenterInput('input_pallet');
                    $('#warning_invalid_plant').hide();
                    // get sto period for selected plant //
                    var plant = $('#input_plant').val();
                    get_sto_period(plant);
                    // save current user selected plant //
                    set_current_plant(plant);
                }else{
                    // display error  //
                    $('#warning_invalid_plant_message').html(response.message);
                    $('#warning_invalid_plant').show();
                    // blur from plant input //
                    $('#input_plant').blur();
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
        $('#warning_invalid_plant_message').html("Please enter PLANT");
        $('#warning_invalid_plant').show();
        $('#input_plant').focus();
    }

}


// submit pallet code entry //
function submitPalletCode(){
    var period_val = $('#input_period').val();
    var plant_val = $('#input_plant').val();
    var pallet_val = $('#input_pallet').val();
    // if input pallet entered //
    if(pallet_val.length > 0){
        // verify pallet code //
        $.ajax({
            // url: baseUrl + '/api?cmd=check_pallet',
            url: '/sts/api2/check_pallet',
            // url: './api/entry-api.php?cmd=check_pallet', 
            type: 'GET',
            data: {
                period: period_val,
                plant: plant_val,
                pallet: pallet_val
            },
            timeout: 5000,
            dataType: 'json', 
            success: function(response) {
                console.log(response);
                // console.log('Success:', response);
                // console.log(response.status);
                if(response.status == "ok"){
                    // pallet ok //
                    $('#input_pallet').disable();
                    $('#input_rack').focus();
                    focusAndCenterInput('input_rack');
                    $('#warning_invalid_pallet').hide();

                    // get rack //
                    getRack();
                    // get items inside pallet //
                    getItems();

                    // display items list //
                    $("#items_list_container").show();
                }else if(response.status == "warning"){
                    // - warning found - //
                    // display warning window //
                    var warnings = response['message'];
                    $('#main_form').hide();
                    $('#loading_window').hide();
                    $('#pallet_warning_window').show();
                    $('#pallet_warning_message').html(warnings);
                    // get rack //
                    getRack();
                    // get items inside pallet //
                    getItems();
                    // display items list //
                    $("#items_list_container").show();
                }else{
                    // invalid pallet //
                    $("#input_pallet").autocomplete("close");
                    $('#warning_invalid_pallet_message').html(response.message);
                    $('#warning_invalid_pallet').show();
                    // focus on input_item_code //
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


// submit rack code entry //
function submitRack(){
    var period_val = $('#input_period').val();
    var plant_val = $('#input_plant').val();
    var pallet_val = $('#input_pallet').val();
    // if input pallet entered //
    if(pallet_val.length > 0){
        // verify input item code //
        $.ajax({
            url: './api/entry-api.php?cmd=check_pallet', 
            type: 'GET',
            data: {
                period: period_val,
                plant: plant_val,
                pallet: pallet_val
            },
            dataType: 'json', 
            success: function(response) {
                console.log(response);
                // console.log('Success:', response);
                // console.log(response.status);
                if(response.status == "ok"){
                    // pallet ok //
                    $('#input_rack').focus();
                    $('#warning_invalid_pallet').hide();

                    // get rack //
                    getRack();
                    // get items inside pallet //
                    getItems();

                    // display items list //
                    $("#items_list_container").show();
                }else if(response.status == "warning"){
                    // - warning found - //
                    // alert("warning found");

                    // display warning window //
                    var warnings = response['message'];
                    $('#main_form').hide();
                    $('#loading_window').hide();
                    $('#pallet_warning_window').show();
                    $('#pallet_warning_message').html(warnings);
                    // get rack //
                    getRack();
                    // get items inside pallet //
                    getItems();
                    // display items list //
                    $("#items_list_container").show();
                }else{
                    // invalid pallet //
                    $('#warning_invalid_pallet_message').html(response.message);
                    $('#warning_invalid_pallet').show();
                    // focus on input_item_code //
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
                    $('#input_item_code').disable();
                    $('#input_lot_no').focus();
                    $('#warning_invalid_item').hide();
                    // set item name //
                    $('#input_item_name').val(response.item_name);
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
    $('#input_sloc').focus();
    /*
    var period_val = $('#input_period').val();
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
                cmd: "get_sloc",
                period: period_val,
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
                    // set item name //
                    $('#input_sloc').val(response.data);
                    $("#input_sloc").autocomplete('close');
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
            }
        });        
    }else{
        // display error  //
        $('#warning_invalid_item_message').html("Please enter ITEM CODE");
        $('#warning_invalid_item').show();
    }
    */   
}



// - submit sloc - //
function submitSloc(){
    $('#input_qty').focus();
    /*
    var period_val = $('#input_period').val();
    var plant_val = $('#input_plant').val();
    var item_code_val = $('#input_item_code').val();
    var lot_no_val = $('#input_lot_no').val();

    // if lot no entered //
    if(lot_no_val.length > 0){
        // verify input item code //
        $.ajax({
            url: baseUrl + '/api', 
            type: 'GET',
            data: {
                cmd: "get_sloc",
                period: period_val,
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
                    $('#input_lot_no').disable();
                    $('#input_sloc').focus();
                    $('#warning_invalid_lot_no').hide();
                    // set item name //
                    $('#input_sloc').val(response.data);
                    $("#input_sloc").autocomplete('close');
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
            }
        });        
    }else{
        // display error  //
        $('#warning_invalid_item_message').html("Please enter ITEM CODE");
        $('#warning_invalid_item').show();
    }
    */
}


// clear pallet code input //
function clearPalletCode(){
    $('#input_pallet').val("");
    $('#input_pallet').focus();
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
    var pallet = $('#input_pallet').val();
    var jqxhr = $.ajax({
        url: "./entry/pallet/validate_pallet_api.php",
        method: "GET",
        data: {
                cmd: "check_pallet",
                pallet_code: pallet
            }
    });
    jqxhr.done(function(msg) {
        var response = JSON.parse(msg);
        if(response['status'] == 'error'){
            // invalid pallet code - display error  //
            $('#warning_invalid_pallet_message').html(response['message']);
            $('#warning_invalid_pallet').show();
            $('#input_pallet').prop("disabled", false);
            $('#btn_submit_pallet').prop("disabled", false);
            $('#btn_save').prop("disabled", true);
            // $('#input_pallet').focus();
            // $('#input_pallet').select();
        }else{
            // pallet code valid //
            $('#input_pallet').prop("disabled", true);
            $('#btn_submit_pallet').prop("disabled", true);
            $('#btn_submit_pallet').hide();
            $('#warning_invalid_pallet').hide();
            $('#btn_add').prop("disabled", false);
            $('#input_counter').prop("disabled", false);
            $('#input_notes').prop("disabled", false);
            // $('#btn_save').prop("disabled", false);
            $('#input_rack').focus();
            $('#input_rack').select();

            // get rack for current pallet //
            getRack();
            // get list of items //
            getItems();            
        }
    });
    jqxhr.fail(function(err) {
        console.error(err);
        alert(err);
    });
}


// -- check for already entered pallet function -- //
function checkPallet(){
    var result = false;
    var pallet = $('#input_pallet').val();
    var jqxhr = $.ajax({
        url: "./entry/pallet/entry_pallet_api.php",
        method: "POST",
        data: {
                cmd: "check_pallet",
                pallet_code: pallet
            }
    });

    jqxhr.done(function(msg) {
        var response = JSON.parse(msg);
        if(response['status'] == 'warning'){
            // display warning bootstrap modal window //
            $('#modal_warning_pallet .modal-title').html("Warning! Pallet already counted");
            $('#modal_warning_pallet .modal-body').html(response['html']);
            $('#modal_warning_pallet').modal();
            $('#bs_btn_close').focus();

            // return TRUE if pallet already counted //
            result = true;
        }
    });

    jqxhr.fail(function(err) {
        console.error(err);
        alert(err);
    });

    return result;
}


// ignore counted pallet warning //
function ignore_pallet_warning(){
    // hide warning modal //
    $('#modal_warning').modal("hide");
    // get rack for current pallet //
    getRack();
    // get list of items //
    getItems();
}


// -- get rack from pallet code //
function getRack(){
    var period_val = $('#input_period').val();
    var plant_val = $('#input_plant').val();
    var pallet_val = $('#input_pallet').val();
    
    $.ajax({
        url: '/sts/api?cmd=get_rack',
        // url: '/sts/api2/get_rack',
        // url: './api/entry-api.php?cmd=get_rack',
        type: 'GET',
        data: {
            period: period_val,
            plant: plant_val,
            pallet: pallet_val
        },
        dataType: 'json', 
        success: function(response) {
            // console.log(response.status);
            // console.log('Success:', response);
            // console.log(response.status);
            if(response.status == "ok"){
                // data ok //
                $('#input_rack').val(response.data);
            }else{
                // data not found //
                $('#input_rack').val("TEMP_REC");
            }
        },
        error: function(xhr, status, error) {
            // if server send invalid JSON reply //
            console.log('Error:', error); 
            console.log('Invalid server response:', xhr.responseText); 
            display_error('Invalid server response: <hr/>' + xhr.responseText); 
        }
    });        
    

    /*
    var pallet = $('#input_pallet').val();
    var jqxhr = $.ajax({
        // url: "./entry/pallet/entry_pallet_api.php",
        url: './api/entry-api.php?cmd=get_rack', 
        method: "POST",
        data: {
                period: $('#input_period').val(),
                plant: $('#input_plant').val(),
                pallet: $('#input_pallet').val()
            }
    });

    jqxhr.done(function(msg) {
        // var response = JSON.parse(msg);
        var response = msg;
        if(response['status'] == 'ok'){
            $('#input_rack').val(response['data']);
        }else{
            // rack not found //
            $('#input_rack').val("");
        }

        // enable rack entry //
        $('#input_rack').prop("disabled", false);
        $('#btn_add').prop("disabled", false);
        $('#input_rack').focus();
        $('#input_rack').select();
    });

    jqxhr.fail(function(err) {
        alert(err);
    });
    */
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

// -- get list of items inside pallet -- //
function getItems(){
    var period_val = $('#input_period').val();
    var plant_val = $('#input_plant').val();
    var pallet_val = $('#input_pallet').val();
    
    $.ajax({
        url: '/sts/api?cmd=load_items', 
        type: 'GET',
        data: {
            period: period_val,
            plant: plant_val,
            pallet: pallet_val
        },
        dataType: 'json', 
        success: function(response) {
            console.log(response);
            // console.log('Success:', response);
            // console.log(response.status);
            if(response.status == "ok"){
                // data ok //
                // $('#input_rack').val(response.data);
                // parse items list //
                var items_list = [];
                try {
                    items_list = JSON.parse(response['item_list']);
                    $('#json_items_list').text(response['item_list']);

                    console.log(items_list);

                    // add row id //
                    for(var i = 0; i < items_list.length; i++){
                        items_list[i]['id'] = (i + 1).toString();
                    }
                    console.log(items_list);

                    // // datatables json data //
                    // var dt_data = {};
                    // dt_data['data'] = [items_list];

                    // console.log(dt_data);

                    // $('#text1').text(JSON.stringify(dt_data));

                    // store items count //
                    var items_count = items_list.length;
                    localStorage.setItem('items_count', items_count);

                    // store items list array //
                    localStorage.setItem('items_list', JSON.stringify(items_list));

                    // update items list //
                    updateItemsList();

                    // disable pallet input //
                    // $('#input_pallet').prop("disabled", true);
                    // focus to rack entry //
                    $('#input_rack').focus();
                    $('#input_rack').select();

                    // update tabulator row data //
                    // table.setData(items_list);
                    // table.redraw(true);

                    // set items count //
                    $('#items_count').val(items_list.length);
                    // set last row id //
                    $('#last_row').val(items_list.length);

                    // enable add button //
                    $('#btn_add').prop("disabled", false);

                    // alert("items count : " + items_list.length);

                } catch (error) {
                    // on json parse error //
                    console.error(error);
                    display_error('Failed parsing JSON items: <hr/>' + error);
                }
            }else{
                // data not found //
                // $('#input_rack').val("TEMP_REC");
            }
        },
        error: function(xhr, status, error) {
            // if server send invalid JSON reply //
            console.log('Error:', error); 
            console.log('Invalid server response:', xhr.responseText); 
            display_error('Invalid server response: <hr/>' + xhr.responseText); 
        },
        beforeSend: function () {
            $("#items_loading").show(); // Show loading animation
        },
        complete: function () {
            $("#items_loading").hide(); // Hide loading animation
        }
    });        

    /*
    // get entered pallet code //
    var input_pallet_val = $('#input_pallet').val();

    
    // get items list from database //
    $('.loading').show();
    var jqxhr = $.ajax({
        url: "./entry/pallet/entry_pallet_api.php",
        method: "POST",
        data: {
                cmd: "load_items",
                pallet: input_pallet_val,
                plant: $('#input_plant').val()
            }
    });

    jqxhr.done(function(msg){
        console.log(msg);
        // parse response //
        try {
            var response = JSON.parse(msg);
        } catch (error) {
            console.error(error);
            console.log(msg);
        }
        // parse items list //
        var items_list = [];
        try {
            items_list = JSON.parse(response['item_list']);
            $('#json_items_list').text(response['item_list']);

            console.log(items_list);

            // add row id //
            for(var i = 0; i < items_list.length; i++){
                items_list[i]['id'] = (i + 1).toString();
            }
            console.log(items_list);

            // // datatables json data //
            // var dt_data = {};
            // dt_data['data'] = [items_list];

            // console.log(dt_data);

            // $('#text1').text(JSON.stringify(dt_data));
            // localStorage.setItem('dt_data', JSON.stringify(dt_data));


            // disable pallet input //
            // $('#input_pallet').prop("disabled", true);
            // focus to rack entry //
            $('#input_rack').focus();
            $('#input_rack').select();

            // update tabulator row data //
            table.setData(items_list);
            table.redraw(true);

            // set items count //
            $('#items_count').val(items_list.length);
            // set last row id //
            $('#last_row').val(items_list.length);

            // alert("items count : " + items_list.length);

        } catch (error) {
            console.error(error);
            console.log(msg);
        }
 
        // clear item list //
        clearItem();

        // clear item index //
        localStorage.setItem('item_index', '[]');

        // display each item list //
        for(i = 0; i < items_list.length; i++){
            // parse current item data //
            var item_data = (items_list[i]);
            var item_id = item_data['id'];
            var item_code = item_data['item_code'];
            var item_name = item_data['item_name'];
            var lot_no = item_data['lot_no'];
            var lot_no_list = item_data['lot_no_list'];
            var rack = item_data['rack'];
            var plant = item_data['plant'];
            var qty = item_data['qty'];
            var sloc = item_data['sloc'];
            var sloc_list = item_data['sloc_list'];

            item_data['pallet'] = input_pallet_val;
            item_data['rack'] = $('#input_rack').val();

            item_data['counter'] = $("#input_counter").val();
            item_data['username'] = $("#username").val();

            // add item into list //
            addItem(item_code, item_name, lot_no_list, lot_no, qty, sloc_list, sloc, $('#input_pallet').val(), $('#input_rack').val());

        }

        // store items list into localstorage //
        item_list_json = JSON.stringify(item_list);
        localStorage.setItem('item_list', item_list_json);

        $('.loading').hide();

    });

    jqxhr.fail(function(err){
        console.error(err);
        alert(err);

        $('.loading').hide();
    });

    */
        
}



// -- check entry before saving -- /
function checkEntry(ignore_warning){

    console.log(" > checking entry pallet");

    // $('#btn_save').prop("disabled", true);
    // $("#btn_save").text("Saving");

    // $('.loading').show();
    $('#main_form').hide();
    $('#loading_message').html('Checking...');
    $('#loading_window').show();
    
    // get form data //
    var val_username = $("#username").val();            // user name //
    var val_period = $("#input_period").val();          // period //
    var val_plant = $("#input_plant").val();            // plant //
    var val_rack = $("#input_rack").val();              // rack //
    var val_pallet = $("#input_pallet").val();          // pallet //
    var val_book_no = $("#input_book_no").val();        // book number //
    var val_counter = $("#input_counter").val();        // counter/checker //
    var val_notes = $("#input_notes").val();            // notes //

    // autosave counter name //
    localStorage.setItem('counter', val_counter);

    // prepare entry items list //
    var entry_items = Array();

    // get items list from localstorage //
    var items_list = JSON.parse(localStorage.getItem('items_list'));
    console.log(items_list);
    items_list.forEach(item_data => {
        console.log(item_data);
        // create entry data //
        var entry_data = {};
        entry_data['username'] = val_username;
        entry_data['period'] = val_period;
        entry_data['plant'] = val_plant;
        entry_data['tag'] = "";
        entry_data['item_code'] = item_data.item_code;
        entry_data['item_name'] = item_data.item_name;
        entry_data['lot_no'] = item_data.lot_no;
        entry_data['sloc'] = item_data.sloc;
        entry_data['qty'] = item_data.qty;
        entry_data['rack'] = val_rack;
        entry_data['pallet'] = val_pallet;
        entry_data['book_no'] = "";
        entry_data['counter'] = val_counter;
        entry_data['notes'] = val_notes;
        entry_data['source'] = "pallet";
        entry_data['id'] = item_data.id;

        // insert entry data into sto entry items list //
        entry_items.push(JSON.stringify(entry_data));
    })

    console.log(entry_items);

    // - send entry data to server - //
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


    /*
    // - send entry data into server - //
    var jqxhr = $.ajax({
        // url: "./api/sto_entry_api.php",
        url: baseUrl + "/api?cmd=check_entry",
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
            // entry valid //
            saveEntry();
        // -- display error message -- //
        }else if(response.status == 'error'){
            console.log("error found");

            $('#loading').hide();
            // $('#btn_save').prop("disabled", false);
            // $("#btn_save").text("Save");
            
            // display error window //
            console.log(response['message']);

            var errors = response['messages'];
            console.log(errors);
            $('#main_form').hide();
            $('#loading_window').hide();
            $('#error_window').show();
            $('#error_message').html(errors);

            /*
            var error_list = "<b>Mohon dicek kembali.</b><br/>";
            for (const err_msg of errors) {
                error_list = error_list + "<li>" + err_msg + "</li>";
            }
            

            // console.log(error_list);
            // display_error(error_list);
            // display_error(errors);
            

        // -- display warning message -- //
        }else if(response.status == 'warning'){
            console.log("warning found")
            console.log(response);
            var warnings = response['messages'];
            // alert(warning_messages);

            $('#main_form').hide();
            $('#loading_window').hide();
            $('#entry_warning_window').show();
            $('#entry_warning_message').html(warnings);
        }

        // $('#main_form').show();
        // $('#loading_window').hide();
        // $('#loading_message').html('Checking...');
        
    });
    
    // on server request error //
    jqxhr.fail(function(err) {
        console.error(err.responseText);
        $('#loading').hide();

        console.error(err);
        // $('#btn_save').prop("disabled", false);
        // $("#btn_save").text("Save");
        $('#loading').hide();

        display_error("<b>Invalid server response!</b> <hr/>" + err.responseText);

        // $('#main_form').show();
        $('#loading_window').hide();
        $('#main_form').show();
    });   
    
    */

}


// -- save entry into database -- /
function saveEntry(ignore_warning){

    console.log(" > saving entry pallet");

    $('#main_form, #entry_warning_window').hide();
    $('#loading_message').html('Saving...');
    $('#loading_window').show();
    
    // $('#btn_save').prop("disabled", true);
    // $("#btn_save").text("Saving");

    // $('.loading').show();

    // get form data //
    var val_username = $("#username").val();            // user name //
    var val_period = $("#input_period").val();          // period //
    var val_plant = $("#input_plant").val();            // plant //
    var val_rack = $("#input_rack").val();              // rack //
    var val_pallet = $("#input_pallet").val();          // pallet //
    var val_book_no = $("#input_book_no").val();        // book number //
    var val_counter = $("#input_counter").val();        // counter/checker //
    var val_notes = $("#input_notes").val();            // notes //

    // prepare entry items list //
    var entry_items = Array();

    // get items list from localstorage //
    var items_list = JSON.parse(localStorage.getItem('items_list'));
    console.log(items_list);
    items_list.forEach(item_data => {
        console.log(item_data);
        // create entry data //
        var entry_data = {};
        entry_data['username'] = val_username;
        entry_data['period'] = val_period;
        entry_data['plant'] = val_plant;
        entry_data['tag'] = "";
        entry_data['item_code'] = item_data.item_code;
        entry_data['item_name'] = item_data.item_name;
        entry_data['lot_no'] = item_data.lot_no;
        entry_data['sloc'] = item_data.sloc;
        entry_data['qty'] = item_data.qty;
        entry_data['rack'] = val_rack;
        entry_data['pallet'] = val_pallet;
        entry_data['book_no'] = "";
        entry_data['counter'] = val_counter;
        entry_data['notes'] = val_notes;
        entry_data['source'] = "pallet";
        entry_data['id'] = item_data.id;

        // insert entry data into sto entry items list //
        entry_items.push(JSON.stringify(entry_data));
    })

    console.log(entry_items);

    // - send entry data into server - //
    var jqxhr = $.ajax({
        url: baseUrl + '/api?cmd=save_entry',
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

            // clear form //
            clearForm();
            // location.reload();

            $('#main_form').show();
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

        // alert("Critical error occured! Please check error logs!");
        // $('#btn_save').prop("disabled", false);
        $("#btn_save").text("Save");
        $('#loading').hide();

        display_error("<b>Invalid server response!</b> <hr/>" + err.responseText);

        $('#main_form').show();
        $('#loading_window').hide();
    });    

}



//////////////////////////////
//  -- ITEMS LIST EDITOR -- //
//////////////////////////////

// -- open add new item form -- //
function addItem(){
    // create empty item data //
    $('#item_type').val('new');
    $('#item_id').val("");
    $('#input_item_code').prop("disabled", false);
    $('#input_item_code').val("");
    $('#input_item_name').val("");
    $('#input_lot_no').val("");
    $('#input_sloc').val("");
    $('#input_qty').val("");

    // open form for editing //
    $('#item_editor_container').show();
    $('#items_list_container').hide();
    $('#item_editor_container').show();
    $('#item_type').val('new');
    $('#input_item_code').focus();
    focusAndCenterInput('input_item_code');
}


// update items list table from JSON data //
function updateItemsList(){
    var items_list = JSON.parse(localStorage.getItem('items_list'));
    console.log(items_list);
    // populateTable(items_list);
    // clear table row //
    $('#items_list_table tbody').empty();
    // Loop through each item in the JSON array
    $.each(items_list, function(index, item) {
        // Create a new table row for each object
        var id = item.id;
        var newRow = '<tr onclick=\"openItem(\'' + id + '\')\">';
        newRow += '<td>' + item.item_name + '</td>';
        newRow += '<td>' + item.lot_no + '</td>';
        newRow += '<td>' + item.sloc + '</td>';
        newRow += '<td>' + item.qty + '</td>';
        newRow += '</tr>';

        // Append the new row to the table body
        $('#items_list_table tbody').append(newRow);
    });
}

// - open item for editing - //
function openItem(id){
    // var id = id - 1;
    // console.log(id);
    // alert("opening item " + id);
    // get item detail //
    var items_list = JSON.parse(localStorage.getItem('items_list'));
    // console.log(items_list[id]);
    // var item = items_list[id];
    var item_data = items_list.find(item => item.id === id);
    // console.log(item_data);
    // console.log(items_list.length);
    // put item detail into editor //
    $('#item_type').val('existing');
    $('#item_id').val(item_data.id);
    $('#input_item_code').val(item_data.item_code);
    $('#input_item_name').val(item_data.item_name);
    $('#input_lot_no').val(item_data.lot_no);
    $('#input_sloc').val(item_data.sloc);
    $('#input_qty').val(item_data.qty);

    // open form for editing //
    $('#item_editor_container').show();
    $('#items_list_container').hide();

    // disable certain forms //
    $('#input_item_code, #btn_submit_item_code').prop("disabled", true);

}


// - save current item - //
function saveItem(){
    // get opened item type //
    var item_type = $('#item_type').val();
   
    // if existing item //
    if(item_type === "existing"){
        // alert("existing item");

        // get opened item id //
        var item_id = $('#item_id').val();
        // alert("saving item id " + item_id);
        var lot_no_val = $("#input_lot_no").val();
        var sloc_val = $("#input_sloc").val();
        var qty_val = $("#input_qty").val();

        // get current item list data //
        var items_list = JSON.parse(localStorage.getItem('items_list'));

        // update data with id //
        items_list = items_list.map(item => {
            if (item.id === item_id) {
                item.lot_no = lot_no_val;  // change sloc //
                item.sloc = sloc_val;  // change sloc //
                item.qty = qty_val;  // change qty //
            }
            return item;
        });

        // update items list data //
        localStorage.setItem('items_list', JSON.stringify(items_list));

    }else if(item_type == "new"){
        // if new item //
        // alert('new item');
        // create new item data //

        // get form data //
        var val_username = $("#username").val();            // user name //
        var val_period = $("#input_period").val();          // period //
        var val_plant = $("#input_plant").val();            // plant //
        var val_item_code = $("#input_item_code").val();              // item code //
        var val_item_name = $("#input_item_name").val();              // item name //
        var val_lot_no = $("#input_lot_no").val();              // lot no //
        var val_sloc = $("#input_sloc").val();              // sloc //
        var val_qty = $("#input_qty").val();              // qty //
        var val_rack = $("#input_rack").val();              // rack //
        var val_rack = $("#input_rack").val();              // rack //
        var val_pallet = $("#input_pallet").val();          // pallet //
        var val_book_no = $("#input_book_no").val();        // book number //
        var val_counter = $("#input_counter").val();        // counter/checker //
        var val_notes = $("#input_notes").val();            // notes //

        // get new item id //
        var val_id = JSON.parse(localStorage.getItem('items_count'));
        var val_id = val_id + 1;
        // update new items count //
        localStorage.setItem('items_count', val_id);
        
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
        json_data['id'] = val_id.toString();

        var item_data = json_data;

        console.log(item_data);

        // get current item list data //
        var items_list = JSON.parse(localStorage.getItem('items_list'));
        items_list.push(item_data);

        // update items list data //
        localStorage.setItem('items_list', JSON.stringify(items_list));
        console.log(items_list);


        
    }

    updateItemsList();

    // back to items list //
    $('#item_editor_container').hide();
    $('#items_list_container').show();
    /*
    // Update the age of the user with id = 2
    users = users.map(user => {
        if (user.id === 2) {
        user.age = 32;  // Change age
        }
        return user;
    });
    */
}

// - delete current item - //
function deleteItem(){
    if(confirm("Delete this item?")){
        // get opened item id //
        var item_id = $('#item_id').val();
        // console.log(item_id);
        // alert("delete item " + item_id);

        // delete from items list json data //
        const idToDelete = item_id;
        var items_list = JSON.parse(localStorage.getItem('items_list'));
        items_list = items_list.filter(item => item.id !== idToDelete);
        // console.log(items_list);

        // update items list data //
        localStorage.setItem('items_list', JSON.stringify(items_list));

        updateItemsList();

        // back to items list //
        $('#item_editor_container').hide();
        $('#items_list_container').show();
    }
    

    /*
    // get item detail //
    var items_list = JSON.parse(localStorage.getItem('items_list'));
    console.log(items_list[id]);
    var item = items_list[id];
    // console.log(items_list.length);
    // put item detail into editor //
    $('#input_item_code').val(item.item_code);
    $('#input_item_name').val(item.item_name);
    $('#input_lot_no').val(item.lot_no);
    $('#input_sloc').val(item.sloc);
    $('#input_qty').val(item.qty);
    */
}


// - close item editor form - //
function closeItemEditor(){
    $('#item_editor_container').hide();
    $('#items_list_container').show();
}




// on counter checker entry //
$('#input_counter').keyup(function(event){
    // if entry not empty //
    if($('#input_counter').val().length > 0){
        // store counter name into cookies //
        setCookie('counter', $('#input_counter').val(), 2);
        // enable save button //
        $('#btn_save').prop("disabled", false);
    }else{
        $('#btn_save').prop("disabled", true);
    }        
})


// cancel ongoing entry process //
function stocktaking_entry_cancel(){
    // clear items list //
    // localStorage.clear();
    $("#items_list").html("");
    // clear pallet entry //
    $('#input_pallet').val('');
    $('#input_pallet').prop("disabled", false);
    $('#input_pallet').focus();
    // clear rack entry //
    $('#input_rack').val("");
    $('#input_rack').prop("disabled", true);
    // clear sloc entry //
    $('#input_sloc').val("");
    $('#input_sloc').prop("disabled", true);
    // clear counter entry //
    $('#input_counter').val("");
    // $('#input_counter').prop("disabled", true);

    // hide warning message //
    $('#bs_warning_message').hide();
}

// auto capital input //
$('#input_lot_no').keyup(function(){
    // alert("haaa");
})


// - validate counter name - //
function validateCounter(){
    // disable save button if counter name is empty //
    var val_counter = $('#input_counter').val();
    console.log(val_counter.length);
    if(val_counter.length > 0){
        // counter name valid //
        $('#warning_invalid_counter').hide();
        $('#btn_save').prop("disabled", false);
        // autosave counter name into localstorage //
        localStorage.setItem('counter', val_counter);
    }else{
        // invalid counter name entry - display error  //
        $('#warning_invalid_counter').html("Counter name cannot be empty!");
        $('#warning_invalid_counter').show();
        $('#btn_save').prop("disabled", true);
        $('#input_counter').focus();
    }
}


// clear form //
function clearForm(){
    // simply reload the page //
    location.reload();
    
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

    

    // hide items list / editor //
    $('#items_list_container, #item_editor').hide();
    $('#pallet_warning_window, #entry_warning_window').hide();

    // clear items list //
    localStorage.setItem('items_list', "[]");
    // update items list //
    updateItemsList();

    // disable save button //
    // $('#btn_save').prop("disabled", true);

    // autoload counter name from localstorage //
    $('#input_counter').val(localStorage.getItem('counter'));

    $('#main_form').show();
    $("#input_plant, #btn_submit_plant").prop("disabled", false);
    $("#input_plant").focus();
    $("#input_plant").autocomplete("close");
    // focusAndCenterInput('input_plant');

    // get current user plant from server //
    get_current_plant();
    
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

// - ignore warning - //
function ignoreWarning(){
    $('#main_form').show();
    $('#pallet_warning_window, #entry_warning_window').hide();
    $('#input_rack').focus();
    // get rack //
    // getRack();
    // get items inside pallet //
    // getItems();
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
            if(response.status == "ok"){
                console.log('Success:', response);
                var sto_period = response.data;
                $('#input_period').val(sto_period);
            }else{
                display_error("Failed obtaining current STO period! <hr/>" + response.message);
            }
            
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


// - reset form - //
function resetForm(){
    location.reload();
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
