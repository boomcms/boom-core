-- //

alter table person_page drop index person_page_person_id;
alter table person_page drop index person_page_page_id_person_id_last_active;
create index person_page_page_id_person_id_last_active on person_page(person_id, page_id, last_active);

-- //@UNDO



-- //