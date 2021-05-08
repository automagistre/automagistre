SELECT ct.id,
       ct.operand_id,
       ct.amount_currency_code || ' ' || ct.amount_amount AS amount,
       ct.source,
       CASE
           WHEN
               ct.source IN (5, 10)
               THEN
               wt.wallet_id
           ELSE
               ct.source_id
           END,
       ct.description,
       cb.created_at,
       cb.user_id                                         AS created_by
FROM customer_transaction ct
         JOIN created_by cb ON cb.id = ct.id
         LEFT JOIN wallet_transaction wt ON wt.id = ct.source_id
