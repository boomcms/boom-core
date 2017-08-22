<?php

namespace BoomCMS\Chunk;

use BoomCMS\Contracts\Models\Asset as AssetContract;
use BoomCMS\Link\Link as LinkObject;
use BoomCMS\Support\Facades\Asset as AssetFacade;
use Illuminate\Support\Facades\View;

class Asset extends BaseChunk
{
    /**
     * @var AssetContract
     */
    protected $asset;

    /**
     * @var string
     */
    private $filterByType;

    /**
     * @var LinkObject
     */
    private $link;

    protected function show()
    {
        return View::make($this->viewPrefix."asset.$this->template", [
            'assetId' => $this->getAssetId(),
            'caption' => $this->getCaption(),
            'title'   => $this->getTitle(),
            'link'    => $this->getLink(),
            'asset'   => function () {
                return $this->getAsset();
            },
        ]);
    }

    /**
     * Returns an array of attributes.
     *
     * @return array
     */
    public function attributes(): array
    {
        return [
            $this->attributePrefix.'target'       => $this->target(),
            $this->attributePrefix.'filterByType' => $this->filterByType,
        ];
    }

    /**
     * Returns the associated asset.
     *
     * @return AssetContract
     */
    public function getAsset()
    {
        if ($this->asset === null) {
            $this->asset = AssetFacade::find($this->getAssetId());
        }

        return $this->asset;
    }

    /**
     * Returns the associated asset ID.
     *
     * @return int
     */
    public function getAssetId(): int
    {
        $assetId = $this->attrs['asset_id'] ?? 0;

        return (int) $assetId;
    }

    /**
     * Returns the caption attribute.
     *
     * @return string
     */
    public function getCaption(): string
    {
        return $this->attrs['caption'] ?? '';
    }

    /**
     * Returns a LinkObject for the associated link.
     *
     * @return LinkObject
     */
    public function getLink()
    {
        if ($this->link === null && isset($this->attrs['url'])) {
            $this->link = LinkObject::factory($this->attrs['url']);
        }

        return $this->link;
    }

    /**
     * Returns the title attribute of the chunk.
     *
     * @return string
     */
    public function getTitle(): string
    {
        return$this->attrs['title'] ?? '';
    }

    /**
     * Whether the chunk has content.
     *
     * @return bool
     */
    public function hasContent(): bool
    {
        return !empty($this->getAssetId());
    }

    /**
     * Set which type of asset can be put into the chunk.
     * No validation is done but by default the asset picker will filter by this type.
     *
     * @param string $type An asset type. e.g. 'pdf', 'image'.
     */
    public function setFilterByType($type)
    {
        $this->filterByType = $type;

        return $this;
    }

    /**
     * Returns the ID of the asset in the chunk, or 0 if the chunk is empty.
     *
     * @return int
     */
    public function target(): int
    {
        return $this->hasContent() ? $this->getAssetId() : 0;
    }
}
