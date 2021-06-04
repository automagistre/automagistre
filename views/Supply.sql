SELECT part_supply.part_id,
       part_supply.supplier_id,
       SUM(part_supply.quantity)  AS quantity,
       MAX(created_by.created_at) AS updated_at
FROM part_supply
         LEFT JOIN created_by ON created_by.id = part_supply.id
GROUP BY part_supply.part_id, part_supply.supplier_id
HAVING SUM(part_supply.quantity) <> 0
