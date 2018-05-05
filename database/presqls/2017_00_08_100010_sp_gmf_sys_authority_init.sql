
DELIMITER $$

DROP PROCEDURE IF EXISTS `sp_gmf_sys_authority_init`$$


CREATE PROCEDURE `sp_gmf_sys_authority_init`(IN p_fm_ent CHAR(200),IN p_to_ent CHAR(200),IN p_user CHAR(200))    
BEGIN

INSERT INTO `gmf_sys_authority_roles`(id,ent_id,`code`,`name`,type_enum,created_at)
SELECT MD5(REPLACE(UUID_SHORT(),'-','')) AS id,p_to_ent AS ent_id,f.code,f.name,f.type_enum,NOW() AS created_at
FROM `gmf_sys_authority_roles` AS f
WHERE f.ent_id=p_fm_ent AND (f.type_enum IS NULL OR f.type_enum='admin') AND f.`revoked`=0
AND NOT EXISTS(SELECT d.id FROM gmf_sys_authority_roles AS d WHERE d.ent_id=p_to_ent AND (d.type_enum IS NULL OR d.type_enum='admin') AND f.code=d.code);

INSERT INTO `gmf_sys_authority_role_menus`(id,ent_id,`role_id`,`menu_id`,opinion_enum,created_at)
SELECT MD5(REPLACE(UUID_SHORT(),'-','')) AS id,p_to_ent AS ent_id,t.id AS role_id,fm.`menu_id`,fm.`opinion_enum`,NOW() AS created_at
FROM `gmf_sys_authority_roles` AS f
INNER JOIN `gmf_sys_authority_role_menus` AS fm ON f.id=fm.role_id
INNER JOIN gmf_sys_authority_roles AS t ON f.code=t.code AND t.ent_id=p_to_ent
WHERE f.ent_id=p_fm_ent AND (f.type_enum IS NULL OR f.type_enum='admin') AND f.`revoked`=0
AND NOT EXISTS(SELECT d.id FROM `gmf_sys_authority_role_menus` AS d  WHERE d.ent_id=p_to_ent AND d.role_id=t.id AND fm.menu_id=d.menu_id);

INSERT INTO `gmf_sys_authority_role_users`(id,ent_id,`role_id`,`user_id`,created_at)
SELECT MD5(REPLACE(UUID_SHORT(),'-','')) AS id,p_to_ent AS ent_id,t.id AS role_id,fu.`user_id`,NOW() AS created_at
FROM `gmf_sys_authority_roles` AS f
INNER JOIN `gmf_sys_authority_role_users` AS fu ON f.id=fu.role_id AND fu.user_id=p_user
INNER JOIN gmf_sys_authority_roles AS t ON f.code=t.code AND t.ent_id=p_to_ent
WHERE f.ent_id=p_fm_ent AND (f.type_enum IS NULL OR f.type_enum='admin') AND f.`revoked`=0
AND NOT EXISTS(SELECT d.id FROM `gmf_sys_authority_role_users` AS d  WHERE d.ent_id=p_to_ent AND d.role_id=t.id AND fu.user_id=d.user_id);


END$$

DELIMITER ;