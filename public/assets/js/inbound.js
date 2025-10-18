// init item index //
item_index = [];

// item no. //
var i = 1;

// new item entry validation //
var item_code_valid = false;
var lot_no_valid = false;
var qty_valid = false;
var location_valid = false;


// on page loaded //
$(function(){

    // -- on item code entry -- //
    $('#edit_item_code').keyup(function(event){
        if(event.which > 47 || event.which == 8){  // except backspace //
            var search_text_val = $('#edit_item_code').val();
            // if not empty //
            if(search_text_val.length > 0){
                // remove special character //
                $('#edit_item_code').val(search_text_val.replace(/[^a-zA-Z0-9]/g, ""));
                // convert to uppercase //
                autocompleteItemCode();
            }
        }else{
            // - on [Enter] key press - //
            if(event.key == "Enter"){
                // get item name from item code //
                var item_code_val = $('#edit_item_code').val();
                // do not process empty item code //
                if(item_code_val.length > 0){
                    // remove special character //
                    $('#edit_item_code').val(item_code_val.replace(/[^a-zA-Z0-9]/g, ""));
                    // choose item code //
                    chooseItem();
                }
            }
        }
    })

    // auto choose item code on leaving input //
    $('#edit_item_code').blur(function(){
        // get item name from item code //
        var item_code_val = $('#edit_item_code').val();
        // do not process empty item code //
        if(item_code_val.length > 0){
            // remove special character //
            $('#edit_item_code').val(item_code_val.replace(/[^a-zA-Z0-9]/g, ""));
            // choose item code //
            chooseItem();
        }
    })


    // validate lot_no on leaving input //
    $('#edit_lot_no').blur(function(){
        // get item name from item code //
        var item_code_val = $('#edit_lot_no').val();
        // do not process empty item code //
        if(item_code_val.length > 0){
            // remove special character //
            $('#edit_lot_no').val(item_code_val.replace(/[^a-zA-Z0-9]/g, ""))
            // lot no valid //
            lot_no_valid = true;
            $('#warning_invalid_lot_message').text("")
        }else{
            // prevent empty lot no //
            $('#warning_invalid_lot_message').text("Lot Number required!")
            // lot no valid //
            lot_no_valid = false;
        }
    })


    // -- on location entry -- //
    $('#edit_location').keyup(function(event){
        if(event.which > 47 || event.which == 8){  // except backspace //
            var search_text_val = $('#edit_location').val();
            // if not empty //
            if(search_text_val.length > 0){
                // remove special character //
                $('#edit_location').val(search_text_val.replace(/[^a-zA-Z0-9]/g, ""));
                // convert to uppercase //
                
                autocompleteLocation();
            }
        }else{
            // - on [Enter] key press - //
            if(event.key == "Enter"){
                // get item name from item code //
                var item_code_val = $('#edit_location').val();
                // do not process empty item code //
                if(item_code_val.length > 0){
                    // remove special character //
                    $('#edit_location').val(item_code_val.replace(/[^a-zA-Z0-9]/g, ""));
                    // choose item code //
                    chooseLocation();
                }
            }
        }
    })


    // validate qty on leaving input //
    $('#edit_qty').blur(function(){
        console.log("checking qty");
        var qty_val = $('#edit_qty').val();
        // do not process empty input //
        if(qty_val.length > 0){
            // accept only positive number //
            console.log(qty_val);
            if(Number(qty_val) > 0){
                // qty valid //
                qty_valid = true;
                $('#warning_invalid_qty_message').text("")
            }else{
                $('#warning_invalid_qty_message').text("Invalid Qty!")
                // invalid qty //
                qty_valid = false;
            }   
        }
    })


    // auto choose location on leaving input //
    $('#edit_location').blur(function(){
        // get location name from item code //
        var location_val = $('#edit_location').val();
        // do not process empty location code //
        if(location_val.length > 0){
            // remove special character //
            $('#edit_location').val(location_val.replace(/[^a-zA-Z0-9]/g, ""));
            // choose location code //
            chooseLocation();
        }
    })

    // hide edito forms //
    $('#item_editor_form').hide();

})


// display form //
function showForm(form){
    $('#item_list, #item_editor_form, #new_item_form, #edit_item_form').hide();
    $('#' + form).show();
}

// add new item //
function addNew(){
    // clear form //
    $("#edit_item_code, #edit_item_name, #edit_lot_no, #edit_qty, #edit_location, #edit_location_name").val("");
    showForm('item_editor_form');
}

// cancel add new item //
function newItemCancel(){
    showForm('item_list');
}

// cancel edit item //
function editItemCancel(){
    showForm('item_list');
}


// add new item into list //
data_list = {};
function saveItemNew(item_code, item_name, lot_no, qty, location){
    // check for entry validity //
    console.log(item_code_valid);
    console.log(lot_no_valid);
    console.log(qty_valid);
    console.log(location_valid);

    entry_valid = item_code_valid && lot_no_valid && qty_valid && location_valid;
    console.log(entry_valid);

    if(entry_valid){
        // get item data //
        var item_id = $('#item_id').val();
        var item_code = $('#edit_item_code').val();
        var item_name = $('#edit_item_name').val();
        var lot_no = $('#edit_lot_no').val();
        var qty = $('#edit_qty').val();
        var location = $('#edit_location').val();

        var data = {};
        data['item_code'] = item_code;
        data['item_name'] = item_name;
        data['lot_no'] = lot_no;
        data['qty'] = qty;
        data['location'] = location;

        // insert item data into list //
        data_list[i] = data;

        // insert item index //
        item_index.push(i);

        console.log(data_list);
        console.log(item_index);
        console.log(data);

        i++;

        // show items list editor //
        showForm("item_list");
        // reload items list //
        loadItem();
    }else{
        alert("You have invalid entry! Please check again!");
    }

    $('#items_list_error_message').text("");
}


// delete item row //
function delete_item(item){
    if(confirm("Delete this item?")){
        // delete from item index //
        item_index.splice(item_index.indexOf(item), 1);
        console.log(item_index);

        // reload items list //
        loadItem();
    }
}



// load items into list //
function loadItem(){
    // clear row //
    $("#items").find('tbody').html('');

    // get current item index //
    item_index.forEach(index_loop);

    // foreach item index //
    function index_loop(item, index){
        console.log(item + " -- " + index);
        // insert item into table row //
        var cur_item_code = data_list[item].item_code;
        var cur_item_name = data_list[item].item_name;
        var cur_lot_no = data_list[item].lot_no;
        var cur_qty = data_list[item].qty;
        var cur_location = data_list[item].location;

        console.log(data_list[item]);
        console.log(data_list[item].item_code);

        $("#items").find('tbody')
        .append($('<tr>')
            .append($('<td>')
                .append(item)
            )
            .append($('<td>')
                .append(cur_item_code)
            )
            .append($('<td>')
                .append(cur_item_name)
            )
            .append($('<td>')
                .append(cur_lot_no)
            )
            .append($('<td>')
                .append(cur_qty)
            )
            .append($('<td>')
                .append(cur_location)
            )
            .append($('<td>')
                .append("<button onclick='delete_item(" + item + ")'>Delete</button>")
            )
        );
    }


}


// - autocomplete item code - //
function autocompleteItemCode(){
    var ajax = $.ajax({
        url: "./api/item_list.php",
        method: "GET",
        data: {
            search_text: $('#edit_item_code').val(), 
        }
    });

    ajax.done(function(data){
        console.log(data);
        var response = JSON.parse(data);
        console.log(response);
        if(response['status'] == 'ok'){
            // put item list into datalist //
            $('#items_list').html(response['data']);
        }else{
            // clear datalist //
            $('#items_list').html("");
        }
    });

    ajax.fail(function(err){
        alert(err);
        console.log(err);
    });
}


// - choose item code - //
function chooseItem(){
    var jqxhr = $.ajax({
        url: "./api/item_name.php",
        method: "GET",
        data: {
            item_code : $('#edit_item_code').val()
        }
    });
    jqxhr.done(function(msg) {
        console.log(msg);
        // try to parse json response //
        try{
            var response = JSON.parse(msg);
            console.log(response);
            // if item code valid //
            if(response['status'] == 'ok'){
                // display item name //
                var item_name = response['item_name'];
                console.log(item_name);
                // item code valid //
                item_code_valid = true;
                $('#warning_invalid_item_message').html("");
                
                $('#edit_item_name').val(item_name);
                
            }else{
                // invalid item code - display error  //
                $('#warning_invalid_item_message').html("Item code <b>" + $('#edit_item_code').val() + "</b> not found!");
                $('#warning_invalid_item').show();

                // item code not valid //
                item_code_valid = false;
            }
            
        }catch(err){
            alert(err);
            alert(msg);
            display_error(err + "<br/>" + msg)
        }

    });
    
    // on server request error //
    jqxhr.fail(function(err) {
        alert("Error!");
        console.log(err)
        
        $('#loading').hide();

    });
}


// - autocomplete location - //
function autocompleteLocation(){
    var ajax = $.ajax({
        url: "./api/location_list.php",
        method: "GET",
        data: {
            search_text: $('#edit_location').val(), 
        }
    });

    ajax.done(function(data){
        console.log(data);
        var response = JSON.parse(data);
        console.log(response);
        if(response['status'] == 'ok'){
            // put item list into datalist //
            $('#location_list').html(response['data']);
        }else{
            // clear datalist //
            $('#location_list').html("");

        }
    });

    ajax.fail(function(err){
        alert(err);
        console.log(err);
    });
}


// - choose location - //
function chooseLocation(){
    var jqxhr = $.ajax({
        url: "./api/location_name.php",
        method: "GET",
        data: {
            location : $('#edit_location').val()
        }
    });
    jqxhr.done(function(msg) {
        console.log(msg);
        // try to parse json response //
        try{
            var response = JSON.parse(msg);
            console.log(response);
            // if location code valid //
            if(response['status'] == 'ok'){
                // display location name //
                var location_name = response['location_name'];
                console.log(location_name);
                // item code valid //
                location_valid = true;
                $('#warning_invalid_location_message').html("");
                
                $('#edit_location_name').val(location_name);
                
            }else{
                // invalid location code - display error  //
                $('#edit_location_name').val("");
                $('#warning_invalid_location_message').html("Location <b>" + $('#edit_location').val() + "</b> not found!");

                // location not valid //
                location_valid = false;
            }
            
        }catch(err){
            alert(err);
            alert(msg);
            display_error(err + "<br/>" + msg)
        }

    });
    
    // on server request error //
    jqxhr.fail(function(err) {
        alert("Error!");
        console.log(err)
        
        $('#loading').hide();

    });
}


// save inbound data //
function saveInbound(){
    // $('#btn_save_inbound').prop("disabled", true);
    $("#btn_save_inbound").text("Saving");

    // prevent submit empty items //
    var items_count = item_index.length;
    if(items_count > 0){

        $('#items_list_error_message').text("");

        // get current inbound data //
        var inbound_number = $('#edit_inbound_number').val();
        var posting_date = $('#edit_posting_date').val();
        var po_number = $('#edit_po_number').val();
        var doc_number = $('#edit_doc_number').val();

        // get items list //
        var items_list = [];

        // get current item index //
        item_index.forEach(index_loop);

        // foreach item index //
        function index_loop(item, index){
            console.log(item + " -- " + index);
            var cur_item_code = data_list[item].item_code;
            var cur_item_name = data_list[item].item_name;
            var cur_lot_no = data_list[item].lot_no;
            var cur_qty = data_list[item].qty;
            var cur_location = data_list[item].location;
            // create item data //
            var data = {};
            data.item_code = cur_item_code;
            data.item_name = cur_item_name;
            data.lot_no = cur_lot_no;
            data.qty = cur_qty;
            data.location = cur_location;

            console.log(data);

            // insert data into items list //
            items_list.push(data);
        }

        console.log(items_list);

        // - send entry items into server - //
        var jqxhr = $.ajax({
            url: "./api/inbound.php",
            method: "POST",
            data: {
                inbound_number: inbound_number,
                posting_date: posting_date,
                po_number: po_number,
                doc_number: doc_number,
                transaction: "purchase",
                items_list: JSON.stringify(items_list)
            }
        });

        jqxhr.done(function(msg) {

            console.log(msg);

            $('#text1').text(msg);

            $('.loading').hide();

            // default modal title //
            var modal_title = "Message";

            try{
                var response = JSON.parse(msg);
                // if response ok //
                if(response['status'] == 'ok'){
                    // save successful //

                    // close modal window //
                    $('#modal_warning').modal('hide');
                    // hide loading bar //
                    $('#loading').hide();
                    // display toast saved message //
                    toast("Saved");

                    // clear entry pallet form //
                    $('#input_pallet').prop("disabled", false);
                    $('#input_pallet').val("");
                    $('#input_pallet').focus();
                    $('#input_rack').prop("disabled", true);
                    $('#input_rack').val("");
                    $('#input_sloc').prop("disabled", true);
                    $('#input_sloc').val("");
                    // $("#items_list").html("");
                    $('#input_counter').val("");
                    $('#input_notes').val("");
                    $('#btn_save').prop("disabled", true);
                    $("#btn_save").text("Save");

                    // update tabulator row data //
                    table.setData([]);
                    table.redraw(true);

                // -- display error message -- //
                }else if(response['status'] == 'error'){
                    var modal_title = "Error!";
                    // alert(response['message']);
                    // open_modal_error(response['message']);
                    // $("#btn_modal_ok").text("Check Entry");

                    // set ok button action to check entry //
                    // ok_button_act = "check_entry";

                    $('#loading').hide();
                    $('#btn_save').prop("disabled", false);
                    $("#btn_save").text("Save");

                    // display warning bootstrap modal window //
                    $('#modal_error .modal-title').html(modal_title);
                    $('#modal_error .modal-body').html(response['message']);
                    $('#modal_error').modal();
                    $('#bs_btn_close').focus();

                // -- display warning message -- //
                }else if(response['status'] == 'warning'){
                    var modal_title = "Warning!";
                    // alert(response['message']);
                    // open_modal_warning(response['message']);
                    // $("#btn_modal_ok").text("Save Anyway");

                    // set ok button action to save anyway //
                    // ok_button_act = "save_anyway";

                    $('#loading').hide();
                    $('#btn_save').prop("disabled", false);
                    $("#btn_save").text("Save");

                    // display warning bootstrap modal window //
                    $('#modal_warning .modal-title').html(modal_title);
                    $('#modal_warning .modal-body').html(response['message']);
                    $('#modal_warning #bs_btn_save').text("Continue");
                    $('#modal_warning').modal();
                    $('#bs_btn_close').focus();
                }
                
            // on JSON response parse error //
            }catch(err){
                $('.loading').hide();

                console.error(err);
                alert(err);
                console.error(msg);
                display_error(err + "<br/>" + msg)
            }

            // // display bootstrap modal window //
            // $('#myModal .modal-title').html(modal_title);
            // $('#myModal .modal-body').html(response['message']);
            // $('#myModal').modal();
            // $('#bs_btn_close').focus();
            
        });
        
        // on server request error //
        jqxhr.fail(function(err) {
            alert(err);
            $('#btn_save').prop("disabled", false);
            $("#btn_save").text("Save");
            $('#loading').hide();
        });    

    }else{
        $('#items_list_error_message').text("Items list cannot be empty!");
    }
}

// sandbox function //
function sb(){
    alert("sandbox function");
}