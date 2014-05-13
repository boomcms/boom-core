<?php

use \Boom\TextFilter\Dispatcher as TextFilter;
use \Boom\TextFilter\Filter;

class Boom_Model_Chunk_Text extends ORM
{
	protected $_table_columns = array(
		'text'		=>	'',
		'id'		=>	'',
		'slotname'	=>	'',
		'page_vid' => '',
		'is_block'	=>	'',
		'site_text' => '',
	);

	protected $_table_name = 'chunk_texts';

	/**
	 *
	 * @return string
	 */
	public function clean_text()
	{
		$rules = Kohana::$config->load('text')->get('clean');

		foreach ($rules as $property => $values)
		{
			foreach ($values as $value => $functions)
			{
				if ($this->$property == $value)
				{
					foreach ($functions as $func)
					{
						$this->_object['text'] = call_user_func($func, $this->_object['text']);
					}
					
				}
			}
		}

		return $this;
	}

	/**
	 * When creating a text chunk log which assets are linked to from it.
	 *
	 * @param	Validation $validation
	 * @return 	Boom_Model_Chunk_Text
	 */
	public function create(Validation $validation = null)
	{
		$dispatcher = new TextFilter;
		$dispatcher->addFilter(new Filter\MungeAssetLinks);

		$this->_object['text'] = $dispatcher->filterText($this->_object['text']);

		// Find which assets are linked to within the text chunk.
		preg_match_all('~hoopdb://((image)|(asset))/(\d+)~', $this->_object['text'], $matches);

		$dispatcher
			->addFilter(new Filter\OEmbed)
			->addFilter(new Filter\StorifyEmbed);

		$this->site_text = $dispatcher->filterText($this->_object['text']);

		// Create the text chunk.
		parent::create($validation);

		// Are there any asset links?
		if ( ! empty($matches[4]))
		{
			$assets = array_unique($matches[4]);

			// Log which assets are being referenced with a multi-value insert.
			$query = DB::insert('chunk_text_assets', array('chunk_id', 'asset_id', 'position'));

			foreach ($assets as $i => $asset_id)
			{
				$query->values(array($this->id, $asset_id, $i));
			}

			try
			{
				$query->execute();
			}
			catch (Database_Exception $e)
			{
				// Don't let database failures in logging prevent the chunk from being saved.
				Kohana_Exception::log($e);
			}
		}

		return $this;
	}

	public function filters()
	{
		return array(
			'text' => array(
				array(array($this, 'make_links_relative')),
			),
		);
	}

	public function make_links_relative($text)
	{
		return ($base = URL::base(Request::current()))? preg_replace("|<(.*?)href=(['\"])".$base."(.*?)(['\"])(.*?)>|", '<$1href=$2/$3$4$5>', $text) : $text;
	}

	/**
	 * Turns text chunk contents into HTML.
	 * e.g. replaces hoopdb:// links to <img> and <a> links
	 *
	 * @param string
	 * @return string
	 */
	public function unmunge($text)
	{
		$text = $this->unmunge_image_links_with_only_asset_id($text);
		$text = $this->unmunge_image_links_with_multiple_params($text);
		$text = $this->unmunge_non_image_asset_links($text);
		$text = $this->unmunge_page_links($text);

		return $text;
	}

	public function unmunge_image_links_with_only_asset_id($text)
	{
		return preg_replace('|hoopdb://image/(\d+)([\'"])|', '/asset/view/$1$2', $text);
	}

	public function unmunge_image_links_with_multiple_params($text)
	{
		return preg_replace('|hoopdb://image/(\d+)/|', '/asset/view/$1/', $text);
	}

	public function unmunge_non_image_asset_links($text)
	{
		return preg_replace_callback('|<a.*?href=[\'\"]hoopdb://asset/(\d+).*?</a>|', function($matches)
			{
				$asset_id = $matches[1];
				$asset = new Model_Asset($asset_id);

				if ($asset->loaded())
				{
					$text = "<p class='inline-asset'><a class='download ".strtolower(Boom_Asset::type($asset->type))."' href='/asset/view/{$asset->id}.{$asset->get_extension()}'>Download {$asset->title}</a>";

					if (\Boom\Editor::instance()->isDisabled())
					{
						$text .= " (".Text::bytes($asset->filesize)." ".ucfirst(Boom_Asset::type($asset->type)).")";
					}

					$text .= "</p>";
					return $text;
				}
			}, $text);
	}

	public function unmunge_page_links($text)
	{
		$text = preg_replace_callback('|hoopdb://page/(\d+)|',
			function ($match)
			{
				return new Model_Page_URL(array('page_id' => $match[1], 'is_primary' => true));
			},
			$text
		);

		return $text;
	}
}