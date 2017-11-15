UPDATE  `gmf_sys_entity_fields` AS l
INNER JOIN `gmf_sys_entities` AS lt ON l.type_type=lt.name
SET l.type_id=lt.id,l.type_enum=lt.type
WHERE l.type_id<>lt.id OR l.type_enum<>lt.type OR l.type_id IS NULL OR l.type_enum IS NULL;