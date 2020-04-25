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
            $response = "";
            $negaritClients = NegaritClient::where('status', '=', true)->get();
            foreach ($negaritClients as $negaritClient) {
                if ($negaritClient instanceof NegaritClient) {
                    $value = Cache::get("SYNC_SENT_MESSAGES_FROM_NEGARIT");
                    if (!$value) {
                        $response = $response . "\n NEW SYNC SERVICE STARTED";
                        dispatch(new SyncSentMessageTask($negaritClient));
                    }
                }
            }
            if ($response == "") {
                return response()->json(['status' => true, 'message' => "SYNC STOPPED", 'result' => $response], 200);
            } else {
                return response()->json(['status' => true, 'message' => "SYNC REQUESTED", 'result' => $response], 200);
            }
        } catch (\Exception $exception) {
            return response()->json(['status' => true, 'message' => "SYNC FAILED", 'result' => $exception->getMessage()], 200);
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
