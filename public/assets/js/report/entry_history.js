$(document).ready(function() {
    // -- get period list from server -- //
    $.ajax({
        // url: baseUrl + '/api', 
        url: '/sts/api2/get_period_list', 
        type: 'GET',
        // data: {
        //     cmd: "get_entry_period_list",
        // },
        dataType: 'json', 
        timeout: 5000,
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
            console.log('AJAX status:', status); // e.g. "timeout", "error", "abort", "parsererror"
            console.log('HTTP status code:', xhr.status); // e.g. 502, 0, 404
            console.log('Server Error:', error);
            console.log('Invalid server response:', xhr.responseText); 

            // create server error message //
            let message = 'Unknown server error. Please try again.';
            if (xhr.status === 0) {
                console.error('Server is unreachable. Please check your connection.');
                display_error('Server is unreachable. Please check your connection.');
                location.reload();
            } else if (xhr.status === 404) {
                message = '<b>[ERROR 404]</b> <hr/> Requested URL not found! <hr/>';
                display_error('<b>[ERROR 404]</b> <hr/> Requested URL not found! <hr/>');
            } else if (xhr.status === 502) {
                message = 'Server temporarily unavailable (502 Bad Gateway).';
                display_error('Server temporarily unavailable (502 Bad Gateway).');
            } else if (xhr.status >= 500) {
                message = 'Internal server error (' + xhr.status + ').';
                display_error('Internal server error (' + xhr.status + ').');
            }else{
                // if server send invalid JSON reply //
                console.log('Server Error:', error); 
                console.log('Invalid server response:', xhr.responseText); 
                display_error('Invalid server response:<hr/>' + xhr.responseText); 
            }
        },
        beforeSend: function () {
            $("#period_loading").show().css("display", "inline-block"); // Show loading animation
            loadingStart();
        },
        complete: function () {
            $("#period_loading").hide(); // Hide loading animation
            loadingEnd();
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


    // - activate datatable - //
    var table = $('#table_entry_history').DataTable( {
        ajax: {
            // url: baseUrl + '/api?cmd=get_sto_entry',
            url: '/sts/api2/get_sto_entry', 
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
            error: function(xhr, status, error) {
                console.log('AJAX status:', status); // e.g. "timeout", "error", "abort", "parsererror"
                console.log('HTTP status code:', xhr.status); // e.g. 502, 0, 404
                console.log('Server Error:', error);
                console.log('Invalid server response:', xhr.responseText); 

                // create server error message //
                let message = 'Unknown server error. Please try again.';
                if (xhr.status === 0) {
                    console.error('Server is unreachable. Please check your connection.');
                    display_error('Server is unreachable. Please check your connection.');
                    location.reload();
                } else if (xhr.status === 404) {
                    message = '<b>[ERROR 404]</b> <hr/> Requested URL not found! <hr/>';
                    display_error('<b>[ERROR 404]</b> <hr/> Requested URL not found! <hr/>');
                } else if (xhr.status === 502) {
                    message = 'Server temporarily unavailable (502 Bad Gateway).';
                    display_error('Server temporarily unavailable (502 Bad Gateway).');
                // } else if (xhr.status >= 500) {
                //     message = 'Internal server error (' + xhr.status + ').';
                //     display_error('Internal server error (' + xhr.status + ').');
                }else{
                    // if server send invalid JSON reply //
                    console.log('Server Error:', error); 
                    console.log('Invalid server response:', xhr.responseText); 
                    display_error('Invalid server response:<hr/>' + xhr.responseText); 
                }
            }
        },
        "columnDefs": [
            {
                "targets": 8, // Assuming you want to format the first column
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
        "order": [[0, 'desc']], // Default sorting by first column (index 0) in ascending order
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
            // Clear the global search
            table.search('').draw();
        }
        
    })    

});