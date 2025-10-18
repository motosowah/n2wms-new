$(document).ready(function(){
    // -- get period list from server -- //
    $.ajax({
        url: baseUrl + '/api', 
        type: 'GET',
        data: {
            cmd: "get_unit_cost_period_list",
        },
        dataType: 'json', 
        success: function(response) {
            // console.log(response.status);
            // console.log('Success:', response);
            // console.log(response.data);

            period_list = response.data;
            $("#period").autocomplete({
                source: period_list,
                minLength: 0 // Allow search with zero characters
            }).on("focus", function() {
                $(this).autocomplete("search", ""); // Trigger search with an empty string
            });
        },
        error: function(xhr, status, error) {
            // if server send invalid JSON reply //
            console.log('Error:', error); 
            console.log('Invalid server response:', xhr.responseText); 
            display_error('Invalid server response:<hr/>' + xhr.responseText); 
        },
        beforeSend: function () {
            $("#period_loading").show().css("display", "inline-block"); // Show loading animation
        },
        complete: function () {
            $("#period_loading").hide(); // Hide loading animation
        }
    });


    // -- get plant list from server -- //
    var plant_list = ['', 'ID1A', 'ID1B', 'ID1C', 'ID1D'];
    $("#plant").autocomplete({
        source: plant_list,
        minLength: 0 // Allow search with zero characters
    }).on("focus", function() {
        $(this).autocomplete("search", ""); // Trigger search with an empty string
    });


    // - activate datatable - //
    var table = $('#table_unit_cost').DataTable( {
        ajax: {
            url: baseUrl + '/api?cmd=get_unit_cost',
            // "url": './api/master-api.php?cmd=get_sap_stock',
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
                "targets": 5, // Assuming you want to format the first column
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
        "order": [[4, 'asc']], // Default sorting by first column (index 0) in ascending order
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
        if($('#period').val() == ""){
            alert("Please enter period!");
            $('#period').focus();
        }else{
            table.ajax.reload(null, false); // false keeps pagination state
        }
        
    })    

}); 


// toggle sap unit cost upload form //
function toggle_unit_cost_upload(){
    $('#upload_unit_cost_container').toggle();
}


// - preview sap unit cost data text - //
function preview_unit_cost(){
    // get text data //
    var text_data_val = $('#text_unit_cost_data').val();
    // console.log(text_data_val);
    if(text_data_val.length > 0){
        $('.loading').show();
        $('#btn_preview').disable();
        var ajax = $.ajax({
                url: baseUrl + '/api?cmd=preview_unit_cost',
                // url: "./master/upload-master-api.php",
                method: "POST",
                data: {
                    cmd: "unit_cost_preview",
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
            // console.log(msg);
            $('.loading').hide();
            try {
                // var response = JSON.parse(msg);
                var response = msg;
                // hide text data editor //
                $('#text_unit_cost_data').hide();
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

                    $('#btn_upload').prop("disabled", true);
                    $('#btn_preview').prop("disabled", true);
                }
            } catch (error) {
                console.log(error);
                $('.loading').hide();
                $('#btn_preview').enable();
                // alert("Invalid server response!");
                display_error(error.responseText); 
            }
        });

        ajax.fail(function(err){
            console.log(err);
            $('.loading').hide();
            $('#btn_preview').enable();
            alert("Invalid server response!");
            display_error("Invalid server response!<hr/>" + err.responseText);
        });
        
    }else{
        alert("Text data cannot be empty");
    }
    
}

// - upload sap unit cost data text - //
function upload_unit_cost(){
    if(confirm("Are you sure to upload this data?")){
        // get text data //
        var text_data_val = $('#text_unit_cost_data').val();
        // console.log(text_data_val);
        if(text_data_val.length > 0){
            $('.loading').show();
            $('#btn_upload').disable();
            var ajax = $.ajax({
                    url: baseUrl + '/api?cmd=upload_unit_cost',
                    method: "POST",
                    data: {
                        cmd: "unit_cost_upload",
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
                // console.log(msg);
                $('.loading').hide();
                $('#btn_upload').enable();
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