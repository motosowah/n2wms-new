-- DELIVERY ON PROCESS PICKING VIEW --
DROP VIEW public.delivery_on_process_view;
CREATE OR REPLACE VIEW public.delivery_on_process_view
 AS
 SELECT DISTINCT on_process.id_t,
    on_process.trans_type,
    sap_data.shipping_point_receiving_pt AS plant,
    on_process.doc_number,
    sap_data.name_of_the_ship_to_party AS customer,
    sap_data.deliv_date_from_to AS do_date,
    on_process.pallet_no,
    on_process.storage_location,
    on_process.item_code,
    on_process.lot_no,
    on_process.inv_qty AS quantity,
    on_process.created_by,
    on_process.created_date,
    item_master.item_desc,
    item_master.inv_unit,
    item_master.str_unit
   FROM mobt400t_temp on_process
     LEFT JOIN cmitm001t item_master ON on_process.item_code::text = item_master.item_code::text
     LEFT JOIN dssls100t sap_data ON on_process.doc_number::text = sap_data.delivery::text;


-- DELIVERY BALANCE VIEW --
DROP VIEW public.delivery_balance_view;
CREATE OR REPLACE VIEW public.delivery_balance_view
 AS
 SELECT sap_data.shipping_point_receiving_pt AS plant,
    sap_data.deliv_date_from_to AS do_date,
    sap_data.delivery AS do_no,
    sap_data.name_of_the_ship_to_party AS customer,
    sap_data.material AS item_code,
    sap_data.description AS item_desc,
    sap_data.batch AS lot_no,
    COALESCE(sap_data.actual_delivery_qty, 0::double precision) AS delivery_qty,
    COALESCE(sum(finished.inv_qty), 0::numeric) AS picking_qty,
    COALESCE(sap_data.actual_delivery_qty, 0::double precision) - COALESCE(sum(finished.inv_qty), 0::numeric)::double precision AS balance,
    COALESCE(finished.images, '- no image -'::character varying) AS images
   FROM dssls100t sap_data
     LEFT JOIN mobt400t finished ON finished.doc_number::text = sap_data.delivery::text AND finished.item_code::text = sap_data.material::text AND finished.lot_no::text = sap_data.batch::text
  GROUP BY sap_data.deliv_date_from_to, sap_data.delivery, sap_data.name_of_the_ship_to_party, sap_data.material, sap_data.description, sap_data.batch, sap_data.shipping_point_receiving_pt, sap_data.actual_delivery_qty, finished.images;


-- DELIVERY FINISHED PICKING VIEW --
DROP VIEW public.delivery_finished_view;
CREATE OR REPLACE VIEW public.delivery_finished_view
 AS
 SELECT finished.id_t,
    sap_data.shipping_point_receiving_pt AS plant,
    finished.doc_number,
    sap_data.deliv_date_from_to AS do_date,
    sap_data.name_of_the_ship_to_party AS customer,
    finished.item_code,
    finished.item_desc,
    finished.lot_no,
    finished.storage_location AS rack,
    finished.pallet_no AS pallet,
    finished.inv_qty AS qty,
    'KG'::text AS unit,
    finished.created_by,
    finished.created_date
   FROM mobt400t finished
     LEFT JOIN ( SELECT DISTINCT ON (dssls100t.delivery) dssls100t.delivery,
            dssls100t.shipping_point_receiving_pt,
            dssls100t.deliv_date_from_to,
            dssls100t.name_of_the_ship_to_party
           FROM dssls100t) sap_data ON sap_data.delivery::text = finished.doc_number::text;


-- DELIVERY GENERATE BALANCE VIEW --
DROP VIEW delivery_generate_balance_view;
CREATE OR REPLACE VIEW public.delivery_generate_balance_view
 AS
 SELECT plant,
    do_date,
    do_no,
    customer,
    delivery_qty,
	on_process_picking_qty,
    finished_picking_qty,
    delivery_qty - on_process_picking_qty - finished_picking_qty AS outstanding_stock
   FROM ( SELECT sap_data.shipping_point_receiving_pt AS plant,
            sap_data.deliv_date_from_to AS do_date,
            sap_data.delivery AS do_no,
            sap_data.name_of_the_ship_to_party AS customer,
            COALESCE(sum(sap_data.actual_delivery_qty), 0::numeric) AS delivery_qty,
			COALESCE(sum(on_process.inv_qty), 0::numeric) AS on_process_picking_qty,
            COALESCE(sum(finished.inv_qty), 0::numeric) AS finished_picking_qty
           FROM dssls100t sap_data
		   	 LEFT JOIN mobt400t_temp on_process ON on_process.doc_number::text = sap_data.delivery::text
             LEFT JOIN mobt400t finished ON finished.doc_number::text = sap_data.delivery::text
          GROUP BY sap_data.shipping_point_receiving_pt, sap_data.deliv_date_from_to, sap_data.delivery, sap_data.name_of_the_ship_to_party) t;
