<script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>


<h3>Barcode Scan Test (AJAX)</h3>
  <input type="text" name="barcode" id="barcode" value="" style="width: 100%;" placeholder="Scan barcode here..." />
  <hr/>
  <button onclick="send_barcode()">SEND</button>
<pre><?= esc($data ?? '') ?></pre>

<script>
// - send barcode to server - //
function send_barcode(){
  var barcode = $('#barcode').val();
  alert(barcode);

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
        Swal.fire({
            icon: "success",
            // title: "Delivery Data Saved",
            text: "Process picking delivery ID #" + id + " completed!",
            // footer: "*You have to fill all required fields"
        });
        // reload table data //
        table_on_process.ajax.reload(null, false);
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
        Swal.fire({
        icon: "error",
        title: "Invalid server response!",
        // text: xhr.responseText,
        html: xhr.responseText,
        footer: "Please contact system administrator!"
        });
        
    },
    beforeSend: function () {
        $("#table_on_process_overlay").show(); // display table 
    },
    complete: function () {
        $("#table_on_process_overlay").hide(); // display table 
    }
  });
}
</script>