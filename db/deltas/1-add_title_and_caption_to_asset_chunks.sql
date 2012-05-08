-- //

alter table `chunk_asset` change `text` `caption` varchar(100);
alter table `chunk_asset` add `title` varchar(20);

-- //@UNDO

alter table `chunk_asset` drop `title`;
alter table `chunk_asset` change `caption` `text` varchar(100);

-- //
