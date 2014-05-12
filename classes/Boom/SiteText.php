<?php

namespace Boom;

/**
 * Returns a string of text with all transformations applied to display the text in the site view.
 * e.g. with external resources embedded such as tweets and videos.
 * 
 */
class SiteText
{
	protected $_originalText;

	public function __construct($text)
	{
		$this->_originalText = $text;
	}

	public function __toString()
	{
		return $this->execute();
	}

	protected function _doEmbedStorify($text)
	{
		return \preg_replace("/\<p\>(https?\:\/\/(?:www\.)?storify\.com\/(?:[^\/]+)\/(?:[^\/]+))\/?\<\/p\>/i", '<script type="text/javascript" src="${1}.js"></script>', $text);
	}

	protected function _doEmbed($text)
	{
		require_once \Kohana::find_file('vendor', 'embera/Lib/Embera/Autoload');
		$embera = new \Embera\Embera();
		return $embera->autoEmbed($text);
	}

	public function execute()
	{
		$text = $this->_originalText;
		$text = $this->_doEmbedStorify($text);
		$text = $this->_doEmbed($text);

		return $text;
	}
}