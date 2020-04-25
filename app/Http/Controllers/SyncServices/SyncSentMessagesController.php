<?php

namespace App\Http\Controllers\SyncServices;

use App\Http\Controllers\Controller;
use App\Jobs\SyncMessages\SyncSentMessageTask;
use App\Models\NegaritClient;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;

class SyncSentMessagesController extends Controller
{

    /**
     * SyncSentMessagesController constructor.
     */
    public function __construct()
    {
    }

    public function pullSentMessage()
    {
        try {
            $negaritClients = NegaritClient::where('status', '=', true)->get();
            foreach ($negaritClients as $negaritClient) {
                if ($negaritClient instanceof NegaritClient) {
                    dispatch(new SyncSentMessageTask($negaritClient));
                }
            }
        } catch (\Exception $exception) {

        }
    }

    public function set($id)
    {
        Cache::put('MY_CACHE_KEY', $id, Carbon::now()->addMinutes(1));
        return "CACHE SET";
    }

    public function get()
    {
        return Cache::pull('MY_CACHE_KEY');
    }
}
