-- //

alter table asset_v add duration int unsigned;
alter table asset_v add encoded boolean default true;
alter table asset_v add views int unsigned;

-- //@UNDO

alter table asset_v drop duration;
alter table asset_v drop encoded;
alter table asset_v drop views;

-- //