-- //

create index slideshowimages_chunk_id on slideshowimages(chunk_id);
create index changelog_delta_set on changelog(delta_set);

-- //@UNDO

alter table slideshowimages drop index slideshowimages_chunk_id;
alter table changelog drop index changelog_delta_set;

-- //