-- //

alter table `slideshowimages` add `caption` varchar(100);
alter table `slideshowimages` add `title` varchar(20);

-- //@UNDO

alter table `slideshowimages` drop `title`;
alter table `slideshowimages` drop `caption`;

-- //