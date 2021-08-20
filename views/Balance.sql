SELECT o.id,
       o.tenant_id,
       COALESCE(SUM(ct.amount_amount), 0)       AS amount,
       COALESCE(ct.amount_currency_code, 'RUB') AS currency_code
FROM organization o
         LEFT JOIN customer_transaction ct ON ct.operand_id = o.id
GROUP BY o.id, ct.amount_currency_code

UNION ALL

SELECT o.id,
       o.tenant_id,
       COALESCE(SUM(ct.amount_amount), 0)       AS amount,
       COALESCE(ct.amount_currency_code, 'RUB') AS currency_code
FROM person o
         LEFT JOIN customer_transaction ct ON ct.operand_id = o.id
GROUP BY o.id, ct.amount_currency_code

UNION ALL

SELECT w.id,
       w.tenant_id,
       COALESCE(SUM(wt.amount_amount), 0)       AS amount,
       COALESCE(wt.amount_currency_code, 'RUB') AS currency_code
FROM wallet w
         LEFT JOIN wallet_transaction wt ON wt.wallet_id = w.id
GROUP BY w.id, wt.amount_currency_code
