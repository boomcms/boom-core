<?php

namespace BoomCMS\Observers;

use BoomCMS\Foundation\Database\Model;
use Illuminate\Contracts\Auth\Guard;

class CreationLogObserver
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
     * Set created_at and created_by on the model prior to creation
     *
     * @param  Model  $model
     *
     * @return void
     */
    public function creating(Model $model)
    {
        $model->created_at = time();
        $model->created_by = $this->guard->check() ? $this->guard->user()->getId() : null;
    }
}
