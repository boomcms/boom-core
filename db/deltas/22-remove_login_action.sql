-- //

delete from actions where name = 'login';

-- //@UNDO

insert into actions (name, description, component) values ('login', 'Login to the CMS', 'sledge/main');

-- //