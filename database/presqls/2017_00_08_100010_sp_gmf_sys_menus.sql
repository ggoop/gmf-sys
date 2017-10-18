
DELIMITER $$

DROP PROCEDURE IF EXISTS `sp_gmf_sys_menus`$$


CREATE PROCEDURE `sp_gmf_sys_menus`(IN p_ent CHAR(200),IN p_user CHAR(200),IN p_tag CHAR(200))    
BEGIN

DECLARE v_level INT DEFAULT 0;

DROP TEMPORARY TABLE IF EXISTS temp_menus_data;
CREATE TEMPORARY TABLE IF NOT EXISTS temp_menus_data(
`root_id` NVARCHAR(100),
`parent_id` NVARCHAR(100),
  `id` NVARCHAR(100),
  `code` NVARCHAR(100),
  `name` NVARCHAR(100),
  `level` INT DEFAULT 0
);

DROP TEMPORARY TABLE IF EXISTS temp_opinion_data;
CREATE TEMPORARY TABLE IF NOT EXISTS temp_opinion_data(
  `menu_id` NVARCHAR(100),
  `opinion_enum` NVARCHAR(100)
);

INSERT INTO temp_opinion_data(menu_id,opinion_enum)
SELECT rm.menu_id,rm.opinion_enum FROM `gmf_sys_authority_role_users` AS ru
INNER JOIN `gmf_sys_authority_roles` AS r ON ru.role_id=r.id
INNER JOIN `gmf_sys_authority_role_menus` AS rm ON ru.role_id=rm.role_id
WHERE ru.user_id=p_user AND ru.ent_id=p_ent AND rm.ent_id=p_ent 
 AND ru.`is_revoked`=0 AND rm.`is_revoked`=0 AND r.`is_revoked`=0
GROUP BY rm.menu_id,rm.opinion_enum;

INSERT INTO temp_menus_data(root_id,parent_id,id,CODE,NAME)
SELECT m.root_id,m.parent_id,m.id,m.code,m.name
FROM gmf_sys_menus AS m
WHERE m.tag=p_tag AND m.is_leaf=1
AND m.id IN (SELECT menu_id FROM temp_opinion_data AS d WHERE d.opinion_enum='permit');

DELETE FROM temp_menus_data WHERE id IN (SELECT menu_id FROM temp_opinion_data AS d WHERE d.opinion_enum!='permit');

/*上1级*/
WHILE v_level>=0 DO 
	INSERT INTO temp_menus_data(root_id,parent_id,id,`code`,`name`,`level`)
	SELECT m.root_id,m.parent_id,m.id,m.code,m.name,v_level+1
	FROM gmf_sys_menus AS m
	WHERE m.id IN (SELECT d.parent_id FROM temp_menus_data AS d WHERE d.id!=d.root_id AND d.level=v_level AND d.parent_id IS NOT NULL);

	IF ROW_COUNT()=0 THEN
		SET v_level=-2;
	END IF;
	SET v_level=v_level+1;
END WHILE;

SELECT m.root_id,m.parent_id,m.id,m.code,m.name,m.uri,m.sequence,m.memo,m.icon,m.style,m.params
 FROM `gmf_sys_menus` AS m WHERE m.id IN (SELECT d.id FROM temp_menus_data AS d )
ORDER BY m.sequence,m.root_id,m.parent_id,m.code;

END$$

DELIMITER ;