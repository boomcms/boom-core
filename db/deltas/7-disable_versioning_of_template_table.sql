-- //

delete template_v.* from template_v left join template on template_v.id = template.active_vid where template.id is null;
drop table template;
alter table template_v rename to template;
alter table template drop id;
alter table template change rid id tinyint(3) unsigned primary key auto_increment;
alter table template drop audit_person;
alter table template drop audit_time;
alter table template drop deleted;

-- //@UNDO


-- //