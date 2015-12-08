<?php

namespace BoomCMS\Settings;

use Illuminate\Filesystem\Filesystem;

class Store implements \ArrayAccess
{
    protected $filename;

    /**
     * @var Filesystem
     */
    protected $filesystem;

    /**
     * @var array
     */
    protected $settings;

    public function __construct(Filesystem $filesystem)
    {
        $this->filename = storage_path().'/boomcms/settings.json';
        $this->filesystem = $filesystem;
        $this->settings = $this->loadSettings();
    }

    protected function loadSettings()
    {
        if ($this->filesystem->exists($this->filename)) {
            $settings = $this->filesystem->get($this->filename);

            return $settings ? (array) json_decode($settings) : [];
        } else {
            return [];
        }
    }

    public function exists($key)
    {
        return isset($this->settings[$key]) || array_key_exists($key, $this->settings);
    }

    public function delete($key)
    {
        if ($this->exists($key)) {
            unset($this->settings[$key]);
        }

        return $this;
    }

    public function get($key, $default = null)
    {
        return $this->exists($key) ? $this->settings[$key] : $default;
    }

    public function getAllSettings()
    {
        return $this->settings;
    }

    public function replaceAll(array $settings)
    {
        $this->settings = $settings;
        $this->save();

        return $this;
    }

    protected function save()
    {
        $this->filesystem->put($this->filename, json_encode($this->settings));
    }

    public function set($key, $value = null)
    {
        if (is_array($key)) {
            $this->settings = array_merge($this->settings, $key);
        } else {
            $this->settings[$key] = $value;
        }

        $this->save();

        return $this;
    }

    public function offsetExists($offset)
    {
        return $this->exists($offset);
    }

    public function offsetGet($offset)
    {
        return $this->get($offset);
    }

    public function offsetSet($offset, $value)
    {
        return $this->set($offset, $value);
    }

    public function offsetUnset($offset)
    {
        return $this->delete($offset);
    }
}
