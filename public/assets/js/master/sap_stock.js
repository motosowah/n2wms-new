$(document).ready(function() {
    // -- get period list from server -- //
    $.ajax({
        url: baseUrl + '/api', 
        type: 'GET',
        data: {
            cmd: "get_sap_period_list",
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
    var table = $('#table_sap_stock').DataTable( {
        ajax: {
            url: baseUrl + '/api?cmd=get_sap_stock',
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
            },
            {
                orderable: false,
                targets: 0
            }
        ],
        dom: 'Bfrtip',
        "iDisplayLength": 25,
        // fixedHeader: true,
        responsive: true,
        "order": [[5, 'asc']], // Default sorting by first column (index 0) in ascending order
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

    // search entry history based on parameter //
    $('#btn1').click(function(){
        alert('button clicked');
        // Get data from column index 0
        var columnData = table.column(0).data().toArray();
        
        console.log(columnData);
        // alert('Column data: ' + columnData.join(', '));        
    })
    
    

});


// checkbox click listener //
$(document).on('click', '.row-checkbox', function() {
    /*
    // alert("checkbox clicked");
    if ($(this).is(':checked')) {
        console.log("Checked:", $(this).val());
    } else {
        console.log("Unchecked:", $(this).val());
    }
        */
    let checkedCount = $('.row-checkbox:checked').length;
    console.log("Checked row count:", checkedCount);

    // Auto update 'select all' checkbox
    let totalCheckboxes = $('.row-checkbox').length;
    $('#select-all').prop('checked', totalCheckboxes === checkedCount);

    // display selected item count //
    if(checkedCount > 0){
        $('#btn_delete_sap').show();
        $('#btn_delete_sap').text("Delete (" + checkedCount + ")");
    }else{
        $('#btn_delete_sap').text("Delete");
        $('#btn_delete_sap').hide();
    }
});


// Get all selected IDs (you can call this on button click)
function getSelectedIds() {
    let ids = [];
    $('.row-checkbox:checked').each(function () {
        ids.push($(this).val());
    });
    console.log("Selected IDs:", ids);
    return ids;
}


// toggle sap stock upload form //
function toggle_sap_stock_upload(){
    $('#upload_sap_stock_container').toggle();
}

// - preview sap stock data text - //
function preview_sap_stock(){
    // get text data //
    var text_data_val = $('#text_sap_stock_data').val();
    // console.log(text_data_val);
    if(text_data_val.length > 0){
        // loading progress //
        $('.loading').show();
        $('#btn_preview').disable();
        $('#progress-text').html("Checking ...");
        var ajax = $.ajax({
                url: baseUrl + '/api?cmd=preview_sap_stock',
                // url: "./api/master-api.php?cmd=preview_sap_stock",
                method: "POST",
                data: {
                    // cmd: "sap_stock_preview",
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
            // loading progress //
            $('.loading').hide();
            $('#btn_preview').enable();
            // console.log(msg);
            try {
                // var response = JSON.parse(msg);
                var response = msg;
                // hide text data editor //
                $('#text_sap_stock_data').hide();
                // data valid //
                if(response['status'] == 'ok'){
                    // console.log(response['data']);
                    // display output preview //
                    $('#sap_stock_upload_preview_output').html(response['data']);
                    // enable upload button //
                    $('#btn_upload').prop("disabled", false);
                    // disable preview button //
                    $('#btn_preview').prop("disabled", true);
                }else{
                    console.log(response['data']);
                    // data invalid //
                    $('#sap_stock_upload_preview_output').html(response['message']);
                    // disable button //
                    $('#btn_upload').prop("disabled", true);
                    $('#btn_preview').prop("disabled", true);
                }
            } catch (error) {
                alert(error);
                console.error(error);
            }

        });

        ajax.fail(function(err){
            console.log(err);
            $('.loading').hide();
            $('#btn_preview').enable();
            alert("Invalid server response!");
            display_error("Invalid server response!<hr/>" + err.responseText);
            console.error(err.responseText);
        });
        
    }else{
        alert("Text data cannot be empty");
    }
    
}

// - upload sap stock data text - //
function upload_sap_stock(){
    if(confirm("Are you sure to upload this data?")){
        // get text data //
        var text_data_val = $('#text_sap_stock_data').val();
        // console.log(text_data_val);
        if(text_data_val.length > 0){
            $('.loading').show();
            $('#progress-text').html("Uploading ...");
            $('#btn_upload').disable();
            var ajax = $.ajax({
                    // url: "./api/master-api.php?cmd=upload_sap_stock",
                    url: baseUrl + '/api?cmd=upload_sap_stock',
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

// get selected checkbox inside table //
function get_selected_checkbox(){
    let selectedIds = [];
    $('.row-checkbox:checked').each(function () {
        selectedIds.push($(this).val());
    });
    console.log("Selected row IDs:", selectedIds);
}


// delete sap stock //
function deleteItem(id){
    if(confirm("Delete sap stock id " + id + "?")){
        var ajax = $.ajax({
                // url: "./api/master-api.php?cmd=upload_sap_stock",
                url: baseUrl + '/api?cmd=delete_sap_stock',
                method: "GET",
                data: {
                    id: id
                },
                beforeSend: function () {
                    loadingStart("Deleting...");
                },
                complete: function () {
                    loadingEnd();
                }
            });
        
        ajax.done(function(msg){
            loadingEnd();
            try {
                // var response = JSON.parse(msg);
                var response = msg;
                // data valid //
                if(response['status'] == 'ok'){
                    alert(response['message']);
                    $('#btn_search').click();
                    // location.reload();
                    // table.ajax.reload(null, false);
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
            display_error("Invalid server response!<hr/>" + err.responseText);
        });
    }
}