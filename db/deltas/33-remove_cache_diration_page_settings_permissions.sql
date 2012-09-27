-- //

delete permissions.* from permissions inner join actions on actions.id = action_id where actions.name = 'view_cache_duration';
delete permissions.* from permissions inner join actions on actions.id = action_id where actions.name = 'edit_cache_duration';
delete from actions where name = 'view_cache_duration';
delete from actions where name = 'edit_cache_duration';

-- //@UNDO

-- //