<?= $this->extend('layouts/n2wms_layout') ?>

<?= $this->section('title') ?>
Palletizing - N2WMS New | Warehouse Management System
<?= $this->endSection() ?>


<!-- page content start -->
<?= $this->section('content') ?>

<style>

/* invalid entry message */
.invalid-feedback{
    display: none;
}

.spinner-border{
    display: none;
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
</style>

<div class="card card-outline card-primary" style="max-width: 500px;">
  <div class="card-body">
    <label for="plant" class="form-label">Plant</label>
    <input type="text" class="form-control plant" id="plant" placeholder="Plant" value="<?=esc($default_plant)?>" />
    <div class="invalid-feedback" id="invalid_plant">Please Enter Plant</div>

    <label for="transaction" class="form-label">Transaction Type</label>
    <select class="form-select" id="transaction" required="">
        <option>production receipt</option>
        <option>purchase receipt</option>
        <option>sales return</option>
        <option>production return</option>
        <option>stock adjustment</option>
        <option>others</option>
    </select>

    <label for="doc_no" class="form-label">Document Number</label>
    <input type="text" class="form-control" id="doc_no" placeholder="Document Number" value="NO_DOC" />

    <label for="pallet" class="form-label">Pallet Code</label>
    <input type="text" class="form-control" id="pallet" placeholder="Pallet Code" value="" />
    <div class="invalid-feedback" id="invalid_pallet">Invalid Pallet Code</div>

    <label for="rack" class="form-label">Rack Code</label>
    <input type="text" class="form-control" id="rack" placeholder="Rack Code" value="TEMP_REC" />
    <div class="invalid-feedback" id="invalid_rack">Invalid Rack Code</div>

    <label for="item_code" class="form-label">Item Code</label>
    <input type="text" class="form-control" id="item_code" placeholder="Item Code" value="" />
    <div class="invalid-feedback" id="invalid_item_code">Invalid Item Code</div>

    <label for="item_desc" class="form-label">Item Description</label>
    <input type="text" class="form-control" id="item_desc" placeholder="Item Description" value="" readonly />
    
    <label for="lot_no" class="form-label">Lot Number</label>
    <input type="text" class="form-control" id="lot_no" placeholder="Lot Number" value="" />
    <div class="invalid-feedback" id="invalid_lot_no">Invalid Lot Number</div>

    <label for="qty" class="form-label">Quantity</label>
    <input type="number" class="form-control" id="qty" placeholder="Quantity" value="" />
    <div class="invalid-feedback" id="invalid_qty">Invalid Quantity</div>

    <hr/>
    <div class="row">
        <div class="col-2">
            <button id="btn_save" class="btn btn-success" onclick="save()">SAVE</button>
        </div>
        <div class="col-1">
            <div class="spinner-border text-success mb-3" role="status" id="saving">
                <span class="sr-only">Saving...</span>
            </div>
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
                $("#pallet_loading").show().css("display", "inline-block"); // Show loading animation
            },
            complete: function () {
                $("#pallet_loading").hide(); // Hide loading animation
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
            },
            complete: function () {
                $("#pallet_loading").hide(); // Hide loading animation
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


// - autocomplete item code - //
$("#item_code").autocomplete({
    source: function (request, response) {
        $.ajax({
            // url: baseUrl + "/api",
            url: '/master/search_item',
            dataType: "json",
            data: {
                cmd: "search_item",
                plant: $("#plant").val(),
                search_text: $("#item_code").val() // Search term from the input field
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
$('#item_code').blur(function(){
    submitItemCode();
})

// get item description from item code //
function submitItemCode(){
    var item_code = validate_input("item_code");
    if(item_code){
        $.ajax({
            url: '/master/get_item_desc',
            type: 'GET',
            data: {
                item_code: item_code,
            },
            dataType: 'json', 
            success: function(response) {
                console.log(response);
                if(response.status == "ok"){
                    // - response ok - //
                    $('#item_desc').val(response.item_desc);
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
        $('#item_desc').val('');
    }
}


// save palletizing data //
function save(){
    // var new_item_code = validate_input("item_code");
    if(confirm("Are you sure?")){
        $.ajax({
            url: '/palletizing/save',
            type: 'GET',
            data: {
                plant: $('#plant').val(),
                transaction: $('#transaction').val(),
                doc_no: $('#doc_no').val(),
                pallet: $('#pallet').val(),
                rack: $('#rack').val(),
                item_code: $('#item_code').val(),
                item_desc: $('#item_desc').val(),
                lot_no: $('#lot_no').val(),
                qty: $('#qty').val(),
            },
            dataType: 'json', 
            success: function(response) {
                console.log(response);
                if(response.status == "ok"){
                    // - response ok - //
                    // alert("Saved!");

                    Swal.fire({
                        icon: "success",
                        // title: "Delivery Data Saved",
                        text: "Palletizing Data Saved!",
                        allowOutsideClick: false,
                        // footer: "*You have to fill all required fields"
                    }).then((result) => {
                    if (result.isConfirmed) {
                        // This runs when the OK (confirm) button is clicked
                        console.log("OK clicked!");
                        $('#pallet').focus();
                    } else if (result.isDismissed) {
                        // This runs if the user cancels, clicks outside, or presses Esc
                        console.log("Cancelled!");
                    }
                    });
                    
                    // clear form input //
                    $('#pallet, #rack, #item_code, #item_desc, #lot_no, #qty').val('');
                    $('#doc_no').val("NO_DOC");
                    // $('#pallet').focus();
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