<?php

namespace App\Http\Controllers\Messages;

use App\Http\Controllers\Controller;
use App\Jobs\SyncMessages\SyncDeliveryReportTask;
use App\Jobs\SyncMessages\SyncReceivedMessageTask;
use App\Libs\Repositories\ReceivedMessagesRepository;
use App\Models\DeliveryReport;
use App\Models\NegaritClient;
use App\Models\ReceivedMessage;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Http\Request;

class ReceivedMessagesController extends Controller
{
    protected $receivedMessagesRepo;

    /**
     * ReceivedMessagesController constructor.
     * @param ReceivedMessagesRepository $repository
     */
    public function __construct(ReceivedMessagesRepository $repository)
    {
        $this->middleware('auth:api');
        $this->receivedMessagesRepo = $repository;
    }

    public function getReceivedMessagesList()
    {
        try {
            $this->authorize('view', new NegaritClient());
            $clients = $this->receivedMessagesRepo->getAll();
            return response()->json(['status' => true, 'message' => 'received messages fetched successfully', 'result' => $clients, 'error' => null], 200);
        } catch (AuthorizationException $e) {
            return response()->json(['status' => false, 'message' => $e->getMessage(), 'result' => null, 'error' => $e->getCode()], 500);
        }
    }

    public function getReceivedMessagesPaginated()
    {
        try {
            $PAGINATE_NUM = request()->input('PAGINATE_SIZE') ? request()->input('PAGINATE_SIZE') : 10;
            $this->authorize('view', new NegaritClient());
            $clients = $this->receivedMessagesRepo->getAllPaginated($PAGINATE_NUM);
            return response()->json(['status' => true, 'message' => 'received messages fetched successfully', 'result' => $clients, 'error' => null], 200);
        } catch (AuthorizationException $e) {
            return response()->json(['status' => false, 'message' => $e->getMessage(), 'result' => null, 'error' => $e->getCode()], 500);
        }
    }

    public function createdReceivedMessage()
    {
        try {
            $incoming_message = request()->all();
            // Create New Received Message
            if (isset($incoming_message['binary']) && isset($incoming_message['to'])) {
                $negaritClient = NegaritClient::where('short_code', '=', $incoming_message['to'])->where('status', '=', true)->first();
                if ($negaritClient instanceof NegaritClient) {
                    $new_received_message = new ReceivedMessage();
                    $new_received_message->negarit_client_id = $negaritClient->id;
                    $new_received_message->sent_from = $incoming_message['from'];
                    $new_received_message->sent_to = $incoming_message['to'];
                    $new_received_message->coding = $incoming_message['coding'];
                    if ($incoming_message['coding'] != '0') {
                        $new_received_message->message = mb_convert_encoding(hex2bin($incoming_message['binary']), 'UTF-8', 'UTF-16BE');
                    } else {
                        $new_received_message->message = $incoming_message['content'];
                    }
                    if ($new_received_message->save()) {
                        dispatch(new SyncReceivedMessageTask($new_received_message));
                        return 'ACK/Jasmin';
                    }
                }

            } // Create Delivery Report
            elseif (isset($incoming_message['message_status']) && isset($incoming_message['id'])) {
                // Create Type 1 Deliver Report
                if (isset($incoming_message['donedate']) && isset($incoming_message['id_smsc'])) {
                    $newDeliveryMessage = new DeliveryReport();
                    $newDeliveryMessage->delivery_type = DeliveryReport::DELIVERY_REPORT_TYPE['TYPE_ONE'];
                    $newDeliveryMessage->message_id = isset($incoming_message['id'])? $incoming_message['id']: null;
                    $newDeliveryMessage->message_status = isset($incoming_message['message_status'])? $incoming_message['message_status']: null;
                    $newDeliveryMessage->level = isset($incoming_message['level'])? $incoming_message['level']: null;
                    $newDeliveryMessage->delivered = isset($incoming_message['dlvrd'])? $incoming_message['dlvrd']: null;
                    $newDeliveryMessage->error = isset($incoming_message['err'])? $incoming_message['err']: null;
                    if($newDeliveryMessage->save()) {
                        dispatch(new SyncDeliveryReportTask($newDeliveryMessage));
                        return 'ACK/Jasmin';
                    }
                } elseif (isset($incoming_message['level'])) {
                    // Create Type 2 Deliver Report
                    $newDeliveryMessage2 = new DeliveryReport();
                    $newDeliveryMessage2->delivery_type = DeliveryReport::DELIVERY_REPORT_TYPE['TYPE_TWO'];
                    $newDeliveryMessage2->message_id = isset($incoming_message['id'])? $incoming_message['id']: null;
                    $newDeliveryMessage2->message_status = isset($incoming_message['message_status'])? $incoming_message['message_status']: null;
                    $newDeliveryMessage2->level = isset($incoming_message['level'])? $incoming_message['level']: null;
                    $newDeliveryMessage2->delivered = null;
                    $newDeliveryMessage2->error = null;
                    if($newDeliveryMessage2->save()) {
                        dispatch(new SyncDeliveryReportTask($newDeliveryMessage2));
                        return 'ACK/Jasmin';
                    }
                }
            }
        } catch (\Exception $exception) {

        }
    }
}
