-- //

alter table `chunk_asset` add `url` varchar(255);

-- //@UNDO

alter table `chunk_asset` drop `url`;

-- //
