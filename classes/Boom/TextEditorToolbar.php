<?php

class Boom_TextEditorToolbar
{
	protected $_config;
	protected $_html_before = '<div id="wysihtml5-toolbar" class="b-toolbar b-toolbar-vertical b-toolbar-text">';
	protected $_html_after = '</div>';

	protected $_button_set = 'text';

	public function __construct($button_set = null)
	{
		$button_set && $this->_button_set = $button_set;
		$this->_config = Kohana::$config->load('text_editor_toolbar');
	}

	public function __toString()
	{
		return (string) $this->render();
	}

	public function get_button($type)
	{
		return Arr::get($this->_config->get('buttons'), $type);
	}

	public function get_buttons()
	{
		return Arr::get($this->_config->get('button_sets'), $this->_button_set);
	}

	public function render()
	{
		return $this->_html_before.$this->_show_buttons().$this->_html_after;
	}

	protected function _show_buttons()
	{
		$buttons = '';

		foreach ($this->get_buttons() as $type)
		{
			list($text, $attrs) = $this->get_button($type);
			$buttons .= BoomUI::button($type, $text, (array) $attrs);
		}

		return $buttons;
	}
}