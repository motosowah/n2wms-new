$(document).ready(function() {

    // create plant list //
    var plant_list = ['', 'ID1A', 'ID1B', 'ID1C', 'ID1D'];

    $("#plant").autocomplete({
        source: plant_list,
        minLength: 0 // Allow search with zero characters
    }).on("focus", function() {
        $(this).autocomplete("search", ""); // Trigger search with an empty string
    });


    // activate datatable //
    var table = $('#table_batch_master').DataTable( {
        ajax: {
            url: baseUrl + '/api?cmd=get_batch_master',
            // "url": './api/master-api.php?cmd=get_batch_master',
            "data": function(d) {
                // get search input parameter //
                var plant = $('#plant').val();
                var search_item_code = $('#search_item_code').val();
                var search_item_name = $('#search_item_name').val();

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
        dom: 'Bfrtip',
        "iDisplayLength": 25,
        // fixedHeader: true,
        responsive: true,
        "order": [[3, 'asc']], // Default sorting by first column (index 0) in ascending order
        buttons: [
            'copy',
            {
                extend: 'excel',
                title: ''
            }
        ]
    } );

    // search entry history based on parameter //
    $('#btn_search').click(function(){
        // alert('button clicked');
        if($('#plant').val() == ""){
            alert("Please enter Plant!");
            $('#plant').focus();
        }else{
            table.ajax.reload(null, false); // false keeps pagination state
        }
        
    })

});

// toggle item master upload form //
function toggle_batch_master_upload(){
    $('#upload_batch_master_container').toggle();
}


// toggle new item master form //
function toggle_new_batch_master(){
    $('#new_batch_master_container').toggle();
}


// - preview item master data text - //
function preview_batch_master(){
    // get text data //
    var text_data_val = $('#text_batch_master_data').val();
    // console.log(text_data_val);
    if(text_data_val.length > 0){
        $('.loading').show();
        $('#progress-text').html("Checking ...");
        var ajax = $.ajax({
                url: baseUrl + '/api?cmd=preview_batch_master',
                // url: "./api/master-api.php?cmd=preview_batch_master",
                method: "POST",
                data: {
                    // cmd: "wms_stock_preview",
                    text_data: text_data_val
                },
                beforeSend: function () {
                    $("#preview_loading").show().css("display", "inline-block"); // Show loading animation
                    // $("#table_container").hide(); // hide the table
                },
                complete: function () {
                    $("#preview_loading").hide(); // Hide loading animation
                    // $("#table_container").show(); // show the table
                }
            });
        
        ajax.done(function(msg){
            $('.loading').hide();
            // console.log(msg);
            $('#btn_preview').disable();
            try {
                // var response = JSON.parse(msg);
                var response = msg;
                // hide text data editor //
                $('#text_batch_master_data').hide();
                // data valid //
                if(response['status'] == 'ok'){
                    // display output preview //
                    $('#sap_stock_upload_preview_output').html(response['data']);
                    // enable upload button //
                    $('#btn_upload').prop("disabled", false);
                    $('#btn_preview').prop("disabled", true);
                }else{
                    // data invalid //
                    // alert(response['message']);
                    $('#sap_stock_upload_preview_output').html(response['message']);
                    // disable button //
                    $('#btn_upload').prop("disabled", true);
                    $('#btn_preview').prop("disabled", true);
                }
            } catch (error) {
                alert(error);
                console.error(msg);
                console.error(error);
                display_error(error);
            }
            
            // alert(msg);
            
        });

        ajax.fail(function(err){
            $('.loading').hide();
            $('#btn_preview').enable();
            console.log('Error:', err); 
            console.log('Invalid server response:', err.responseText);
            display_error("Invalid server response!<hr/> " + err.responseText)
        });
        
    }else{
        alert("Text data cannot be empty");
    }
    
}


// - upload item master data text - //
function upload_batch_master(){
    if(confirm("Are you sure to upload this data?")){
        // get text data //
        var text_data_val = $('#text_batch_master_data').val();
        // console.log(text_data_val);
        if(text_data_val.length > 0){
            $('.loading').show();
            $('#btn_upload').disable();
            $('#progress-text').html("Uploading ...");

            var ajax = $.ajax({
                // url: "./api/master-api.php?cmd=upload_batch_master",
                    url: baseUrl + '/api?cmd=upload_batch_master',
                    method: "POST",
                    data: {
                        text_data: text_data_val
                    },
                    beforeSend: function () {
                        $("#upload_loading").show().css("display", "inline-block"); // Show loading animation
                        // $("#table_container").hide(); // hide the table
                    },
                    complete: function () {
                        $("#upload_loading").hide(); // Hide loading animation
                        // $("#table_container").show(); // show the table
                    }
                });
            
            ajax.done(function(msg){
                $('.loading').hide();
                $('#btn_upload').enable();
                // console.log(msg);
                try {
                    // var response = JSON.parse(msg);
                    var response = msg;
                    // data valid //
                    if(response['status'] == 'ok'){
                        // display output preview //
                        $('#sap_stock_upload_preview_output').html(response['message']);
                        alert(response['message']);
                        location.reload();
                    }else{
                        // data invalid //
                        $('#sap_stock_upload_preview_output').html(response['message']);
                        alert(response['message']);
                    }
                } catch (error) { 
                    alert(error);
                    console.error(msg);
                    console.error(error);
                    display_error(error);
                }

            });

            ajax.fail(function(err){
                console.log(err);
                $('.loading').hide();
                $('#btn_upload').enable();
                alert("Invalid server response!");
                display_error("Invalid server response!<hr/>" + err.responseText);
                });
            
        }else{
            alert("Text data cannot be empty");
        }
    }
    
}


// - save new batch master - //
function saveNewBatchMaster(){
if(confirm("Create new item master?")){
    var plant_val = $('#input_plant').val();
    var item_code_val = $('#input_item_code').val();
    var item_name_val = $('#input_item_name').val();
    var division_val = $('#input_division').val();
    var form_invalid = ifEmpty(plant_val) || ifEmpty(item_code_val) || ifEmpty(item_name_val);  // if form invalid //
    if(!form_invalid){
        var ajax = $.ajax({
                url: baseUrl + '/api?cmd=new_batch_master',
                // url: "./api/master-api.php?cmd=preview_batch_master",
                method: "POST",
                data: {
                    plant: plant_val,
                    item_code: item_code_val,
                    item_name: item_name_val,
                    division: division_val,
                },
                beforeSend: function () {
                    $("#new_batch_master_loading").show().css("display", "inline-block"); // Show loading animation
                    // $("#table_container").hide(); // hide the table
                },
                complete: function () {
                    $("#new_batch_master_loading").hide(); // Hide loading animation
                    // $("#table_container").show(); // show the table
                }
            });
        
        ajax.done(function(msg){
            $('.loading').hide();
            // console.log(msg);
            $('#btn_preview').disable();
            try {
                // var response = JSON.parse(msg);
                var response = msg;
                // hide text data editor //
                $('#text_batch_master_data').hide();
                // data valid //
                if(response['status'] == 'ok'){
                    alert("New item master created!");
                    location.reload();
                    
                }else{
                    // data invalid //
                    // alert(response['message']);
                    $('#sap_stock_upload_preview_output').html(response['message']);
                    // disable button //
                    $('#btn_upload').prop("disabled", true);
                    $('#btn_preview').prop("disabled", true);
                }
            } catch (error) {
                alert(error);
                console.error(msg);
                console.error(error);
                display_error(error);
            }
            
            // alert(msg);
            
        });

        ajax.fail(function(err){
            $('.loading').hide();
            $('#btn_preview').enable();
            console.log('Error:', err); 
            console.log('Invalid server response:', err.responseText);
            display_error("Invalid server response! " + err.responseText)
        });
        
    }else{
        alert("Data cannot be empty!");
    }
}
}