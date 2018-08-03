
DELIMITER $$

DROP PROCEDURE IF EXISTS `sp_gmf_sys_change_table_character`$$


CREATE PROCEDURE `sp_gmf_sys_change_table_character`(IN p_CHARACTER CHAR(200) ,IN p_COLLATE CHAR(200))    
BEGIN 
  /*
  ALTER DATABASE tableName DEFAULT CHARACTER SET utf8mb4  COLLATE utf8mb4_unicode_ci;
  */
  DECLARE v_table_name VARCHAR(100);	-- 定义接收游标数据的变量 
  DECLARE SQL_FOR_SELECT VARCHAR(1000); -- 定义接收游标数据的变量 
  DECLARE done INT DEFAULT FALSE; -- 遍历数据结束标志
  DECLARE v_db VARCHAR(100) DEFAULT DATABASE(); -- 遍历数据结束标志
  
  DECLARE cur CURSOR FOR (SELECT table_name FROM information_schema.`TABLES` WHERE TABLE_SCHEMA = v_db);  -- 游标
  DECLARE CONTINUE HANDLER FOR NOT FOUND SET done = TRUE;  -- 将结束标志绑定到游标
  
  IF LENGTH(p_CHARACTER)<=1 OR LENGTH(p_COLLATE)<=1 THEN
	SET p_CHARACTER='utf8mb4';
	SET p_COLLATE='utf8mb4_unicode_ci';
  END  IF;
  
  

  IF LENGTH(p_CHARACTER)>1 AND LENGTH(p_COLLATE)>1 AND LENGTH(v_db)>1 THEN 
	
	
	 -- 打开游标
	  OPEN cur; 
			-- 开始循环（loop循环）
			read_loop: LOOP
				-- 提取游标里的数据，这里只有一个，多个的话也一样；
				FETCH cur INTO v_table_name;
				
				-- 声明结束的时候
				IF done THEN
					LEAVE read_loop;
				END IF;
				-- 要循环的事件，使用了动态sql拼接alter语句，直接写的话报错	
				SET SQL_FOR_SELECT = CONCAT("alter table ", v_table_name, " convert to character set ",p_CHARACTER,"  COLLATE ",p_COLLATE); -- 拼接
				SET @sql = SQL_FOR_SELECT;  
				PREPARE stmt FROM @sql; 	-- 预处理
				EXECUTE stmt;  		-- 执行
				DEALLOCATE PREPARE stmt;	-- 释放prepare
	 
			END LOOP;
	 
	  -- 关闭游标
	  CLOSE cur;
  END  IF;
 
 
END$$

DELIMITER ;