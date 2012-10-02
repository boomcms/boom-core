-- //

drop table tag_mptt;
alter table tag change parent_tag_id parent_id int unsigned;

-- //@UNDO


-- //