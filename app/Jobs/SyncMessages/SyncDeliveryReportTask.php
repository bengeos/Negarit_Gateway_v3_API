<?php

namespace App\Jobs\SyncMessages;

use App\Http\Controllers\Controller;
use App\Models\DeliveryReport;
use App\Models\NegaritClient;
use App\Models\SentMessage;
use Carbon\Carbon;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class SyncDeliveryReportTask implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;
    protected $deliveryMessage;
    protected $myController;

    /**
     * Create a new job instance.
     *
     * @param DeliveryReport $deliveryReport
     */
    public function __construct(DeliveryReport $deliveryReport)
    {
        $this->deliveryMessage = $deliveryReport;
        $this->myController = new Controller();
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        try {
            $sentMessage = SentMessage::where('message_id', '=', $this->deliveryMessage->message_id)->select(['id', 'negarit_message_id', 'negarit_client_id', 'message_id'])->first();
            if ($sentMessage instanceof SentMessage) {
                $negaritClient = NegaritClient::where('id', '=', $sentMessage->negarit_client_id)->first();
                if ($negaritClient instanceof NegaritClient) {
                    $new_push_message = array();
                    $new_push_message['gateway_code'] = $negaritClient->gateway_code;
                    $new_push_message['delivery_message'] = $sentMessage;
                    $response = $this->myController->sendPostRequestTooNegarit('sync/push_delivery_message', json_encode($new_push_message));
                    logger('SyncDelivery Message', ['data'=>$response]);
                    if ($response) {
                        $this->deliveryMessage->attempts = $this->deliveryMessage->attempts + 1;
                        $foundResponse = json_decode($response);
                        if ($foundResponse && $foundResponse->status) {
                            $this->deliveryMessage->is_sent = true;
                            $this->deliveryMessage->process_time = null;
                            $this->deliveryMessage->description = 'MESSAGE DELIVERED TO NEGARIT';
                        } else {
                            $this->deliveryMessage->process_time = Carbon::now()->addMinutes(3);
                            $this->deliveryMessage->description = 'FAILED TO DELIVER MESSAGE TO NEGARIT';
                        }
                    } else {
                        $this->deliveryMessage->process_time = Carbon::now()->addMinutes(3);
                        $this->deliveryMessage->description = 'WHOOPS NEGARIT FAILED TO RECEIVE';
                    }
                    $this->deliveryMessage->update();
                } else {
                    logger('SyncDelivery Message', ['data'=>'No Client', 'message'=>$sentMessage]);
                }
            } else {
                logger('SyncDelivery Message', ['data'=>'No Sent Message']);
            }
        } catch (\Exception $exception) {
            logger('SyncDelivery Message', ['data'=>$exception->getMessage()]);
        }
    }
}
