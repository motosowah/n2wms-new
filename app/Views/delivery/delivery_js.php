<script>
$(document).ready(function() {
    // - plant list - //
    var plant_list = ['', 'ID1A', 'ID1C', 'ID1D', 'ID1E'];
    $(".plant").autocomplete({
        source: plant_list,
        minLength: 0 // Allow search with zero characters
    }).on("focus", function() {
        $(this).autocomplete("search", ""); // Trigger search with an empty string
    });


    // DELIVERY SAP DATA //
    table_sap_data = $('#table_sap_data').DataTable( {
        ajax: {
            url: '/delivery/get_sap_data',
            "data": function(d) {
                // get search input parameter //
                var date_from = $('#date_from_sap_data').val();
                var date_to = $('#date_to_sap_data').val();
                var plant = $('#plant_sap_data').val();
                var do_no = $('#do_no_sap_data').val();

                d.date_from = date_from;
                d.date_to = date_to;
                d.plant = plant;
                d.do_no = do_no;

            },
            timeout: 15000,
            beforeSend: function () {
                $("#loading_sap_data").show().css("display", "inline-block"); // Show loading animation
                $("#table_sap_data_overlay").show(); // display table overlay //
            },
            complete: function () {
                $("#loading_sap_data").hide(); // Hide loading animation
                $("#table_sap_data_overlay").hide(); // display table overlay //
            },
            error: function(xhr, status, error) {
                console.log("Invalid server response!\n" + status);
                console.error('AJAX status:', status); // e.g. "timeout", "error", "abort", "parsererror"
                console.error('HTTP status code:', xhr.status); // e.g. 502, 0, 404
                console.error('Server Error:', error);
                console.error('Invalid server response:', xhr.responseText);
                Swal.fire({
                    icon: "error",
                    text: "Server response error! (get_sap_data)",
                    footer: "- Please check error logs!"
                });
            },
        },
        scrollX: true,
        "columnDefs": [
            {
                "targets": [8], 
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
        "order": [[0, 'desc']],
        buttons: [
            'copy',
            {
                extend: 'excel',
                title: ''
            }
        ]
    } );

    // update data //
    $('#btn_search_sap_data, #tab0-tab').click(function(){
        // get search input parameter //
        var date_from = $('#date_from_sap_data').val();
        var date_to = $('#date_to_sap_data').val();
        var plant = $('#plant_sap_data').val();

        var date_from_sap_data_valid = false;
        if(date_from === ""){
            $("#invalid_date_from_sap_data").show();
            date_from_sap_data_valid = false;
        }else{
            $("#invalid_date_from_sap_data").hide();
            date_from_sap_data_valid = true;
        };

        var date_to_sap_data_valid = false;
        if(date_to === ""){
            $("#invalid_date_to_sap_data").show();
            date_to_sap_data_valid = false;
        }else{
            $("#invalid_date_to_sap_data").hide();
            date_to_sap_data_valid = true;
        };

        var plant_sap_data_valid = false;
        if(plant === ""){
            $("#invalid_plant_sap_data").show();
            plant_sap_data_valid = false;
        }else{
            $("#invalid_plant_sap_data").hide();
            plant_sap_data_valid = true;
        };

        // if form valid //
        if(date_from_sap_data_valid && date_to_sap_data_valid & plant_sap_data_valid){
            // reload table data //
            table_sap_data.ajax.reload(null, false);
            // Clear the global search
            // table_sap_data.search('').draw();
        }
                
    })

    // ====================================== //


    // WAITING LIST DO GENERATE //
    var table_generate = $('#table_generate').DataTable( {
        ajax: {
            url: '/delivery/get_generate',
            // "data": function(d) {},
            timeout: 15000,
            beforeSend: function () {
                // $("#loading_generate").show().css("display", "inline-block"); // Show loading animation
                miniLoading(true);
            },
            complete: function () {
                // $("#loading_generate").hide(); // Hide loading animation
                miniLoading(false);
            },
            error: function(xhr, status, error) {
                console.log("Invalid server response!\n" + status);
                console.error('AJAX status:', status); // e.g. "timeout", "error", "abort", "parsererror"
                console.error('HTTP status code:', xhr.status); // e.g. 502, 0, 404
                console.error('Server Error:', error);
                console.error('Invalid server response:', xhr.responseText);
                Swal.fire({
                    icon: "error",
                    text: "Server response error! (get_generate_do)",
                    footer: "- Please check error logs!"
                });
                miniLoading(false);
            },
        },
        scrollX: true,
        "columnDefs": [
            {
                "targets": [4,5,6,7], 
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
        "order": [[2, 'asc']],
        buttons: [
            'copy',
            {
                extend: 'excel',
                title: ''
            }
        ]
    } );

    // search data //
    $('#tab1-tab').click(function(){
        // alert('clicked');
        // table_generate.ajax.reload(null, false); // false keeps pagination state
        // // Clear the global search
        // table_generate.search('').draw();
        // Clear search AND wait for one draw
        // table_generate.search('').draw(false);
        table_generate
            .search('')        // Clear search
            .ajax.reload(null, false); // Reload with pagination state
    })

    // ====================================== //



    // ON PROCESS PICKING  //
    table_on_process = $('#table_on_process').DataTable( {
        ajax: {
            url: '/delivery/get_on_process',
            "data": function(d) {},
            timeout: 15000,
            beforeSend: function () {
                // $("#loading_generate").show().css("display", "inline-block"); // Show loading animation
                $("#table_on_process_overlay").show(); // display table 
            },
            complete: function () {
                // $("#loading_generate").hide(); // Hide loading animation
                $("#table_on_process_overlay").hide(); // display table 
            },
            error: function(xhr, status, error) {
                console.log("Invalid server response!\n" + status);
                console.error('AJAX status:', status); // e.g. "timeout", "error", "abort", "parsererror"
                console.error('HTTP status code:', xhr.status); // e.g. 502, 0, 404
                console.error('Server Error:', error);
                console.error('Invalid server response:', xhr.responseText); 
                Swal.fire({
                    icon: "error",
                    text: "Server response error! (get_on_process)",
                    footer: "- Please check error logs!"
                });
            },
        },
        scrollX: true,
        "columnDefs": [
            {
                "targets": [9], 
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
        "iDisplayLength": 50,
        // fixedHeader: true,
        responsive: true,
        "order": [[2, 'asc']],
        buttons: [
            'copy',
            {
                extend: 'excel',
                title: ''
            }
        ]
    } );

    // on click update data //
    $('#tab2-tab').click(function(){
        // alert('clicked');
        table_on_process.ajax.reload(null, false); // false keeps pagination state
        // Clear the global search
        //table_on_process.search('').draw();
    })

    // ====================================== //



    // FINISHED PICKING //
    var table_finished = $('#table_finished').DataTable( {
        ajax: {
            url: '/delivery/get_finished',
            "data": function(d) {
                // get search input parameter //
                var date_from = $('#date_from_finished').val();
                var date_to = $('#date_to_finished').val();
                var plant = $('#plant_finished').val();
                
                d.date_from = date_from;
                d.date_to = date_to;
                d.plant = plant; 
            },
            timeout: 15000,
            beforeSend: function () {
                $("#loading_finished").show().css("display", "inline-block"); // Show loading animation
                $("#table_finished_overlay").show();
            },
            complete: function () {
                $("#loading_finished").hide(); // Hide loading animation
                $("#table_finished_overlay").hide();
            },
            error: function(xhr, status, error) {
                console.log("Invalid server response!\n" + status);
                console.error('AJAX status:', status); // e.g. "timeout", "error", "abort", "parsererror"
                console.error('HTTP status code:', xhr.status); // e.g. 502, 0, 404
                console.error('Server Error:', error);
                console.error('Invalid server response:', xhr.responseText); 
                Swal.fire({
                    icon: "error",
                    text: "Server response error! (get_finished_picking)",
                    footer: "- Please check error logs!"
                });
            },
        },
        scrollX: true,
        "columnDefs": [
            {
                "targets": [10], 
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
        "order": [[0, 'desc']],
        buttons: [
            'copy',
            {
                extend: 'excel',
                title: ''
            }
        ]
    } );

    // update data //
    $('#btn_search_finished, #tab3-tab').click(function(){
        // get search input parameter //
        var date_from = $('#date_from_finished').val();
        var date_to = $('#date_to_finished').val();
        var plant = $('#plant_finished').val();

        var date_from_finished_valid = false;
        if(date_from === ""){
            $("#invalid_date_from_finished").show();
            date_from_finished_valid = false;
        }else{
            $("#invalid_date_from_finished").hide();
            date_from_finished_valid = true;
        };

        var date_to_finished_valid = false;
        if(date_to === ""){
            $("#invalid_date_to_finished").show();
            date_to_finished_valid = false;
        }else{
            $("#invalid_date_to_finished").hide();
            date_to_finished_valid = true;
        };

        var plant_finished_valid = false;
        if(plant === ""){
            $("#invalid_plant_finished").show();
            plant_finished_valid = false;
        }else{
            $("#invalid_plant_finished").hide();
            plant_finished_valid = true;
        };

        // if form valid //
        if(date_from_finished_valid && date_to_finished_valid && plant_finished_valid){
            // reload table data //
            table_finished.ajax.reload(null, false);
            // Clear the global search
            //table_finished.search('').draw();
        }
    })

    // ====================================== //



    // BALANCE DO //
    var table_balance = $('#table_balance').DataTable( {
        ajax: {
            url: '/delivery/get_balance',
            "data": function(d) {
                // get search input parameter //
                var date_from = $('#date_from_balance').val();
                var date_to = $('#date_to_balance').val();
                var plant = $('#plant_balance').val();
                var do_no = $('#do_no_balance').val();

                d.date_from = date_from;
                d.date_to = date_to;
                d.plant = plant;
                d.do_no = do_no;
            },
            timeout: 15000,
            beforeSend: function () {
                $("#loading_balance").show().css("display", "inline-block"); // Show loading animation
                $("#table_balance_overlay").show();
            },
            complete: function () {
                $("#loading_balance").hide(); // Hide loading animation
                $("#table_balance_overlay").hide();
            },
            error: function(xhr, status, error) {
                console.log("Invalid server response!\n" + status);
                console.error('AJAX status:', status); // e.g. "timeout", "error", "abort", "parsererror"
                console.error('HTTP status code:', xhr.status); // e.g. 502, 0, 404
                console.error('Server Error:', error);
                console.error('Invalid server response:', xhr.responseText); 
                Swal.fire({
                    icon: "error",
                    text: "Server response error! (get_balance_do)",
                    footer: "- Please check error logs!"
                });
            },
        },
        scrollX: true,
        "columnDefs": [
            {
                "targets": [7,8,9,10], 
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
        fixedHeader: true,
        responsive: true,
        "order": [[2, 'desc']],
        buttons: [
            'copy',
            {
                extend: 'excel',
                title: ''
            }
        ]
    } );

    // update data //
    $('#btn_search_balance, #tab4-tab').click(function(){
        // get search input parameter //
        var date_from = $('#date_from_balance').val();
        var date_to = $('#date_to_balance').val();
        var plant = $('#plant_balance').val();

        var date_from_balance_valid = false;
        if(date_from === ""){
            $("#invalid_date_from_balance").show();
            date_from_balance_valid = false;
        }else{
            $("#invalid_date_from_balance").hide();
            date_from_balance_valid = true;
        };

        var date_to_balance_valid = false;
        if(date_to === ""){
            $("#invalid_date_to_balance").show();
            date_to_balance_valid = false;
        }else{
            $("#invalid_date_to_balance").hide();
            date_to_balance_valid = true;
        };

        var plant_balance_valid = false;
        if(plant === ""){
            $("#invalid_plant_balance").show();
            plant_balance_valid = false;
        }else{
            $("#invalid_plant_balance").hide();
            plant_balance_valid = true;
        };

        // if form valid //
        if(date_from_balance_valid && date_to_balance_valid & plant_balance_valid){
            // reload table data //
            table_balance.ajax.reload(null, false);
            // Clear the global search
            //table_balance.search('').draw();
        }
      
    })

    // ====================================== //


    $('#btn_search_balance, #tab5-tab').click(function(){
        $("#loading_new_do").hide(); // Hide loading animation
    });

    // CREATE NEW DELIVERY DO //
    // $(document).ready(function(){
    // alert("jquery loaded");

    // save new do //
    $('#btn_save').click(function(){
        save_delivery();
    // })
    })


    // - autocomplete item code - //
    $("#new_item_code").autocomplete({
        source: function (request, response) {
            $.ajax({
                // url: baseUrl + "/api",
                url: '/item/search_item',
                dataType: "json",
                data: {
                    cmd: "search_item",
                    plant: $("#new_do_plant").val(),
                    search_text: $("#new_item_code").val() // Search term from the input field
                },
                success: function (data) {
                    // response(data); // Pass the data to the autocomplete widget
                    response(data.data); // Pass the data to the autocomplete widget
                },
                error: function () {
                    console.error("Failed to fetch data");
                },
                beforeSend: function () {
                    $("#loading_new_do").css("display", "inline-block"); // Show loading animation
                },
                complete: function () {
                    $("#loading_new_do").hide(); // Hide loading animation
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


    // auto get item description from item code //
    $('#new_item_code').blur(function(){
        var new_item_code = validate_input("new_item_code");
        if(new_item_code){
            $.ajax({
                url: '/item/get_item_desc/' + new_item_code,
                type: 'GET',
                data: {
                    item_code: new_item_code,
                },
                dataType: 'json', 
                success: function(response) {
                    console.log(response);
                    if(response.status == "ok"){
                        // - response ok - //
                        $('#new_item_desc').val(response.item_desc);
                    }else if(response.status == "warning"){
                        // - warning found - //
                        console.log("warning found")
                        console.log(response);
                        
                    }else if(response.status == "error"){
                        // - errors found - //                            
                    }
                },
                error: function(xhr, status, error) {
                    // if server send invalid JSON reply //
                    console.error('Error:', error); 
                    console.error('Invalid server response:', xhr.responseText); 
                },
                beforeSend: function () {
                    $("#loading_new_do").show().css("display", "inline-block"); // Show loading animation
                },
                complete: function () {
                    $("#loading_new_do").hide(); // Hide loading animation
                }
            });    
        }else{
            $('#new_item_desc').val('');
        }
        
    })


    
});




// -- input validation function -- //
function validate_input(input_id){
    var input_val = $("#" + input_id).val();
    var result = false;
    if(input_val === ""){
        $("#invalid_" + input_id).show();
        return false;
    }else{
        $("#invalid_" + input_id).hide();
        // result = true;
        return input_val;
    };
}


// -- save new delivery DO data -- //
function save_delivery(){
    // validate new do form //
    var new_do_plant = validate_input("new_do_plant");
    var new_do_date = validate_input("new_do_date");
    var new_do_no = validate_input("new_do_no");
    var new_customer = validate_input("new_customer");
    var new_item_code = validate_input("new_item_code");
    var new_item_desc = validate_input("new_item_desc");
    var new_lot_no = validate_input("new_lot_no");
    var new_qty = validate_input("new_qty");
    // if form input valid //
    if(new_do_plant && new_do_date && new_do_no && new_customer && new_item_code && new_lot_no && new_qty){
        if(confirm("Confirm create new delivery?")){
        // - send entry data into server - //
        $.ajax({
            url: '/delivery/create_delivery',
            type: 'POST',
            data: {
                plant: new_do_plant,
                do_date: new_do_date,
                do_no: new_do_no,
                customer: new_customer,
                item_code: new_item_code,
                item_desc: new_item_desc,
                lot_no: new_lot_no,
                qty: new_qty,
            },
            dataType: 'json', 
            success: function(response) {
                console.log(response);
                if(response.status == "ok"){
                    // - response ok - //
                    // alert("New delivery data created")
                    Swal.fire({
                        icon: "success",
                        // title: "Delivery Data Saved",
                        text: "Delivery Data Saved!",
                        // footer: "*You have to fill all required fields"
                    });
                    // clear form //
                    $('#new_do_plant, #new_do_date, #new_do_no, #new_customer, #new_item_code, #new_item_desc, #new_lot_no, #new_qty').val('');
                }else if(response.status == "warning"){
                    // - warning found - //
                    console.log("warning found")
                    console.log(response);
                    
                }else if(response.status == "error"){
                    // - errors found - //                            
                }
            },
            error: function(xhr, status, error) {
                // if server send invalid JSON reply //
                console.error('Error:', error); 
                console.error('Invalid server response:', xhr.responseText); 
            },
            beforeSend: function () {
                $('#btn_save').text("SAVING");
                $("#loading_new_do").show().css("display", "inline-block"); // Show loading animation
            },
            complete: function () {
                $('#btn_save').text("SAVE");
                $("#loading_new_do").hide(); // Hide loading animation
            }
        });
        }
    }else{
        // alert('Please check your input!');
        Swal.fire({
            icon: "error",
            title: "Input Error",
            text: "Please check your input!",
            footer: "* You have to fill all required fields!",
            animation: false
        });
    }
}


// - delete sap data delivery - //
function delete_sap_data(id){
    if(confirm("Delete this data?")){
         $.ajax({
            url: '/delivery/delete_sap/' + id,
            type: 'GET',
            data: {
                // item_code: new_item_code,
            },
            dataType: 'json', 
            success: function(response) {
                console.log(response);
                if(response.status == "ok"){
                    // - response ok - //
                    Swal.fire({
                        icon: "success",
                        // title: "Delivery Data Saved",
                        text: "SAP delivery data id #" + id + " deleted!",
                        // footer: "*You have to fill all required fields"
                    });
                    // reload table data //
                    table_sap_data.ajax.reload(null, false);
                }else if(response.status == "warning"){
                    // - warning found - //
                    console.log("warning found")
                    console.log(response);
                    
                }else if(response.status == "error"){
                    // - errors found - //                            
                }
            },
            error: function(xhr, status, error) {
                // if server send invalid JSON reply //
                console.error('Error:', error); 
                console.error('Invalid server response:', xhr.responseText);
                Swal.fire({
                    icon: "error",
                    title: "Invalid server response!",
                    text: xhr.responseText,
                    footer: "Please contact system administrator!"
                });
                
            },
            beforeSend: function () {
                $("#loading_sap_data").show().css("display", "inline-block"); // Show loading animation
            },
            complete: function () {
                $("#loading_sap_data").hide(); // Hide loading animation
            }
        });

        
    }
}


// - delete on process picking - //
function delete_on_process(id){
    // alert(id);
    if(confirm("Delete on process picking ID #" + id + " ?")){
         $.ajax({
            url: '/delivery/delete_on_process/' + id,
            type: 'GET',
            data: {
                // id: id,
            },
            dataType: 'json', 
            success: function(response) {
                console.log(response);
                if(response.status == "ok"){
                    // - response ok - //
                    Swal.fire({
                        icon: "success",
                        // title: "Delivery Data Saved",
                        text: "On process picking delivery ID #" + id + " deleted!",
                        // footer: "*You have to fill all required fields"
                    });
                    // reload table data //
                    table_on_process.ajax.reload(null, false);
                }else if(response.status == "warning"){
                    // - warning found - //
                    console.log("warning found")
                    console.log(response);
                    
                }else if(response.status == "error"){
                    // - errors found - //                            
                }
            },
            error: function(xhr, status, error) {
                // if server send invalid JSON reply //
                console.error('Error:', error); 
                console.error('Invalid server response:', xhr.responseText);
                Swal.fire({
                    icon: "error",
                    title: "Invalid server response!",
                    // text: xhr.responseText,
                    html: xhr.responseText,
                    footer: "Please contact system administrator!"
                });
                
            },
            beforeSend: function () {
                $("#loading_sap_data").show().css("display", "inline-block"); // Show loading animation
            },
            complete: function () {
                $("#loading_sap_data").hide(); // Hide loading animation
            }
        });
   
    }
}


// - execute process picking delivery - //
function process_picking(id){
    // alert(id);
    if(confirm("Confirm process picking delivery ID #" + id + " ?")){
         $.ajax({
            url: '/delivery/process_picking/' + id,
            type: 'GET',
            data: {
                // id: id,
            },
            dataType: 'json', 
            success: function(response) {
                console.log(response);
                if(response.status == "ok"){
                    // - response ok - //
                    Swal.fire({
                        icon: "success",
                        // title: "Delivery Data Saved",
                        text: "Process picking delivery ID #" + id + " completed!",
                        // footer: "*You have to fill all required fields"
                    });
                    // reload table data //
                    table_on_process.ajax.reload(null, false);
                }else if(response.status == "warning"){
                    // - warning found - //
                    console.log("warning found")
                    console.log(response);
                    
                }else if(response.status == "error"){
                    // - errors found - //                            
                }
            },
            error: function(xhr, status, error) {
                // if server send invalid JSON reply //
                console.error('Error:', error); 
                console.error('Invalid server response:', xhr.responseText);
                Swal.fire({
                    icon: "error",
                    title: "Invalid server response!",
                    // text: xhr.responseText,
                    html: xhr.responseText,
                    footer: "Please contact system administrator!"
                });
                
            },
            beforeSend: function () {
                $("#table_on_process_overlay").show(); // display table 
            },
            complete: function () {
                $("#table_on_process_overlay").hide(); // display table 
            }
        });
   
    }
}




</script>