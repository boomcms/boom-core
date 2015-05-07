<?php

namespace BoomCMS\Core\Asset\Type;

use BoomCMS\Core\Asset\Asset;

class Image extends Asset
{
    public function getAspectRatio()
    {
        return ($this->getHeight() > 0) ? ($this->getWidth() / $this->getHeight()) : 1;
    }

    public function getHeight()
    {
        return $this->model->height;
    }

    public function getType()
    {
        return "Image";
    }

    public function getWidth()
    {
        return $this->model->width;
    }

    /**
	 *
	 * @param int $height
	 * @return \Boom\Asset\Type\Image
	 */
    public function setHeight($height)
    {
        $this->model->height = $height;

        return $this;
    }

    /**
	 *
	 * @param int $width
	 * @return \Boom\Asset\Type\Image
	 */
    public function setWidth($width)
    {
        $this->model->width = $width;

        return $this;
    }
}
