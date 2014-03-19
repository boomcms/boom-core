<?php

/**
 * Returns a string of text with all transformations applied to display the text in the site view.
 * e.g. with external resources embedded such as tweets and videos.
 * 
 */
class Boom_SiteText
{
	protected $_original_text;

	public function __construct($text)
	{
		$this->_original_text = $text;
	}

	public function __toString()
	{
		return $this->execute();
	}

	protected function _do_embed_storify($text)
	{
		return preg_replace("/\<p\>(https?\:\/\/(?:www\.)?storify\.com\/(?:[^\/]+)\/(?:[^\/]+))\/?\<\/p\>/i", '<script type="text/javascript" src="${1}.js"></script>', $text);
	}

	protected function _do_oembed($text)
	{
		require_once Kohana::find_file('vendor', 'embera/Lib/Embera/Autoload');
		$embera = new \Embera\Embera();
		return $embera->autoEmbed($text);
	}

	public function execute()
	{
		$text = $this->_original_text;
		$text = $this->_do_embed_storify($text);
		$text = $this->_do_oembed($text);

		return $text;
	}
}
