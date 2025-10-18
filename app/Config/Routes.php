<?php

use CodeIgniter\Router\RouteCollection;

/**
 * @var RouteCollection $routes
 */
$routes->get('/', 'Dashboard::index');
$routes->get('/dashboard', 'Dashboard::index');

// item master //
$routes->get('/item', 'ItemController::index');

$routes->get('/item/get_item_desc/(:segment)', 'ItemController::get_item_desc/$1');   // get item description from item code //


// master data //
$routes->get('/master', 'MasterDataController::index');
$routes->get('/master/search_pallet', 'MasterDataController::search_pallet');   // search pallet //
$routes->get('/master/search_rack', 'MasterDataController::search_rack');   // search rack //
$routes->get('/master/search_item', 'MasterDataController::search_item');   // search item //
$routes->get('/master/get_item_desc', 'MasterDataController::get_item_desc');   // get item description from item code //
$routes->get('/master/check_pallet_code', 'MasterDataController::check_pallet_code');
$routes->get('/master/check_rack_code', 'MasterDataController::check_rack_code');

// delivery //
$routes->get('/delivery', 'DeliveryController::index');
$routes->get('/delivery/get_sap_data', 'DeliveryController::get_sap_data');
$routes->get('/delivery/get_generate', 'DeliveryController::get_generate');
$routes->get('/delivery/get_on_process', 'DeliveryController::get_on_process');
$routes->get('/delivery/delete_on_process/(:segment)', 'DeliveryController::delete_on_process/$1');
$routes->get('/delivery/process_picking/(:segment)', 'DeliveryController::process_picking/$1');
$routes->get('/delivery/open_picking/(:segment)', 'DeliveryController::open_picking/$1');
$routes->get('/delivery/save_picking', 'DeliveryController::save_picking');
$routes->get('/delivery/get_finished', 'DeliveryController::get_finished');
$routes->get('/delivery/get_balance', 'DeliveryController::get_balance');
$routes->get('/delivery/generate', 'DeliveryController::generate');
$routes->get('/delivery/generate/(:segment)', 'DeliveryController::generate/$1');
$routes->get('/delivery/test', 'DeliveryController::test');
// $routes->get('item/get/(:segment)', 'Item::get/$1');
$routes->post('/delivery/create_delivery', 'DeliveryController::create_delivery');
$routes->get('/delivery/delete_sap/(:segment)', 'DeliveryController::delete_sap/$1');   // delete delivery sap data 


// - transaction API - //
$routes->get('/palletizing', 'PalletizingController::index');
$routes->get('/palletizing/save', 'PalletizingController::save');   // save palletizing //
$routes->get('/palletizing/save_mobile', 'PalletizingController::save_mobile'); // save palletizing from mobile device //

$routes->get('/staging', 'StagingController::index');
$routes->get('/staging/get_item_pallet', 'StagingController::get_item_pallet');
$routes->get('/staging/check_pallet_code', 'StagingController::check_pallet_code');
$routes->get('/staging/check_rack_code', 'StagingController::check_rack_code');
$routes->get('/staging/save', 'StagingController::save');

$routes->get('/splitting', 'SplittingController::index');
$routes->get('/splitting/get_item_pallet', 'SplittingController::get_item_pallet');
$routes->get('/splitting/check_pallet_code', 'SplittingController::check_pallet_code');
$routes->get('/splitting/check_rack_code', 'SplittingController::check_rack_code');
$routes->get('/splitting/save', 'SplittingController::save');

$routes->get('/stock-detail', 'StockController::index');
$routes->get('/stock/get_stock_detail', 'StockController::get_stock_detail');
// $routes->get('/stock-detail/search', 'Stock::search');


// - mobile interface - //
$routes->get('/mobile', 'MobileController::index');
$routes->get('/mobile/barcode', 'MobileController::barcode');
// palletizing page //
$routes->get('/mobile/palletizing', 'MobileController::palletizing');
// staging page //
$routes->get('/mobile/staging', 'MobileController::staging');

$routes->get('/mobile/add_item', 'MobileController::add_item');
$routes->get('/mobile/add_temp_pltz', 'MobileController::add_temp_pltz');   // insert temporary palletizing item //
$routes->get('/mobile/get_temp_pltz', 'MobileController::get_temp_pltz');   // get list of temporary palletizing item //
$routes->get('/mobile/edit_temp_pltz', 'MobileController::edit_temp_pltz');   // open temporary palletizing item for editing //
$routes->get('/mobile/save_temp_pltz', 'MobileController::save_temp_pltz');   // save modified temporary palletizing item //
$routes->get('/mobile/clear_temp_pltz', 'MobileController::clear_temp_pltz');   // clear temporary palletizing item //
$routes->get('/mobile/delete_temp_pltz', 'MobileController::delete_temp_pltz');   // delete temporary palletizing item //
$routes->get('/mobile/search_pallet', 'MobileController::search_pallet');   // search pallet code for autocomplete //
$routes->get('/mobile/search_rack', 'MobileController::search_rack');   // search rack code for autocomplete //
$routes->get('/mobile/validate_pallet/(:segment)', 'MobileController::validate_pallet/$1');   // validate pallet code //
$routes->get('/mobile/validate_rack/(:segment)', 'MobileController::validate_rack/$1');   // validate rack code //
$routes->get('/mobile/search_item', 'MobileController::search_item');   // search item for autocomplete //


// auth system //
$routes->get('login', 'Auth::login');
$routes->post('login', 'Auth::doLogin');
$routes->get('logout', 'Auth::logout');

// database api //
$routes->get('/api', 'ApiController::index');
$routes->get('/api/stock-detail', 'ApiController::stock_detail');

// sandbox page //
$routes->get('/sb', 'SbController::index');
$routes->get('/sb/text_editor', 'SbController::text_editor');
$routes->post('/sb/save_text', 'SbController::save_text');
$routes->get('/sb/barcode_test', 'SbController::barcode_test');
$routes->get('/sb/barcode_test_ajax', 'SbController::barcode_test_ajax');