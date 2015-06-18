<?php

namespace BoomCMS\Core\Models\Chunk;

class Slideshow extends BaseChunk
{
    protected $_has_many = [
        'slides' => ['model' => 'Chunk_Slideshow_Slide', 'foreign_key' => 'chunk_id'],
    ];

    protected $_slides;

    protected $table = 'chunk_slideshows';

    public function create(Validation $validation = null)
    {
        parent::create($validation);

        $this->save_slides();

        return $this;
    }

    public function filters()
    {
        return [
            'title'    => [
                ['strip_tags'],
            ]
        ];
    }

    /**
	 * Sets or gets the slideshows slides
	 *
	 */
    public function slides($slides = null)
    {
        if ($slides === null) {
            if ($this->_slides === null) {
                $this->_slides = $this
                    ->slides
                    ->with('asset')
                    ->find_all()
                    ->as_array();
            }

            return $this->_slides;
        } else {
            // If the slides are arrays of data then turn them into Chunk_Slideshow_Slides objects.
            foreach ($slides as & $slide) {
                if ( ! $slide instanceof Model_Chunk_Slideshow_Slide && isset($slide['asset_id']) && $slide['asset_id'] > 0) {
                    $slide['url'] = (isset($slide['page']) && $slide['page'] > 0) ? $slide['page'] : isset($slide['url']) ? $slide['url'] : null;

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

        foreach ( (array) $this->_slides as $slide) {
            if (is_object($slide) && $slide instanceof Model_Chunk_Slideshow_Slide) {
                $slide->chunk_id = $this->id;

                try {
                    $slide->save();
                } catch (Exception $e) {}
            }
        }

        return $this;
    }

    public function thumbnail()
    {
        if ($this->_slides === null) {
            return $this->slides
                ->with('asset')
                ->find()
                ->asset;
        } else {
            return $this->_slides[0]->asset;
        }
    }
}
