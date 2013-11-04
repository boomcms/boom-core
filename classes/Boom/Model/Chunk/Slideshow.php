<?php defined('SYSPATH') OR die('No direct script access.');
/**
 * Slideshow chunk model
 *
 * @package	BoomCMS
 * @category	Chunks
 * @category	Models
 *
 */
class Boom_Model_Chunk_Slideshow extends Model_Chunk
{
	/**
	* Properties to create relationships with Kohana's ORM
	*/
	protected $_has_many = array(
		'slides' => array('model' => 'Chunk_Slideshow_Slide', 'foreign_key' => 'chunk_id'),
	);

	protected $_slides;

	protected $_table_columns = array(
		'title'		=>	'',
		'id'		=>	'',
		'slotname'	=>	'',
		'page_vid' => '',
	);

	protected $_table_name = 'chunk_slideshows';

	public function copy()
	{
		$new = parent::copy();

		$slides = array();
		foreach ($this->slides() as $s)
		{
			$slides[] = $s->object();
		}

		return $new->slides($slides);
	}

	public function create(Validation $validation = NULL)
	{
		parent::create($validation);

		$this->save_slides();
		return $this;
	}

	public function filters()
	{
		return array(
			'title'	=> array(
				array('strip_tags'),
			)
		);
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
				$this->_slides = $this
					->slides
					->with('asset')
					->find_all()
					->as_array();
			}

			return $this->_slides;
		}
		else
		{
			// If the slides are arrays of data then turn them into Chunk_Slideshow_Slides objects.
			foreach ($slides as & $slide)
			{
				if ( ! $slide instanceof Model_Chunk_Slideshow_Slide)
				{
					$slide = ORM::factory('Chunk_Slideshow_Slide')
						->values( (array) $slide);
				}
			}
			$this->_slides = $slides;

			return $this;
		}
	}

	/**
	 * Persists slide data to the database.
	 *
	 * @return \Boom_Model_Chunk_Slideshow
	 */
	public function save_slides()
	{
		// Remove all existing slides.
		DB::delete('chunk_slideshow_slides')
			->where('chunk_id', '=', $this->id)
			->execute();

		foreach ( (array) $this->_slides as $slide)
		{
			$slide->chunk_id = $this->id;

			try
			{
				$slide->save();
			}
			catch (Exception $e) {}
		}

		return $this;
	}

	public function thumbnail()
	{
		if ($this->_slides === NULL)
		{
			return $this->slides
				->with('asset')
				->find()
				->asset;
		}
		else
		{
			return $this->_slides[0]->asset;
		}
	}
}