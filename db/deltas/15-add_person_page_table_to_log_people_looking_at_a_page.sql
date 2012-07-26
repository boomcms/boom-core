-- //

create table person_page(person_id smallint(5) unsigned, page_id smallint(5) unsigned, since int(10) unsigned, last_active int(10) unsigned, saved boolean default false, primary key(person_id, page_id));
create unique index person_page_person_id on person_page(person_id);

-- //@UNDO

drop table person_page;

-- //