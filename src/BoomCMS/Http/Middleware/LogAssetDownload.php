<?php

namespace BoomCMS\Http\Middleware;

use BoomCMS\Database\Models\Asset\Download as AssetDownload;
use Closure;
use Illuminate\Auth\AuthManager;
use Illuminate\Http\Request;

class LogAssetDownload
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

        if ($asset && !$this->auth->check()) {
            $ip = ip2long($request->ip());

            if (!AssetDownload::recentlyLogged($asset->getId(), $ip)->count() > 0) {
                AssetDownload::create([
                    'asset_id' => $asset->getId(),
                    'ip'       => $ip,
                    'time'     => time(),
                ]);

                $asset->incrementDownloads();
            }
        }

        return $next($request);
    }
}
