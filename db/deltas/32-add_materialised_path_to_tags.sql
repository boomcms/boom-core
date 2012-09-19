-- //

alter table tag add path varchar(1000);

update tag inner join (select tt.id, concat( group_concat(tag_v.name separator '/'), '/', tx.name) as path from tag inner join tag_v on active_vid = tag_v.id inner join tag_mptt on tag.id = tag_mptt.id inner join tag_mptt as t on tag_mptt.lft < t.lft and tag_mptt.rgt > t.rgt inner join tag as tt on t.id = tt.id inner join tag_v as tx on tt.active_vid = tx.id group by t.id) as q on q.id = tag.id set tag.path = q.path;
drop table tag_mptt;

-- //@UNDO

-- //