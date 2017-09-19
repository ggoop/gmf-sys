UPDATE  `gmf_sys_entity_fields` AS l
INNER JOIN `gmf_sys_entities` AS lt ON l.type_id=lt.name
SET l.type_id=lt.id 
WHERE l.id<>''

