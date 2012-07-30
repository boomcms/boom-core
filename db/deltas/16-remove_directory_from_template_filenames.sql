-- //

update template set filename = replace(filename, 'site/templates/', '');
update template set filename = replace(filename, '.php', '');
alter table template change filename filename varchar(25) not null;

-- //@UNDO

alter table template change filename filename varchar(150) not null;
update template set filename = concat('site/templates/', filename);

-- //