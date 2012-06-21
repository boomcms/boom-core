-- //

create index page_v_internal_name on page_v(internal_name);
create index template_visible on template( visible );
create index tagged_objects_object_id_object_type on tagged_objects( object_id, object_type );

-- //@UNDO

alter table page_v drop index page_v_internal_name;
alter table template drop index template_visible;
alter table tagged_objects drop index tagged_objects_object_id_object_type;

-- //