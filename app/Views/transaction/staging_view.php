<?= $this->extend('layouts/n2wms_layout') ?>

<?= $this->section('title') ?>
Staging - N2WMS New | Warehouse Management System
<?= $this->endSection() ?>


<!-- page content start -->
<?= $this->section('content') ?>

<style>

/* invalid entry message */
.invalid-feedback{
    display: none;
}

.spinner-border{
    /* display: none; */
}

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

/* Full-screen overlay */
.spinner-overlay {
    position: fixed;
    top: 0;
    left: 0;
    width: 100vw;
    height: 100vh;
    background-color: rgba(255, 255, 255, 0.8); /* semi-transparent background */
    display: flex;
    justify-content: center;
    align-items: center;
    z-index: 1050; /* higher than modal backdrop */
}


    .dot-spinner {
      display: flex;
      align-items: center;
      gap: 0.4rem;
      height: 1rem;
    }

    .dot-spinner span {
      width: 0.6rem;
      height: 0.6rem;
      background-color: #007bff; /* Bootstrap's primary blue */
      border-radius: 50%;
      animation: blink 1.4s infinite;
      opacity: 0.2;
    }

    .dot-spinner span:nth-child(1) {
      animation-delay: 0s;
    }

    .dot-spinner span:nth-child(2) {
      animation-delay: 0.2s;
    }

    .dot-spinner span:nth-child(3) {
      animation-delay: 0.4s;
    }

    @keyframes blink {
      0%, 80%, 100% {
        opacity: 0.2;
        transform: scale(1);
      }
      40% {
        opacity: 1;
        transform: scale(1.3);
      }
    }
  
</style>

<!-- Spinner Overlay -->
<div id="spinnerOverlay" class="spinner-overlay">
  <div class="d-flex flex-column align-items-center">
    <div class="spinner-border text-primary" style="width: 4rem; height: 4rem;" role="status">
      <span class="visually-hidden">Loading...</span>
    </div>
    <div class="mt-3 fs-5 text-dark">
      Please wait...
    </div>
  </div>
</div>


  <script>
    // Demo: Remove spinner after 2 seconds
    window.addEventListener('load', () => {
      setTimeout(() => {
        document.getElementById('spinnerOverlay').style.display = 'none';
      }, 1000);
    });
  </script>

<div class="card card-outline card-primary" style="max-width: 500px; min-height: 500px;">
  <div class="card-body">
    <input type="hidden" id="rack_source" />
    <input type="hidden" id="items_count" />
    
    <input type="hidden" id="pallet_valid" value="false" />
    <input type="hidden" id="rack_valid" value="false" />
    
    <label for="plant" class="form-label">Plant</label>
    <input type="text" class="form-control plant" id="plant" placeholder="Plant" value="<?=esc($default_plant)?>" />
    <div class="invalid-feedback" id="invalid_plant">Please Enter Plant</div>
<!--
    <label for="pallet" class="form-label">Pallet Code</label>
    <input type="text" class="form-control" id="pallet" placeholder="Pallet Code" value="" />
    <div class="invalid-feedback" id="invalid_pallet">Invalid Pallet Code</div>
-->
    <label for="pallet" class="form-label">Pallet Code</label>
    <div class="d-flex align-items-center gap-2">
        <input type="text" class="form-control" id="pallet" placeholder="Pallet Code" value="" />
        <div class="dot-spinner" id="pallet_loading" style="display: none;">
            <span></span><span></span><span></span>
        </div>
    </div>
    <div class="invalid-feedback" id="invalid_pallet">Invalid Pallet Code</div>

    
    <label for="rack" class="form-label">Rack Code</label>
    <div class="d-flex align-items-center gap-2">
        <input type="text" class="form-control" id="rack" placeholder="Destination Rack" value="" />
        <div class="dot-spinner" id="rack_loading" style="display: none;">
            <span></span><span></span><span></span>
        </div>
    </div>
    <div class="invalid-feedback" id="invalid_rack">Invalid Rack Code</div>

    <hr/>
    <div class="row">
        <div class="col-2" style="margin-right: 100px;">
            <button id="btn_save" class="btn btn-success" onclick="save_staging()">SAVE</button>
        </div>
        <div class="col-1 text-end">
            <div class="spinner-border text-success mb-3" role="status" id="saving" style="display: none;">
                <span class="sr-only">Saving...</span>
            </div>
        </div>
    </div>
    <hr/>

    <div class="card card-outline card-success" style="min-height: 300px;">
        <div class="card-body" id="items_container">
            
        </div>
    </div>
  </div>

  
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

    $('#pallet').focus();
});


// -- on pallet entry -- //
$('#pallet').keyup(function(event){
    // convert to uppercase //
    $('#pallet').val($('#pallet').val().toUpperCase());
    // - on [Enter] key press - //
    if(event.key == "Enter"){
        check_pallet_code();
    }
    if(event.which > 47 || event.which == 8){  // except backspace //

    }else{
        
    }
})


// on input changed //
$('#pallet').change(function(event){
    // get_item_pallet();
    check_pallet_code();
})





// check pallet code //
function check_pallet_code(){
    var pallet = validate_input("pallet");
    $.ajax({
        url: '/master/check_pallet_code',
        type: 'GET',
        data: {
            pallet: $('#pallet').val(),
        },
        dataType: 'json', 
        success: function(response) {
            console.log(response);
            if(response.status == "ok"){
                // - pallet code valid, get items list - //
                get_item_pallet();
                $('#invalid_pallet').hide();
                $('#rack').focus();
                $('pallet_valid').val('true');
            }else{
                // invalid pallet code //
                $('pallet_valid').val('false');
                $("#pallet").autocomplete("close");
                $('#invalid_pallet').text("Pallet code not found!");
                $('#invalid_pallet').show();
                $('#items_container').html('');
            }
        },
        error: function(xhr, status, error) {
            // if server send invalid JSON reply //
            console.error('Error:', error); 
            console.error('Invalid server response:', xhr.responseText); 
        },
        beforeSend: function () {
            $("#saving").show().css("display", "inline-block"); // Show loading animation
            $('#pallet_loading').show();
        },
        complete: function () {
            $("#saving").hide(); // Hide loading animation
            $('#pallet_loading').hide();
        }
    });
}


// check rack code //
function check_rack_code(){
    var rack = validate_input("rack");
    $.ajax({
        url: '/master/check_rack_code',
        type: 'GET',
        data: {
            rack: $('#rack').val(),
        },
        dataType: 'json', 
        success: function(response) {
            console.log(response);
            if(response.status == "ok"){
                $('rack_valid').val('true');
                // get_item_pallet();
                $('#invalid_rack').hide();
                $('#btn_save').focus();
            }else{
                $('rack_valid').val('false');
                $("#rack").autocomplete("close");
                $('#invalid_rack').text("Rack code not found!");
                $('#invalid_rack').show();
                // $('#items_container').html('');
            }
        },
        error: function(xhr, status, error) {
            // if server send invalid JSON reply //
            console.error('Error:', error); 
            console.error('Invalid server response:', xhr.responseText); 
        },
        beforeSend: function () {
            $("#saving").show().css("display", "inline-block"); // Show loading animation
            $('#rack_loading').show();
        },
        complete: function () {
            $("#saving").hide(); // Hide loading animation
            $('#rack_loading').hide();
        }
    });
}


// - reset form - //
function reset_form(){
    $('#pallet, #rack').val('');
    $('#items_container').html('');
    $('#pallet').focus();
}




// - autocomplete pallet list - //
var plant_val = $('#plant').val();
// jquery autocomplete //
$("#pallet").autocomplete({
    source: function (request, response) {
        $.ajax({
            url: '/master/search_pallet', 
            dataType: "json",
            data: {
                plant: $("#plant").val(),
                search: $("#pallet").val(),
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
                // $("#pallet_loading").show().css("display", "inline-block"); // Show loading animation
                $("#pallet_loading").show();
            },
            complete: function () {
                $("#pallet_loading").hide(); // Hide loading animation
                // $(".dot-spinner").hide(); // Hide loading animation
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


// - autocomplete rack list - //
var plant_val = $('#plant').val();
// jquery autocomplete //
$("#rack").autocomplete({
    source: function (request, response) {
        $.ajax({
            url: '/master/search_rack', 
            dataType: "json",
            data: {
                plant: $("#plant").val(),
                search: $("#rack").val(),
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
                $('#rack_loading').show();
            },
            complete: function () {
                $("#pallet_loading").hide(); // Hide loading animation
                $('#rack_loading').hide();
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


// -- on rack entry -- //
$('#rack').keyup(function(event){
    // convert to uppercase //
    $('#rack').val($('#rack').val().toUpperCase());
    // - on [Enter] key press - //
    if(event.key == "Enter"){
        check_rack_code();
    }
    if(event.which > 47 || event.which == 8){  // except backspace //

    }else{
        
    }
})


// on input changed //
$('#rack').change(function(event){
    // get_item_pallet();
    check_rack_code();
})


// get items inside palet //
function get_item_pallet(){
    var pallet = validate_input("pallet");
    if(true){
        $.ajax({
            url: '/staging/get_item_pallet',
            type: 'GET',
            data: {
                pallet: $('#pallet').val(),
            },
            dataType: 'json', 
            success: function(response) {
                console.log(response);
                if(response.status == "ok"){
                    // - response ok - //
                    var data = response.data;
                    console.log(data);
                    console.log(data.length);
                    var items_count = data.length;
                    $('#items_count').val(items_count);

                    // clear content //
                    $('#items_container').html('');

                    // display item list inside item container //
                    data.forEach(function(item){
                        console.log(item);
                        var item_code = item['item_code'];
                        var item_desc = item['item_desc'];
                        var lot_no = item['lot_no'];
                        var qty = item['qty_available'];
                        var rack = item['rack'];
                        var pallet = item['pallet'];

                        var card_html = `
                        <div class='card mb-3'>
                        <div class='card-body'>
                            <div class='row'>
                                <div class='col-5'>Item Code</div>
                                <div class='col-7'><b>${item_code}</b></div>
                            </div>
                            <div class='row'>
                                <div class='col-5'>Item Description</div>
                                <div class='col-7'><b>${item_desc}</b></div>
                            </div>
                            <div class='row'>
                                <div class='col-5'>Lot Number</div>
                                <div class='col-7'><b>${lot_no}</b></div>
                            </div>
                            <div class='row'>
                                <div class='col-5'>Qty</div>
                                <div class='col-7'><b>${qty}</b></div>
                            </div>
                            <div class='row'>
                                <div class='col-5'>Current Rack</div>
                                <div class='col-7'><b>${rack}</b></div>
                            </div>
                            <div class='row'>
                                <div class='col-5'>Current Pallet</div>
                                <div class='col-7'><b>${pallet}</b></div>
                            </div>
                        </div>
                        </div>
                        `;

                        $('#items_container').append(card_html);
                        
                    });


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
                $("#saving").show().css("display", "inline-block"); // Show loading animation
            },
            complete: function () {
                $("#saving").hide(); // Hide loading animation
            }
        });    
    }else{
        // $('#item_desc').val('');
    }
    
}


// save staging data //
function save_staging(){
    $.ajax({
        url: '/staging/save',
        type: 'GET',
        data: {
            plant: $('#plant').val(),
            pallet: $('#pallet').val(),
            rack: $('#rack').val(),
        },
        dataType: 'json', 
        success: function(response) {
            console.log(response);
            if(response.status == "ok"){
                // - response ok - //
                // alert("Saved!");
                Swal.fire({
                    icon: "success",
                    title: "Success",
                    text: "Staging Data Saved!",
                    timer: 2000,
                    allowOutsideClick: false,
                });
                // clear form input //
                reset_form();
            }else if(response.status == "warning"){
                // - warning found - //
                console.log("warning found")
                console.log(response);
            }else if(response.status == "error"){
                // - errors found - //
                Swal.fire({
                    icon: "error",
                    title: "Error",
                    html: response.message,
                    allowOutsideClick: false,
                    // timer: 2000,
                    footer: "Please check your entry"
                });                        
            }
        },
        error: function(xhr, status, error) {
            // if server send invalid JSON reply //
            console.error('Error:', error); 
            console.error('Invalid server response:', xhr.responseText); 
        },
        beforeSend: function () {
            $("#saving").show().css("display", "inline-block"); // Show loading animation
            $('#btn_save').prop("disabled", true);
            $('#btn_save').text("SAVING");
            $('#spinnerOverlay').show();
        },
        complete: function () {
            $("#saving").hide(); // Hide loading animation
            $('#btn_save').prop("disabled", false);
            $('#btn_save').text("SAVE");
            $('#spinnerOverlay').hide();
        }
    });
/*
    var items_count = parseFloat($('#items_count').val());
    // if items inside pallet not empty //
    var items_count_valid = false;
    if(items_count > 0){
        items_count_valid = true;
    }else{
        items_count_valid = false;
    }
    if(items_count_valid){
        // check for destination rack //
        var rack_valid = $('#rack_valid').val();
        console.log(rack_valid);
        if(rack_valid){
            if(confirm("Are you sure?")){
                $.ajax({
                    url: '/staging/save',
                    type: 'GET',
                    data: {
                        plant: $('#plant').val(),
                        pallet: $('#pallet').val(),
                        rack: $('#rack').val(),
                    },
                    dataType: 'json', 
                    success: function(response) {
                        console.log(response);
                        if(response.status == "ok"){
                            // - response ok - //
                            // alert("Saved!");
                            Swal.fire({
                                icon: "success",
                                title: "Success",
                                text: "Staging Data Saved!",
                                timer: 2000,
                                allowOutsideClick: false,
                            });
                            // clear form input //
                            reset_form();
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
                        $("#saving").show().css("display", "inline-block"); // Show loading animation
                        $('#btn_save').prop("disabled", true);
                        $('#btn_save').text("SAVING");
                    },
                    complete: function () {
                        $("#saving").hide(); // Hide loading animation
                        $('#btn_save').prop("disabled", false);
                        $('#btn_save').text("SAVE");
                    }
                });    
            }else{
                // $('#item_desc').val('');
            }
        }else{
            // invalid rack rode //
            Swal.fire({
                icon: "error",
                title: "Error",
                text: "Rack Code Not Found!",
                allowOutsideClick: false,
                // timer: 2000,
                footer: "Please check your destination rack"
            });
        }
    }else{
        // pallet items empty //
        Swal.fire({
            icon: "error",
            title: "Error",
            text: "Pallet Empty!",
            allowOutsideClick: false,
            // timer: 2000,
            footer: "Unable to staging empty pallet"
        });
    }
*/        
}


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


</script>

<?= $this->endSection() ?>
<!-- page content end -->