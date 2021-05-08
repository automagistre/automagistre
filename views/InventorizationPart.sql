SELECT ip.inventorization_id,
       ip.part_id,
       ip.quantity,
       COALESCE(stock.quantity, 0)    AS in_stock,
       COALESCE(reserved.quantity, 0) AS reserved
FROM inventorization_part ip
         LEFT JOIN (SELECT motion.part_id, SUM(motion.quantity) AS quantity FROM motion GROUP BY motion.part_id) stock
                   ON stock.part_id = ip.part_id
         LEFT JOIN (SELECT order_item_part.part_id, SUM(reservation.quantity) AS quantity
                    FROM reservation
                             JOIN order_item_part ON order_item_part.id = reservation.order_item_part_id
                    GROUP BY order_item_part.part_id) AS reserved
                   ON reserved.part_id = ip.part_id
