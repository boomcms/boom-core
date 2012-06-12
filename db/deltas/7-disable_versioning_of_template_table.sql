-- //

drop table template;
alter table template_v rename to template;
alter table template drop id;
alter table template change rid id tinyint(3) unsigned primary key auto_increment;
alter table template drop audit_person;
alter table template drop audit_time;

-- //@UNDO


-- //