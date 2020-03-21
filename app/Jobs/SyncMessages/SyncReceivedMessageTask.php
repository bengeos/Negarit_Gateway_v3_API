<?php

namespace App\Jobs\SyncMessages;

use App\Http\Controllers\Controller;
use App\Models\NegaritClient;
use App\Models\ReceivedMessage;
use Carbon\Carbon;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class SyncReceivedMessageTask implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;
    protected $receivedMessage;
    protected $myController;

    /**
     * Create a new job instance.
     *
     * @param ReceivedMessage $message
     */
    public function __construct(ReceivedMessage $message)
    {
        $this->receivedMessage = $message;
        $this->myController = new Controller();
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        $negaritClient = NegaritClient::where('id', '=', $this->receivedMessage->negarit_client_id)->first();
        if ($negaritClient instanceof NegaritClient) {
            $new_push_message = array();
            $new_push_message['gateway_code'] = $negaritClient->gateway_code;
            $new_push_message['message'] = $this->getReceivedMessageData($negaritClient->gateway_code);
            $response = $this->myController->sendPostRequestTooNegarit('sync/push_received_message', json_encode($new_push_message));
            if ($response) {
                $this->receivedMessage->attempts = $this->receivedMessage->attempts + 1;
                $foundResponse = json_decode($response);
                if ($foundResponse && $foundResponse->status) {
                    $this->receivedMessage->is_sent = true;
                    $this->receivedMessage->is_delivered = true;
                    $this->receivedMessage->process_time = null;
                    $this->receivedMessage->description = 'MESSAGE DELIVERED TO NEGARIT';
                } else {
                    $this->receivedMessage->process_time = null;
                    $this->receivedMessage->process_time = Carbon::now()->addMinutes(3);
                    $this->receivedMessage->description = 'FAILED TO DELIVER MESSAGE TO NEGARIT';
                }
            } else {
                $this->receivedMessage->process_time = Carbon::now()->addMinutes(3);
                $this->receivedMessage->description = 'WHOOPS NEGARIT FAILED TO RECEIVE';
            }
            $this->receivedMessage->update();
        }
    }

    private function getReceivedMessageData($gateway_id)
    {
        $received_message = array();
        $received_message['gateway_id'] = $gateway_id;
        $received_message['message_id'] = $this->receivedMessage->message_id;
        $received_message['message'] = $this->receivedMessage->message;
        $received_message['coding'] = $this->receivedMessage->coding;
        $received_message['sent_from'] = $this->receivedMessage->sent_from;
        $received_message['received_date'] = $this->receivedMessage->created_at;
        return $this->receivedMessage;
    }
}
