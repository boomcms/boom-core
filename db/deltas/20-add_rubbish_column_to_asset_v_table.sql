-- //

alter table asset_v add rubbish boolean default false;

-- //@UNDO

alter table asset_v drop rubbish;

-- //