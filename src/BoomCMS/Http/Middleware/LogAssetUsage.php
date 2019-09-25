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

        if ($asset && !$this->auth->check()) {
            $ip = ip2long($request->ip());

            AssetUsage::create([
                'asset_id' => $asset->getId(),
                'ip_address'       => $ip,
                'browser'     => $request->header('User-Agent'),
                'created_at'     => date('Y-m-d H:i:s'),
            ]);
        }

        return $next($request);
    }
}
