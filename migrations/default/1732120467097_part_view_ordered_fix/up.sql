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

DROP FUNCTION public.part_view_order_item_part_sync();

CREATE FUNCTION public.part_view_order_item_part_sync(_order_id uuid) RETURNS void
    LANGUAGE plpgsql AS
$$
BEGIN
    UPDATE public.part_view
    SET ordered = COALESCE(sub.quantity, (0)::bigint)
    FROM (SELECT order_item_part.part_id,
                 order_item.tenant_id,
                 CASE
                     WHEN order_close.id IS NULL THEN SUM(order_item_part.quantity)
                     ELSE 0
                     END AS quantity
          FROM ((public.order_item_part JOIN public.order_item
                 ON ((order_item.id = order_item_part.id)))
              LEFT JOIN public.order_close
                ON ((order_item.order_id = order_close.order_id)))
          WHERE order_item.order_id = _order_id
          GROUP BY order_item_part.part_id, order_item.tenant_id, order_close.id) sub
    WHERE part_view.id = sub.part_id
      AND part_view.tenant_id = sub.tenant_id;
END ;
$$;

CREATE OR REPLACE FUNCTION public.part_view_order_item_part_sync_trigger() RETURNS trigger
    LANGUAGE plpgsql
AS
$$
DECLARE
    _order_id uuid;
BEGIN
    IF (tg_op = 'INSERT') OR (tg_op = 'UPDATE') THEN
        SELECT order_id INTO _order_id FROM order_item WHERE id = new.id;
    ELSEIF (tg_op = 'DELETE') THEN
        _order_id = old.order_id;
    END IF;

    PERFORM public.part_view_order_item_part_sync(_order_id);

    RETURN NULL; -- возвращаемое значение для триггера AFTER игнорируется
END;
$$;

DROP TRIGGER part_view_order_item_part_sync ON public.order_item_part;
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
    PERFORM public.part_view_order_item_part_sync(new.order_id);

    RETURN NULL; -- возвращаемое значение для триггера AFTER игнорируется
END;
$$;

CREATE TRIGGER part_view_order_close_sync_trigger
    AFTER INSERT
    ON public.order_close
    FOR EACH ROW
EXECUTE PROCEDURE public.part_view_order_close_sync_trigger();

-- Fix old data on closed orders

SELECT public.part_view_order_item_part_sync(order_id)
FROM public.order_close;
