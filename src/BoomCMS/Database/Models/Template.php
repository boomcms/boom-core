<?php

namespace BoomCMS\Database\Models;

use BoomCMS\Contracts\Models\Template as TemplateInterface;
use BoomCMS\Core\Theme\Theme;
use BoomCMS\Support\Traits\Comparable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\View;

class Template extends Model implements TemplateInterface
{
    use Comparable;

    const ATTR_ID = 'id';
    const ATTR_NAME = 'name';
    const ATTR_FILENAME = 'filename';
    const ATTR_DESCRIPTION = 'description';
    const ATTR_THEME = 'theme';

    protected $table = 'templates';

    protected $appends = ['file_exists'];

    protected $guarded = [
        self::ATTR_ID,
    ];

    public $timestamps = false;

    /**
     * @return bool
     */
    public function fileExists()
    {
        return View::exists($this->getFullFilename());
    }

    /**
     * @return string
     */
    public function getDescription()
    {
        return $this->{self::ATTR_DESCRIPTION};
    }

    /**
     * Returns the file_exists attribute for the JSON form.
     *
     * @return int
     */
    public function getFileExistsAttribute()
    {
        return (int) $this->fileExists();
    }

    /**
     * @return string
     */
    public function getFilename()
    {
        return $this->{self::ATTR_FILENAME};
    }

    /**
     * @return int
     */
    public function getId()
    {
        return  (int) $this->{self::ATTR_ID};
    }

    /**
     * @return Theme
     */
    public function getTheme()
    {
        return new Theme($this->getThemeName());
    }

    /**
     * @return string
     */
    public function getThemeName()
    {
        return $this->{self::ATTR_THEME};
    }

    /**
     * @return string
     */
    public function getFullFilename()
    {
        return $this->getTheme()->getName().'::templates.'.$this->getFilename();
    }

    /**
     * @return string
     */
    public function getName()
    {
        return $this->{self::ATTR_NAME};
    }

    public function getView()
    {
        return ($this->fileExists()) ? View::make($this->getFullFilename()) : View::make('boomcms::templates.default');
    }

    /**
     * @return string
     */
    public function getViewName()
    {
        return $this->getThemeName().':'.'templates.'.$this->getFilename();
    }

    /**
     * @param string $description
     *
     * @return $this
     */
    public function setDescription($description)
    {
        $this->{self::ATTR_DESCRIPTION} = $description;

        return $this;
    }

    /**
     * @param string $filename
     *
     * @return $this
     */
    public function setFilename($filename)
    {
        $this->{self::ATTR_FILENAME} = $filename;

        return $this;
    }

    /**
     * @param string $name
     *
     * @return $this
     */
    public function setName($name)
    {
        $this->{self::ATTR_NAME} = $name;

        return $this;
    }
}
