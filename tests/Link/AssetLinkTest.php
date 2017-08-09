<?php

namespace BoomCMS\Tests\Link;

use BoomCMS\Database\Models\Asset;
use BoomCMS\Link\AssetLink as Link;
use BoomCMS\Support\Facades\Asset as AssetFacade;
use Illuminate\Support\Facades\Auth;

class AssetLinkTest extends InternalTest
{
    protected $linkClass = Link::class;
    protected $objectClass = Asset::class;

    public function testGetFeatureImageIdReturnsLinkAssetThumbnail()
    {
        $asset = new Asset([Asset::ATTR_THUMBNAIL_ID => 1]);
        $link = new Link($asset);

        $this->assertEquals($asset->getThumbnailAssetId(), $link->getFeatureImageId());
    }

    public function testGetFeatureImageIdReturnsAssetId()
    {
        $asset = new Asset();
        $asset->{Asset::ATTR_ID} = 1;

        $link = new Link($asset);

        $this->assertEquals($asset->getId(), $link->getFeatureImageId());
    }

    public function testGetAssetWithInjectedAsset()
    {
        $asset = new Asset();
        $link = new Link($asset);

        $this->assertEquals($asset, $link->getAsset());
    }

    public function testGetAssetFromAssetUrl()
    {
        $assetId = 1;
        $asset = new Asset();

        AssetFacade::shouldReceive('find')->once()->with($assetId)->andReturn($asset);

        $link = new Link("/asset/$assetId");

        $this->assertEquals($asset, $link->getAsset());
    }

    public function testGetTitleReturnsAssetTitle()
    {
        $attrs = [
            ['title' => null],
            ['title' => ''],
            [],
        ];

        foreach ($attrs as $a) {
            $title = 'asset title';
            $asset = new Asset([Asset::ATTR_TITLE => $title]);

            $link = new Link($asset, $a);

            $this->assertEquals($title, $link->getTitle());
        }
    }

    public function testGetTextReturnsAssetDescription()
    {
        $attrs = [
            ['text' => null],
            ['text' => ''],
            [],
        ];

        foreach ($attrs as $a) {
            $text = 'asset description';
            $asset = new Asset([Asset::ATTR_DESCRIPTION => $text]);

            $link = new Link($asset, $a);

            $this->assertEquals($text, $link->getText());
        }
    }

    public function testIsValidReturnsFalseIfNoAsset()
    {
        $assetId = 1;

        AssetFacade::shouldReceive('find')
            ->once()
            ->with($assetId)
            ->andReturn(null);

        $link = new Link($assetId);

        $this->assertFalse($link->isValid());
    }

    public function testIsVisibleIfAssetIsPublic()
    {
        $asset = new Asset([Asset::ATTR_PUBLIC => true]);
        $link = new Link($asset);

        $this->assertTrue($link->isVisible());
    }

    public function testIsVisibleIfAssetIsPrivateAndUserIsLoggedIn()
    {
        $asset = new Asset([Asset::ATTR_PUBLIC => false]);
        $link = new Link($asset);

        Auth::shouldReceive('check')->once()->andReturn(true);

        $this->assertTrue($link->isVisible());
    }

    public function testIsNotVisibleIfAssetIsPrivateAndUserIsNotLoggedIn()
    {
        $asset = new Asset([Asset::ATTR_PUBLIC => false]);
        $link = new Link($asset);

        Auth::shouldReceive('check')->once()->andReturn(false);

        $this->assertFalse($link->isVisible());
    }

    public function testUrlGeneratesAnAssetUrl()
    {
        $asset = new Asset();
        $asset->{Asset::ATTR_ID} = 1;

        $link = new Link($asset);

        $this->assertEquals(route('asset', ['asset' => $asset]), $link->url());
    }
}
