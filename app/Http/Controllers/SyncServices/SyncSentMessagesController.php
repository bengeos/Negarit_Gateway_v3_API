<?php

namespace App\Http\Controllers\SyncServices;

use App\Http\Controllers\Controller;
use App\Jobs\SyncMessages\SyncSentMessageTask;
use App\Models\NegaritClient;
use Illuminate\Http\Request;

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
}
