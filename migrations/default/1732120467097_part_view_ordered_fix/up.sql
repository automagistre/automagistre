-- add order_id to public.order_item_part

ALTER TABLE public.order_item_part
    ADD order_id uuid REFERENCES public.orders (id);

UPDATE public.order_item_part
SET order_id = order_item.order_id
FROM order_item
WHERE order_item.id = public.order_item_part.id;

CREATE OR REPLACE FUNCTION public.order_item_part_order_id_sync_trigger() RETURNS trigger
    LANGUAGE plpgsql
AS
$$
BEGIN
    new.order_id = (SELECT order_item.order_id
                    FROM order_item
                    WHERE order_item.id = new.id);

    RETURN new;
END;
$$;

CREATE TRIGGER order_item_part_order_id_sync_trigger
    BEFORE INSERT
    ON public.order_item_part
    FOR EACH ROW
EXECUTE PROCEDURE public.order_item_part_order_id_sync_trigger();

--- update by order_id

CREATE FUNCTION public.part_view_order_item_part_sync(_part_id uuid, _tenant_id uuid) RETURNS void
    LANGUAGE plpgsql AS
$$
BEGIN
    UPDATE public.part_view
    SET ordered = COALESCE(sub.quantity, (0)::bigint)
    FROM (SELECT SUM(oip.quantity) AS quantity
          FROM order_item_part oip
                   JOIN order_item oi ON oip.id = oi.id
                   LEFT JOIN order_close od ON oi.order_id = od.order_id
          WHERE oip.part_id = _part_id
            AND oi.tenant_id = _tenant_id
            AND od.id IS NULL) sub
    WHERE part_view.id = _part_id
      AND part_view.tenant_id = _tenant_id;
END ;
$$;

CREATE OR REPLACE FUNCTION public.part_view_order_item_part_sync_trigger() RETURNS trigger
    LANGUAGE plpgsql
AS
$$
DECLARE
    _part_id   uuid;
    _tenant_id uuid;
BEGIN
    IF (tg_op = 'INSERT') OR (tg_op = 'UPDATE') THEN
        _part_id = new.part_id;
        SELECT tenant_id INTO _tenant_id FROM orders WHERE id = new.order_id;
    ELSEIF (tg_op = 'DELETE') THEN
        _part_id = old.part_id;
        SELECT tenant_id INTO _tenant_id FROM orders WHERE id = old.order_id;
    END IF;

    PERFORM public.part_view_order_item_part_sync(_part_id, _tenant_id);

    RETURN NULL; -- возвращаемое значение для триггера AFTER игнорируется
END;
$$;

CREATE TRIGGER part_view_order_item_part_sync
    AFTER INSERT OR UPDATE OF quantity OR DELETE
    ON public.order_item_part
    FOR EACH ROW
EXECUTE PROCEDURE public.part_view_order_item_part_sync_trigger();

--- Remove ordered on order closed

CREATE OR REPLACE FUNCTION public.part_view_order_close_sync_trigger() RETURNS trigger
    LANGUAGE plpgsql
AS
$$
BEGIN
    PERFORM public.part_view_order_item_part_sync(oip.part_id, new.tenant_id)
    FROM public.order_item_part oip
    WHERE oip.order_id =
          new.order_id;

    RETURN NULL; -- возвращаемое значение для триггера AFTER игнорируется
END;
$$;

CREATE TRIGGER part_view_order_close_sync_trigger
    AFTER INSERT
    ON public.order_close
    FOR EACH ROW
EXECUTE PROCEDURE public.part_view_order_close_sync_trigger();

--- Sync stock

DROP FUNCTION public.part_view_motion_sync();
CREATE FUNCTION public.part_view_motion_sync(_part_id uuid, _tenant_id uuid) RETURNS void
    LANGUAGE plpgsql AS
$$
BEGIN
    WITH sum AS (SELECT SUM(quantity) AS quantity
                 FROM public.motion
                 WHERE part_id = _part_id
                   AND tenant_id = _tenant_id
                 GROUP BY part_id)
    UPDATE public.part_view pv
    SET quantity = sm.quantity
    FROM sum sm
    WHERE pv.id = _part_id
      AND pv.tenant_id = _tenant_id;
END ;
$$;

CREATE OR REPLACE FUNCTION public.part_view_motion_sync_trigger() RETURNS trigger
    LANGUAGE plpgsql
AS
$$
DECLARE
    _part_id   uuid;
    _tenant_id uuid;
BEGIN
    IF (tg_op = 'INSERT') OR (tg_op = 'UPDATE') THEN
        _part_id = new.part_id;
        _tenant_id = new.tenant_id;
    ELSEIF (tg_op = 'DELETE') THEN
        _part_id = old.part_id;
        _tenant_id = old.tenant_id;
    END IF;

    PERFORM public.part_view_motion_sync(_part_id, _tenant_id);

    RETURN NULL; -- возвращаемое значение для триггера AFTER игнорируется
END;
$$;

DROP TRIGGER motion_part_view_sync ON public.motion;
CREATE TRIGGER motion_part_view_sync
    AFTER INSERT OR UPDATE OR DELETE
    ON public.motion
    FOR EACH ROW
EXECUTE PROCEDURE public.part_view_motion_sync_trigger();

--- Sync reserved

DROP FUNCTION public.part_view_reservation_sync();
CREATE FUNCTION public.part_view_reservation_sync(_part_id uuid, _tenant_id uuid) RETURNS void
    LANGUAGE plpgsql AS
$$
BEGIN
    WITH sum AS (SELECT SUM(reservation.quantity) AS quantity
                 FROM public.reservation
                          JOIN order_item_part oip ON oip.id = reservation.order_item_part_id
                 WHERE oip.part_id = _part_id
                   AND reservation.tenant_id = _tenant_id
                 GROUP BY oip.part_id)
    UPDATE public.part_view
    SET reserved = sum.quantity
    FROM sum
    WHERE part_view.id = _part_id
      AND part_view.tenant_id = _tenant_id;
END ;
$$;

CREATE OR REPLACE FUNCTION public.part_view_reservation_sync_trigger() RETURNS trigger
    LANGUAGE plpgsql
AS
$$
DECLARE
    _part_id   uuid;
    _tenant_id uuid;
BEGIN
    IF (tg_op = 'INSERT') OR (tg_op = 'UPDATE') THEN
        SELECT part_id INTO _part_id FROM order_item_part oip WHERE oip.id = new.order_item_part_id;
        _tenant_id = new.tenant_id;
    ELSEIF (tg_op = 'DELETE') THEN
        SELECT part_id INTO _part_id FROM order_item_part oip WHERE oip.id = old.order_item_part_id;
        _tenant_id = old.tenant_id;
    END IF;

    PERFORM public.part_view_reservation_sync(_part_id, _tenant_id);

    RETURN NULL; -- возвращаемое значение для триггера AFTER игнорируется
END;
$$;

DROP TRIGGER part_view_reservation_sync ON public.reservation;
CREATE TRIGGER part_view_reservation_sync
    AFTER INSERT OR UPDATE OR DELETE
    ON public.reservation
    FOR EACH ROW
EXECUTE PROCEDURE public.part_view_reservation_sync_trigger();
