<?php

namespace App\Jobs\SyncMessages;

use App\Http\Controllers\Controller;
use App\Models\NegaritClient;
use App\Models\SentMessage;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class SyncSentMessageTask implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;
    protected $negaritClient;
    protected $myController;

    /**
     * Create a new job instance.
     *
     * @param NegaritClient $negaritClient
     */
    public function __construct(NegaritClient $negaritClient)
    {
        $this->myController = new Controller();
        $this->negaritClient = $negaritClient;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        try {
            $response = $this->myController->sendGetRequestToNegarit('sync/pull_sent_messages/' . $this->negaritClient->gateway_code);
            if ($response) {
                $responseData = json_decode($response);
                if ($responseData && $responseData->sent_messages && $responseData->status) {
                    $send_message_logs = [];
                    $send_message_logs['gateway_code'] = $this->negaritClient->gateway_code;
                    $send_message_logs['send_message_logs'] = [];
                    foreach ($responseData->sent_messages as $sentMessage) {
                        $oldSentMessage = SentMessage::where('negarit_client_id', '=', $this->negaritClient->id)->where('negarit_message_id', '=', $sentMessage->id)->first();
                        if ($oldSentMessage instanceof SentMessage) {
                            $send_message_log = array();
                            $send_message_log['negarit_message_id'] = $oldSentMessage->negarit_message_id;
                            $send_message_log['gateway_message_id'] = $oldSentMessage->id;
                            $send_message_log['state'] = true;
                            $send_message_logs['send_message_logs'][] = $send_message_log;
                        } else {
                            $new_Send_Message = new SentMessage();
                            $new_Send_Message->negarit_client_id = $this->negaritClient->id;
                            $new_Send_Message->negarit_message_id = $sentMessage->id;
                            $new_Send_Message->sent_to = $sentMessage->sent_to;
                            $new_Send_Message->sent_from = $this->negaritClient->short_code;
                            $new_Send_Message->message = $sentMessage->message;
                            if ($new_Send_Message->save()) {
                                $send_message_log = array();
                                $send_message_log['negarit_message_id'] = $new_Send_Message->negarit_message_id;
                                $send_message_log['gateway_message_id'] = $new_Send_Message->id;
                                $send_message_log['state'] = true;
                                $send_message_logs['send_message_logs'][] = $send_message_log;
                            }
                        }
                    }
                    $logMessage = $this->myController->sendPostRequestTooNegarit('sync/push_send_messages_logs', json_encode($send_message_logs));
                    logger('Log-Message', ['message' => $logMessage]);
                    sleep(10);
                    dispatch(new SyncSentMessageTask($this->negaritClient));
                } else {
                    sleep(5);
                    dispatch(new SyncSentMessageTask($this->negaritClient));
                }
            }
        } catch (\Exception $exception) {
            sleep(30);
            logger('SyncSentMessageTask', ['exception' => $exception->getMessage(), 'type' => 'Execution Error']);
            dispatch(new SyncSentMessageTask($this->negaritClient));
        }
    }

    public function failed(\Exception $e = null)
    {
        sleep(30);
        $error = "Job Scheduler Error";
        if ($e != null) {
            $error = $e->getMessage();
        }
        logger('SyncSentMessageTask', ['exception' => $error, 'type' => 'Job Scheduler Error']);
        dispatch(new SyncSentMessageTask($this->negaritClient));
    }
}
