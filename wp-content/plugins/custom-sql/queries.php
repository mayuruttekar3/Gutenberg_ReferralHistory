<?php
// Which query would you write to find every duplicate in a table? You should mention possible methods.

Answer 1 : We can possiblty write two method

1) Using GROUP BY and HAVING clause

SELECT column, COUNT(*) as no_count FROM table_name 
GROUP BY column
HAVING COUNT(*) > 1;

Example : 

SELECT first_name, COUNT(*) as no_of_first FROM wp_users
GROUP BY first_name
HAVING COUNT(*) > 1;

2) Using Self Join : Join table to it self. basically for duplicated entry we used it if you dont work with wordpress 

Example:

SELECT a.* FROM wp_users a
JOIN wp_users b ON a.email = b.email AND a.id < b.id;

This query output the duplicate emails of same wp_users table

