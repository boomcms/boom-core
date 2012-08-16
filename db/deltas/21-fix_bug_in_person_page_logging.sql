-- //

alter table person_page drop primary key;
alter table person_page add (id smallint unsigned auto_increment primary key);
create unique index person_page_person_id on person_page(person_id);

-- //@UNDO


-- //