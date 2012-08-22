-- //

delete from actions where name = 'view_visible_from';
delete from actions where name = 'edit_visible_from';
delete from actions where name = 'view_visible_to';
delete from actions where name = 'edit_visible_to';

-- //@UNDO

insert into actions (name, description, component) values ('edit_visibility', 'Edit the page visibility', 'sledge/main');

-- //