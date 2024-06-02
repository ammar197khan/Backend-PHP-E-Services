DELIMITER $$
CREATE PROCEDURE drop_tables_like(pattern VARCHAR(255), db VARCHAR(255))
BEGIN
    SELECT @str_sql:=CONCAT('drop table ', GROUP_CONCAT(table_name))
    FROM information_schema.tables
    WHERE table_schema=db AND table_name LIKE pattern;

    PREPARE stmt from @str_sql;
    EXECUTE stmt;
    DROP prepare stmt;
END$$

DELIMITER ;

CALL drop_tables_like('%warehouse_parts', 'qqq');
CALL drop_tables_like('%warehouse_requests', 'qqq');
DROP PROCEDURE IF EXISTS `drop_tables_like`;
