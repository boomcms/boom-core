<?php

namespace BoomCMS\Contracts;

interface LinkableInterface
{
    public function getFeatureImageId(): int;

    public function getTitle(): string;

    public function url();

    public function hasFeatureImage(): bool;
}
