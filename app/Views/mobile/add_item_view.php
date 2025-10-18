<?= $this->extend('layouts/mobile_layout') ?>

<?= $this->section('title') ?>
Add Item - Palletizing - WMS Mobile New
<?= $this->endSection() ?>

<!-- page content start -->
<?= $this->section('content') ?>

    <div class="progress" style="display: none;">
      <div class="indeterminate"></div>
    </div>

    <div class="col s12">
      <ul class="tabs">
        <li class="tab col s3"><a class="active" href="#scan">BARCODE</a></li>
        <li class="tab col s3"><a href="#search">MANUAL</a></li>
      </ul>
    </div>

    <!-- BARCODE SCAN TAB -->
    <div id="scan" class="col s12">        

        <!-- barcode scan input -->
        <div class="row">
          <div class="input-field col s12" style="position: relative;">
            <input id="barcode" type="text" class="validate">
            <label for="barcode">Scan Barcode</label>
            
            <a class="btn-floating waves-effect waves-light" 
              onclick="send_barcode()" 
              style="position: absolute; right: 10px; top: 50%; transform: translateY(-50%);">
              <i class="material-icons">send</i>
            </a>
          </div>
        </div>

        <div class="card-stacked">
          <div class="card-content">
            <div class="input-field col s12">
              <input id="item_code" type="text" class="" value=" " readonly>
              <label for="item_code">Item Code</label>
            </div>

            <div class="input-field col s12">
              <input id="item_desc" type="text" class="" value=" " readonly>
              <label for="item_desc">Item Description</label>
            </div>

            <div class="input-field col s12">
              <input id="lot_no" type="text" class="validate" value=" ">
              <label for="lot_no">Lot Number</label>
            </div>

            <div class="row">
              <div class="input-field col s4">
                <input placeholder="Qty (PCS)" id="qty_pcs" type="number" class="validate input-qty" style="font-size: 1.5em;">
                <label for="qty_pcs">Qty (PCS)</label>
              </div>
              <div class="input-field col s4">
                <input type="text" id="packaging" class="autocomplete">
                <label for="packaging">Packaging</label>
              </div>
              <div class="input-field col s4">
                <input placeholder="Size (KG)" id="size" type="number" class="validate input-qty" style="font-size: 1.5em;">
                <label for="size">Size (KG)</label>
              </div>
            </div>

            <div class="input-field col s12">
              <input id="total_qty" type="number" class="validate" value="0" style="font-size: 2em;">
              <label for="total_qty">Total Qty (KG)</label>
            </div>

        </div>
      </div>

      <div class="row">
        <div class="col s6">
          <a class="waves-effect waves-light btn full-width" onclick="save_item()"><i class="material-icons left">check</i>SAVE</a>
        </div>
        <div class="col s6">
          <a class="waves-effect waves-light btn full-width grey" onclick="cancel_entry()"><i class="material-icons left">cancel</i>CANCEL</a>
        </div>
      </div>
        
    </div>


    <!-- SEARCH ITEM TAB -->
    <div id="search" class="col s12">
      <div class="card-stacked">
        <div class="card-content">

          <!-- MANUAL ENTRY FORM -->
          <div id="search_item_form">
            <!-- search item text -->            
            <div class="input-field col s12">
              <input id="search_item" type="text" class="" value="">
              <label for="search_item">Search Item</label>
            </div>
          
            <!-- found items list -->
            <div id="items_found_list"></div>
          </div>
          

          <div id="manual_entry_form">
            <div class="input-field col s12">
              <input id="item_code_manual" type="text" class="" value=" " readonly>
              <label for="item_code_manual">Item Code</label>
            </div>
          
            <div class="input-field col s12">
              <input id="item_desc_manual" type="text" class="" value=" " readonly>
              <label for="item_desc_manual">Item Description</label>
            </div>
            
            <div class="input-field col s12">
              <input id="lot_no_manual" type="text" class="validate" value=" ">
              <label for="lot_no_manual">Lot Number</label>
            </div>

            <div class="row">
              <div class="input-field col s4">
                <input placeholder="Qty (PCS)" id="qty_pcs_manual" type="number" class="validate input-qty" style="font-size: 1.5em;">
                <label for="qty_pcs_manual">Qty (PCS)</label>
              </div>

              <div class="input-field col s4">
                <input type="text" id="packaging_manual" class="autocomplete">
                <label for="packaging_manual">Packaging</label>
              </div>

              <div class="input-field col s4">
                <input placeholder="Size (KG)" id="size_manual" type="number" class="validate input-qty" style="font-size: 1.5em;">
                <label for="size_manual">Size (KG)</label>
              </div>
            </div>

              <div class="input-field col s12">
                <input id="total_qty_manual" type="number" class="validate input-qty" value="0" style="font-size: 2em;">
                <label for="total_qty_manual">Total Qty (KG)</label>
              </div>

              <div class="row">
                <div class="col s6">
                  <a class="waves-effect waves-light btn-large full-width" onclick="save_item_manual()"><i class="material-icons left">check</i>SAVE</a>
                </div>
                <div class="col s6">
                  <a class="waves-effect waves-light btn-large full-width grey" onclick="cancel_entry()"><i class="material-icons left">cancel</i>CANCEL</a>
                </div>
              </div>

          </div>
        </div>

        </div>
      </div>

    </div>
  



  <!-- Modal Structure -->
  <div id="modal1" class="modal">
    <div class="modal-content">
      <h4>Modal Header</h4>
      <p>A bunch of text</p>
    </div>
    <div class="modal-footer">
      <a href="#!" class="modal-close waves-effect waves-green btn-flat">Agree</a>
    </div>
  </div>


  <!-- item not found modal message -->
  <div id="modal_invalid_item" class="modal">
    <div class="modal-content">
      <h5>Item Code Not Found!</h5>
      <p>Kode item tidak ditemukan.</p>
      <p>Silahkan hubungi admin.</p>
    </div>
    <div class="modal-footer">
      <a class="modal-close waves-effect waves-green btn-flat">OK</a>
    </div>
  </div>
          


<!-- helper style -->
<style>
.full-width {
  width: 100%;
}

/* item search list */
.box{
  font-size: 16px;
  border: 1px solid silver;
  margin: 10px 2px;
  padding: 5px;
  cursor: pointer;
}

#manual_entry_form{
  display: none;
}
</style>


<!-- tabs style -->
<style>
  /* Indicator bar under active tab */
  .tabs .indicator {
    background-color: #2196f3 !important; /* Material Design Blue 500 */
  }
  
  /* Remove default teal background on active tab */
  .tabs .tab a:focus, 
  .tabs .tab a:focus.active {
    background-color: transparent !important; 
  }

  /* Tab text color */
  .tabs .tab a {
    color: #555 !important; /* default/inactive text */
  }
  .tabs .tab a.active {
    color: #2196f3 !important; /* active tab text */
  }
  .tabs .tab a:hover {
    color: #1976d2 !important; /* hover state, darker blue */
  }
</style>


<script src="/assets/js/jquery.min.js"></script>
<script>
$(document).ready(function(){
  console.log('jquery loaded');
  $('select').formSelect();
  // activate tabs //
  // $('.tabs').tabs();

  $('.tabs').tabs({
    onShow: function(tab) {
      // tab = the activated tab content (div)
      console.log("Switched to:", tab.id);

      if (tab.id === "search") {
        // Run your function only when switching to Tab 2
        // myFunction();
        // alert('search tab clicked');
        $('#search_item').focus();
      }
    }
  });

  // activate modals //
  $('.modal').modal();

  // on barcode input enter key press //
  $('#barcode').on('keyup', function(e) {
    if (e.key === "Enter" || e.keyCode === 13) {
        send_barcode();
    }
  });


  // - auto calculate total qty - //
  $('.input-qty').on('keyup', function(e) {
    calculate_qty();
  });


  // packaging autocomplete //
  $('#packaging, #packaging_manual').autocomplete({
    data: {
      "CAN": null,
      "PAIL": null,
      "DRUM": null,
      "BAG": null,
      "PCS": null,
    },
    minLength: 0,
  });


  // search item //
  $('#search_item').keyup(function(){
    console.log('search item keyup');
    var search_text = $('#search_item').val();
    console.log(search_text);

    // if search text not empty //
    if(search_text.length > 0){
      // get data from server //
      $.ajax({
        url: '/mobile/search_item',
        type: 'GET',
        data: {
            search: search_text,
        },
        dataType: 'json', 
        success: function(response) {
            console.log(response);
            if(response.status == "ok"){
              // - response ok - //
              console.log(response);
              $('#items_found_list').html(response.html);
              
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
            // $(".progress").show(); // display progress
        },
        complete: function () {
            // $(".progress").hide(); // hide progress
        }
      });
    }else{
      console.log('empty search text');
      // clear items list //
      $('#items_found_list').html('');
    }

  });

});


// - choose item from search list - //
function choose_item(item_code, item_desc){
  $('#search_item_form').hide();
  $('#manual_entry_form').show();
  $('#item_code_manual').val(item_code);
  $('#item_desc_manual').val(item_desc);
}


// select text on cursor focus //
$('#barcode').focus(function(){
  $('#barcode').select();
})


// - calculate total qty - //
function calculate_qty(){
  var qty_pcs = $('#qty_pcs').val();
  var size = $('#size').val();
  var total_qty = qty_pcs * size;
  $('#total_qty').val(total_qty);

  var qty_pcs_manual = $('#qty_pcs_manual').val();
  var size_manual = $('#size_manual').val();
  var total_qty_manual = qty_pcs_manual * size_manual;
  $('#total_qty_manual').val(total_qty_manual);
}


// - send barcode to server - //
function send_barcode(){
  var barcode = $('#barcode').val();

  $.ajax({
    url: '/mobile/barcode',
    type: 'GET',
    data: {
        scan: barcode,
    },
    dataType: 'json', 
    success: function(response) {
        console.log(response);
        if(response.status == "ok"){
          // - response ok - //
          console.log(response);
          // place item data into forms //
          $('#item_code').val(response.item_code);
          $('#item_desc').val(response.item_desc);
          $('#lot_no').val(response.lot_no);
          $('#size').val(response.size);
          // focus to input pcs //
          $('#qty_pcs').focus();
          
        }else if(response.status == "error"){
          // - errors found - //
          // alert('invalid barcode');
          $('#barcode').focus();
          // open modal window //
          $('#modal_invalid_item').modal('open');
        }
    },
    error: function(xhr, status, error) {
        // if server send invalid JSON reply //
        console.error('Error:', error); 
        console.error('Invalid server response:', xhr.responseText);
    },
    beforeSend: function () {
        $(".progress").show(); // display progress
    },
    complete: function () {
        $(".progress").hide(); // hide progress
    }
  });
}


// - save item into temporary palletizing list - //
function save_item(){
  // create item data //
  var data = Array();
  data['item_code'] = $('#item_code').val();
  data['item_desc'] = $('#item_desc').val();
  data['lot_no'] = $('#lot_no').val();
  data['qty_pcs'] = $('#qty_pcs').val();
  data['packaging'] = $('#packaging').val();
  data['size'] = $('#size').val();
  data['total_qty'] = $('#total_qty').val();

  console.log(data);
  // send item data to server //
  $.ajax({
    url: '/mobile/add_temp_pltz',
    type: 'GET',
    data: {
        item_code: $('#item_code').val(),
        item_desc: $('#item_desc').val(),
        lot_no: $('#lot_no').val(),
        qty_pcs: $('#qty_pcs').val(),
        packaging: $('#packaging').val(),
        packing_size: $('#size').val(),
        total_qty: $('#total_qty').val(),
    },
    dataType: 'json', 
    success: function(response) {
        console.log(response);
        if(response.status == "ok"){
          // - response ok - //
          console.log(response);
          window.history.back();
          
        }else if(response.status == "error"){
          // - errors found - //
          // open modal error window //
          $('#modal_error').modal('open');
          $('#modal_error_message').html(response.message);
        }
    },
    error: function(xhr, status, error) {
        // if server send invalid JSON reply //
        console.error('Error:', error); 
        console.error('Invalid server response:', xhr.responseText);
    },
    beforeSend: function () {
        $(".progress").show(); // display progress
    },
    complete: function () {
        $(".progress").hide(); // hide progress
    }
  });
  
}


// - save item into temporary palletizing list (MANUAL) - //
function save_item_manual(){
  // send item data to server //
  $.ajax({
    url: '/mobile/add_temp_pltz',
    type: 'GET',
    data: {
        item_code: $('#item_code_manual').val(),
        item_desc: $('#item_desc_manual').val(),
        lot_no: $('#lot_no_manual').val(),
        qty_pcs: $('#qty_pcs_manual').val(),
        packaging: $('#packaging_manual').val(),
        packing_size: $('#size_manual').val(),
        total_qty: $('#total_qty_manual').val(),
    },
    dataType: 'json', 
    success: function(response) {
        console.log(response);
        if(response.status == "ok"){
          // - response ok - //
          console.log(response);
          window.history.back();
          
        }else if(response.status == "error"){
          // - errors found - //
          // open modal error window //
          $('#modal_error').modal('open');
          $('#modal_error_message').html(response.message);
        }
    },
    error: function(xhr, status, error) {
        // if server send invalid JSON reply //
        console.error('Error:', error); 
        console.error('Invalid server response:', xhr.responseText);
    },
    beforeSend: function () {
        $(".progress").show(); // display progress
    },
    complete: function () {
        $(".progress").hide(); // hide progress
    }
  });
  
}


// - cancel entry - //
function cancel_entry(){
  // back to previous page //
  window.history.back();
}


// - test function - //
function test(){
  alert('test function');
  console.log($('#transaction').val());
  // display toast message //
  M.toast({html: 'I am a toast!'})
}
</script>


<?= $this->endSection() ?>
<!-- page content end -->