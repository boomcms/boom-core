-- //

alter table person_page drop primary_key;
alter table person_page add primary key(person_id);

-- //@UNDO



-- //