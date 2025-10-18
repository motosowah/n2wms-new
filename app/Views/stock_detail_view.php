<?= $this->extend('layouts/n2wms_layout') ?>

<?= $this->section('title') ?>
Stock Detail - N2WMS New | Warehouse Management System
<?= $this->endSection() ?>


<!-- page content start -->
<?= $this->section('content') ?>

<style>
.table-overlay{
    display: none;
    position: absolute;
    top: 0;
    left: 0;
    width: 100%;
    height: 100%;
    background-color: rgba(255,255,255,0.7);
    z-index: 1000;

    display: flex;
    justify-content: center;
    align-items: center;

    font-size: 20px;
    color: #333;
}
</style>

<div class="card">
  <div class="card-body">
    <div class="row gx-1">
        <div class="col">
            <input type="text" class="form-control" id="item_code" placeholder="Item Code">
        </div>
        <div class="col-2">
            <input type="text" class="form-control" id="item_desc" placeholder="Item Description">
        </div>
        <div class="col">
            <input type="text" class="form-control" id="lot_no" placeholder="Lot Number">
        </div>

        <div class="col">
            <input type="text" class="form-control" id="rack" placeholder="Rack">
        </div>
        <div class="col">
            <input type="text" class="form-control" id="pallet" placeholder="Pallet">
        </div>
        <div class="col-1">
            <input type="text" class="form-control" id="plant" placeholder="Plant">
            <div class="invalid-feedback" id="invalid_plant" style="display: none;">Enter Plant</div>
        </div>
        <div class="col">
            <button id="btn_search" class="btn btn-primary">Search</button>
        </div>
        <div class="col-1">
            <div class="spinner-border text-primary" role="status" id="loading">
                <span class="sr-only">Loading...</span>
            </div>
        </div>
    </div>
  </div>

    <hr/>
    <div id="table-container" style="position: relative;">
        <div id="table_stock_detail_overlay" class="table-overlay">
            Loading...
        </div>
        <table id="table_stock_detail" class='stripe nowrap' width="100%">
        <thead>
        <tr>
            <th>Plant</th>
            <th>Item Code</th>
            <th>Item Description</th>
            <th>Lot Number</th>
            <th>Rack</th>
            <th>Pallet</th>
            <th>Onhand Stock</th>
            <th>Allocated</th>
            <th>Available Stock</th>
        </tr>
        </thead>
        <tbody></tbody>
    </table>
    </div>
    <hr/>
</div>


<script>
$(document).ready(function() {
    // - plant list - //
    var plant_list = ['', 'ID1A', 'ID1C', 'ID1D', 'ID1E'];
    $("#plant").autocomplete({
        source: plant_list,
        minLength: 0 // Allow search with zero characters
    }).on("focus", function() {
        $(this).autocomplete("search", ""); // Trigger search with an empty string
    });

    // activate datatable //
    var table = $('#table_stock_detail').DataTable( {
        ajax: {
            url: '/stock/get_stock_detail',
            // url: '/api/stock-detail',
            // "url": './api/master-api.php?cmd=get_wms_stock',
            "data": function(d) {
                // get search input parameter //
                var plant = $('#plant').val();
                var item_code = $('#item_code').val();
                var item_desc = $('#item_desc').val();
                var lot_no = $('#lot_no').val();
                var rack = $('#rack').val();
                var pallet = $('#pallet').val();

                d.plant = plant;
                d.item_code = item_code;
                d.item_desc = item_desc;
                d.lot_no = lot_no;
                d.rack = rack;
                d.pallet = pallet;

                // if input empty, load empty data //
                var input_length = plant.length + item_code.length + item_desc.length + lot_no.length + rack.length + pallet.length;
                if(input_length == 0){
                    d.empty = "yes";
                }
            },
            beforeSend: function () {
                $("#loading").show().css("display", "inline-block"); // Show loading animation
                $("#table_stock_detail_overlay").show(); // display table overlay //
            },
            complete: function () {
                $("#loading").hide(); // Hide loading animation
                $("#table_stock_detail_overlay").hide(); // display table overlay //
            },
            error: function(xhr, status, error) {
                alert("Invalid server response!");
                console.error('AJAX status:', status); // e.g. "timeout", "error", "abort", "parsererror"
                console.error('HTTP status code:', xhr.status); // e.g. 502, 0, 404
                console.error('Server Error:', error);
                console.error('Invalid server response:', xhr.responseText); 
            },
        },
        scrollX: true,
        "columnDefs": [
            {
                "targets": [6, 7, 8], // Assuming you want to format the first column
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
        "order": [[2, 'asc']], // Default sorting by first column (index 0) in ascending order
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
        var plant = $('#plant').val();
        var plant_valid = false;
        if(plant === ""){
            $("#invalid_plant").show();
            plant_valid = false;
        }else{
            $("#invalid_plant").hide();
            plant_valid = true;
        };

        // if form valid //
        if(plant_valid){
            // reload table data //
            table.ajax.reload(null, false);
            // Clear the global search
            table.search('').draw();
        }

        // // alert('button clicked');
        // if($('#period').val() == ""){
        //     alert("Please enter period!");
        //     $('#period').focus();
        // }else{
        //     table.ajax.reload(null, false); // false keeps pagination state
        // }
        
    })    

    // - on input enter key - //
    $('.form-control').on('keydown', function (e) {
        if (e.key === 'Enter' || e.keyCode === 13) {
            // Your code here
            e.preventDefault(); // Optional: prevents form submission if it's in a form
            $('#btn_search').click();
        }
    });
});


// toggle sap stock upload form //
function toggle_wms_stock_upload(){
    $('#upload_wms_stock_container').toggle();
}

// - preview sap stock data text - //
function preview_wms_stock(){
    // get text data //
    var text_data_val = $('#text_wms_stock_data').val();
    // console.log(text_data_val);
    if(text_data_val.length > 0){
        $('.loading').show();
        $('#btn_preview').disable();
        $('#progress-text').html("Checking ...");
        var ajax = $.ajax({
                url: baseUrl + '/api?cmd=preview_wms_stock',
                // url: "./api/master-api.php?cmd=preview_wms_stock",
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
            try {
                // var response = JSON.parse(msg);
                var response = msg;
                // hide text data editor //
                $('#text_wms_stock_data').hide();
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
                console.error(error);
            }
            
            // alert(msg);
            
        });

        ajax.fail(function(err){
            console.log(err);
            $('.loading').hide();
            alert("Invalid server response!");
            display_error("Invalid server response!<hr/>" + err.responseText);
        });
        
    }else{
        alert("Text data cannot be empty");
    }
    
}

// - upload sap stock data text - //
function upload_wms_stock(){
    if(confirm("Are you sure to upload this data?")){
        // get text data //
        var text_data_val = $('#text_wms_stock_data').val();
        // console.log(text_data_val);
        if(text_data_val.length > 0){
            $('.loading').show();
            $('#progress-text').html("Uploading ...");
            $('#btn_upload').disable();
            var ajax = $.ajax({
                // url: "./api/master-api.php?cmd=upload_wms_stock",
                    url: baseUrl + '/api?cmd=upload_wms_stock',
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
</script>

<?= $this->endSection() ?>
<!-- page content end -->