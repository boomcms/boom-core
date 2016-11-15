<?php

namespace BoomCMS\Observers;

use BoomCMS\Foundation\Database\Model;
use Illuminate\Contracts\Auth\Guard;

class DeletionLogObserver
{
    /**
     * @var Guard
     */
    protected $guard;

    /**
     * @param Guard $guard
     */
    public function __construct(Guard $guard)
    {
        $this->guard = $guard;
    }

    /**
     * Set created_by on the model prior to deletion
     *
     * @param  Model  $model
     *
     * @return void
     */
    public function deleting(Model $model)
    {
        $model->deleted_by = $this->guard->check() ? $this->guard->user()->getId() : null;
    }
}
