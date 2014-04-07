<?php

/**
 * Retrive an array of the assets which are used in a particular page version in asset chunks, slideshows, and inline in text chunks.
 *
 */
class Boom_Page_AssetsUsed
{
	protected $_version;
	protected $_query;

	public function __construct(Model_Page_Version $version)
	{
		$this->_version = $version;
		$this->_query = ORM::factory('Asset')->distinct(TRUE)->order_by('asset.id');
	}

	public function get_all()
	{
		return ORM::factory('Asset')

			// Asset chunks
			->join('chunk_assets', 'left')
			->on('asset.id', '=', 'chunk_assets.asset_id')
			->or_where('chunk_assets.page_vid', '=', $this->_version->id)

			// Text chunks
			->join('chunk_text_assets', 'left')
			->on('asset.id', '=', 'chunk_text_assets.asset_id')
			->join('chunk_texts', 'left')
			->on('chunk_text_assets.chunk_id', '=', 'chunk_texts.id')
			->or_where('chunk_texts.page_vid', '=', $this->_version->id)

			// Slideshows
			->join('chunk_slideshow_slides', 'left')
			->on('asset.id', '=', 'chunk_slideshow_slides.asset_id')
			->join('chunk_slideshows', 'left')
			->on('chunk_slideshow_slides.chunk_id', '=', 'chunk_slideshows.id')
			->or_where('chunk_slideshows.page_vid', '=', $this->_version->id)

			->distinct(TRUE)
			->order_by('asset.id')
			->find_all();
	}

	public function get_asset_chunks()
	{
		return ORM::factory('Asset')
			->join('chunk_assets', 'inner')
			->on('asset.id', '=', 'chunk_assets.asset_id')
			->where('chunk_assets.page_vid', '=', $this->_page->version()->id)
			->order_by('asset.id')
			->find_all();
	}

	public function get_text_chunks()
	{
		return ORM::factory('Asset')
			->join('chunk_text_assets', 'inner')
			->on('asset.id', '=', 'chunk_text_assets.asset_id')
			->join('chunk_texts', 'inner')
			->on('chunk_text_assets.chunk_id', '=', 'chunk_texts.id')
			->where('chunk_texts.page_vid', '=', $this->_page->version()->id)
			->order_by('asset.id')
			->find_all();
	}

	public function get_slideshows()
	{
		return ORM::factory('Asset')
			->join('chunk_slideshow_slides', 'inner')
			->on('asset.id', '=', 'chunk_slideshow_slides.asset_id')
			->join('chunk_slideshows', 'inner')
			->on('chunk_slideshow_slides.chunk_id', '=', 'chunk_slideshows.id')
			->where('chunk_slideshows.page_vid', '=', $this->_page->version()->id)
			->order_by('asset.id')
			->find_all();
	}

	/**
	 * Only retrieve assets of a particular type.
	 *
	 * Accepts a numberical asset type, e.g. Boom_Asset::IMAGE
	 *
	 * @param int $type
	 */
	public function set_type($type)
	{
		$this->_query->where('asset.type', '=', $type);

		return $this;
	}
}