<?php

abstract class Controller_Cms_Tags extends Boom\Controller
{
    /**
	 *
	 * @var array
	 */
    public $ids = [];

    public $model;

    public $tags;

    /**
	 *
	 * @var integer
	 */
    public $type;

    protected $tag;
}
