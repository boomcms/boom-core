-- //

create index tagged_objects_object_type_object_id on tagged_objects(object_type, object_id);

-- //@UNDO

alter table tagged_objects drop index tagged_objects_object_type_object_id;

-- //