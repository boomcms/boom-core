-- //

delete tag.* from tag inner join tag_v on active_vid = tag_v.id where deleted = true;
alter table tag add name varchar(255) not null;
alter table tag add parent_tag_id int unsigned;
update tag inner join tag_v on active_vid = tag_v.id set tag.name = tag_v.name, tag.parent_tag_id = tag_v.parent_tag_id;
alter table tag add type tinyint(1) unsigned not null;
update tag set type = 2;
update tag set type = 1 where path like 'Tags/Assets/%';
alter table tag drop active_vid;
drop table tag_v;
create index tag_name_type on tag(name, type);
create index tag_type_path on tag(type, path);

-- //@UNDO


-- //