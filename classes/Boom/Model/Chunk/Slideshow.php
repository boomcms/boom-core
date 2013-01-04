<?php defined('SYSPATH') OR die('No direct script access.');
/**
 * Slideshow chunk model
 *
 * @package	BoomCMS
 * @category	Chunks
 * @category	Models
 * @author	Rob Taylor
 * @copyright	Hoop Associates
 *
 */
class Boom_Model_Chunk_Slideshow extends ORM
{
	/**
	* Properties to create relationships with Kohana's ORM
	*/
	protected $_has_many = array(
		'slides' => array('model' => 'Chunk_Slideshow_Slide', 'foreign_key' => 'chunk_id'),
	);
	protected $_load_with = array('slides');

	protected $_slides;

	protected $_table_columns = array(
		'title'		=>	'',
		'id'		=>	'',
		'slotname'	=>	'',
	);

	protected $_table_name = 'chunk_slideshows';

	public function create(Validation $validation = NULL)
	{
		parent::create($validation);

		// Remove all existing slides.
		DB::delete('chunk_slideshow_slides')
			->where('chunk_id', '=', $this->id)
			->execute();

		foreach ($this->_slides as $slide)
		{
			$slide->chunk_id = $this->chunk_id;
			$slide->create();
		}

		return $this;
	}

	/**
	* Sets or gets the slideshows slides
	*
	*/
	public function slides($slides = NULL)
	{
		if ($slides === NULL)
		{
			if ($this->_slides === NULL)
			{
				$this->_slides = $this->slides->find_all();
			}

			return $this->_slides;
		}
		else
		{
			// If the slides are arrays of data then turn them into Chunk_Slideshow_Slides objects.
			foreach ($slides as & $slide)
			{
				$slide = ORM::factory('Chunk_Slideshow_Slide')
					->values($slide);
			}
			$this->_slides = $slides;

			return $this;
		}
	}
}