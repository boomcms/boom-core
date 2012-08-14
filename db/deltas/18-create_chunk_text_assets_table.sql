-- //

create table chunk_text_assets (chunk_id mediumint(8) unsigned, asset_id smallint(5));
create unique index chunk_text_assets on chunk_text_assets(chunk_id, asset_id);
create index chunk_text_assets_chunk_id on chunk_text_assets(chunk_id);

-- //@UNDO

drop table chunk_text_assets;

-- //