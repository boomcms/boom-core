-- //

alter table person_group drop primary key;
alter table person_group add id smallint unsigned auto_increment primary key;
create unique index person_group_person_id_group_id on person_group(person_id, group_id);

-- //@UNDO

-- //