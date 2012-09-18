-- //

alter table tag_v add parent_tag_id mediumint unsigned;
update tag_v inner join tag_mptt on rid = tag_mptt.id set tag_v.parent_tag_id = tag_mptt.parent_id;

-- //@UNDO

alter table tag_v drop parent_id;

-- //