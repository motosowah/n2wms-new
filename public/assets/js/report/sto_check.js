$(document).ready(function() {
    // -- get period list from server -- //
    $.ajax({
        url: baseUrl + '/api', 
        type: 'GET',
        data: {
            cmd: "get_entry_period_list",
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
        },
        beforeSend: function () {
            $("#period_loading").show().css("display", "inline-block"); // Show loading animation
        },
        complete: function () {
            $("#period_loading").hide(); // Hide loading animation
        }
    });


    // -- get sto check category list -- //
    $.ajax({
        url: baseUrl + '/api', 
        type: 'GET',
        data: {
            cmd: "get_sto_check_category",
        },
        dataType: 'json', 
        success: function(response) {
            // console.log(response.status);
            // console.log('Success:', response);
            // console.log(response.data);

            period_list = response.data;
            $("#category").autocomplete({
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
        },
        beforeSend: function () {
            $("#category_loading").show().css("display", "inline-block"); // Show loading animation
        },
        complete: function () {
            $("#category_loading").hide(); // Hide loading animation
        }
    });


    // - plant list - //
    var plant_list = ['', 'ID1A', 'ID1B', 'ID1C', 'ID1D'];
    $("#plant").autocomplete({
        source: plant_list,
        minLength: 0 // Allow search with zero characters
    }).on("focus", function() {
        $(this).autocomplete("search", ""); // Trigger search with an empty string
    });


    // - area list - //
    var area_list = ['', 'warehouse', 'production'];
    $("#area").autocomplete({
        source: area_list,
        minLength: 0 // Allow search with zero characters
    }).on("focus", function() {
        $(this).autocomplete("search", ""); // Trigger search with an empty string
    });


    // - activate datatable - //
    var table = $('#table_sto_check').DataTable( {
        ajax: {
            url: baseUrl + '/api?cmd=get_sto_check',
            "data": function(d) {
                // get search input parameter //
                var period = $('#period').val();
                var category = $('#category').val();
                var area = $('#area').val();
                var plant = $('#plant').val();
                var item_code = $('#search_item_code').val();
                var item_name = $('#search_item_name').val();
                var lot_no = $('#search_lot_no').val();
                var only_diff_detail = $('#only_diff_detail').prop('checked');
                var only_diff_total = $('#only_diff_total').prop('checked');
                only_diff_detail_val = only_diff_detail ? "yes" : "no";
                only_diff_total_val = only_diff_total ? "yes" : "no";
                console.log(only_diff_detail_val);

                d.period = period;
                d.category = category;
                d.area = area;
                d.plant = plant;
                d.item_code = item_code;
                d.item_name = item_name;
                d.lot_no = lot_no;
                d.only_diff_detail = only_diff_detail_val;
                d.only_diff_total = only_diff_total_val;
                
            },
            beforeSend: function () {
                $("#search_loading").show().css("display", "inline-block"); // Show loading animation
                // $("#table_container").hide(); // hide the table
            },
            complete: function () {
                $("#search_loading").hide(); // Hide loading animation
                // $("#table_container").show(); // show the table
            },
        },
        "columnDefs": [
            {
                "targets": [7,8,9], // Assuming you want to format the first column
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
            },
            {
                targets: [1,9],   // The column index you want to hide (zero-based)
                visible: false
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
            // reload datatable data source //
            table.ajax.reload(null, false); // false keeps pagination state
            // Clear the global search
            // table.search('').draw();
            // - get sap total detail qty - //
            get_total_sap_detail_qty();
        }
        
    })

});


// - get sap total detail qty - //
function get_total_sap_detail_qty(){
    var period = $('#period').val();
    var plant = $('#plant').val();

    $.ajax({
        url: baseUrl + '/api', 
        type: 'GET',
        data: {
            cmd: "get_total_sap_detail_qty",
            period: period,
            plant: plant
        },
        dataType: 'json', 
        success: function(response) {
            console.log(response);
            $('#total_plus_qty').text(response.total_plus_qty);
            $('#total_minus_qty').text(response.total_minus_qty);
            $('#total_diff_qty').text(response.total_diff_qty);
        },
        error: function(xhr, status, error) {
            // if server send invalid JSON reply //
            console.error('Error:', error); 
            console.error('Invalid server response:', xhr.responseText);
            display_error('Invalid server response: <hr/>' + xhr.responseText); 
        },
        beforeSend: function () {
            // $("#period_loading").show().css("display", "inline-block"); // Show loading animation
        },
        complete: function () {
            // $("#period_loading").hide(); // Hide loading animation
        }
    });
}