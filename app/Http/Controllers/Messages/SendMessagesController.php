<?php

namespace App\Http\Controllers\Messages;

use App\Http\Controllers\Controller;
use App\Models\SentMessage;
use Illuminate\Http\Request;

class SendMessagesController extends Controller
{

    /**
     * SendMessagesController constructor.
     */
    public function __construct()
    {
    }

    public function sendPendingMessage()
    {
        $sent_messages = SentMessage::where('is_sent', '=', false)->orderBy('created_at', 'ASC')->take(30)->get();
        if ($sent_messages != null) {
            $send_status = array();
            foreach ($sent_messages as $message) {
                if ($message instanceof SentMessage) {
                    if ($this->getValidatedPhone($message->sent_to) && $message->message != null) {
                        $base_url = 'http://127.0.0.1:1401/send';
                        $params = '?username=agelgel';
                        $params .= '&password=agelgel';
                        $params .= '&coding=8';
                        $params .= '&dlr=yes';
                        $params .= '&dlr-level=3';
                        $params .= '&dlr-method=POST';
                        $params .= '&dlr-url=http://127.0.0.1/api/received_message';
                        $params .= '&hex-content=' . bin2hex(mb_convert_encoding($message->message, 'UTF-16BE', 'UTF-8'));
                        $params .= '&to=' . urlencode($this->getValidatedPhone($message->sent_to));
                        $params .= '&from=' . urlencode($message->sent_from);
                        $response = $this->sendGetRequest($base_url . $params);
//                        $response = file_get_contents($base_url . $params);
                        $p1 = stripos($response, '"') + 1;
                        $p2 = strrpos($response, '"');
                        $delivery_message_id = substr($response, $p1, $p2 - $p1);
                        if ($delivery_message_id) {
                            $send_status[] = ['status' => true, 'sent_message_id' => $message->id];
                            $message->message_id = $delivery_message_id;
                            $message->is_delivered = false;
                            $message->is_sent = true;
                            $message->update();
                        }
                    } else {
                        $message->message_id = "INVALID-PHONE-NUMBER";
                        $message->is_delivered = false;
                        $message->is_sent = true;
                        $message->update();
                    }
                }
            }
            return response()->json(['sent_message_statuses' => $send_status], 200);
        }
    }

    private function getValidatedPhone($phone_number)
    {
        $phone = (int)preg_replace('/\s+/', '', $phone_number);
        if (strlen($phone) == 9 && substr($phone, 0, 1) === ("9")) {
            return "251" . $phone;
        } elseif (strlen($phone) > 9 && (substr($phone, 0, 1) === ("9") || substr($phone, 0, 2) === ("09") || substr($phone, 0, 4) === ("2519")) || substr($phone, 0, 5) === ("+2519")) {
            return "251" . substr($phone, strlen($phone) - 9, 9);
        }
        return null;
    }
}
