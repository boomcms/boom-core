-- //

alter table chunk_text_assets add position smallint unsigned;

-- //@UNDO

alter table chunk_text_assets drop position;

-- //