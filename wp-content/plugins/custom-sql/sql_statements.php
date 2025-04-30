<?php

1).

SELECT clo.name AS clothes, col.name AS color, cust.first_name, cust.last_name
FROM clothing clo
JOIN color col ON clo.color_id = col.id
JOIN customer cust ON cust.favorite_color_id = col.id
JOIN clothing_order ord ON ord.clothing_id = clo.id AND ord.customer_id = cust.id
ORDER BY col.name ASC;

2).

SELECT main_distance,
    COUNT(CASE WHEN age < 20 THEN 1 END) AS under_20,
    COUNT(CASE WHEN age BETWEEN 20 AND 29 THEN 1 END) AS age_20_29,
    COUNT(CASE WHEN age BETWEEN 30 AND 39 THEN 1 END) AS age_30_39,
    COUNT(CASE WHEN age BETWEEN 40 AND 49 THEN 1 END) AS age_40_49,
    COUNT(CASE WHEN age >= 50 THEN 1 END) AS over_50
FROM runner
GROUP BY main_distance;
