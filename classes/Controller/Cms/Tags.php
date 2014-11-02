<?php

abstract class Controller_Cms_Tags extends Boom\Controller
{
    /**
	 *
	 * @var array
	 */
    public $ids = array();

    public $model;

    public $tags;

    /**
	 *
	 * @var integer
	 */
    public $type;

    protected $tag;
}
