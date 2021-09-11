SELECT o.id,
       o.tenant_id,
       o.name                     AS full_name,
       COALESCE(balance.money, 0) AS balance,
       o.email,
       o.telephone,
       o.office_phone,
       o.seller,
       o.contractor,
       o.address,
       o.requisite_bank,
       o.requisite_bik,
       o.requisite_inn,
       o.requisite_kpp,
       o.requisite_ks,
       o.requisite_rs,
       o.requisite_legal_address,
       o.requisite_ogrn
FROM organization o
         LEFT JOIN (
    SELECT ct.operand_id         AS id,
           SUM(ct.amount_amount) AS money
    FROM customer_transaction ct
    GROUP BY ct.operand_id, ct.amount_currency_code
) balance ON balance.id = o.id
