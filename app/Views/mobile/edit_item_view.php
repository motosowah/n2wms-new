<?= $this->extend('layouts/mobile_layout') ?>

<?= $this->section('title') ?>
Edit Item - Palletizing - WMS Mobile New
<?= $this->endSection() ?>

<!-- page content start -->
<?= $this->section('content') ?>

    <div class="progress" style="display: none;">
      <div class="indeterminate"></div>
    </div>

    <div id="scan" class="col s12">
        <input type="hidden" id="id" value="<?= esc($id); ?>" />
        <div class="card-stacked">
        <div class="card-content">
          <div class="input-field col s12">
            <input id="item_code" type="text" class="" value="<?= esc($item_code); ?>" readonly>
            <label for="item_code">Item Code</label>
          </div>

          <div class="input-field col s12">
            <input id="item_desc" type="text" class="" value="<?= esc($item_desc); ?>" readonly>
            <label for="item_desc">Item Description</label>
          </div>

          <div class="input-field col s12">
            <input id="lot_no" type="text" class="validate" value="<?= esc($lot_no); ?>">
            <label for="lot_no">Lot Number</label>
          </div>

          <div class="row">
          <div class="input-field col s4">
            <input placeholder="Qty (PCS)" id="qty_pcs" type="number" class="validate" style="font-size: 1.5em;" value="<?= esc($qty_pcs); ?>">
            <label for="qty_pcs">Qty (PCS)</label>
          </div>
          <div class="input-field col s4">
            <input type="text" id="packaging" class="autocomplete" value="<?= esc($packaging); ?>">
            <label for="packaging">Packaging</label>
          </div>
          <div class="input-field col s4">
            <input placeholder="Size (KG)" id="packing_size" type="number" class="validate" style="font-size: 1.5em;" value="<?= esc($packing_size); ?>">
            <label for="packing_size">Size (KG)</label>
          </div>
        </div>

        <div class="input-field col s12">
          <input id="total_qty" type="number" class="validate" style="font-size: 2em;" value="<?= esc($total_qty); ?>">
          <label for="total_qty">Total Qty (KG)</label>
        </div>

        

        </div>
      </div>

      <div class="row">
        <div class="col s6">
          <a class="waves-effect waves-light btn-large full-width" onclick="save_item()"><i class="material-icons left">check</i>SAVE</a>
        </div>
        <div class="col s6">
          <a class="waves-effect waves-light btn-large full-width grey" onclick="delete_item()"><i class="material-icons left">delete</i>DELETE</a>
        </div>
      </div>
        
  

  <!-- Modal Trigger -->
  <!-- <a class="waves-effect waves-light btn modal-trigger" href="#modal1">Modal</a> -->

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
</style>


<script src="/assets/js/jquery.min.js"></script>
<script>
$(document).ready(function(){
  console.log('jquery loaded');
  $('select').formSelect();
  // activate tabs //
  $('.tabs').tabs();
  // activate modals //
  $('.modal').modal();

  // on barcode input enter key press //
  $('#barcode').on('keyup', function(e) {
    if (e.key === "Enter" || e.keyCode === 13) {
        send_barcode();
    }
  });


  // - auto calculate total qty - //
  $('#qty_pcs, #packing_size').on('keyup', function(e) {
    calculate_total();
  });


  // packaging autocomplete //
  $('#packaging').autocomplete({
    data: {
      "CAN": null,
      "PAIL": null,
      "DRUM": null,
      "BAG": null,
    },
    minLength: 0,
  });

  // refresh input labels //
  M.updateTextFields();

});


// select text on cursor focus //
$('#barcode').focus(function(){
  $('#barcode').select();
})


// - calculate total qty - //
function calculate_total(){
  var qty_pcs = $('#qty_pcs').val();
  var packing_size = $('#packing_size').val();
  var total_qty = qty_pcs * packing_size;
  $('#total_qty').val(total_qty);
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
  // send item data to server //
  $.ajax({
    url: '/mobile/save_temp_pltz',
    type: 'GET',
    data: {
        id: $('#id').val(),
        lot_no: $('#lot_no').val(),
        qty_pcs: $('#qty_pcs').val(),
        packaging: $('#packaging').val(),
        packing_size: $('#packing_size').val(),
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


// - delete item from temporary palletizing list - //
function delete_item(){
  var id = $('#id').val();
  if(confirm("Delete this item?")){
    $.ajax({
      url: '/mobile/delete_temp_pltz',
      type: 'GET',
      data: {
          id: $('#id').val()
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