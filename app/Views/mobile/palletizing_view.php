<?= $this->extend('layouts/mobile_layout') ?>

<?= $this->section('title') ?>
Palletizing - WMS Mobile New
<?= $this->endSection() ?>

<!-- page content start -->
<?= $this->section('content') ?>

<div class="progress" style="display: none;">
  <div class="indeterminate"></div>
</div>

<div class="input-field col s12">
  <select id="transaction">
    <option>production receipt</option>
    <option>purchase receipt</option>
    <option>production return</option>
    <option>sales return</option>
    <option>stock adjustment</option>
    <option>others</option>
  </select>
  <label>Transaction</label>
</div>

<div class="input-field col s12">
  <input id="doc_no" type="text" class="validate" value="NO_DOC">
  <label for="doc_no">Document Number</label>
</div>

<div class="input-field col s12">
  <input id="pallet" type="text" class="validate">
  <label for="pallet">Pallet</label>
  <span class="helper-text" id="pallet_helper" data-error="Invalid pallet code"></span>
</div>

<div class="input-field col s12">
  <input id="rack" type="text" class="validate" value="TEMP_REC">
  <label for="rack">Rack</label>
  <span class="helper-text" id="rack_helper" data-error="Invalid rack code"></span>
</div>

<div class="row">
  <div class="input-field col s6">
    <a href="/mobile/add_item" class="waves-effect waves-light btn full-width"><i class="material-icons left">add</i>ADD ITEM</a>
  </div>
  <div class="input-field col s6">
    <a class="waves-effect waves-light btn green full-width" onclick="validate_form()"><i class="material-icons left">save</i>SAVE</a>
  </div>
</div>


<hr/>

<!-- TEMPORARY ITEMS LIST -->
<div id="items_list">

</div>


<style>
.btn{
  /* padding: 15px;  */
}

.card-content{
  border: 1px solid silver;
  /* padding: 10px; */
  margin: 5px;
}

.full-width {
  width: 100%;
}

.helper-text{
  display: none;
}
</style>

<script src="/assets/js/jquery.min.js"></script>
<script>
$(document).ready(function(){
  console.log('jquery loaded');
  // activate option select //
  $('select').formSelect();

  // select text on cursor focus //
  $('#pallet').focus(function(){
    $('#pallet').select();
  })
  $('#rack').focus(function(){
    $('#rack').select();
  })


  // autoload pallet and rack values from cookies //
  $('#pallet').val(CookieUtils.get('pallet'));
  $('#rack').val(CookieUtils.get('rack'));


  // - autocomplete pallet code - //
  $('#pallet').autocomplete({
    data: {},
    limit: 5, // max results
    minLength: 1, // start after 2 chars
    onAutocomplete: function(val) {
      console.log("Selected:", val);
      // autosave pallet code value //
      CookieUtils.set('pallet', val);
    }
  });

  $('#pallet').on('input', function() {
    let query = $(this).val();

    if (query.length >= 1) {
      $.ajax({
        url: '/mobile/search_pallet',   // your endpoint
        data: { search: query },  // send query string
        success: function(res) {
          // assume res is array: ["Apple", "Banana", "Orange"]
          console.log(res);
          let data = {};
          res.forEach(item => { data[item] = null }); // Materialize expects { "Text": null }
          
          // update autocomplete source
          $('#pallet').autocomplete('updateData', data);
          $('#pallet').autocomplete('open'); // reopen dropdown
        }
      });
    }
  });


  // - autocomplete rack code - //
  $('#rack').autocomplete({
    data: {},
    limit: 5, // max results
    minLength: 1, // start after 2 chars
    onAutocomplete: function(val) {
      console.log("Selected:", val);
      // autosave rack code value //
      CookieUtils.set('rack', val);
    }
  });

  $('#rack').on('input', function() {
    let query = $(this).val();

    if (query.length >= 1) {
      $.ajax({
        url: '/mobile/search_rack',   // your endpoint
        data: { search: query },  // send query string
        success: function(res) {
          // assume res is array: ["Apple", "Banana", "Orange"]
          console.log(res);
          let data = {};
          res.forEach(item => { data[item] = null }); // Materialize expects { "Text": null }
          
          // update autocomplete source
          $('#rack').autocomplete('updateData', data);
          $('#rack').autocomplete('open'); // reopen dropdown
        }
      });
    }
  });



  // load temporary palletizing items //
  load_items();

  
});


// - autoload temporary items list - //
function load_items(){
  $.ajax({
    url: '/mobile/get_temp_pltz',
    type: 'GET',
    data: {
        // item_code: $('#item_code').val(),
    },
    dataType: 'json', 
    success: function(response) {
        console.log(response);
        if(response.status == "ok"){
          // - response ok - //
          console.log(response);
          // display items list //
          $('#items_list').html(response.html);
          // refresh input labels //
          M.updateTextFields();

          // put items into cookies //
          var items_list = response.items_list;
          console.log(items_list);
          console.log(JSON.stringify(items_list));
          CookieUtils.set('items_list', JSON.stringify(response.items_list));
          
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


//  validate forms //
function validate_form(){
  $(".progress").show(); // display progress
  var pallet = $('#pallet').val();
  var rack = $('#rack').val();

  var pallet_valid = false;
  var rack_valid = false;

  console.log(pallet.length);

  // if pallet not empty //
  if(pallet.length > 0){
    $.ajax({
      url: '/mobile/validate_pallet/' + pallet,
      type: 'GET',
      data: {},
      dataType: 'json', 
      success: function(response) {
          console.log(response);
          if(response.status == "ok"){
            // - response ok - //
            console.log(response);
            // pallet code valid
            pallet_valid = true;
            $('#pallet_helper').hide();

            // - validate rack code - //
            // if rack not empty //
            if(rack.length > 0){
              $.ajax({
                url: '/mobile/validate_rack/' + rack,
                type: 'GET',
                data: {},
                dataType: 'json', 
                success: function(response) {
                    console.log(response);
                    if(response.status == "ok"){
                      // - response ok - //
                      console.log(response);
                      // rack code valid
                      rack_valid = true;
                      $('#rack_helper').hide();
                      // save item //
                      save_item();
                    }else if(response.status == "error"){
                      // - errors found - //
                      // invalid rack code - //
                      rack_valid = false;
                      $('#rack').focus();
                      $('#rack').select();
                      $('#rack_helper').show();
                      $('#rack').addClass('invalid').removeClass('valid');
                      M.toast({
                        html: 'Rack code not found!',
                        classes: 'red darken-1 white-text'
                      });
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
            }else{
              // empty rack code //
              rack_valid = false;
              $('#rack').focus();
              $('#rack').select();
              $('#rack_helper').show();
              $('#rack').addClass('invalid').removeClass('valid');
            }
            

          }else if(response.status == "error"){
            // - errors found - //
            // invalid pallet code //
            console.log(' > invalid pallet code');
            pallet_valid = false;
            $('#pallet').focus();
            $('#pallet').select();
            $('#pallet_helper').show();
            $('#pallet').addClass('invalid').removeClass('valid');
            M.toast({
              html: 'Pallet code not found!',
              classes: 'red darken-1 white-text'
            });
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
  }else{
    pallet_valid = false;
    console.log(' > empty pallet code');
    $('#pallet').focus();
    $('#pallet').select();
    $('#pallet_helper').show();
    $('#pallet').addClass('invalid').removeClass('valid');
  }

  var result = pallet_valid && rack_valid;
  return result;
}


// - save item into temporary palletizing list - //
function save_item(){
  $(".progress").show(); // display progress
  // get items list //
  var items_list = JSON.parse(CookieUtils.get('items_list'));
  console.log(items_list.length);
  // if items list not empty //
  if(items_list.length > 0){
    //if(confirm("Confirm save palletizing?")){
      $(".progress").show(); // display progress
      var transaction = $('#transaction').val();
      var doc_no = $('#doc_no').val();
      var pallet = $('#pallet').val();
      var rack = $('#rack').val();
    
      // create item data //
      var data = Array();
      data['item_desc'] = $('#item_desc').val();
      data['lot_no'] = $('#lot_no').val();
      data['qty_pcs'] = $('#qty_pcs').val();
      data['packaging'] = $('#packaging').val();
      data['size'] = $('#size').val();
      data['total_qty'] = $('#total_qty').val();

      console.log(data);
      console.log(' > saving palletizing items');
      // send item data to server //
      $.ajax({
        url: '/palletizing/save_mobile',
        type: 'GET',
        data: {
          transaction: transaction,
          doc_no: doc_no,
          pallet: pallet,
          rack: rack,
          items_list: items_list
        },
        dataType: 'json', 
        success: function(response) {
            console.log(response);
            if(response.status == "ok"){
              // - response ok - //
              console.log(response);
              M.toast({
                html: 'Item saved successfully!',
                classes: 'green darken-1 white-text'
              });

              // reset form //
              CookieUtils.remove('pallet');
              CookieUtils.set('rack', 'TEMP_REC');
              $('#pallet').val('');
              $('#rack').val(CookieUtils.get('rack'))

              // clear temporary items //
              CookieUtils.remove('items_list');
              $.ajax({
                url: '/mobile/clear_temp_pltz',
                type: 'GET',
                data: {
                  
                },
                dataType: 'json', 
                success: function(response) {
                    console.log(response);
                    if(response.status == "ok"){
                      // - response ok - //
                      console.log(response);
                      // reset items list //
                      load_items();
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
              
            }else if(response.status == "error"){
              // - errors found - //
              M.toast({
                html: 'Error!',
                classes: 'red darken-1 white-text'
              });
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
    // }
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