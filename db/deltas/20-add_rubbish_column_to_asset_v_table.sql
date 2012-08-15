-- //

alter table asset_v add rubbish boolean default false;
create index asset_v_rubbish on asset_v(rubbish);

-- //@UNDO

alter table asset_v drop rubbish;

-- //