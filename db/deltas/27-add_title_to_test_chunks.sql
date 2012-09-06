-- //

alter table chunk_text add title varchar(50);

-- //@UNDO

alter table chunk_text drop title;

-- //