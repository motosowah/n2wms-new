// ajax command //
var cmd_val = "";

// -- display new user form //
function new_user(){

    $('#caption').text("New User");
    $('#form_manage_user').show();
    $('#btn_reset_password').hide();
    $('#btn_save_user_new').show();
    $('#btn_save_user').hide();
    $('#btn_delete_user').hide();
}

// -- create new user -- //
function new_user_save(){
    // get username data //
    var username_val = $('#input_username').val();
    var fullname_val = $('#input_fullname').val();
    var password_val = $('#input_password').val();
    var level_val = $('#option_level').val();
    var plant_val = $('#option_plant').val();
    cmd_val = "new_user";

    var ajax = $.ajax({
            url: baseUrl + "/api?cmd=new_user",
            method: "POST",
            data: {
                username: username_val,
                fullname: fullname_val,
                password: password_val,
                level: level_val,
                plant: plant_val
            }
        });
    
    ajax.done(function(msg){
        alert(msg);
        console.log(msg);
        // parse JSON response //
        try {
            var response = JSON.parse(msg);
            if(response['status'] == 'ok'){
                // data valid //
                alert(response['message']);
                // hide user form //
                $('#form_manage_user').hide();
                // reload page //
                location.reload();
            }else{
                // data invalid //
                alert(response['message']);
            }
        } catch (error) {
        // invalid JSON response //
            alert(error);
            console.log(msg);
        }
    });

    ajax.fail(function(err){
        alert(err);
    });
}


// -- edit current user -- //
function edit_user(user_id){
    // set user id //
    $('#user_id').val(user_id);
    $('#caption').text("Edit User");
    $('#form_manage_user').show();
    $('#btn_save_user_new').hide();
    $('#btn_save_user').show();
    $('#btn_delete_user').show();

    // get user detail //
    var ajax = $.ajax({
            url: baseUrl + "/api",
            method: "GET",
            data: {
                cmd: "get_user_data",
                id: user_id
            },
            beforeSend: function () {
                loadingStart();
            },
            complete: function () {
                loadingEnd();
            },
        });
    
    ajax.done(function(msg){
        // alert(msg);
        console.log(msg);
        try {
            // var response = JSON.parse(msg);
            // console.log(response);
            response = msg;
            if(response['status'] == 'ok'){
                // data valid // 
                // var user_data = JSON.parse(response['data']);
                var user_data = response['data'];
                console.log(user_data);
                // display user data //
                $('#input_username').val(user_data['user_name']);
                $('#input_fullname').val(user_data['full_name']);
                $('#input_password').val(user_data['user_password']);
                $('#option_level').val(user_data['level']);
                $('#input_plant').val(user_data['plant']);
                $('#input_current_plant').val(user_data['current_plant']);
                // focus to user name //
                $('#input_username').focus();

            }else{
                // data invalid //
            }
        } catch (error) {  
            alert(error);
            console.error(error);
        }
    });

    ajax.fail(function(err){
        alert(error);
        console.error(error);
    });
                    
}


// -- save user data -- //
function save_user(){
    // get username data //
    var userid_val = $('#user_id').val();
    var username_val = $('#input_username').val();
    var fullname_val = $('#input_fullname').val();
    var password_val = $('#input_password').val();
    var plant_val = $('#input_plant').val();
    var current_plant_val = $('#input_current_plant').val();
    var level_val = $('#option_level').val();
    var cmd_val = "save_user";

    var ajax = $.ajax({
            url: baseUrl + "/api?cmd=save_user",
            method: "POST",
            data: {
                userid: userid_val,
                username: username_val,
                fullname: fullname_val,
                password: password_val,
                plant: plant_val,
                current_plant: current_plant_val,
                level: level_val,
            },
            beforeSend: function () {
                loadingStart("Saving");
            },
            complete: function () {
                loadingEnd();
            },
        });
    
    ajax.done(function(msg){
        // alert(msg);
        console.log(msg);
        try {
            var response = (msg);
            if(response['status'] == 'ok'){
                // data valid //
                alert(response['message']);
                // reload page //
                location.reload();
            }else{
                // data invalid //
                alert(response['message']);
            }
        } catch (error) {
            alert("Invalid server response!");
            console.error(error);
        }
    });

    ajax.fail(function(error){
        alert("Invalid server response!");
        display_error(error.responseText);
        console.error(error);
    });
                    
}

// -- delete current user -- //
function delete_user(){

}

$(document).ready(function(){
    // - USERS LIST TABLE - //
    var table = $('#table_users').DataTable( {
        ajax: {
            url: baseUrl + '/api?cmd=get_users_list',
            "data": function(d) {
                // get search input parameter //
                var period = $('#period').val();
                var sto_date = $('#sto_date').val();
                var plant = $('#plant').val();
                var search_item_code = $('#search_item_code').val();
                var search_item_name = $('#search_item_name').val();

                d.period = period;
                d.sto_date = sto_date;
                d.plant = plant;
                d.item_code = search_item_code;
                d.item_name = search_item_name;
                
            },
            beforeSend: function () {
                $("#search_loading").show().css("display", "inline-block"); // Show loading animation
                $("#table_container").hide(); // hide the table
            },
            complete: function () {
                $("#search_loading").hide(); // Hide loading animation
                $("#table_container").show(); // show the table
            },
        },
        "columnDefs": [
            {
                "targets": [], 
                "createdCell": function(td, cellData, rowData, row, col) {
                    // Format the number with thousand separators and 2 decimal places
                    var formattedValue = Number(cellData).toLocaleString('en-US', { 
                        minimumFractionDigits: 3, 
                        maximumFractionDigits: 3 
                    });
                    
                    // Set the formatted value in the cell
                    $(td).text(formattedValue);
                },
                "className": 'dt-right' // Align the column to the right
            }
        ],
        dom: 'Bfrtip',
        "iDisplayLength": 25,
        // fixedHeader: true,
        responsive: true,
        "order": [[1, 'asc']], // Default sorting by first column (index 0) in ascending order
        buttons: [
            'copy',
            {
                extend: 'excel',
                title: ''
            }
        ]
    } );

    // reset user password //
    $('#btn_reset_password').click(function(){
        var user_id = $('#user_id').val();
        alert("reset password for user_id " + user_id);
    })
});    