<?= $this->extend('layouts/n2wms_layout') ?>

<?= $this->section('title') ?>
Splitting - N2WMS New | Warehouse Management System
<?= $this->endSection() ?>


<!-- page content start -->
<?= $this->section('content') ?>

<style>
.nav-tabs .nav-link.active {
    background-color: #0d6efd; /* Bootstrap primary blue */
    color: white;
    border-color: #0d6efd #0d6efd #fff;
}

.nav-tabs .nav-link {
    color: #0d6efd; /* Optional: text color for non-active tabs */
}

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
    }, 500);
});

// get selected radio plant //
function get_plant(){
    var selected_plant = $("input[name='radio_plant']:checked").val();
    console.log(selected_plant);
    return selected_plant;
}
</script>


<div class="card card-outline card-primary" style="max-width: 500px; min-height: 500px;">
  <div class="card-body">
    <!-- <img src="/images/angles-right-solid-full.svg" /> -->

    <label for="plant" class="form-label">Plant</label>
    <input type="text" class="form-control plant" id="plant" placeholder="Plant" value="<?=esc($default_plant)?>" />
    <div class="invalid-feedback" id="invalid_plant">Please Enter Plant</div>

    <div class="row">
        <div class="col">
            <div class="form-check">
            <input class="form-check-input" type="radio" name="radio_plant" id="id1a" value="ID1A" checked>
            <label class="form-check-label" for="id1a">
                ID1A
            </label>
            </div>
        </div>
        <div class="col">
            <div class="form-check">
            <input class="form-check-input" type="radio" name="radio_plant" id="id1c" value="ID1C">
            <label class="form-check-label" for="id1c">
                ID1C
            </label>
            </div>
        </div>
        <div class="col">
            <div class="form-check">
            <input class="form-check-input" type="radio" name="radio_plant" id="id1d" value="ID1D">
            <label class="form-check-label" for="id1d">
                ID1D
            </label>
            </div>
        </div>
        <div class="col">
            <div class="form-check">
            <input class="form-check-input" type="radio" name="radio_plant" id="id1e" value="ID1E">
            <label class="form-check-label" for="id1e">
                ID1E
            </label>
            </div>
        </div>
    </div>
    <hr/>   

    <!-- TAB NAVIGATION -->
    <ul class="nav nav-tabs" id="myTab" role="tablist">
        <li class="nav-item" role="presentation">
            <button class="nav-link active" id="tab0-tab" data-bs-toggle="tab" data-bs-target="#tab0" type="button" role="tab" aria-controls="tab0" aria-selected="true">SOURCE</button>
        </li>
        <li class="nav-item" role="presentation">
            <button class="nav-link" id="tab1-tab" data-bs-toggle="tab" data-bs-target="#tab1" type="button" role="tab" aria-controls="tab1" aria-selected="false">DESTINATION</button>
        </li>
    </ul>

    <div class="tab-content" id="myTabContent">
            
        <div class="tab-pane fade show active" id="tab0" role="tabpanel" aria-labelledby="tab0-tab">
            <div class="card card-outline card-primary" style="max-width: 500px; min-height: 0px;">
            <div class="card-body">

            <!-- SOURCE LOCATION -->
            <label for="source_pallet" class="form-label">Source Pallet</label>
            <div class="d-flex align-items-center gap-2">
                <input type="text" class="form-control" id="source_pallet" placeholder="Pallet Code" value="" />
                <img src="/images/angles-right-solid-full.svg" style="width: 32px; cursor: pointer;" onclick="check_pallet_code()" />
                <div class="dot-spinner" id="source_pallet_loading" style="display: none;">
                    <span></span><span></span><span></span>
                </div>
            </div>
            <div class="invalid-feedback" id="invalid_source_pallet">Invalid Pallet Code</div>

            <i class="fa-solid fa-angles-right"></i>

            <hr/>
            <div class="card card-outline card-success" id="card_items_container" style="min-height: 0px;">
                <div class="card-body" id="items_container">
                    
                </div>
            </div>

            </div>
            </div>
        </div>

        <div class="tab-pane fade" id="tab1" role="tabpanel" aria-labelledby="tab1-tab">
            <div class="card card-outline card-primary" style="max-width: 500px; min-height: 0px;">
            <div class="card-body">

            <!-- DESTINATION LOCATION -->
             <label for="pallet" class="form-label">Destination Pallet</label>
            <div class="d-flex align-items-center gap-2">
                <input type="text" class="form-control" id="destination_pallet" placeholder="Pallet Code" value="" />
                <div class="dot-spinner" id="destination_pallet_loading" style="display: none;">
                    <span></span><span></span><span></span>
                </div>
            </div>
            <div class="invalid-feedback" id="invalid_destination_pallet">Invalid Pallet Code</div>

            <label for="destination_rack" class="form-label">Destination Rack</label>
            <div class="d-flex align-items-center gap-2">
                <input type="text" class="form-control" id="destination_rack" placeholder="Rack Code" value="" />
                <div class="dot-spinner" id="destination_rack_loading" style="display: none;">
                    <span></span><span></span><span></span>
                </div>
            </div>
            <div class="invalid-feedback" id="invalid_destination_rack">Invalid Rack Code</div>

            <label for="destination_qty" class="form-label">Qty</label>
            <div class="d-flex align-items-center gap-2">
                <input type="number" class="form-control" id="destination_qty" placeholder="Qty" value="" />
            </div>
            <div class="invalid-feedback" id="invalid_split_qty">Invalid Qty</div>

            <hr/>

            <div class='card mb-3'>
            <div class='card-body'>
                <div class='row'>
                    <div class='col-5'>Item Code</div>
                    <div class='col-7'><b><span id="destination_item_code"></span></b></div>
                </div>
                <div class='row'>
                    <div class='col-5'>Item Description</div>
                    <div class='col-7'><b><span id="destination_item_desc"></span></b></div>
                </div>
                <div class='row'>
                    <div class='col-5'>Lot Number</div>
                    <div class='col-7'><b><span id="destination_lot_no"></span></b></div>
                </div>
                <div class='row'>
                    <div class='col-5'>Qty</div>
                    <div class='col-7'><b><span id="current_qty"></span></b></div>
                </div>
                <div class='row'>
                    <div class='col-5'>Current Rack</div>
                    <div class='col-7'><b><span id="current_rack"></span></b></div>
                </div>
            </div>
            </div>

            </div>
            </div>

        </div>

    </div>

    <input type="hidden" id="rack_source" />
    <input type="hidden" id="items_count" />
    
    <input type="hidden" id="pallet_valid" value="false" />
    <input type="hidden" id="rack_valid" value="false" />
    
    
<!--
    <label for="pallet" class="form-label">Pallet Code</label>
    <input type="text" class="form-control" id="pallet" placeholder="Pallet Code" value="" />
    <div class="invalid-feedback" id="invalid_pallet">Invalid Pallet Code</div>
-->
    

    
    
    <hr/>
    <div class="row">
        <div class="col-2" style="margin-right: 100px;">
            <button id="btn_save" class="btn btn-success" onclick="save_splitting()">SAVE</button>
        </div>
        <div class="col-1 text-end">
            <div class="spinner-border text-success mb-3" role="status" id="saving" style="display: none;">
                <span class="sr-only">Saving...</span>
            </div>
        </div>
    </div>
    <hr/>

    
  </div>

  
</div>

<pre><?php
    // $http_host = $_SERVER['HTTP_HOST'];
    // echo $http_host;
    // echo "<hr/>";
    // print_r($_SERVER);
?></pre>


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

    // $('#pallet').focus();
});


// -- on pallet entry -- //
$('#source_pallet').keyup(function(event){
    // convert to uppercase //
    $('#source_pallet').val($('#source_pallet').val().toUpperCase());
    check_pallet_code();
    // - on [Enter] key press - //
    if(event.key == "Enter"){
        check_pallet_code();
    }
    if(event.which > 47 || event.which == 8){  // except backspace //

    }else{
        
    }
})


// on input changed //
$('#source_pallet').change(function(event){
    // get_item_pallet();
    // check_pallet_code();
})


// check pallet code //
function check_pallet_code(){
    var pallet = validate_input("source_pallet");
    $.ajax({
        url: '/master/check_pallet_code',
        type: 'GET',
        data: {
            pallet: $('#source_pallet').val(),
        },
        dataType: 'json', 
        success: function(response) {
            console.log(response);
            if(response.status == "ok"){
                // - pallet code valid, get items list - //
                get_item_pallet();
                $('#invalid_source_pallet').hide();
                $('pallet_valid').val('true');
                // $("#source_pallet").autocomplete("close");
            }else{
                // invalid pallet code //
                $('pallet_valid').val('false');
                // $("#source_pallet").autocomplete("close");
                $('#invalid_source_pallet').text("Pallet code not found!");
                $('#invalid_source_pallet').show();
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
            $('#source_pallet_loading').show();
        },
        complete: function () {
            $("#saving").hide(); // Hide loading animation
            $('#source_pallet_loading').hide();
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
// - source pallet - //
$("#source_pallet").autocomplete({
    source: function (request, response) {
        $.ajax({
            url: '/master/search_pallet', 
            dataType: "json",
            data: {
                plant: $("#plant").val(),
                search: $("#source_pallet").val(),
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
                $("#source_pallet_loading").show();
            },
            complete: function () {
                $("#source_pallet_loading").hide(); // Hide loading animation
                // $(".dot-spinner").hide(); // Hide loading animation
            }
        });
    },
    minLength: 1, // Minimum number of characters before searching
    select: function (event, ui) {
        check_pallet_code();
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

// - destination pallet - //
$("#destination_pallet").autocomplete({
    source: function (request, response) {
        $.ajax({
            url: '/master/search_pallet', 
            dataType: "json",
            data: {
                plant: $("#plant").val(),
                search: $("#destination_pallet").val(),
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
                $("#destination_pallet_loading").show();
            },
            complete: function () {
                $("#destination_pallet_loading").hide(); // Hide loading animation
                // $(".dot-spinner").hide(); // Hide loading animation
            }
        });
    },
    minLength: 1, // Minimum number of characters before searching
    select: function (event, ui) {
        check_pallet_code();
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
$("#destination_rack").autocomplete({
    source: function (request, response) {
        $.ajax({
            url: '/master/search_rack', 
            dataType: "json",
            data: {
                plant: $("#plant").val(),
                search: $("#destination_rack").val(),
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
$('#destination_rack').keyup(function(event){
    // convert to uppercase //
    $('#destination_rack').val($('#destination_rack').val().toUpperCase());
    // - on [Enter] key press - //
    if(event.key == "Enter"){
        check_rack_code();
    }
    if(event.which > 47 || event.which == 8){  // except backspace //

    }else{
        
    }
})


// on input changed //
$('#destination_rack').change(function(event){
    // get_item_pallet();
    check_rack_code();
})


// get items inside palet //
function get_item_pallet(){
    var pallet = validate_input("source_pallet");
    if(true){
        $.ajax({
            url: '/splitting/get_item_pallet',
            type: 'GET',
            data: {
                pallet: $('#source_pallet').val(),
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
                    $('#card_items_container').hide();
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
                        <div class='card-body' style='cursor: pointer;' onclick='choose_item("${item_code}", "${item_desc}", "${lot_no}", "${qty}", "${rack}");'>
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

                    $('#card_items_container').show();


                }else if(response.status == "warning"){
                    // - warning found - //
                    console.log("warning found")
                    console.log(response);
                }else if(response.status == "error"){
                    // - errors found - //
                    $('#card_items_container').hide();
                }
            },
            error: function(xhr, status, error) {
                // if server send invalid JSON reply //
                console.error('Error:', error); 
                console.error('Invalid server response:', xhr.responseText); 
            },
            beforeSend: function () {
                $("#saving").show().css("display", "inline-block"); // Show loading animation
                $('#card_items_container').hide();
            },
            complete: function () {
                $("#saving").hide(); // Hide loading animation
                $('#card_items_container').show();
            }
        });    
    }else{
        // $('#item_desc').val('');
    }
}


// choose item for destination //
function choose_item(item_code, item_desc, lot_no, qty, rack){
    // alert(item_code);
    console.log(item_code);
    console.log(item_desc);
    console.log(lot_no);
    $('#tab1-tab').tab('show');
    $('#destination_item_code').text(item_code);
    $('#destination_item_desc').text(item_desc);
    $('#destination_lot_no').text(lot_no);
    $('#current_qty').text(qty);
    $('#current_rack').text(rack);
    $('#rack_source').val(rack);

    $('#destination_pallet').focus();

}


// save splitting data //
function save_splitting(){
    $.ajax({
        url: '/splitting/save',
        type: 'GET',
        data: {
            plant: $('#plant').val(),
            pallet_source: $('#source_pallet').val(),
            pallet_dest: $('#destination_pallet').val(),
            rack_source: $('#rack_source').val(),
            rack_dest: $('#destination_rack').val(),
            item_code: $('#destination_item_code').text(),
            lot_no: $('#destination_lot_no').text(),
            qty: $('#destination_qty').val(),
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
                    text: "Splitting Data Saved!",
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
                    url: '/splitting/save',
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
                                text: "splitting Data Saved!",
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
            footer: "Unable to splitting empty pallet"
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