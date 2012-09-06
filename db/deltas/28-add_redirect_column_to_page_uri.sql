-- //

alter table page_uri add redirect boolean default true;

-- //@UNDO

alter table page_uri drop redirect;

-- //