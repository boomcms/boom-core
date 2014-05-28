<?php

namespace Boom\Asset\Type;

use Boom\Asset as Asset;

class Image extends Asset
{
	public function getAspectRatio()
	{
		return ($this->getHeight() > 0)? ($this->getWidth() / $this->getHeight()) : 1;
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
}