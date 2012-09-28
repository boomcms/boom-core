-- //

alter table page_v add published boolean default false;
create index page_v_published_rid on page_v(publisheh, rid);
update page_v inner join page on page_v.id = page.published_vid set published = true;

-- //@UNDO

alter table page_v drop published;

-- //