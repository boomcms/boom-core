-- //

alter table chunk_page rename to page_chunk;

-- //@UNDO

alter table page_chunk rename to chunk_page;

-- //