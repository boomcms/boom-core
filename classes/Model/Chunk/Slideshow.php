<?php

class Model_Chunk_Slideshow extends ORM
{
    protected $_has_many = [
        'slides' => ['model' => 'Chunk_Slideshow_Slide', 'foreign_key' => 'chunk_id'],
    ];

    protected $_slides;

    protected $_table_columns = [
        'title'        =>    '',
        'id'        =>    '',
        'slotname'    =>    '',
        'page_vid' => '',
    ];

    protected $_table_name = 'chunk_slideshows';

    public function copy($from_version_id)
    {
        $subquery = DB::select(DB::expr($this->id), 'asset_id', 'url', 'caption', 'chunk_slideshow_slides.title')
            ->from('chunk_slideshow_slides')
            ->join('chunk_slideshows', 'inner')
            ->on('chunk_slideshows.id', '=', 'chunk_slideshow_slides.chunk_id')
            ->where('slotname', '=', $this->slotname)
            ->where('page_vid', '=', $from_version_id);

        DB::insert('chunk_slideshow_slides', ['chunk_id', 'asset_id', 'url', 'caption', 'title'])
            ->select($subquery)
            ->execute($this->_db);
    }

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
