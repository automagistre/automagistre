SELECT w.id,
       w.tenant_id,
       w.name,
       w.currency_code,
       w.default_in_manual_transaction,
       w.show_in_layout,
       w.use_in_income,
       w.use_in_order,
       COALESCE(balance.money, 0) AS balance
FROM wallet w
         LEFT JOIN (
    SELECT wt.wallet_id          AS id,
           SUM(wt.amount_amount) AS money
    FROM wallet_transaction wt
    GROUP BY wt.wallet_id, wt.amount_currency_code
) balance ON balance.id = w.id
