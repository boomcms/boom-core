-- //

alter table chunk drop audit_person;
alter table chunk drop audit_time;
alter table chunk drop deleted;
alter table page_v drop sitemap_priority;
alter table page_v drop sitemap_update_frequency;
alter table page_v drop search_priority;
alter table page_v drop default_child_sitemap_priority;
alter table page_v drop default_child_sitemap_update_frequency;
alter table asset_v drop crop_start_x;
alter table asset_v drop crop_start_y;
alter table asset_v drop crop_end_x;
alter table asset_v drop crop_end_y;
alter table asset_v drop synced;
alter table asset_v drop search_priority;
alter table tag_v drop can_be_slot_perm;
alter table tag_v drop hidden_from_tree;
alter table page drop created;
alter table page drop parent;
alter table page drop level;

-- //@UNDO


-- //