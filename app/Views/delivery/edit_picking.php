<!-- edit picking data form -->
<div id="form_edit_picking" style="display: none;">
  <input type="hidden" id="edit_plant" />
  <input type="hidden" id="picking_id" />
  <div class="card card-primary card-outline mb-4">
  <div class="card-header">
    <div class="card-title">Edit Picking Location</div>
  </div>
  <div class="card-body">
    <div class="row">
    <div class="col-6">
      <div class="mb-3">
      <label for="edit_do_no" class="form-label">DO Number</label>
      <input type="text" class="form-control" id="edit_do_no" placeholder="DO Number" readonly />
      </div>
      <div class="mb-3">
      <label for="edit_item_code" class="form-label">Item Code</label>
      <input type="text" class="form-control" id="edit_item_code" placeholder="Item Code" readonly />
      </div>
      <div class="mb-3">
      <label for="edit_item_desc" class="form-label">Item Description</label>
      <input type="text" class="form-control" id="edit_item_desc" placeholder="Item Description"  readonly />
      </div>
      <div class="mb-3">
      <label for="edit_lot_no" class="form-label">Lot Number</label>
      <input type="text" class="form-control" id="edit_lot_no" placeholder="Lot Number" readonly />
      </div>
    </div>
    <div class="col-6">
      
      <div class="mb-3">
      <label for="edit_rack" class="form-label">Rack</label>
      <input type="text" class="form-control" id="edit_rack" placeholder="Rack">
      <div class="invalid-feedback" id="invalid_edit_rack">Please Enter Rack</div>
      </div>
      <div class="mb-3">
      <label for="edit_pallet" class="form-label">Pallet</label>
      <input type="text" class="form-control" id="edit_pallet" placeholder="Pallet">
      <div class="invalid-feedback" id="invalid_edit_pallet">Please Enter Pallet</div>
      </div>
      <div class="mb-3">
      <label for="picking_qty" class="form-label">Picking Qty</label>
      <input type="number" class="form-control" id="picking_qty" placeholder="Picking Qty">
      <div class="invalid-feedback" id="invalid_picking_qty">Please Enter Picking Qty</div>
      </div>
      
      <div class="col-3 d-flex align-items-center">
        <button id="btn_add_picking" class="btn btn-success ms-2" onclick="add_picking()">ADD</button>
        <button id="btn_save_edit_picking" class="btn btn-primary ms-2" onclick="save_edit_picking()">SAVE</button>
        <button id="btn_cancel_edit_picking" class="btn btn-secondary ms-2" onclick="cancel_edit_picking()">CANCEL</button>
      </div>
      
    </div>
    </div>
  </div>
  </div>
</div>

<style>
  #form_edit_picking .btn{
    min-width: 100px;
  }
</style>
<script>
// - open picking data for editing - //
function edit_picking(id){
  // alert("edit picking " + id);
  // - send entry data into server - //
  $.ajax({
    url: '/delivery/open_picking/' + id,
    type: 'GET',
    data: {
      // id: id,
    },
    dataType: 'json', 
    success: function(response) {
      console.log(response);
      if(response.status == "ok"){
        // get picking data //
        var data = response.data;
        console.log(data);
        $('#picking_id').val(data.id_t);
        $('#edit_do_no').val(data.doc_number);
        $('#edit_item_code').val(data.item_code);
        $('#edit_item_desc').val(data.item_desc);
        $('#edit_lot_no').val(data.lot_no);
        $('#edit_rack').val(data.storage_location);
        $('#edit_pallet').val(data.pallet_no);
        $('#picking_qty').val(data.inv_qty);
        // get plant from current pallet //
        $('#edit_plant').val("ID1" + data.pallet_no.substr(0, 1));
        $('#form_edit_picking').show();
        // Smooth scroll to top
        window.scrollTo({
        top: 0,
        behavior: 'smooth'
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
      miniLoading(true);
    },
    complete: function () {
      miniLoading(false);
    }
  });
}


// - add new location - //
function add_picking(){
  if(confirm('Add new location?')){
    // set empty location //
    $('#edit_rack').val('');
    $('#edit_pallet').val('');
    $('#picking_qty').val('');
  }
}


// - save modified picking data - //
function save_edit_picking(){
  var id = $('#picking_id').val();
  var do_no = $('#edit_do_no').val();
  var item_code = $('#edit_item_code').val();
  var lot_no = $('#edit_lot_no').val();
  var rack = $('#edit_rack').val();
  var pallet = $('#edit_pallet').val();
  var picking_qty = $('#picking_qty').val();
  // alert("saving picking " + id);
  // - send data into server - //
  $.ajax({
    url: '/delivery/save_picking',
    type: 'GET',
    data: {
      id: id,
      do_no: do_no,
      item_code: item_code,
      lot_no: lot_no,
      rack: rack,
      pallet: pallet,
      picking_qty: picking_qty,
    },
    dataType: 'json', 
    success: function(response) {
      console.log(response);
      if(response.status == "ok"){
        // - response ok - //
        Swal.fire({
          icon: "success",
          text: "Picking data #" + id + " saved!",
        });
        // hide editor form //
        $('#form_edit_picking').hide();
        // reload table data //
        table_on_process.ajax.reload(null, false);
      }else if(response.status == "warning"){
        // - warning found - //
        console.log("warning found")
        console.log(response);
      }else if(response.status == "error"){
        // - errors found - //
        Swal.fire({
          icon: "error",
          // title: "Invalid server response!",
          text: response.message,
          // footer: "Please contact system administrator!"
        });
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
      miniLoading(true);
    },
    complete: function () {
      miniLoading(false);
    }
  });
}


// - close edit picking form - //
function cancel_edit_picking(){
  $('#form_edit_picking').hide();
}


// - autocomplete rack list - //
$("#edit_rack").autocomplete({
  source: function (request, response) {
    $.ajax({
      url: '/master/search_rack', 
      dataType: "json",
      data: {
        plant: $("#edit_plant").val(),
        search: $("#edit_rack").val(),
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


// - autocomplete pallet entry - //
$("#edit_pallet").autocomplete({
  source: function (request, response) {
    $.ajax({
      url: '/master/search_pallet', 
      dataType: "json",
      data: {
        plant: $("#edit_plant").val(),
        search: $("#edit_pallet").val(),
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

</script>