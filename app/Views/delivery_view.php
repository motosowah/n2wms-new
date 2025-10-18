<?= $this->extend('layouts/n2wms_layout') ?>

<?= $this->section('title') ?>
Delivery - N2WMS New | Warehouse Management System
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

.nav-tabs .nav-link:hover {
    /* color: #0a58ca; */
}

.orange{
    color: orange;
}

th, td, tr{
    vertical-align: top !important;
}

/* invalid entry message */
.invalid-feedback{
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

<div class="card card-outline card-primary">
  <div class="card-body">
    
    <!-- TAB NAVIGATION -->
    <ul class="nav nav-tabs" id="myTab" role="tablist">
        <li class="nav-item" role="presentation">
            <button class="nav-link active" id="tab0-tab" data-bs-toggle="tab" data-bs-target="#tab0" type="button" role="tab" aria-controls="sap" aria-selected="true">DATA SAP</button>
        </li>
        <li class="nav-item" role="presentation">
            <button class="nav-link" id="tab1-tab" data-bs-toggle="tab" data-bs-target="#tab1" type="button" role="tab" aria-controls="tab1" aria-selected="false">WAITING LIST DO</button>
        </li>
        <li class="nav-item" role="presentation">
            <button class="nav-link" id="tab2-tab" data-bs-toggle="tab" data-bs-target="#tab2" type="button" role="tab" aria-controls="tab2" aria-selected="false">ON PROCESS PICKING</button>
        </li>
        <li class="nav-item" role="presentation">
            <button class="nav-link" id="tab3-tab" data-bs-toggle="tab" data-bs-target="#tab3" type="button" role="tab" aria-controls="tab3" aria-selected="false">FINISHED PICKING</button>
        </li>
        <li class="nav-item" role="presentation">
            <button class="nav-link" id="tab4-tab" data-bs-toggle="tab" data-bs-target="#tab4" type="button" role="tab" aria-controls="tab4" aria-selected="false">BALANCE DO</button>
        </li>
        <li class="nav-item" role="presentation">
            <button class="nav-link" id="tab5-tab" data-bs-toggle="tab" data-bs-target="#tab5" type="button" role="tab" aria-controls="tab5" aria-selected="false"><i class="glyphicon glyphicon-download"></i>Create New DO</button>
        </li>
        <li class="nav-item ms-auto" role="presentation">
            <button class="nav-link orange" id="tab6-tab" data-bs-toggle="tab" data-bs-target="#tab6" type="button" role="tab" aria-controls="tab6" aria-selected="false"><strong>Import Data SAP</strong></button>
        </li>
    </ul>

    <div class="tab-content" id="myTabContent">
        <div class="tab-pane fade show active" id="tab0" role="tabpanel" aria-labelledby="tab0-tab">
            <!-- DELIVERY SAP DATA -->
            <hr/>
            <div class="row">
                <div class="col-2">
                    <label for="date_from" class="form-label">Date From</label>
                    <input type="date" class="datepicker form-control" id="date_from_sap_data" placeholder="Date From" value="<?= $default_date_from ?>">
                    <div class="invalid-feedback" id="invalid_date_from_sap_data">Please Enter Date From</div>
                </div>
                <div class="col-2">
                    <label for="date_to" class="form-label">Date To</label>
                    <input type="date" class="form-control" id="date_to_sap_data" placeholder="Date To" value="<?= $default_date_to ?>">
                    <div class="invalid-feedback" id="invalid_date_to_sap_data">Please Enter Date To</div>
                </div>
                <div class="col-2">
                    <label for="do_no_sap_data" class="form-label">Delivery Number</label>
                    <input type="text" class="form-control" id="do_no_sap_data" placeholder="Delivery Number">
                </div>
                <div class="col-2">
                    <label for="plant" class="form-label">Plant</label>
                    <input type="text" class="form-control plant" id="plant_sap_data" placeholder="Plant">
                    <div class="invalid-feedback" id="invalid_plant_sap_data">Please Enter Plant</div>
                </div>
                <div class="col-3 d-flex align-items-center">
                    <button id="btn_search_sap_data" class="btn btn-primary">Search</button>
                    <span class="spinner-border text-primary container" role="status" id="loading_sap_data">
                        <span class="sr-only">Loading...</span>
                    </span>
                </div>
                <div class="col-1">
                    
                </div>
            </div>
            
            <hr/>
            <div id="table-container" style="position: relative;">
                <div id="table_sap_data_overlay" class="table-overlay">
                    Loading...
                </div>
                <table id="table_sap_data" class="table table-striped align-top small" width="100%">
                    <thead>
                    <tr>
                        <th>ID</th>
                        <th>Plant</th>
                        <th>DO Number</th>
                        <th>Delivery Date</th>
                        <th>Customer</th>
                        <th>Item Code</th>
                        <th style="min-width: 200px;">Item Description</th>
                        <th>Lot Number</th>
                        <th>Qty</th>
                        <th>Unit</th>
                        <th>Created By</th>
                        <th>Created Time</th>
                        <th>Actions</th>
                    </tr>
                    </thead>
                    <tbody></tbody>
                </table>
            </div>
            <hr/>
        </div>

        <div class="tab-pane fade" id="tab1" role="tabpanel" aria-labelledby="tab1-tab">
            <!-- WAITING LIST DO GENERATE -->
            <hr/>
            <table id="table_generate" class='table table-striped align-top small' width="100%">
                <thead>
                <tr>
                    <th>Plant</th>
                    <th>DO Number</th>
                    <th>Delivery Date</th>
                    <th style="min-width: 150px;">Customer</th>
                    <th>Qty DO</th>
                    <th>On Process</th>
                    <th>Finished</th>
                    <th>Balance</th>
                    <th>Status</th>
                    <th>Generate</th>
                </tr>
                </thead>
                <tbody></tbody>
            </table>
            <hr/>
        </div>

        <div class="tab-pane fade" id="tab2" role="tabpanel" aria-labelledby="tab2-tab">
            <!-- ON PROCESS PICKING -->
            <?php include 'delivery/edit_picking.php'; ?>

            <hr/>
            <!-- on process picking data -->
            <div id="table-container" style="position: relative;">
                <div id="table_on_process_overlay" class="table-overlay">
                    Loading...
                </div>
                <table id="table_on_process" class="table table-striped align-top small" width="100%">
                    <thead>
                    <tr>
                        <th>ID</th>
                        <th>Plant</th>
                        <th>DO Number</th>
                        <th>Delivery Date</th>
                        <th>Rack</th>
                        <th>Pallet</th>
                        <th>Material</th>
                        <th style="min-width: 200px;">Item Description</th>
                        <th>Batch</th> 
                        <th>Qty</th>
                        <th>Created By</th>
                        <th>Created Time</th>
                        <th style="min-width: 90px;">Actions</th>
                    </tr>
                    </thead>
                    <tbody></tbody>
                </table>
            </div>
            <hr/>
        </div>

        <div class="tab-pane fade" id="tab3" role="tabpanel" aria-labelledby="tab3-tab">
            <!-- FINISHED PICKING -->
            <hr/>
            <div class="row">
                <div class="col-2">
                    <label for="date_from_finished" class="form-label">Date From</label>
                    <input type="date" class="datepicker form-control" id="date_from_finished" placeholder="Date From" value="<?= $default_date_from ?>">
                    <div class="invalid-feedback" id="invalid_date_from_finished">Please Enter Date From</div>
                </div>
                <div class="col-2">
                    <label for="date_to_finished" class="form-label">Date To</label>
                    <input type="date" class="form-control" id="date_to_finished" placeholder="Date To" value="<?= $default_date_to ?>">
                    <div class="invalid-feedback" id="invalid_date_to_finished">Please Enter Date To</div>
                </div>
                <div class="col-2">
                    <label for="plant_finished" class="form-label">Plant</label>
                    <input type="text" class="form-control plant" id="plant_finished" placeholder="Plant">
                    <div class="invalid-feedback" id="invalid_plant_finished">Please Enter Plant</div>
                </div>
                <div class="col-3 d-flex align-items-center">
                    <button id="btn_search_finished" class="btn btn-primary">Search</button>
                    <span class="spinner-border text-primary container" role="status" id="loading_finished">
                        <span class="sr-only">Loading...</span>
                    </span>
                </div>
                <div class="col-1">
                    
                </div>
            </div>
            <hr/>
            <div id="table-container" style="position: relative;">
                <div id="table_finished_overlay" class="table-overlay">
                    Loading...
                </div>
                <table id="table_finished" class="table table-striped small" width="100%">
                    <thead>
                    <tr>
                        <th>ID</th>
                        <th>Plant</th>
                        <th>DO Number</th>
                        <th>Delivery Date</th>
                        <th>Customer</th>
                        <th>Item Code</th>
                        <th style="min-width: 200px;">Item Description</th>
                        <th>Lot Number</th>
                        <th>Rack</th>
                        <th>Pallet</th>
                        <th>Qty</th>
                        <th>Unit</th>
                        <th>Username</th>
                        <th>Picking Time</th>
                        <th>Actions</th>
                    </tr>
                    </thead>
                    <tbody></tbody>
                </table>
            </div>
            <hr/>
        </div>

        <div class="tab-pane fade" id="tab4" role="tabpanel" aria-labelledby="tab4-tab">
            <!-- BALANCE DO -->
            <hr/>
            <div class="row">
                <div class="col-2">
                    <label for="date_from" class="form-label">Date From</label>
                    <input type="date" class="datepicker form-control" id="date_from_balance" placeholder="Date From" value="<?= $default_date_from ?>">
                    <div class="invalid-feedback" id="invalid_date_from_balance">Please Enter Date From</div>
                </div>
                <div class="col-2">
                    <label for="date_to" class="form-label">Date To</label>
                    <input type="date" class="form-control" id="date_to_balance" placeholder="Date To"value="<?= $default_date_to ?>">
                    <div class="invalid-feedback" id="invalid_date_to_balance">Please Enter Date To</div>
                </div>
                <div class="col-2">
                    <label for="do_no_balance" class="form-label">Delivery Number</label>
                    <input type="text" class="form-control" id="do_no_balance" placeholder="Delivery Number">
                </div>
                <div class="col-2">
                    <label for="plant" class="form-label">Plant</label>
                    <input type="text" class="form-control plant" id="plant_balance" placeholder="Plant">
                    <div class="invalid-feedback" id="invalid_plant_balance">Please Enter Plant</div>
                </div>
                <div class="col-3 d-flex align-items-center">
                    <button id="btn_search_balance" class="btn btn-primary">Search</button>
                    <span class="spinner-border text-primary container" role="status" id="loading_balance">
                        <span class="sr-only">Loading...</span>
                    </span>
                </div>
                <div class="col-1">
                    
                </div>
            </div>
            <hr/>
            <div id="table-container" style="position: relative;">
                <div id="table_balance_overlay" class="table-overlay">
                    Loading...
                </div>
                <table id="table_balance" class="table table-striped align-top small" width="100%">
                    <thead>
                    <tr>
                        <!-- <th>ID</th> -->
                        <th>Plant</th>
                        <th>DO Number</th>
                        <th>Delivery Date</th>
                        <th>Customer</th>
                        <th>Item Code</th>
                        <th style="min-width: 200px;">Item Description</th>
                        <th>Lot Number</th>
                        <th>DO Qty</th>
                        <th style="text-align: left;">On Process</th>
                        <th>Finished</th>
                        <th>Balance</th>
                        <th>Photo</th>
                    </tr>
                    </thead>
                    <tbody></tbody>
                </table>
            </div>
            <hr/>
        </div>

        <div class="tab-pane fade" id="tab5" role="tabpanel" aria-labelledby="tab5-tab">
            <!-- CREATE NEW DO -->
            <div class="card card-primary card-outline mb-4">
                  <!--begin::Header-->
                  <div class="card-header"><div class="card-title">Create New DO</div></div>
                  <!--end::Header-->
                <!--begin::Body-->
                <div class="card-body">
                    <div class="row">
                        <div class="col-5">
                            <div class="mb-3">
                                <label for="new_do_plant" class="form-label">Plant</label>
                                <input type="text" class="form-control plant" id="new_do_plant" placeholder="Plant">
                                <div class="invalid-feedback" id="invalid_new_do_plant">Please Enter Plant</div>
                            </div>
                            <div class="mb-3">
                                <label for="new_do_date" class="form-label">DO Date</label>
                                <input type="date" class="form-control" id="new_do_date" placeholder="DO Date">
                                <div class="invalid-feedback" id="invalid_new_do_date">Please Enter DO Date</div>
                            </div>
                            <div class="mb-3">
                                <label for="new_do_no" class="form-label">DO Number</label>
                                <input type="text" class="form-control" id="new_do_no" placeholder="DO Number">
                                <div class="invalid-feedback" id="invalid_new_do_no">Please Enter DO Number</div>
                                </div>
                            <div class="mb-3">
                                <label for="new_customer" class="form-label">Customer Name</label>
                                <input type="text" class="form-control" id="new_customer" placeholder="Customer Name">
                                <div class="invalid-feedback" id="invalid_new_customer">Please Enter Customer Name</div>
                            </div>
                        </div>
                        <div class="col-7">
                            <div class="mb-3">
                                <label for="new_item_code" class="form-label">Item Code</label>
                                <input type="text" class="form-control" id="new_item_code" placeholder="Item Code">
                                <div class="invalid-feedback" id="invalid_new_item_code">Please Enter Item Code</div>
                            </div>
                            <div class="mb-3">
                                <label for="new_item_desc" class="form-label">Item Description</label>
                                <input type="text" class="form-control" id="new_item_desc" placeholder="Item Description" readonly>
                            </div>
                            <div class="mb-3">
                                <label for="new_lot_no" class="form-label">Lot Number</label>
                                <input type="text" class="form-control" id="new_lot_no" placeholder="Lot Number">
                                <div class="invalid-feedback" id="invalid_new_lot_no">Please Enter Lot Number</div>
                            </div>
                            <div class="mb-3">
                                <label for="new_qty" class="form-label">Qty</label>
                                <input type="number" class="form-control" id="new_qty" placeholder="Qty">
                                <div class="invalid-feedback" id="invalid_new_qty">Please Enter Qty</div>
                            </div>
                            <div class="col-3 d-flex align-items-center">
                                <button id="btn_save" class="btn btn-primary">SAVE</button>
                                <span class="spinner-border text-primary container" role="status" id="loading_new_do">
                                    <span class="sr-only">Loading...</span>
                                </span>
                            </div>
                            <hr/>
                            
                        </div>
                    </div>
                </div>
                <!--end::Body-->
                <!--begin::Footer-->
                <div class="card-footer">
                    
                </div>
                <!--end::Footer-->
            </div>
        </div>
        <div class="tab-pane fade" id="tab6" role="tabpanel" aria-labelledby="tab6-tab">
            Tab 6
        </div>
    </div>
    <hr/>
    
</div>


<?php
    include 'delivery/delivery_js.php';
?>



<?= $this->endSection() ?>
<!-- page content end -->