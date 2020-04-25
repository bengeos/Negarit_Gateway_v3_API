<?php

namespace App\Console\Commands;

use App\Http\Controllers\Controller;
use App\Jobs\SyncMessages\SyncSentMessageTask;
use App\Models\NegaritClient;
use App\Models\SentMessage;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Cache;

class PullSentMessages extends Command
{
    protected $myController;
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'pull:sent_messages';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Pull Sent Messages From Negarit';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
        $this->myController = new Controller();
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        try {
            logger('PullSentMessages');
            $negaritClients = NegaritClient::where('status', '=', true)->get();
            foreach ($negaritClients as $negaritClient) {
                if ($negaritClient instanceof NegaritClient) {
                    $value = Cache::get("SYNC_SENT_MESSAGES_FROM_NEGARIT");
                    if (!$value) {
                        dispatch(new SyncSentMessageTask($negaritClient));
                    }
                }
            }
        } catch (\Exception $exception) {
            logger('PullSentMessages', ['exception' => $exception->getMessage()]);
        }
    }
}
