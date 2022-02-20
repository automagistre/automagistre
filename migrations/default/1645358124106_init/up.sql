SET check_function_bodies = FALSE;
CREATE FUNCTION public.set_current_timestamp_updated_at() RETURNS trigger
    LANGUAGE plpgsql AS
$$
DECLARE
    _new record;
BEGIN
    _new := new;
    _new.updated_at = NOW();
    RETURN _new;
END;
$$;
CREATE FUNCTION public.timestampable(target_table regclass) RETURNS void
    LANGUAGE plpgsql AS
$$
DECLARE
    trigger_name text;
BEGIN
    trigger_name = 'set_' || REPLACE(target_table::text, '.', '_') || '_updated_at';
    EXECUTE 'ALTER TABLE ' || target_table || ' ADD COLUMN "created_at" timestamptz NOT NULL DEFAULT NOW()';
    EXECUTE 'ALTER TABLE ' || target_table || ' ADD COLUMN "updated_at" timestamptz NOT NULL DEFAULT NOW()';
    IF (SELECT EXISTS(SELECT 1
                        FROM information_schema.columns
                       WHERE table_schema = 'public'
                         AND table_name = target_table::text
                         AND column_name = 'id'
                         AND data_type = 'uuid')) THEN
        EXECUTE 'UPDATE ' || target_table ||
                ' t SET created_at = cb.created_at, updated_at = cb.created_at FROM public.created_by cb WHERE cb.id = t.id';
    END IF;
    EXECUTE 'CREATE TRIGGER "' || trigger_name || '" BEFORE UPDATE ON ' || target_table ||
            ' FOR EACH ROW EXECUTE PROCEDURE public.set_current_timestamp_updated_at()';
    EXECUTE 'COMMENT ON TRIGGER ' || trigger_name || ' ON ' || target_table ||
            ' IS ''trigger to set value of column "updated_at" to current timestamp on row update''';
END ;
$$;
CREATE TABLE public.appeal_calculator
    (
        id uuid NOT NULL,
        name character varying(255) NOT NULL,
        note character varying(255) DEFAULT NULL::character varying,
        phone character varying(35) NOT NULL,
        date date,
        equipment_id uuid NOT NULL,
        mileage integer NOT NULL,
        total bigint NOT NULL,
        works json NOT NULL,
        tenant_id uuid NOT NULL
    );
COMMENT ON COLUMN public.appeal_calculator.phone IS '(DC2Type:phone_number)';
COMMENT ON COLUMN public.appeal_calculator.date IS '(DC2Type:date_immutable)';
COMMENT ON COLUMN public.appeal_calculator.total IS '(DC2Type:money)';
COMMENT ON COLUMN public.appeal_calculator.works IS '(DC2Type:appeal_calculator_work)';
CREATE TABLE public.appeal_call
    (
        id uuid NOT NULL,
        phone character varying(35) NOT NULL,
        tenant_id uuid NOT NULL
    );
COMMENT ON COLUMN public.appeal_call.phone IS '(DC2Type:phone_number)';
CREATE TABLE public.appeal_cooperation
    (
        id uuid NOT NULL,
        name character varying(255) NOT NULL,
        phone character varying(35) NOT NULL,
        tenant_id uuid NOT NULL
    );
COMMENT ON COLUMN public.appeal_cooperation.phone IS '(DC2Type:phone_number)';
CREATE TABLE public.appeal_postpone
    (
        id uuid NOT NULL,
        appeal_id uuid NOT NULL,
        tenant_id uuid NOT NULL
    );
CREATE TABLE public.appeal_question
    (
        id uuid NOT NULL,
        name character varying(255) NOT NULL,
        email character varying(255) NOT NULL,
        question text NOT NULL,
        tenant_id uuid NOT NULL
    );
CREATE TABLE public.appeal_schedule
    (
        id uuid NOT NULL,
        name character varying(255) NOT NULL,
        phone character varying(35) NOT NULL,
        date date NOT NULL,
        tenant_id uuid NOT NULL
    );
COMMENT ON COLUMN public.appeal_schedule.phone IS '(DC2Type:phone_number)';
COMMENT ON COLUMN public.appeal_schedule.date IS '(DC2Type:date_immutable)';
CREATE TABLE public.appeal_status
    (
        id uuid NOT NULL,
        appeal_id uuid NOT NULL,
        status smallint NOT NULL,
        tenant_id uuid NOT NULL
    );
COMMENT ON COLUMN public.appeal_status.status IS '(DC2Type:appeal_status)';
CREATE TABLE public.appeal_tire_fitting
    (
        id uuid NOT NULL,
        name character varying(255) NOT NULL,
        phone character varying(35) NOT NULL,
        model_id uuid,
        category smallint NOT NULL,
        diameter integer,
        total bigint NOT NULL,
        works json NOT NULL,
        tenant_id uuid NOT NULL
    );
COMMENT ON COLUMN public.appeal_tire_fitting.phone IS '(DC2Type:phone_number)';
COMMENT ON COLUMN public.appeal_tire_fitting.category IS '(DC2Type:tire_fitting_category)';
COMMENT ON COLUMN public.appeal_tire_fitting.total IS '(DC2Type:money)';
COMMENT ON COLUMN public.appeal_tire_fitting.works IS '(DC2Type:appeal_tire_fitting_work)';
CREATE TABLE public.created_by
    (
        id uuid NOT NULL,
        user_id uuid NOT NULL,
        created_at timestamp(0) WITH TIME ZONE NOT NULL
    );
COMMENT ON COLUMN public.created_by.created_at IS '(DC2Type:datetimetz_immutable)';
CREATE VIEW public.appeal_view
AS
SELECT appeal.id, appeal.tenant_id, appeal.name, appeal.type, appeal.phone, appeal.email,
       COALESCE((status.status)::integer, 1) AS status, created_by.created_at
  FROM (((SELECT appeal_calculator.id, appeal_calculator.name, 1 AS type, appeal_calculator.phone, NULL::text AS email,
                 appeal_calculator.tenant_id
            FROM public.appeal_calculator
           UNION ALL
          SELECT appeal_cooperation.id, appeal_cooperation.name, 2 AS type, appeal_cooperation.phone,
                 NULL::text AS email, appeal_cooperation.tenant_id
            FROM public.appeal_cooperation
           UNION ALL
          SELECT appeal_question.id, appeal_question.name, 3 AS type, NULL::character varying AS phone,
                 appeal_question.email, appeal_question.tenant_id
            FROM public.appeal_question
           UNION ALL
          SELECT appeal_schedule.id, appeal_schedule.name, 4 AS type, appeal_schedule.phone, NULL::text AS email,
                 appeal_schedule.tenant_id
            FROM public.appeal_schedule
           UNION ALL
          SELECT appeal_tire_fitting.id, appeal_tire_fitting.name, 5 AS type, appeal_tire_fitting.phone,
                 NULL::text AS email, appeal_tire_fitting.tenant_id
            FROM public.appeal_tire_fitting
           UNION ALL
          SELECT appeal_call.id, ''::character varying AS varchar, 6 AS type, appeal_call.phone, NULL::text AS email,
                 appeal_call.tenant_id
            FROM public.appeal_call) appeal LEFT JOIN LATERAL ( SELECT sub.id, sub.appeal_id, sub.status, sub.tenant_id
                                                                  FROM public.appeal_status sub
                                                                 WHERE (sub.appeal_id = appeal.id)
                                                                 ORDER BY sub.id DESC
                                                                 LIMIT 1) status ON (TRUE))
           JOIN public.created_by
           ON ((created_by.id = appeal.id)));
CREATE TABLE public.calendar_entry
    (
        id uuid NOT NULL,
        tenant_id uuid NOT NULL
    );
CREATE TABLE public.calendar_entry_deletion
    (
        id uuid NOT NULL,
        entry_id uuid NOT NULL,
        reason smallint NOT NULL,
        description text,
        tenant_id uuid NOT NULL
    );
COMMENT ON COLUMN public.calendar_entry_deletion.reason IS '(DC2Type:deletion_reason)';
CREATE TABLE public.calendar_entry_order
    (
        id uuid NOT NULL,
        entry_id uuid NOT NULL,
        order_id uuid NOT NULL,
        tenant_id uuid NOT NULL
    );
CREATE TABLE public.calendar_entry_order_info
    (
        id uuid NOT NULL,
        entry_id uuid,
        tenant_id uuid NOT NULL,
        customer_id uuid,
        car_id uuid,
        description text,
        worker_id uuid
    );
CREATE TABLE public.calendar_entry_schedule
    (
        id uuid NOT NULL,
        entry_id uuid,
        tenant_id uuid NOT NULL,
        date timestamp(0) WITHOUT TIME ZONE NOT NULL,
        duration character varying(255) NOT NULL
    );
COMMENT ON COLUMN public.calendar_entry_schedule.date IS '(DC2Type:datetime_immutable)';
COMMENT ON COLUMN public.calendar_entry_schedule.duration IS '(DC2Type:dateinterval)';
CREATE VIEW public.calendar_entry_view
AS
SELECT e.id, e.tenant_id, ces.date AS schedule_date, ces.duration AS schedule_duration,
       ceoi.customer_id AS order_info_customer_id, ceoi.car_id AS order_info_car_id,
       ceoi.description AS order_info_description, ceoi.worker_id AS order_info_worker_id, ceo.order_id
  FROM ((((public.calendar_entry e LEFT JOIN public.calendar_entry_deletion ced ON ((e.id = ced.entry_id))) LEFT JOIN public.calendar_entry_order ceo ON ((ceo.entry_id = e.id))) JOIN LATERAL ( SELECT sub.id, sub.entry_id, sub.tenant_id, sub.date, sub.duration
                                                                                                                                                                                                   FROM public.calendar_entry_schedule sub
                                                                                                                                                                                                  WHERE (sub.entry_id = e.id)
                                                                                                                                                                                                  ORDER BY sub.id DESC
                                                                                                                                                                                                  LIMIT 1) ces ON (TRUE))
           JOIN LATERAL ( SELECT sub.id, sub.entry_id, sub.tenant_id, sub.customer_id, sub.car_id, sub.description,
                                 sub.worker_id
                            FROM public.calendar_entry_order_info sub
                           WHERE (sub.entry_id = e.id)
                           ORDER BY sub.id DESC
                           LIMIT 1) ceoi
           ON (TRUE))
 WHERE (ced.* IS NULL);
CREATE TABLE public.car
    (
        id uuid NOT NULL,
        vehicle_id uuid,
        identifier character varying(17) DEFAULT NULL::character varying,
        year integer,
        case_type smallint NOT NULL,
        description text,
        mileage integer NOT NULL,
        gosnomer character varying(255) DEFAULT NULL::character varying,
        tenant_group_id uuid NOT NULL,
        equipment_transmission smallint NOT NULL,
        equipment_wheel_drive smallint NOT NULL,
        equipment_engine_name character varying(255) DEFAULT NULL::character varying,
        equipment_engine_type smallint NOT NULL,
        equipment_engine_air_intake smallint NOT NULL,
        equipment_engine_injection smallint NOT NULL,
        equipment_engine_capacity character varying(255) NOT NULL
    );
COMMENT ON COLUMN public.car.case_type IS '(DC2Type:carcase_enum)';
COMMENT ON COLUMN public.car.equipment_transmission IS '(DC2Type:car_transmission_enum)';
COMMENT ON COLUMN public.car.equipment_wheel_drive IS '(DC2Type:car_wheel_drive_enum)';
COMMENT ON COLUMN public.car.equipment_engine_type IS '(DC2Type:engine_type_enum)';
COMMENT ON COLUMN public.car.equipment_engine_air_intake IS '(DC2Type:engine_air_intake)';
COMMENT ON COLUMN public.car.equipment_engine_injection IS '(DC2Type:engine_injection)';
CREATE TABLE public.car_recommendation
    (
        id uuid NOT NULL,
        car_id uuid,
        service character varying(255) NOT NULL,
        worker_id uuid NOT NULL,
        expired_at timestamp(0) WITHOUT TIME ZONE DEFAULT NULL::timestamp WITHOUT TIME ZONE,
        realization uuid,
        tenant_group_id uuid NOT NULL,
        price_amount bigint,
        price_currency_code character varying(3) DEFAULT NULL::character varying
    );
CREATE TABLE public.car_recommendation_part
    (
        id uuid NOT NULL,
        recommendation_id uuid NOT NULL,
        part_id uuid NOT NULL,
        quantity integer NOT NULL,
        tenant_group_id uuid NOT NULL,
        price_amount bigint,
        price_currency_code character varying(3) DEFAULT NULL::character varying
    );
CREATE TABLE public.cron_job
    (
        id integer NOT NULL,
        name character varying(191) NOT NULL,
        command character varying(1024) NOT NULL,
        schedule character varying(191) NOT NULL,
        description character varying(191) NOT NULL,
        enabled boolean NOT NULL
    );
CREATE SEQUENCE public.cron_job_id_seq START WITH 1 INCREMENT BY 1 NO MINVALUE NO MAXVALUE CACHE 1;
CREATE TABLE public.cron_report
    (
        id integer NOT NULL,
        job_id integer,
        run_at timestamp(0) WITHOUT TIME ZONE NOT NULL,
        run_time double precision NOT NULL,
        exit_code integer NOT NULL,
        output text NOT NULL,
        error text NOT NULL
    );
CREATE SEQUENCE public.cron_report_id_seq START WITH 1 INCREMENT BY 1 NO MINVALUE NO MAXVALUE CACHE 1;
CREATE TABLE public.customer_transaction
    (
        id uuid NOT NULL,
        operand_id uuid NOT NULL,
        source smallint NOT NULL,
        source_id uuid NOT NULL,
        description text,
        tenant_id uuid NOT NULL,
        amount_amount bigint,
        amount_currency_code character varying(3) DEFAULT NULL::character varying
    );
COMMENT ON COLUMN public.customer_transaction.source IS '(DC2Type:operand_transaction_source)';
CREATE TABLE public.wallet_transaction
    (
        id uuid NOT NULL,
        wallet_id uuid NOT NULL,
        source smallint NOT NULL,
        source_id uuid NOT NULL,
        description text,
        tenant_id uuid NOT NULL,
        amount_amount bigint,
        amount_currency_code character varying(3) DEFAULT NULL::character varying
    );
COMMENT ON COLUMN public.wallet_transaction.source IS '(DC2Type:wallet_transaction_source)';
CREATE VIEW public.customer_transaction_view
AS
SELECT ct.id, ct.tenant_id, ct.operand_id, ct.amount_amount AS amount, ct.source,
       CASE WHEN (ct.source = ANY (ARRAY [5, 10])) THEN wt.wallet_id ELSE ct.source_id END AS source_id, ct.description,
       cb.created_at, cb.user_id AS created_by
  FROM ((public.customer_transaction ct JOIN public.created_by cb ON ((cb.id = ct.id)))
           LEFT JOIN public.wallet_transaction wt
           ON ((wt.id = ct.source_id)));
CREATE TABLE public.organization
    (
        id uuid NOT NULL,
        name character varying(255) NOT NULL,
        address character varying(255) DEFAULT NULL::character varying,
        telephone character varying(35) DEFAULT NULL::character varying,
        office_phone character varying(35) DEFAULT NULL::character varying,
        email character varying(255) DEFAULT NULL::character varying,
        contractor boolean NOT NULL,
        seller boolean NOT NULL,
        tenant_group_id uuid NOT NULL,
        requisite_bank character varying(255) DEFAULT NULL::character varying,
        requisite_legal_address character varying(255) DEFAULT NULL::character varying,
        requisite_ogrn character varying(255) DEFAULT NULL::character varying,
        requisite_inn character varying(255) DEFAULT NULL::character varying,
        requisite_kpp character varying(255) DEFAULT NULL::character varying,
        requisite_rs character varying(255) DEFAULT NULL::character varying,
        requisite_ks character varying(255) DEFAULT NULL::character varying,
        requisite_bik character varying(255) DEFAULT NULL::character varying
    );
COMMENT ON COLUMN public.organization.telephone IS '(DC2Type:phone_number)';
COMMENT ON COLUMN public.organization.office_phone IS '(DC2Type:phone_number)';
CREATE TABLE public.person
    (
        id uuid NOT NULL,
        firstname character varying(32) DEFAULT NULL::character varying,
        lastname character varying(255) DEFAULT NULL::character varying,
        telephone character varying(35) DEFAULT NULL::character varying,
        office_phone character varying(35) DEFAULT NULL::character varying,
        email character varying(255) DEFAULT NULL::character varying,
        contractor boolean NOT NULL,
        seller boolean NOT NULL,
        tenant_group_id uuid NOT NULL
    );
COMMENT ON COLUMN public.person.telephone IS '(DC2Type:phone_number)';
COMMENT ON COLUMN public.person.office_phone IS '(DC2Type:phone_number)';
CREATE VIEW public.customer_view
AS
SELECT o.id, o.tenant_group_id, o.name AS full_name, COALESCE(balance.money, (0)::numeric) AS balance, o.email,
       o.telephone, o.office_phone, o.seller, o.contractor, o.address, 'organization'::text AS type
  FROM (public.organization o
           LEFT JOIN (SELECT ct.operand_id AS id, SUM(ct.amount_amount) AS money
                        FROM public.customer_transaction ct
                       GROUP BY ct.operand_id, ct.amount_currency_code) balance
           ON ((balance.id = o.id)))
 UNION ALL
SELECT p.id, p.tenant_group_id, CONCAT_WS(' '::text, p.lastname, p.firstname) AS full_name,
       COALESCE(balance.money, (0)::numeric) AS balance, p.email, p.telephone, p.office_phone, p.seller, p.contractor,
       NULL::character varying AS address, 'person'::text AS type
  FROM (public.person p
           LEFT JOIN (SELECT ct.operand_id AS id, SUM(ct.amount_amount) AS money
                        FROM public.customer_transaction ct
                       GROUP BY ct.operand_id, ct.amount_currency_code) balance
           ON ((balance.id = p.id)));
CREATE TABLE public.employee
    (
        id uuid NOT NULL,
        person_id uuid NOT NULL,
        ratio integer NOT NULL,
        hired_at timestamp(0) WITHOUT TIME ZONE NOT NULL,
        fired_at timestamp(0) WITHOUT TIME ZONE DEFAULT NULL::timestamp WITHOUT TIME ZONE,
        tenant_id uuid NOT NULL
    );
CREATE TABLE public.employee_salary
    (
        id uuid NOT NULL,
        employee_id uuid NOT NULL,
        payday integer NOT NULL,
        amount bigint NOT NULL,
        tenant_id uuid NOT NULL
    );
COMMENT ON COLUMN public.employee_salary.amount IS '(DC2Type:money)';
CREATE TABLE public.employee_salary_end
    (
        id uuid NOT NULL,
        salary_id uuid,
        tenant_id uuid NOT NULL
    );
CREATE TABLE public.expense
    (
        id uuid NOT NULL,
        name character varying(255) NOT NULL,
        wallet_id uuid,
        tenant_id uuid NOT NULL
    );
CREATE TABLE public.google_review_token
    (
        id uuid NOT NULL,
        payload json NOT NULL,
        tenant_id uuid NOT NULL
    );
CREATE TABLE public.income
    (
        id uuid NOT NULL,
        supplier_id uuid NOT NULL,
        document character varying(255) DEFAULT NULL::character varying,
        old_id integer,
        tenant_id uuid NOT NULL
    );
CREATE TABLE public.income_accrue
    (
        id uuid NOT NULL,
        income_id uuid,
        tenant_id uuid NOT NULL,
        amount_amount bigint,
        amount_currency_code character varying(3) DEFAULT NULL::character varying
    );
CREATE TABLE public.income_part
    (
        id uuid NOT NULL,
        income_id uuid,
        part_id uuid NOT NULL,
        quantity integer NOT NULL,
        tenant_id uuid NOT NULL,
        price_amount bigint,
        price_currency_code character varying(3) DEFAULT NULL::character varying
    );
CREATE TABLE public.inventorization
    (
        id uuid NOT NULL,
        tenant_id uuid NOT NULL
    );
CREATE TABLE public.inventorization_close
    (
        id uuid NOT NULL,
        inventorization_id uuid,
        tenant_id uuid NOT NULL
    );
CREATE TABLE public.inventorization_part
    (
        inventorization_id uuid NOT NULL,
        part_id uuid NOT NULL,
        quantity integer NOT NULL,
        tenant_id uuid NOT NULL
    );
CREATE TABLE public.motion
    (
        id uuid NOT NULL,
        part_id uuid,
        quantity integer NOT NULL,
        description text,
        tenant_id uuid NOT NULL,
        source_type smallint NOT NULL,
        source_id uuid NOT NULL
    );
COMMENT ON COLUMN public.motion.source_type IS '(DC2Type:motion_source_enum)';
CREATE TABLE public.order_item_part
    (
        id uuid NOT NULL,
        supplier_id uuid,
        part_id uuid NOT NULL,
        quantity integer NOT NULL,
        warranty boolean NOT NULL,
        price_amount bigint,
        price_currency_code character varying(3) DEFAULT NULL::character varying,
        discount_amount bigint,
        discount_currency_code character varying(3) DEFAULT NULL::character varying
    );
CREATE TABLE public.reservation
    (
        id uuid NOT NULL,
        order_item_part_id uuid NOT NULL,
        quantity integer NOT NULL,
        tenant_id uuid NOT NULL
    );
CREATE VIEW public.inventorization_part_view
AS
SELECT ip.inventorization_id, ip.tenant_id, ip.part_id, ip.quantity, COALESCE(stock.quantity, (0)::bigint) AS in_stock,
       COALESCE(reserved.quantity, (0)::bigint) AS reserved
  FROM ((public.inventorization_part ip LEFT JOIN (SELECT motion.part_id, SUM(motion.quantity) AS quantity
                                                     FROM public.motion
                                                    GROUP BY motion.part_id) stock ON ((stock.part_id = ip.part_id)))
           LEFT JOIN (SELECT order_item_part.part_id, SUM(reservation.quantity) AS quantity
                        FROM (public.reservation
                                 JOIN public.order_item_part
                                 ON ((order_item_part.id = reservation.order_item_part_id)))
                       GROUP BY order_item_part.part_id) reserved
           ON ((reserved.part_id = ip.part_id)));
CREATE VIEW public.inventorization_view
AS
SELECT i.id, i.tenant_id, cb.created_at, cbc.created_at AS closed_at
  FROM (((public.inventorization i JOIN public.created_by cb ON ((cb.id = i.id))) LEFT JOIN public.inventorization_close ic ON ((i.id = ic.inventorization_id)))
           LEFT JOIN public.created_by cbc
           ON ((cbc.id = ic.id)));
CREATE TABLE public.manufacturer
    (
        id uuid NOT NULL,
        name character varying(64) NOT NULL,
        localized_name character varying(255) DEFAULT NULL::character varying,
        logo character varying(25) DEFAULT NULL::character varying,
        created_at timestamp WITH TIME ZONE DEFAULT NOW() NOT NULL,
        updated_at timestamp WITH TIME ZONE DEFAULT NOW() NOT NULL
    );
CREATE TABLE public.mc_equipment
    (
        id uuid NOT NULL,
        vehicle_id uuid NOT NULL,
        period integer NOT NULL,
        tenant_id uuid NOT NULL,
        equipment_transmission smallint NOT NULL,
        equipment_wheel_drive smallint NOT NULL,
        equipment_engine_name character varying(255) DEFAULT NULL::character varying,
        equipment_engine_type smallint NOT NULL,
        equipment_engine_air_intake smallint NOT NULL,
        equipment_engine_injection smallint NOT NULL,
        equipment_engine_capacity character varying(255) NOT NULL
    );
COMMENT ON COLUMN public.mc_equipment.equipment_transmission IS '(DC2Type:car_transmission_enum)';
COMMENT ON COLUMN public.mc_equipment.equipment_wheel_drive IS '(DC2Type:car_wheel_drive_enum)';
COMMENT ON COLUMN public.mc_equipment.equipment_engine_type IS '(DC2Type:engine_type_enum)';
COMMENT ON COLUMN public.mc_equipment.equipment_engine_air_intake IS '(DC2Type:engine_air_intake)';
COMMENT ON COLUMN public.mc_equipment.equipment_engine_injection IS '(DC2Type:engine_injection)';
CREATE TABLE public.mc_line
    (
        id uuid NOT NULL,
        equipment_id uuid,
        work_id uuid,
        period integer NOT NULL,
        recommended boolean NOT NULL,
        position integer NOT NULL,
        tenant_id uuid NOT NULL
    );
CREATE TABLE public.mc_part
    (
        id uuid NOT NULL,
        line_id uuid,
        part_id uuid NOT NULL,
        quantity integer NOT NULL,
        recommended boolean NOT NULL,
        tenant_id uuid NOT NULL
    );
CREATE TABLE public.mc_work
    (
        id uuid NOT NULL,
        name character varying(255) NOT NULL,
        description character varying(255) DEFAULT NULL::character varying,
        comment character varying(255) DEFAULT NULL::character varying,
        tenant_id uuid NOT NULL,
        price_amount bigint,
        price_currency_code character varying(3) DEFAULT NULL::character varying
    );
CREATE TABLE public.migration_versions
    (
        version character varying(1024) NOT NULL,
        executed_at timestamp(0) WITHOUT TIME ZONE DEFAULT NULL::timestamp WITHOUT TIME ZONE,
        execution_time integer
    );
CREATE TABLE public.note
    (
        id uuid NOT NULL,
        subject uuid NOT NULL,
        type smallint NOT NULL,
        text text NOT NULL,
        tenant_id uuid NOT NULL
    );
COMMENT ON COLUMN public.note.type IS '(DC2Type:note_type_enum)';
CREATE TABLE public.note_delete
    (
        id uuid NOT NULL,
        note_id uuid,
        description character varying(255) NOT NULL,
        tenant_id uuid NOT NULL
    );
CREATE VIEW public.note_view
AS
SELECT note.id, note.tenant_id, note.subject, note.text, note.type, cb.created_at, cb.user_id AS created_by
  FROM ((public.note JOIN public.created_by cb ON ((cb.id = note.id)))
           LEFT JOIN public.note_delete
           ON ((note_delete.note_id = note.id)))
 WHERE (note_delete.id IS NULL);
CREATE TABLE public.order_cancel
    (
        id uuid NOT NULL
    );
CREATE TABLE public.order_close
    (
        id uuid NOT NULL,
        order_id uuid,
        tenant_id uuid NOT NULL,
        type character varying(255) NOT NULL
    );
CREATE TABLE public.order_deal
    (
        id uuid NOT NULL,
        balance bigint NOT NULL,
        satisfaction smallint NOT NULL
    );
COMMENT ON COLUMN public.order_deal.balance IS '(DC2Type:money)';
COMMENT ON COLUMN public.order_deal.satisfaction IS '(DC2Type:order_satisfaction_enum)';
CREATE TABLE public.order_item
    (
        id uuid NOT NULL,
        order_id uuid,
        parent_id uuid,
        tenant_id uuid NOT NULL,
        type character varying(255) NOT NULL
    );
CREATE TABLE public.order_item_group
    (
        id uuid NOT NULL,
        name character varying(255) NOT NULL,
        hide_parts boolean NOT NULL
    );
CREATE TABLE public.order_item_service
    (
        id uuid NOT NULL,
        service character varying(255) NOT NULL,
        worker_id uuid,
        warranty boolean NOT NULL,
        price_amount bigint,
        price_currency_code character varying(3) DEFAULT NULL::character varying,
        discount_amount bigint,
        discount_currency_code character varying(3) DEFAULT NULL::character varying
    );
CREATE SEQUENCE public.order_number_seq START WITH 1 INCREMENT BY 1 NO MINVALUE NO MAXVALUE CACHE 1;
CREATE TABLE public.order_payment
    (
        id uuid NOT NULL,
        order_id uuid,
        description character varying(255) DEFAULT NULL::character varying,
        tenant_id uuid NOT NULL,
        money_amount bigint,
        money_currency_code character varying(3) DEFAULT NULL::character varying
    );
CREATE TABLE public.order_suspend
    (
        id uuid NOT NULL,
        order_id uuid,
        till timestamp(0) WITHOUT TIME ZONE NOT NULL,
        reason character varying(255) NOT NULL,
        tenant_id uuid NOT NULL
    );
COMMENT ON COLUMN public.order_suspend.till IS '(DC2Type:datetime_immutable)';
CREATE TABLE public.orders
    (
        id uuid NOT NULL,
        worker_id uuid,
        number integer NOT NULL,
        status smallint NOT NULL,
        car_id uuid,
        customer_id uuid,
        mileage integer,
        description text,
        tenant_id uuid NOT NULL
    );
COMMENT ON COLUMN public.orders.status IS '(DC2Type:order_status_enum)';
CREATE VIEW public.organization_view
AS
SELECT o.id, o.tenant_group_id, o.name AS full_name, COALESCE(balance.money, (0)::numeric) AS balance, o.email,
       o.telephone, o.office_phone, o.seller, o.contractor, o.address, o.requisite_bank, o.requisite_bik,
       o.requisite_inn, o.requisite_kpp, o.requisite_ks, o.requisite_rs, o.requisite_legal_address, o.requisite_ogrn
  FROM (public.organization o
           LEFT JOIN (SELECT ct.operand_id AS id, SUM(ct.amount_amount) AS money
                        FROM public.customer_transaction ct
                       GROUP BY ct.operand_id, ct.amount_currency_code) balance
           ON ((balance.id = o.id)));
CREATE TABLE public.part
    (
        id uuid NOT NULL,
        manufacturer_id uuid NOT NULL,
        name character varying(255) NOT NULL,
        number character varying(30) NOT NULL,
        universal boolean NOT NULL,
        unit smallint NOT NULL,
        warehouse_id uuid
    );
COMMENT ON COLUMN public.part.number IS '(DC2Type:part_number)';
COMMENT ON COLUMN public.part.unit IS '(DC2Type:unit_enum)';
CREATE TABLE public.part_cross_part
    (
        part_cross_id uuid NOT NULL,
        part_id uuid NOT NULL
    );
CREATE VIEW public.part_analog_view
AS
SELECT pcp.part_id, analog.part_id AS analog_id
  FROM (public.part_cross_part pcp
           CROSS JOIN LATERAL ( SELECT pcp2.part_id
                                  FROM public.part_cross_part pcp2
                                 WHERE ((pcp.part_cross_id = pcp2.part_cross_id) AND
                                        (pcp.part_id <> pcp2.part_id))) analog);
CREATE TABLE public.part_case
    (
        id uuid NOT NULL,
        part_id uuid NOT NULL,
        vehicle_id uuid NOT NULL
    );
CREATE TABLE public.part_discount
    (
        id uuid NOT NULL,
        part_id uuid NOT NULL,
        since timestamp(0) WITHOUT TIME ZONE NOT NULL,
        tenant_id uuid NOT NULL,
        discount_amount bigint,
        discount_currency_code character varying(3) DEFAULT NULL::character varying
    );
COMMENT ON COLUMN public.part_discount.since IS '(DC2Type:datetime_immutable)';
CREATE TABLE public.part_price
    (
        id uuid NOT NULL,
        part_id uuid NOT NULL,
        since timestamp(0) WITHOUT TIME ZONE NOT NULL,
        tenant_id uuid NOT NULL,
        price_amount bigint,
        price_currency_code character varying(3) DEFAULT NULL::character varying
    );
COMMENT ON COLUMN public.part_price.since IS '(DC2Type:datetime_immutable)';
CREATE TABLE public.part_required_availability
    (
        id uuid NOT NULL,
        part_id uuid NOT NULL,
        order_from_quantity integer NOT NULL,
        order_up_to_quantity integer NOT NULL,
        tenant_id uuid NOT NULL
    );
CREATE TABLE public.part_supply
    (
        id uuid NOT NULL,
        part_id uuid NOT NULL,
        supplier_id uuid NOT NULL,
        quantity integer NOT NULL,
        source smallint NOT NULL,
        source_id uuid NOT NULL,
        tenant_id uuid NOT NULL
    );
COMMENT ON COLUMN public.part_supply.source IS '(DC2Type:part_supply_source_enum)';
CREATE TABLE public.tenant
    (
        id uuid NOT NULL,
        group_id uuid NOT NULL,
        identifier character varying(255) NOT NULL,
        name character varying(255) NOT NULL,
        created_at timestamp WITH TIME ZONE DEFAULT NOW() NOT NULL,
        updated_at timestamp WITH TIME ZONE DEFAULT NOW() NOT NULL
    );
CREATE TABLE public.vehicle_model
    (
        id uuid NOT NULL,
        manufacturer_id uuid NOT NULL,
        name character varying(255) NOT NULL,
        localized_name character varying(255) DEFAULT NULL::character varying,
        case_name character varying(255) DEFAULT NULL::character varying,
        year_from smallint,
        year_till smallint
    );
CREATE VIEW public.part_view
AS
SELECT part.id, tenant.id AS tenant_id, part.name, part.number, part.universal AS is_universal, part.unit,
       part.warehouse_id, COALESCE(stock.quantity, (0)::bigint) AS quantity,
       COALESCE(ordered.quantity, (0)::bigint) AS ordered, COALESCE(reserved.quantity, (0)::bigint) AS reserved,
       COALESCE(crosses.parts, '[]'::json) AS analogs, COALESCE(notes.json, '[]'::json) AS notes,
       m.name AS manufacturer_name, m.id AS manufacturer_id, m.localized_name AS manufacturer_localized_name, pc.cases,
       UPPER(CONCAT_WS(' '::text, part.name, m.name, m.localized_name, pc.cases)) AS search,
       COALESCE(price.price_amount, (0)::bigint) AS price, COALESCE(discount.discount_amount, (0)::bigint) AS discount,
       COALESCE(income.price_amount, (0)::bigint) AS income,
       COALESCE(part_required.order_from_quantity, 0) AS order_from_quantity,
       COALESCE(part_required.order_up_to_quantity, 0) AS order_up_to_quantity,
       COALESCE(supply.json, '[]'::json) AS supplies, COALESCE(supply.quantity, (0)::numeric) AS supplies_quantity
  FROM (((((((((((((public.part JOIN public.tenant ON (TRUE)) JOIN public.manufacturer m ON ((part.manufacturer_id = m.id))) LEFT JOIN (SELECT part_case.part_id,
                                                                                                                                               ARRAY_TO_STRING(ARRAY_AGG(vm.case_name), ' '::text) AS cases
                                                                                                                                          FROM (public.part_case
                                                                                                                                                   LEFT JOIN public.vehicle_model vm
                                                                                                                                                   ON ((vm.id = part_case.vehicle_id)))
                                                                                                                                         GROUP BY part_case.part_id) pc ON ((pc.part_id = part.id))) LEFT JOIN (SELECT
                                                                                                                                                                                                                            ROW_NUMBER()
                                                                                                                                                                                                                            OVER (PARTITION BY pra.part_id, pra.tenant_id ORDER BY pra.id DESC) AS rownum,
                                                                                                                                                                                                                            pra.id,
                                                                                                                                                                                                                            pra.part_id,
                                                                                                                                                                                                                            pra.order_from_quantity,
                                                                                                                                                                                                                            pra.order_up_to_quantity,
                                                                                                                                                                                                                            pra.tenant_id
                                                                                                                                                                                                                  FROM public.part_required_availability pra) part_required ON ((
          (part_required.part_id = part.id) AND (part_required.rownum = 1) AND
          (tenant.id = part_required.tenant_id)))) LEFT JOIN (SELECT ROW_NUMBER()
                                                                     OVER (PARTITION BY pp.part_id, pp.tenant_id ORDER BY pp.id DESC) AS rownum,
                                                                     pp.id, pp.part_id, pp.since, pp.tenant_id,
                                                                     pp.price_amount, pp.price_currency_code
                                                                FROM public.part_price pp) price ON ((
          (price.part_id = part.id) AND (price.rownum = 1) AND (tenant.id = price.tenant_id)))) LEFT JOIN (SELECT
                                                                                                                       ROW_NUMBER()
                                                                                                                       OVER (PARTITION BY pd.part_id, pd.tenant_id ORDER BY pd.id DESC) AS rownum,
                                                                                                                       pd.id,
                                                                                                                       pd.part_id,
                                                                                                                       pd.since,
                                                                                                                       pd.tenant_id,
                                                                                                                       pd.discount_amount,
                                                                                                                       pd.discount_currency_code
                                                                                                             FROM public.part_discount pd) discount ON ((
          (discount.part_id = part.id) AND (discount.rownum = 1) AND
          (tenant.id = discount.tenant_id)))) LEFT JOIN (SELECT ROW_NUMBER()
                                                                OVER (PARTITION BY income_part.part_id, income_part.tenant_id ORDER BY income_part.id DESC) AS rownum,
                                                                income_part.id, income_part.income_id,
                                                                income_part.part_id, income_part.quantity,
                                                                income_part.tenant_id, income_part.price_amount,
                                                                income_part.price_currency_code
                                                           FROM public.income_part) income ON (((income.part_id = part.id) AND (income.rownum = 1)))) LEFT JOIN (SELECT motion.part_id,
                                                                                                                                                                        motion.tenant_id,
                                                                                                                                                                        SUM(motion.quantity) AS quantity
                                                                                                                                                                   FROM public.motion
                                                                                                                                                                  GROUP BY motion.part_id, motion.tenant_id) stock ON (((stock.part_id = part.id) AND (tenant.id = stock.tenant_id)))) LEFT JOIN (SELECT order_item_part.part_id,
                                                                                                                                                                                                                                                                                                         order_item.tenant_id,
                                                                                                                                                                                                                                                                                                         SUM(order_item_part.quantity) AS quantity
                                                                                                                                                                                                                                                                                                    FROM ((public.order_item_part JOIN public.order_item ON ((order_item.id = order_item_part.id)))
                                                                                                                                                                                                                                                                                                             LEFT JOIN public.order_close
                                                                                                                                                                                                                                                                                                             ON ((order_item.order_id = order_close.order_id)))
                                                                                                                                                                                                                                                                                                   WHERE (order_close.* IS NULL)
                                                                                                                                                                                                                                                                                                   GROUP BY order_item_part.part_id, order_item.tenant_id) ordered ON (((ordered.part_id = part.id) AND (tenant.id = ordered.tenant_id)))) LEFT JOIN (SELECT order_item_part.part_id,
                                                                                                                                                                                                                                                                                                                                                                                                                                                             reservation.tenant_id,
                                                                                                                                                                                                                                                                                                                                                                                                                                                             SUM(reservation.quantity) AS quantity
                                                                                                                                                                                                                                                                                                                                                                                                                                                        FROM (public.reservation
                                                                                                                                                                                                                                                                                                                                                                                                                                                                 JOIN public.order_item_part
                                                                                                                                                                                                                                                                                                                                                                                                                                                                 ON ((order_item_part.id = reservation.order_item_part_id)))
                                                                                                                                                                                                                                                                                                                                                                                                                                                       GROUP BY order_item_part.part_id, reservation.tenant_id) reserved ON (((reserved.part_id = part.id) AND (tenant.id = reserved.tenant_id)))) LEFT JOIN (SELECT JSON_AGG(JSON_BUILD_OBJECT(
          'supplier_id', sub.supplier_id, 'quantity', sub.quantity, 'updatedAt', sub.updated_at)) AS json,
                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                     sub.tenant_id,
                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                     sub.part_id,
                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                     SUM(sub.quantity) AS quantity
                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                FROM (SELECT part_supply.part_id,
                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                             part_supply.tenant_id,
                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                             part_supply.supplier_id,
                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                             SUM(part_supply.quantity) AS quantity,
                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                             MAX(created_by.created_at) AS updated_at
                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                        FROM (public.part_supply
                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                 LEFT JOIN public.created_by
                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                 ON ((created_by.id = part_supply.id)))
                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                       GROUP BY part_supply.part_id,
                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                part_supply.tenant_id,
                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                part_supply.supplier_id
                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                      HAVING (SUM(part_supply.quantity) <> 0)) sub
                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                               GROUP BY sub.part_id, sub.tenant_id) supply ON (((supply.part_id = part.id) AND (tenant.id = supply.tenant_id)))) LEFT JOIN (SELECT pcp.part_id,
                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                   JSON_AGG(pcp2.part_id) FILTER (WHERE (pcp2.part_id IS NOT NULL)) AS parts
                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                              FROM (public.part_cross_part pcp
                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                       LEFT JOIN public.part_cross_part pcp2
                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                       ON (((pcp2.part_cross_id = pcp.part_cross_id) AND
                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                            (pcp2.part_id <> pcp.part_id))))
                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                             GROUP BY pcp.part_id) crosses ON ((crosses.part_id = part.id)))
           LEFT JOIN (SELECT note.subject, note.tenant_id,
                             JSON_AGG(JSON_BUILD_OBJECT('type', note.type, 'text', note.text)) AS json
                        FROM public.note
                       GROUP BY note.subject, note.tenant_id) notes
           ON (((notes.subject = part.id) AND (tenant.id = notes.tenant_id))));
CREATE VIEW public.person_view
AS
SELECT p.id, p.tenant_group_id, p.firstname, p.lastname, COALESCE(balance.money, (0)::numeric) AS balance, p.email,
       p.telephone, p.office_phone, p.seller, p.contractor
  FROM (public.person p
           LEFT JOIN (SELECT ct.operand_id AS id, SUM(ct.amount_amount) AS money
                        FROM public.customer_transaction ct
                       GROUP BY ct.operand_id, ct.amount_currency_code) balance
           ON ((balance.id = p.id)));
CREATE TABLE public.publish
    (
        id uuid NOT NULL,
        entity_id uuid NOT NULL,
        published boolean NOT NULL,
        tenant_id uuid NOT NULL
    );
CREATE VIEW public.publish_view
AS
SELECT p.entity_id AS id, p.tenant_id, p.published, cb.user_id AS created_by, cb.created_at
  FROM ((SELECT ROW_NUMBER() OVER (PARTITION BY publish.entity_id ORDER BY publish.id DESC) AS rownum, publish.id,
                publish.entity_id, publish.published, publish.tenant_id
           FROM public.publish) p
           JOIN public.created_by cb
           ON ((cb.id = p.id)))
 WHERE (p.rownum = 1);
CREATE TABLE public.review
    (
        id uuid NOT NULL,
        source_id character varying(255) NOT NULL,
        source smallint NOT NULL,
        author character varying(255) NOT NULL,
        text text NOT NULL,
        rating smallint NOT NULL,
        publish_at timestamp(0) WITHOUT TIME ZONE NOT NULL,
        raw json NOT NULL,
        tenant_id uuid NOT NULL
    );
COMMENT ON COLUMN public.review.source IS '(DC2Type:review_source)';
COMMENT ON COLUMN public.review.rating IS '(DC2Type:review_star_rating)';
COMMENT ON COLUMN public.review.publish_at IS '(DC2Type:datetime_immutable)';
CREATE VIEW public.salary_view
AS
SELECT es.id, es.tenant_id, es.employee_id, es.payday, es.amount, employee.person_id, es_cb.user_id AS created_by,
       es_cb.created_at, ese_cb.user_id AS ended_by, ese_cb.created_at AS ended_at
  FROM ((((public.employee_salary es JOIN public.created_by es_cb ON ((es_cb.id = es.id))) JOIN public.employee ON ((employee.id = es.employee_id))) LEFT JOIN public.employee_salary_end ese ON ((es.id = ese.salary_id)))
           LEFT JOIN public.created_by ese_cb
           ON ((ese_cb.id = ese.id)));
CREATE TABLE public.sms
    (
        id uuid NOT NULL,
        phone_number character varying(35) NOT NULL,
        message character varying(255) NOT NULL,
        date_send timestamp(0) WITHOUT TIME ZONE,
        tenant_id uuid NOT NULL
    );
COMMENT ON COLUMN public.sms.phone_number IS '(DC2Type:phone_number)';
COMMENT ON COLUMN public.sms.date_send IS '(DC2Type:datetime_immutable)';
CREATE TABLE public.sms_send
    (
        id uuid NOT NULL,
        sms_id uuid NOT NULL,
        success boolean NOT NULL,
        payload json NOT NULL,
        tenant_id uuid NOT NULL
    );
CREATE TABLE public.sms_status
    (
        id uuid NOT NULL,
        sms_id uuid NOT NULL,
        payload json NOT NULL
    );
CREATE VIEW public.storage_part_view
AS
SELECT part.id
  FROM public.part;
CREATE VIEW public.supply_view
AS
SELECT part_supply.part_id, part_supply.tenant_id, part_supply.supplier_id, SUM(part_supply.quantity) AS quantity,
       MAX(created_by.created_at) AS updated_at
  FROM (public.part_supply
           LEFT JOIN public.created_by
           ON ((created_by.id = part_supply.id)))
 GROUP BY part_supply.part_id, part_supply.tenant_id, part_supply.supplier_id
HAVING (SUM(part_supply.quantity) <> 0);
CREATE TABLE public.tenant_group
    (
        id uuid NOT NULL,
        identifier character varying(255) NOT NULL,
        name text NOT NULL,
        created_at timestamp WITH TIME ZONE DEFAULT NOW() NOT NULL,
        updated_at timestamp WITH TIME ZONE DEFAULT NOW() NOT NULL
    );
CREATE TABLE public.tenant_group_permission
    (
        user_id uuid NOT NULL,
        tenant_group_id uuid NOT NULL,
        created_at timestamp WITH TIME ZONE DEFAULT NOW() NOT NULL,
        updated_at timestamp WITH TIME ZONE DEFAULT NOW() NOT NULL
    );
CREATE TABLE public.tenant_permission
    (
        id uuid NOT NULL,
        user_id uuid NOT NULL,
        tenant_id uuid NOT NULL,
        created_at timestamp WITH TIME ZONE DEFAULT NOW() NOT NULL,
        updated_at timestamp WITH TIME ZONE DEFAULT NOW() NOT NULL
    );
CREATE TABLE public.wallet
    (
        id uuid NOT NULL,
        name character varying(255) NOT NULL,
        use_in_income boolean NOT NULL,
        use_in_order boolean NOT NULL,
        show_in_layout boolean NOT NULL,
        default_in_manual_transaction boolean NOT NULL,
        tenant_id uuid NOT NULL,
        currency_code character varying(3) DEFAULT NULL::character varying
    );
CREATE VIEW public.wallet_transaction_view
AS
SELECT wt.id, wt.tenant_id, wt.wallet_id, wt.amount_amount AS amount, wt.source,
       CASE WHEN (wt.source = ANY (ARRAY [3, 6])) THEN ct.operand_id ELSE wt.source_id END AS source_id, wt.description,
       cb.created_at, cb.user_id AS created_by
  FROM ((public.wallet_transaction wt JOIN public.created_by cb ON ((cb.id = wt.id)))
           LEFT JOIN public.customer_transaction ct
           ON ((ct.id = wt.source_id)));
CREATE VIEW public.wallet_view
AS
SELECT w.id, w.tenant_id, w.name, w.currency_code, w.default_in_manual_transaction, w.show_in_layout, w.use_in_income,
       w.use_in_order, COALESCE(balance.money, (0)::numeric) AS balance
  FROM (public.wallet w
           LEFT JOIN (SELECT wt.wallet_id AS id, SUM(wt.amount_amount) AS money
                        FROM public.wallet_transaction wt
                       GROUP BY wt.wallet_id, wt.amount_currency_code) balance
           ON ((balance.id = w.id)));
CREATE TABLE public.warehouse
    (
        id uuid NOT NULL,
        tenant_id uuid NOT NULL
    );
CREATE TABLE public.warehouse_code
    (
        id uuid NOT NULL,
        warehouse_id uuid NOT NULL,
        code character varying(255) NOT NULL,
        tenant_id uuid NOT NULL
    );
CREATE TABLE public.warehouse_name
    (
        id uuid NOT NULL,
        warehouse_id uuid NOT NULL,
        name character varying(255) NOT NULL,
        tenant_id uuid NOT NULL
    );
CREATE TABLE public.warehouse_parent
    (
        id uuid NOT NULL,
        warehouse_id uuid NOT NULL,
        warehouse_parent_id uuid,
        tenant_id uuid NOT NULL
    );
CREATE VIEW public.warehouse_view
AS
  WITH RECURSIVE tree(id) AS (SELECT root.id, wp.warehouse_parent_id AS parent_id, 0 AS depth, root.tenant_id
                                FROM (public.warehouse root
                                         LEFT JOIN LATERAL ( SELECT sub.warehouse_parent_id
                                                               FROM public.warehouse_parent sub
                                                              WHERE (sub.warehouse_id = root.id)
                                                              ORDER BY sub.id DESC
                                                              LIMIT 1) wp
                                         ON (TRUE))
                               WHERE (wp.warehouse_parent_id IS NULL)
                               UNION ALL
                              SELECT root.id, wp.warehouse_parent_id AS parent_id, (p.depth + 1) AS depth,
                                     root.tenant_id
                                FROM ((public.warehouse root LEFT JOIN LATERAL ( SELECT sub.warehouse_parent_id
                                                                                   FROM public.warehouse_parent sub
                                                                                  WHERE (sub.warehouse_id = root.id)
                                                                                  ORDER BY sub.id DESC
                                                                                  LIMIT 1) wp ON (TRUE))
                                         JOIN tree p
                                         ON ((p.id = wp.warehouse_parent_id))))
SELECT tree.id, tree.tenant_id, wn.name, wc.code, tree.parent_id, tree.depth
  FROM ((tree JOIN LATERAL ( SELECT sub.name
                               FROM public.warehouse_name sub
                              WHERE (sub.warehouse_id = tree.id)
                              ORDER BY sub.id DESC
                              LIMIT 1) wn ON (TRUE))
           JOIN LATERAL ( SELECT sub.code
                            FROM public.warehouse_code sub
                           WHERE (sub.warehouse_id = tree.id)
                           ORDER BY sub.id DESC
                           LIMIT 1) wc
           ON (TRUE));
ALTER TABLE ONLY public.appeal_calculator
    ADD CONSTRAINT appeal_calculator_pkey
        PRIMARY KEY (id);
ALTER TABLE ONLY public.appeal_call
    ADD CONSTRAINT appeal_call_pkey
        PRIMARY KEY (id);
ALTER TABLE ONLY public.appeal_cooperation
    ADD CONSTRAINT appeal_cooperation_pkey
        PRIMARY KEY (id);
ALTER TABLE ONLY public.appeal_postpone
    ADD CONSTRAINT appeal_postpone_pkey
        PRIMARY KEY (id);
ALTER TABLE ONLY public.appeal_question
    ADD CONSTRAINT appeal_question_pkey
        PRIMARY KEY (id);
ALTER TABLE ONLY public.appeal_schedule
    ADD CONSTRAINT appeal_schedule_pkey
        PRIMARY KEY (id);
ALTER TABLE ONLY public.appeal_status
    ADD CONSTRAINT appeal_status_pkey
        PRIMARY KEY (id);
ALTER TABLE ONLY public.appeal_tire_fitting
    ADD CONSTRAINT appeal_tire_fitting_pkey
        PRIMARY KEY (id);
ALTER TABLE ONLY public.calendar_entry_deletion
    ADD CONSTRAINT calendar_entry_deletion_pkey
        PRIMARY KEY (id);
ALTER TABLE ONLY public.calendar_entry_order_info
    ADD CONSTRAINT calendar_entry_order_info_pkey
        PRIMARY KEY (id);
ALTER TABLE ONLY public.calendar_entry_order
    ADD CONSTRAINT calendar_entry_order_pkey
        PRIMARY KEY (id);
ALTER TABLE ONLY public.calendar_entry
    ADD CONSTRAINT calendar_entry_pkey
        PRIMARY KEY (id);
ALTER TABLE ONLY public.calendar_entry_schedule
    ADD CONSTRAINT calendar_entry_schedule_pkey
        PRIMARY KEY (id);
ALTER TABLE ONLY public.car
    ADD CONSTRAINT car_pkey
        PRIMARY KEY (id);
ALTER TABLE ONLY public.car_recommendation_part
    ADD CONSTRAINT car_recommendation_part_pkey
        PRIMARY KEY (id);
ALTER TABLE ONLY public.car_recommendation
    ADD CONSTRAINT car_recommendation_pkey
        PRIMARY KEY (id);
ALTER TABLE ONLY public.created_by
    ADD CONSTRAINT created_by_pkey
        PRIMARY KEY (id);
ALTER TABLE ONLY public.cron_job
    ADD CONSTRAINT cron_job_pkey
        PRIMARY KEY (id);
ALTER TABLE ONLY public.cron_report
    ADD CONSTRAINT cron_report_pkey
        PRIMARY KEY (id);
ALTER TABLE ONLY public.customer_transaction
    ADD CONSTRAINT customer_transaction_pkey
        PRIMARY KEY (id);
ALTER TABLE ONLY public.employee
    ADD CONSTRAINT employee_pkey
        PRIMARY KEY (id);
ALTER TABLE ONLY public.employee_salary_end
    ADD CONSTRAINT employee_salary_end_pkey
        PRIMARY KEY (id);
ALTER TABLE ONLY public.employee_salary
    ADD CONSTRAINT employee_salary_pkey
        PRIMARY KEY (id);
ALTER TABLE ONLY public.expense
    ADD CONSTRAINT expense_pkey
        PRIMARY KEY (id);
ALTER TABLE ONLY public.google_review_token
    ADD CONSTRAINT google_review_token_pkey
        PRIMARY KEY (id);
ALTER TABLE ONLY public.income_accrue
    ADD CONSTRAINT income_accrue_pkey
        PRIMARY KEY (id);
ALTER TABLE ONLY public.income_part
    ADD CONSTRAINT income_part_pkey
        PRIMARY KEY (id);
ALTER TABLE ONLY public.income
    ADD CONSTRAINT income_pkey
        PRIMARY KEY (id);
ALTER TABLE ONLY public.inventorization_close
    ADD CONSTRAINT inventorization_close_pkey
        PRIMARY KEY (id);
ALTER TABLE ONLY public.inventorization_part
    ADD CONSTRAINT inventorization_part_pkey
        PRIMARY KEY (inventorization_id, part_id);
ALTER TABLE ONLY public.inventorization
    ADD CONSTRAINT inventorization_pkey
        PRIMARY KEY (id);
ALTER TABLE ONLY public.manufacturer
    ADD CONSTRAINT manufacturer_pkey
        PRIMARY KEY (id);
ALTER TABLE ONLY public.mc_equipment
    ADD CONSTRAINT mc_equipment_pkey
        PRIMARY KEY (id);
ALTER TABLE ONLY public.mc_line
    ADD CONSTRAINT mc_line_pkey
        PRIMARY KEY (id);
ALTER TABLE ONLY public.mc_part
    ADD CONSTRAINT mc_part_pkey
        PRIMARY KEY (id);
ALTER TABLE ONLY public.mc_work
    ADD CONSTRAINT mc_work_pkey
        PRIMARY KEY (id);
ALTER TABLE ONLY public.migration_versions
    ADD CONSTRAINT migration_versions_pkey
        PRIMARY KEY (version);
ALTER TABLE ONLY public.motion
    ADD CONSTRAINT motion_pkey
        PRIMARY KEY (id);
ALTER TABLE ONLY public.note_delete
    ADD CONSTRAINT note_delete_pkey
        PRIMARY KEY (id);
ALTER TABLE ONLY public.note
    ADD CONSTRAINT note_pkey
        PRIMARY KEY (id);
ALTER TABLE ONLY public.order_cancel
    ADD CONSTRAINT order_cancel_pkey
        PRIMARY KEY (id);
ALTER TABLE ONLY public.order_close
    ADD CONSTRAINT order_close_pkey
        PRIMARY KEY (id);
ALTER TABLE ONLY public.order_deal
    ADD CONSTRAINT order_deal_pkey
        PRIMARY KEY (id);
ALTER TABLE ONLY public.order_item_group
    ADD CONSTRAINT order_item_group_pkey
        PRIMARY KEY (id);
ALTER TABLE ONLY public.order_item_part
    ADD CONSTRAINT order_item_part_pkey
        PRIMARY KEY (id);
ALTER TABLE ONLY public.order_item
    ADD CONSTRAINT order_item_pkey
        PRIMARY KEY (id);
ALTER TABLE ONLY public.order_item_service
    ADD CONSTRAINT order_item_service_pkey
        PRIMARY KEY (id);
ALTER TABLE ONLY public.order_payment
    ADD CONSTRAINT order_payment_pkey
        PRIMARY KEY (id);
ALTER TABLE ONLY public.order_suspend
    ADD CONSTRAINT order_suspend_pkey
        PRIMARY KEY (id);
ALTER TABLE ONLY public.orders
    ADD CONSTRAINT orders_pkey
        PRIMARY KEY (id);
ALTER TABLE ONLY public.organization
    ADD CONSTRAINT organization_pkey
        PRIMARY KEY (id);
ALTER TABLE ONLY public.part_case
    ADD CONSTRAINT part_case_pkey
        PRIMARY KEY (id);
ALTER TABLE ONLY public.part_cross_part
    ADD CONSTRAINT part_cross_part_pkey
        PRIMARY KEY (part_cross_id, part_id);
ALTER TABLE ONLY public.part_discount
    ADD CONSTRAINT part_discount_pkey
        PRIMARY KEY (id);
ALTER TABLE ONLY public.part
    ADD CONSTRAINT part_pkey
        PRIMARY KEY (id);
ALTER TABLE ONLY public.part_price
    ADD CONSTRAINT part_price_pkey
        PRIMARY KEY (id);
ALTER TABLE ONLY public.part_required_availability
    ADD CONSTRAINT part_required_availability_pkey
        PRIMARY KEY (id);
ALTER TABLE ONLY public.part_supply
    ADD CONSTRAINT part_supply_pkey
        PRIMARY KEY (id);
ALTER TABLE ONLY public.person
    ADD CONSTRAINT person_pkey
        PRIMARY KEY (id);
ALTER TABLE ONLY public.publish
    ADD CONSTRAINT publish_pkey
        PRIMARY KEY (id);
ALTER TABLE ONLY public.reservation
    ADD CONSTRAINT reservation_pkey
        PRIMARY KEY (id);
ALTER TABLE ONLY public.review
    ADD CONSTRAINT review_pkey
        PRIMARY KEY (id);
ALTER TABLE ONLY public.sms
    ADD CONSTRAINT sms_pkey
        PRIMARY KEY (id);
ALTER TABLE ONLY public.sms_send
    ADD CONSTRAINT sms_send_pkey
        PRIMARY KEY (id);
ALTER TABLE ONLY public.sms_status
    ADD CONSTRAINT sms_status_pkey
        PRIMARY KEY (id);
ALTER TABLE ONLY public.tenant_group_permission
    ADD CONSTRAINT tenant_group_permission_pkey
        PRIMARY KEY (user_id, tenant_group_id);
ALTER TABLE ONLY public.tenant_group
    ADD CONSTRAINT tenant_group_pkey
        PRIMARY KEY (id);
ALTER TABLE ONLY public.tenant
    ADD CONSTRAINT tenant_pkey
        PRIMARY KEY (id, group_id);
ALTER TABLE ONLY public.tenant_permission
    ADD CONSTRAINT user_permission_pkey
        PRIMARY KEY (id);
ALTER TABLE ONLY public.vehicle_model
    ADD CONSTRAINT vehicle_model_pkey
        PRIMARY KEY (id);
ALTER TABLE ONLY public.wallet
    ADD CONSTRAINT wallet_pkey
        PRIMARY KEY (id);
ALTER TABLE ONLY public.wallet_transaction
    ADD CONSTRAINT wallet_transaction_pkey
        PRIMARY KEY (id);
ALTER TABLE ONLY public.warehouse_code
    ADD CONSTRAINT warehouse_code_pkey
        PRIMARY KEY (id);
ALTER TABLE ONLY public.warehouse_name
    ADD CONSTRAINT warehouse_name_pkey
        PRIMARY KEY (id);
ALTER TABLE ONLY public.warehouse_parent
    ADD CONSTRAINT warehouse_parent_pkey
        PRIMARY KEY (id);
ALTER TABLE ONLY public.warehouse
    ADD CONSTRAINT warehouse_pkey
        PRIMARY KEY (id);
CREATE INDEX idx_2b65786f4d7b7542 ON public.mc_part USING btree (line_id);
CREATE INDEX idx_42c84955437ef9d2 ON public.reservation USING btree (order_item_part_id);
CREATE INDEX idx_52ea1f09727aca70 ON public.order_item USING btree (parent_id);
CREATE INDEX idx_52ea1f098d9f6d38 ON public.order_item USING btree (order_id);
CREATE INDEX idx_59bb753a4ce34bec ON public.part_price USING btree (part_id);
CREATE INDEX idx_5fbde1c1ba364942 ON public.calendar_entry_order_info USING btree (entry_id);
CREATE INDEX idx_76b231714ce34bec ON public.part_discount USING btree (part_id);
CREATE INDEX idx_834566e8640ed2c0 ON public.income_part USING btree (income_id);
CREATE INDEX idx_86fdaee3ba364942 ON public.calendar_entry_schedule USING btree (entry_id);
CREATE INDEX idx_8e4baaf2c3c6f69f ON public.car_recommendation USING btree (car_id);
CREATE INDEX idx_9b522d468d9f6d38 ON public.order_payment USING btree (order_id);
CREATE INDEX idx_b37ebc5f517fe9fe ON public.mc_line USING btree (equipment_id);
CREATE INDEX idx_b37ebc5fbb3453db ON public.mc_line USING btree (work_id);
CREATE INDEX idx_b6c6a7f5be04ea9 ON public.cron_report USING btree (job_id);
CREATE INDEX idx_c789f0d18d9f6d38 ON public.order_suspend USING btree (order_id);
CREATE INDEX idx_ddc72d65d173940b ON public.car_recommendation_part USING btree (recommendation_id);
CREATE INDEX idx_e52ffdee6b20ba36 ON public.orders USING btree (worker_id);
CREATE INDEX idx_f5fea1e84ce34bec ON public.motion USING btree (part_id);
CREATE UNIQUE INDEX temporal_unique_idx ON public.tenant USING btree (id);
CREATE UNIQUE INDEX un_name ON public.cron_job USING btree (name);
CREATE UNIQUE INDEX uniq_22c02b5326ed0855 ON public.note_delete USING btree (note_id);
CREATE UNIQUE INDEX uniq_2a0e7894ce34bec545317d1 ON public.part_case USING btree (part_id, vehicle_id);
CREATE UNIQUE INDEX uniq_34dcd176450ff010dff2bbb0 ON public.person USING btree (telephone, tenant_group_id);
CREATE UNIQUE INDEX uniq_3d0ae6dc5e237e06 ON public.manufacturer USING btree (name);
CREATE UNIQUE INDEX uniq_425dfa41640ed2c0 ON public.income_accrue USING btree (income_id);
CREATE UNIQUE INDEX uniq_490f70c696901f54a23b42d ON public.part USING btree (number, manufacturer_id);
CREATE UNIQUE INDEX uniq_4e59c462772e836a ON public.tenant USING btree (identifier);
CREATE UNIQUE INDEX uniq_4f6195a04ca655fd ON public.inventorization_close USING btree (inventorization_id);
CREATE UNIQUE INDEX uniq_59455a58b0fdf16e ON public.employee_salary_end USING btree (salary_id);
CREATE UNIQUE INDEX uniq_773de69d772e836adff2bbb0 ON public.car USING btree (identifier, tenant_group_id);
CREATE UNIQUE INDEX uniq_794381c65f8a7f73953c1c619033212a ON public.review USING btree (source, source_id, tenant_id);
CREATE UNIQUE INDEX uniq_909ff5398d9f6d38 ON public.order_close USING btree (order_id);
CREATE UNIQUE INDEX uniq_b53af235a23b42d5e237e06df3ba4b5 ON public.vehicle_model USING btree (manufacturer_id, name, case_name);
CREATE UNIQUE INDEX uniq_e52ffdee96901f549033212a ON public.orders USING btree (number, tenant_id);
CREATE UNIQUE INDEX uniq_f118663dba364942 ON public.calendar_entry_deletion USING btree (entry_id);
CREATE TRIGGER set_manufacturer_updated_at
    BEFORE UPDATE
    ON public.manufacturer
    FOR EACH ROW
EXECUTE FUNCTION public.set_current_timestamp_updated_at();
COMMENT ON TRIGGER set_manufacturer_updated_at ON public.manufacturer IS 'trigger to set value of column "updated_at" to current timestamp on row update';
CREATE TRIGGER set_tenant_group_permission_updated_at
    BEFORE UPDATE
    ON public.tenant_group_permission
    FOR EACH ROW
EXECUTE FUNCTION public.set_current_timestamp_updated_at();
COMMENT ON TRIGGER set_tenant_group_permission_updated_at ON public.tenant_group_permission IS 'trigger to set value of column "updated_at" to current timestamp on row update';
CREATE TRIGGER set_tenant_group_updated_at
    BEFORE UPDATE
    ON public.tenant_group
    FOR EACH ROW
EXECUTE FUNCTION public.set_current_timestamp_updated_at();
COMMENT ON TRIGGER set_tenant_group_updated_at ON public.tenant_group IS 'trigger to set value of column "updated_at" to current timestamp on row update';
CREATE TRIGGER set_tenant_permission_updated_at
    BEFORE UPDATE
    ON public.tenant_permission
    FOR EACH ROW
EXECUTE FUNCTION public.set_current_timestamp_updated_at();
COMMENT ON TRIGGER set_tenant_permission_updated_at ON public.tenant_permission IS 'trigger to set value of column "updated_at" to current timestamp on row update';
CREATE TRIGGER set_tenant_updated_at
    BEFORE UPDATE
    ON public.tenant
    FOR EACH ROW
EXECUTE FUNCTION public.set_current_timestamp_updated_at();
COMMENT ON TRIGGER set_tenant_updated_at ON public.tenant IS 'trigger to set value of column "updated_at" to current timestamp on row update';
ALTER TABLE ONLY public.note_delete
    ADD CONSTRAINT fk_22c02b5326ed0855
        FOREIGN KEY (note_id) REFERENCES public.note (id);
ALTER TABLE ONLY public.mc_part
    ADD CONSTRAINT fk_2b65786f4d7b7542
        FOREIGN KEY (line_id) REFERENCES public.mc_line (id);
ALTER TABLE ONLY public.order_item_part
    ADD CONSTRAINT fk_3db84fc5bf396750
        FOREIGN KEY (id) REFERENCES public.order_item (id) ON DELETE CASCADE;
ALTER TABLE ONLY public.income_accrue
    ADD CONSTRAINT fk_425dfa41640ed2c0
        FOREIGN KEY (income_id) REFERENCES public.income (id);
ALTER TABLE ONLY public.reservation
    ADD CONSTRAINT fk_42c84955437ef9d2
        FOREIGN KEY (order_item_part_id) REFERENCES public.order_item_part (id);
ALTER TABLE ONLY public.inventorization_close
    ADD CONSTRAINT fk_4f6195a04ca655fd
        FOREIGN KEY (inventorization_id) REFERENCES public.inventorization (id);
ALTER TABLE ONLY public.order_item
    ADD CONSTRAINT fk_52ea1f09727aca70
        FOREIGN KEY (parent_id) REFERENCES public.order_item (id) ON DELETE CASCADE;
ALTER TABLE ONLY public.order_item
    ADD CONSTRAINT fk_52ea1f098d9f6d38
        FOREIGN KEY (order_id) REFERENCES public.orders (id);
ALTER TABLE ONLY public.employee_salary_end
    ADD CONSTRAINT fk_59455a58b0fdf16e
        FOREIGN KEY (salary_id) REFERENCES public.employee_salary (id);
ALTER TABLE ONLY public.calendar_entry_order_info
    ADD CONSTRAINT fk_5fbde1c1ba364942
        FOREIGN KEY (entry_id) REFERENCES public.calendar_entry (id);
ALTER TABLE ONLY public.income_part
    ADD CONSTRAINT fk_834566e8640ed2c0
        FOREIGN KEY (income_id) REFERENCES public.income (id);
ALTER TABLE ONLY public.calendar_entry_schedule
    ADD CONSTRAINT fk_86fdaee3ba364942
        FOREIGN KEY (entry_id) REFERENCES public.calendar_entry (id);
ALTER TABLE ONLY public.car_recommendation
    ADD CONSTRAINT fk_8e4baaf2c3c6f69f
        FOREIGN KEY (car_id) REFERENCES public.car (id);
ALTER TABLE ONLY public.order_close
    ADD CONSTRAINT fk_909ff5398d9f6d38
        FOREIGN KEY (order_id) REFERENCES public.orders (id);
ALTER TABLE ONLY public.order_cancel
    ADD CONSTRAINT fk_9599d5a7bf396750
        FOREIGN KEY (id) REFERENCES public.order_close (id) ON DELETE CASCADE;
ALTER TABLE ONLY public.order_payment
    ADD CONSTRAINT fk_9b522d468d9f6d38
        FOREIGN KEY (order_id) REFERENCES public.orders (id);
ALTER TABLE ONLY public.order_deal
    ADD CONSTRAINT fk_ae0ffb01bf396750
        FOREIGN KEY (id) REFERENCES public.order_close (id) ON DELETE CASCADE;
ALTER TABLE ONLY public.mc_line
    ADD CONSTRAINT fk_b37ebc5f517fe9fe
        FOREIGN KEY (equipment_id) REFERENCES public.mc_equipment (id);
ALTER TABLE ONLY public.mc_line
    ADD CONSTRAINT fk_b37ebc5fbb3453db
        FOREIGN KEY (work_id) REFERENCES public.mc_work (id);
ALTER TABLE ONLY public.cron_report
    ADD CONSTRAINT fk_b6c6a7f5be04ea9
        FOREIGN KEY (job_id) REFERENCES public.cron_job (id) ON DELETE CASCADE;
ALTER TABLE ONLY public.order_suspend
    ADD CONSTRAINT fk_c789f0d18d9f6d38
        FOREIGN KEY (order_id) REFERENCES public.orders (id);
ALTER TABLE ONLY public.car_recommendation_part
    ADD CONSTRAINT fk_ddc72d65d173940b
        FOREIGN KEY (recommendation_id) REFERENCES public.car_recommendation (id);
ALTER TABLE ONLY public.orders
    ADD CONSTRAINT fk_e52ffdee6b20ba36
        FOREIGN KEY (worker_id) REFERENCES public.employee (id);
ALTER TABLE ONLY public.order_item_service
    ADD CONSTRAINT fk_ee0028ecbf396750
        FOREIGN KEY (id) REFERENCES public.order_item (id) ON DELETE CASCADE;
ALTER TABLE ONLY public.calendar_entry_deletion
    ADD CONSTRAINT fk_f118663dba364942
        FOREIGN KEY (entry_id) REFERENCES public.calendar_entry (id);
ALTER TABLE ONLY public.order_item_group
    ADD CONSTRAINT fk_f4bda240bf396750
        FOREIGN KEY (id) REFERENCES public.order_item (id) ON DELETE CASCADE;
ALTER TABLE ONLY public.tenant
    ADD CONSTRAINT tenant_group_id_fkey
        FOREIGN KEY (group_id) REFERENCES public.tenant_group (id) ON UPDATE RESTRICT ON DELETE RESTRICT;
ALTER TABLE ONLY public.tenant_group_permission
    ADD CONSTRAINT tenant_group_permission_tenant_group_id_fkey
        FOREIGN KEY (tenant_group_id) REFERENCES public.tenant_group (id);
ALTER TABLE ONLY public.tenant_permission
    ADD CONSTRAINT tenant_permission_tenant_id_fkey
        FOREIGN KEY (tenant_id) REFERENCES public.tenant (id) ON UPDATE RESTRICT ON DELETE RESTRICT;
