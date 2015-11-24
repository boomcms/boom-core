<?php

namespace BoomCMS\Http\Middleware;

use BoomCMS\Core\Auth\Auth;
use BoomCMS\Database\Models\Asset\Download as AssetDownload;
use Closure;
use Illuminate\Http\Request;

class LogAssetDownload
{
    /**
     * @var Auth
     */
    protected $auth;

    public function __construct(Auth $auth)
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
        $asset = $request->route()->getParameter('asset');

        if ($asset && !$this->auth->loggedIn()) {
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
