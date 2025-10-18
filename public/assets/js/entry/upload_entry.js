
// - preview sap stock data text - //
function preview_upload_entry(){
    // get text data //
    var text_data_val = $('#text_upload_entry_data').val();
    // console.log(text_data_val);
    if(text_data_val.length > 0){
        $('.loading').show();
        var ajax = $.ajax({
                // url: "./entry/upload/upload_entry_api.php",
                url: baseUrl + '/api?cmd=preview_upload_entry',
                method: "POST",
                data: {
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
                var response = (msg);
                // hide text data editor //
                // $('#text_upload_entry_data').hide();
                // data valid //
                if(response['status'] == 'ok'){
                    $("#text_upload_entry_data").hide();
                    // display output preview //
                    $('#upload_entry_upload_preview_output').html(response['data']);
                    // enable upload button //
                    $('#btn_upload').prop("disabled", false);
                }else{
                    // data invalid //
                    // alert(response['message']);
                    $('#upload_entry_upload_preview_output').html(response['message']);
                }
            } catch (error) {
                
                alert(error);
                console.log(msg);
                
            }
            
            // alert(msg);
            
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

// - upload STO entry text - //
function upload_upload_entry(){
    if(confirm("Are you sure to upload this data?")){
        // get text data //
        var text_data_val = $('#text_upload_entry_data').val();
        // console.log(text_data_val);
        if(text_data_val.length > 0){
            $('.loading').show();
            var ajax = $.ajax({
                    url: "./entry/upload/upload_entry_api.php",
                    method: "POST",
                    data: {
                        cmd: "upload_entry",
                        text_data: text_data_val
                    }
                });
            
            ajax.done(function(msg){
                $('.loading').hide();
                // console.log(msg);
                try {
                    var response = JSON.parse(msg);
                    // data valid //
                    if(response['status'] == 'ok'){
                        // display output preview //
                        $('#upload_entry_upload_preview_output').html(response['message']);
                        alert(response['message']);
                        location.reload();
                    }else{
                        // data invalid //
                        $('#upload_entry_upload_preview_output').html(response['message']);
                        alert(response['message']);
                    }
                } catch (error) { 
                    alert(error);
                    console.log(msg);
                }

            });

            ajax.fail(function(err){
                $('.loading').hide();
                alert(err);
            });
            
        }else{
            alert("Text data cannot be empty");
        }
    }
    
}
