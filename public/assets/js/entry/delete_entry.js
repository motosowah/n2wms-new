// - on page load completed - //
$(document).ready(function(){
    // - plant list - //
    var plant_list = ['', 'ID1A', 'ID1B', 'ID1C', 'ID1D'];
    $("#input_plant").autocomplete({
        source: plant_list,
        minLength: 0 // Allow search with zero characters
    }).on("focus", function() {
        $(this).autocomplete("search", ""); // Trigger search with an empty string
    });

    // focus to entry id input //
    $('#input_plant').focus();
    $('#input_plant').select();

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


    // on tag number data entry //
    $('#input_entry_id').keyup(function(event){
        if(event.key == "Enter"){
            // [Enter] key pressed //
            submitEntryId();
        }
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
            // url: './api/entry-api.php', 
            type: 'GET',
            data: {
                cmd: "check_user_plant",
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
                    // $('#input_plant').disable();
                    // $('#btn_submit_plant').disable();
                    $('#input_entry_id').focus();
                    // focusAndCenterInput('input_pallet');
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

// submit entry id //
function submitEntryId(){
    var period = $('#input_period').val();
    var plant = $('#input_plant').val();
    var entry_id = $('#input_entry_id').val();

    getItem(period, plant, entry_id);
}

// - get cookie - //
function getCookie(cname) {
    let name = cname + "=";
    let decodedCookie = decodeURIComponent(document.cookie);
    let ca = decodedCookie.split(';');
    for(let i = 0; i <ca.length; i++) {
        let c = ca[i];
        while (c.charAt(0) == ' ') {
            c = c.substring(1);
        }
        if (c.indexOf(name) == 0) {
            return c.substring(name.length, c.length);
        }
    }
    return "";
}

// -- get entry item from id -- //
function getItem(period, plant, entry_id){
    $.ajax({
        // url: './api/config-api.php', 
        url: baseUrl + '/api', 
        type: 'GET',
        data: {
            cmd: "get_sto_entry_id",
            period: period,
            plant: plant,
            id: entry_id,
        },
        dataType: 'json', 
        success: function(response) {
            console.log(response.status);
            // if response OK //
            if(response['status'] == "ok"){
                // parse item data //
                // var item = JSON.parse(response['item_data']);
                var item = (response['item_data']);
                console.log(item);
                // display stock data into data entry form //
                // item code //
                $('#input_plant').val(item['plant']);
                // item code //
                $('#input_item_code').val(item['item_code']);
                // item name //
                $('#input_item_name').val(item['item_name']);
                // lot no //
                $('#input_lot_no').val(item['lot_no']);
                // sloc //
                $('#input_sloc').val(item['sloc']);
                // qty //
                $('#input_qty').val(item['qty']);
                // pallet //
                $('#input_pallet').val(item['pallet']);
                // rack //
                $('#input_rack').val(item['rack']);
                // book no //
                $('#input_book_no').val(item['book_no']);
                // counter //
                $('#input_counter').val(item['counter']);
                // notes //
                $('#input_notes').val(item['notes']);
                
                // focus to delete button //
                $('#btn_delete').focus();

            }else{
                // display_error("Failed obtaining entry data id! <hr/>" + response.responseText);
                alert("Entry data not found!");
                
                // focus to tag no entry //
                $('#input_entry_id').focus();
                $('#input_entry_id').select();
            }            
        },
        error: function(xhr, status, error) {
            // if server send invalid JSON reply //
            console.error('Error:', error); 
            console.error('Invalid server response:', xhr.responseText); 
            display_error('Invalid server response: <hr/>' + xhr.responseText);
        },
        beforeSend: function () {
            $("#id_loading").show().css("display", "inline-block"); // Show loading animation
        },
        complete: function () {
            $("#id_loading").hide(); // Hide loading animation
        }
    });

/*
    // get current user name //
    var user_name = getCookie('username');
    // if entry id not empty //
    if(entry_id.length > 0){
        var jqxhr = $.ajax({
                // url: "./report/delete_entry_api.php",
                url: baseUrl + '/api',
                method: "GET",
                data: {
                    cmd: "get_sto_entry_id",
                    id: entry_id,
                }
            });
            
        // on server request completed //
        jqxhr.done(function(msg) {
            console.log(msg);
            // alert(msg);
            
            // process response //
            var response = (msg);
            console.log(response);
            // if response OK //
            if(response['status'] == "ok"){
                // parse item data //
                var item = JSON.parse(response['item_data']);
                console.log(item);
                // display stock data into data entry form //
                // item code //
                $('#input_plant').val(item['plant']);
                // item code //
                $('#input_item_code').val(item['item_code']);
                // item name //
                $('#input_item_name').val(item['item_name']);
                // lot no //
                $('#input_lot_no').val(item['lot_no']);
                // sloc //
                $('#input_sloc').val(item['sloc']);
                // qty //
                $('#input_qty').val(item['qty']);
                // pallet //
                $('#input_pallet').val(item['pallet']);
                // rack //
                $('#input_rack').val(item['rack']);
                // book no //
                $('#input_book_no').val(item['book_no']);
                // counter //
                $('#input_counter').val(item['counter']);
                // notes //
                $('#input_notes').val(item['notes']);
                
                
                
                // focus to delete button //
                $('#btn_delete').focus();

            }else{
                // TAG number not found //
                alert(response['message']);
                
                // focus to tag no entry //
                $('#input_tag_no').focus();
                $('#input_tag_no').select();
            }
            
            
        });
            
        // on server request error //
        jqxhr.fail(function() {
            alert( "error" );
        });
        
    }else{
        // alert("Enter TAG Number!");
        // clear form content //
        clear_form();
    }
        */
}

// -- delete entry id -- //
function deleteEntry(){
    if(confirm("Delete this entry?")){
        loadingStart('Deleting...');
        // user name //
        var val_username = $("#username").val();
        // entry id //
        var val_entry_id = $("#input_entry_id").val();
        var val_period = $("#input_period").val();
        // plant //
        var val_plant = $("#input_plant").val();
        // item code //
        var val_item_code = $("#input_item_code").val();
        // item name //
        var val_item_name = $("#input_item_name").val();
        // lot no //
        var val_lot_no = $("#input_lot_no").val();
        // qty //
        var val_qty = $("#input_qty").val();
        // sloc //
        var val_sloc = $("#input_sloc").val();
        // rack //
        var val_rack = $("#input_rack").val();
        // pallet //
        var val_pallet = $("#input_pallet").val();
        // book number //
        var val_book_no = $("#input_book_no").val();
        // counter/checker //
        var val_counter = $("#input_counter").val();
        // notes //
        var val_notes = $("#input_notes").val();
        
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
        json_data['qty'] = val_qty * -1;    // reverse the value //
        json_data['rack'] = val_rack;
        json_data['pallet'] = val_pallet;
        json_data['book_no'] = val_book_no;
        json_data['counter'] = val_counter;
        json_data['notes'] = val_notes;
        json_data['source'] = "manual";
        json_data['flags'] = "deleted";     // deleted flags //
        json_data['reference'] = val_entry_id;     // use entry id as reference //
        json_data['id'] = "1";
        
        
        // create entry item list //
        var sto_array = Array();
        // insert entry item into sto entry list //
        sto_array.push(JSON.stringify(json_data))
        entry_items = sto_array;
        console.log(entry_items);
        
        // - send entry items into server - //
        $('.loading').show();
        var jqxhr = $.ajax({
            // url: "./entry/sto_entry_api2.php",
            url: baseUrl + '/api?cmd=save_entry',
            method: "POST",
            data: {
                entry_data: JSON.stringify(entry_items),
            }
        });

        jqxhr.done(function(msg) {
            loadingEnd();
                var response = (msg);
                console.log(response);
                // if response ok //
                if(response['status'] == 'ok'){
                    alert("Entry deleted!");
                    location.reload();
                    
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

/*        
        var entry_id = $('#input_entry_id').val();
        // ajax request //
        var jqxhr = $.ajax({
                url: "./report/delete_entry_api.php",
                method: "GET",
                data: {
                    cmd: "delete_entry",
                    id: entry_id
                }
            });
            
        // on server request completed //
        jqxhr.done(function(msg) {
            console.log(msg);
            alert(msg);
            
            // process response //
            var response = JSON.parse(msg);
            console.log(response);
            // if response OK //
            if(response['status'] == "ok"){
                
            }else{
                
                alert(response['message']);
            }
            
            
        });
            
        // on server request error //
        jqxhr.fail(function(err) {
            console.log(err);
            alert("ajax error");
        });
        
    }else{
        
    }
*/
    
}

// -- clear form -- //
function clear_form(){
    $("#input_entry_id").val("");
    $('#input_entry_id').focus();
    $('#input_entry_id').select()
    $("#input_tag_no").val("");
    $("#input_plant").val("");
    $("#input_tag_no").prop("disabled", false);
    $("#input_tag_no").focus();
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
