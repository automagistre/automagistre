SELECT wt.id,
       wt.wallet_id,
       wt.amount_currency_code || ' ' || wt.amount_amount AS amount,
       wt.source,
       CASE
           WHEN
               wt.source IN (3, 6)
               THEN ct.operand_id
           ELSE
               wt.source_id
           END,
       wt.description,
       cb.created_at,
       cb.user_id                                         AS created_by
FROM wallet_transaction wt
         JOIN created_by cb ON cb.id = wt.id
         LEFT JOIN customer_transaction ct ON ct.id = wt.source_id
