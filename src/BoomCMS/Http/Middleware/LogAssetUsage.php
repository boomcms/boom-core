<?php

namespace BoomCMS\Http\Middleware;

use BoomCMS\Database\Models\Asset\Usage as AssetUsage;
use Closure;
use Illuminate\Auth\AuthManager;
use Illuminate\Http\Request;

class LogAssetUsage
{
    /**
     * @var AuthManager
     */
    protected $auth;

    public function __construct(AuthManager $auth)
    {
        $this->auth = $auth;
    }
    /**
     * Handle an incoming request.
     *
     * @param \Illuminate\Http\Request $request
     * @param \Closure                 $next
     *
     * @return mixed
     */
    public function handle(Request $request, Closure $next)
    {
        $asset = $request->route()->parameter('asset');

        if ($asset && !$this->auth->check() && $asset->getType() === 'doc' && $request->segment(3) == 'view') {
            $ip = ip2long($request->ip());

            if (!AssetUsage::recentlyViewed($asset->getId(), $ip)->count() > 0) {

                AssetUsage::create([
                    'asset_id' => $asset->getId(),
                    'ip_address' => $ip,
                    'time' => time()
                ]);

                $asset->incrementViews();
            }
        }

        return $next($request);
    }
}
