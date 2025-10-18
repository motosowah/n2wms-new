-- View: public._delivery_balance_view

-- DROP VIEW public._delivery_balance_view;

CREATE OR REPLACE VIEW public._delivery_balance_view
 AS
 WITH sap AS (
         SELECT dssls100t.delivery AS do_no,
            dssls100t.material AS item_code,
            dssls100t.batch AS lot_no,
            COALESCE(sum(dssls100t.actual_delivery_qty), 0::double precision) AS delivery_qty
           FROM dssls100t
          GROUP BY dssls100t.delivery, dssls100t.material, dssls100t.batch
        ), on_process AS (
         SELECT mobt400t_temp.doc_number AS do_no,
            mobt400t_temp.item_code,
            mobt400t_temp.lot_no,
            COALESCE(sum(mobt400t_temp.inv_qty), 0::double precision) AS on_process_picking_qty
           FROM mobt400t_temp
          GROUP BY mobt400t_temp.doc_number, mobt400t_temp.item_code, mobt400t_temp.lot_no
        ), finished AS (
         SELECT COALESCE(mobt400t.doc_number, ''::character varying) AS do_no,
            mobt400t.item_code,
            mobt400t.lot_no,
            COALESCE(sum(mobt400t.inv_qty), 0::numeric) AS finished_picking_qty
           FROM mobt400t
          GROUP BY mobt400t.doc_number, mobt400t.item_code, mobt400t.lot_no
        )
 SELECT ( SELECT dssls100t.shipping_point_receiving_pt
           FROM dssls100t
          WHERE dssls100t.delivery::text = s.do_no::text
         LIMIT 1) AS plant,
    ( SELECT dssls100t.deliv_date_from_to
           FROM dssls100t
          WHERE dssls100t.delivery::text = s.do_no::text
         LIMIT 1) AS do_date,
    s.do_no,
    ( SELECT dssls100t.name_of_the_ship_to_party
           FROM dssls100t
          WHERE dssls100t.delivery::text = s.do_no::text
         LIMIT 1) AS customer,
    s.item_code,
    ( SELECT cmitm001t.item_desc
           FROM cmitm001t
          WHERE cmitm001t.item_code::text = s.item_code::text
         LIMIT 1) AS item_desc,
    s.lot_no,
    s.delivery_qty,
    COALESCE(op.on_process_picking_qty, 0::double precision) AS on_process_picking_qty,
    COALESCE(f.finished_picking_qty, 0::numeric) AS finished_picking_qty,
    s.delivery_qty - COALESCE(op.on_process_picking_qty, 0::double precision) - COALESCE(f.finished_picking_qty, 0::numeric)::double precision AS outstanding_stock
   FROM sap s
     LEFT JOIN on_process op ON op.do_no::text = s.do_no::text AND op.item_code::text = s.item_code::text AND op.lot_no::text = s.lot_no::text
     LEFT JOIN finished f ON f.do_no::text = s.do_no::text AND f.item_code::text = s.item_code::text AND f.lot_no::text = s.lot_no::text;

ALTER TABLE public._delivery_balance_view
    OWNER TO n2wms;

-- View: public._delivery_balance_view2

-- DROP VIEW public._delivery_balance_view2;

CREATE OR REPLACE VIEW public._delivery_balance_view2
 AS
 WITH sap AS (
         SELECT dssls100t.material AS item_code,
            dssls100t.batch AS lot_no,
            COALESCE(sum(dssls100t.actual_delivery_qty), 0::double precision) AS delivery_qty
           FROM dssls100t
          GROUP BY dssls100t.material, dssls100t.batch
        ), on_process AS (
         SELECT mobt400t_temp.item_code,
            mobt400t_temp.lot_no,
            COALESCE(sum(mobt400t_temp.inv_qty), 0::double precision) AS on_process_picking_qty
           FROM mobt400t_temp
          GROUP BY mobt400t_temp.item_code, mobt400t_temp.lot_no
        ), finished AS (
         SELECT mobt400t.item_code,
            mobt400t.lot_no,
            COALESCE(sum(mobt400t.inv_qty), 0::numeric) AS finished_picking_qty
           FROM mobt400t
          GROUP BY mobt400t.item_code, mobt400t.lot_no
        )
 SELECT s.item_code,
    ( SELECT cmitm001t.item_desc
           FROM cmitm001t
          WHERE cmitm001t.item_code::text = s.item_code::text
         LIMIT 1) AS item_desc,
    s.lot_no,
    s.delivery_qty,
    COALESCE(op.on_process_picking_qty, 0::double precision) AS on_process_picking_qty,
    COALESCE(f.finished_picking_qty, 0::numeric) AS finished_picking_qty,
    s.delivery_qty - COALESCE(op.on_process_picking_qty, 0::double precision) - COALESCE(f.finished_picking_qty, 0::numeric)::double precision AS outstanding_stock
   FROM sap s
     LEFT JOIN on_process op ON op.item_code::text = s.item_code::text AND op.lot_no::text = s.lot_no::text
     LEFT JOIN finished f ON f.item_code::text = s.item_code::text AND f.lot_no::text = s.lot_no::text;

ALTER TABLE public._delivery_balance_view2
    OWNER TO n2wms;


-- View: public._delivery_finished_view

-- DROP VIEW public._delivery_finished_view;

CREATE OR REPLACE VIEW public._delivery_finished_view
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

ALTER TABLE public._delivery_finished_view
    OWNER TO n2wms;


-- View: public._delivery_generate_balance_view

-- DROP VIEW public._delivery_generate_balance_view;

CREATE OR REPLACE VIEW public._delivery_generate_balance_view
 AS
 WITH sap AS (
         SELECT dssls100t.shipping_point_receiving_pt AS plant,
            dssls100t.deliv_date_from_to AS do_date,
            dssls100t.delivery AS do_no,
            COALESCE(sum(dssls100t.actual_delivery_qty), 0::double precision) AS delivery_qty
           FROM dssls100t
          GROUP BY dssls100t.shipping_point_receiving_pt, dssls100t.deliv_date_from_to, dssls100t.delivery
        ), on_process AS (
         SELECT mobt400t_temp.doc_number::text AS do_no,
            COALESCE(sum(mobt400t_temp.inv_qty), 0::double precision) AS on_process_picking_qty
           FROM mobt400t_temp
          GROUP BY mobt400t_temp.doc_number
        ), finished AS (
         SELECT mobt400t.doc_number::text AS do_no,
            COALESCE(sum(mobt400t.inv_qty), 0::numeric) AS finished_picking_qty
           FROM mobt400t
          GROUP BY mobt400t.doc_number
        )
 SELECT s.plant,
    s.do_date,
    s.do_no,
    ( SELECT dssls100t.name_of_the_ship_to_party
           FROM dssls100t
          WHERE dssls100t.delivery::text = s.do_no::text
         LIMIT 1) AS customer,
    s.delivery_qty,
    COALESCE(op.on_process_picking_qty, 0::double precision) AS on_process_picking_qty,
    COALESCE(f.finished_picking_qty, 0::numeric) AS finished_picking_qty,
    s.delivery_qty - COALESCE(op.on_process_picking_qty, 0::double precision) - COALESCE(f.finished_picking_qty, 0::numeric)::double precision AS outstanding_stock
   FROM sap s
     LEFT JOIN on_process op ON op.do_no = s.do_no::text
     LEFT JOIN finished f ON f.do_no = s.do_no::text;

ALTER TABLE public._delivery_generate_balance_view
    OWNER TO n2wms;


-- View: public._delivery_on_process_view

-- DROP VIEW public._delivery_on_process_view;

CREATE OR REPLACE VIEW public._delivery_on_process_view
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

ALTER TABLE public._delivery_on_process_view
    OWNER TO n2wms;


-- View: public._pallet_master_view

-- DROP VIEW public._pallet_master_view;

CREATE OR REPLACE VIEW public._pallet_master_view
 AS
 SELECT pallet_code,
    'ID1'::text || SUBSTRING(pallet_code FROM 1 FOR 1) AS plant
   FROM cmitm002t;

ALTER TABLE public._pallet_master_view
    OWNER TO n2wms;


-- View: public._rack_master_view

-- DROP VIEW public._rack_master_view;

CREATE OR REPLACE VIEW public._rack_master_view
 AS
 SELECT str_location AS rack_code
   FROM cmitm003t;

ALTER TABLE public._rack_master_view
    OWNER TO n2wms;


-- View: public._stock_balance_view

-- DROP VIEW public._stock_balance_view;

CREATE OR REPLACE VIEW public._stock_balance_view
 AS
 SELECT stock_table.whs_code AS plant,
    stock_table.item_code,
    item_master.item_desc,
    stock_table.lot_no,
    stock_table.pallet_code,
    stock_table.str_location,
    stock_table.inv_on_hand,
    stock_table.inv_allocated,
    COALESCE(stock_table.inv_on_hand, 0::numeric) - COALESCE(stock_table.inv_allocated, 0::numeric) AS available_stock
   FROM dsinv002t stock_table
     LEFT JOIN cmitm001t item_master ON stock_table.item_code::text = item_master.item_code::text
  WHERE stock_table.inv_on_hand > 0::numeric;

ALTER TABLE public._stock_balance_view
    OWNER TO n2wms;


-- View: public._stock_detail_view

-- DROP VIEW public._stock_detail_view;

CREATE OR REPLACE VIEW public._stock_detail_view
 AS
 SELECT stock_table.id_h,
    stock_table.item_code,
    stock_table.lot_no,
    stock_table.pallet_code,
    stock_table.str_location,
    stock_table.inv_on_hand,
    stock_table.inv_on_hold,
    stock_table.inv_on_order,
    stock_table.inv_allocated,
    COALESCE(stock_table.inv_on_hand, 0::numeric) - COALESCE(stock_table.inv_allocated, 0::numeric) AS available_stock,
    stock_table.capacity_unit,
    stock_table.inv_unit,
    stock_table.mfg_date,
    stock_table.move_in_date,
    stock_table.shelf_life_day,
    stock_table.expire_date,
    stock_table.inv_status,
    stock_table.inv_locked,
    stock_table.notes,
    stock_table.created_date,
    stock_table.created_by,
    stock_table.last_updated,
    stock_table.last_updated_by,
    item_master.item_desc,
    stock_table.whs_code
   FROM dsinv002t stock_table
     LEFT JOIN cmitm001t item_master ON stock_table.item_code::text = item_master.item_code::text
  WHERE stock_table.inv_on_hand > 0::numeric;

ALTER TABLE public._stock_detail_view
    OWNER TO n2wms;

