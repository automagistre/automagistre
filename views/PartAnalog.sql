SELECT pcp.part_id, analog.part_id AS analog_id
FROM part_cross_part pcp
         CROSS JOIN LATERAL (SELECT pcp2.part_id
                             FROM part_cross_part pcp2
                             WHERE pcp.part_cross_id = pcp2.part_cross_id
                               AND pcp.part_id <> pcp2.part_id) analog
