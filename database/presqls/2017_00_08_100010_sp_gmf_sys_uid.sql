
DELIMITER $$

DROP PROCEDURE IF EXISTS `sp_gmf_sys_uid`$$

CREATE PROCEDURE `sp_gmf_sys_uid`(IN p_node CHAR(100),INOUT p_num BIGINT)    
BEGIN



DECLARE v_oid CHAR(100);
DECLARE v_len INT DEFAULT 0;

DECLARE v_num BIGINT DEFAULT 0;
DECLARE v_num_d CHAR(100);
DECLARE v_num_s CHAR(100);

IF IFNULL(p_node,'')='' THEN
SET p_node='sys';
END IF;
IF IFNULL(p_num,0)<=0 THEN
SET p_num=1;
END IF;
SELECT id,len,sn INTO v_oid,v_len,v_num FROM gmf_sys_uids WHERE node=p_node;

IF IFNULL(v_oid,'')='' THEN
  SET v_oid=MD5(REPLACE(UUID_SHORT(),'-','')) ;
  SET v_len=12;
  INSERT INTO gmf_sys_uids(id,node,len) VALUE (v_oid,p_node,v_len);
ELSEIF IFNULL(v_len,0)<7 THEN
  SET v_len=12;
  UPDATE gmf_sys_uids SET len=v_len WHERE id=v_oid;
END  IF;

IF v_num>0 THEN
	SET v_num_d=LEFT(v_num,6);
	SET v_num_s=RIGHT(v_num,v_len-6);
ELSE
	SET v_num_d=DATE_FORMAT(NOW(),'%y%m%d');
	SET v_num_s=0;
END IF;

SET v_num=CONCAT(v_num_d,LPAD(v_num_s+p_num,v_len-6,'0'));

SET p_num:=CONCAT(v_num_d,LPAD(v_num_s+1,v_len-6,'0'));

UPDATE gmf_sys_uids SET sn=v_num WHERE id=v_oid;

END$$

DELIMITER ;