<?php


namespace App\Libs\Repositories;


use App\Libs\Interfaces\DefaultInterface;
use App\Models\ReceivedMessage;
use Carbon\Carbon;

class ReceivedMessagesRepository extends DefaultRepository implements DefaultInterface
{
    public function getItem($id, $queryData = null)
    {
        if ($queryData == null) {
            $queryData = array();
        }
        return ReceivedMessage::where('id', '=', $id)
            ->where(function ($query) use ($queryData) {
                $this->queryBuilder($query, $queryData);
            })
            ->first();
    }

    public function getItemBy($queryData = null)
    {
        if ($queryData == null) {
            $queryData = array();
        }
        return ReceivedMessage::where(function ($query) use ($queryData) {
            $this->queryBuilder($query, $queryData);
        })
            ->first();
    }

    public function getAll($queryData = null)
    {
        if ($queryData == null) {
            $queryData = array();
        }
        return ReceivedMessage::where(function ($query) use ($queryData) {
            $this->queryBuilder($query, $queryData);
        })
            ->get();
    }

    public function getAllPaginated($pagination_size = 10, $queryData = null)
    {
        if ($queryData == null) {
            $queryData = array();
        }
        return ReceivedMessage::where(function ($query) use ($queryData) {
            $this->queryBuilder($query, $queryData);
        })
            ->paginate($pagination_size);
    }

    public function addNew($inputData)
    {
        $newReceivedMessage = new ReceivedMessage();
        $newReceivedMessage->negarit_client_id = isset($inputData['negarit_client_id']) ? $inputData['negarit_client_id'] : null;
        $newReceivedMessage->message_id = isset($inputData['message_id']) ? $inputData['message_id'] : null;
        $newReceivedMessage->sent_from = isset($inputData['sent_from']) ? $inputData['sent_from'] : null;
        $newReceivedMessage->sent_to = isset($inputData['sent_to']) ? $inputData['sent_to'] : null;;
        $newReceivedMessage->coding = isset($inputData['coding']) ? $inputData['coding'] : null;
        $newReceivedMessage->message = isset($inputData['message']) ? $inputData['message'] : null;
        $newReceivedMessage->is_sent = isset($inputData['is_sent']) ? $inputData['is_sent'] : false;
        $newReceivedMessage->is_delivered = isset($inputData['is_delivered']) ? $inputData['is_delivered'] : false;
        $newReceivedMessage->attempts = isset($inputData['attempts']) ? $inputData['attempts'] : 0;
        $newReceivedMessage->description = isset($inputData['description']) ? $inputData['description'] : null;
        $newReceivedMessage->process_time = isset($inputData['process_time']) ? $inputData['process_time'] : null;
        $newReceivedMessage->save();
        return $newReceivedMessage;
    }

    public function updateItem($id, $updateData, $queryData = null)
    {
        if ($queryData == null) {
            $queryData = array();
        }
        $queryData['id'] = $id;
        return ReceivedMessage::where(function ($query) use ($queryData) {
            if ($queryData) {
                $this->queryBuilder($query, $queryData);
            }
        }
        )->update($updateData);
    }

    public function updateItemBy($queryData, $updateData)
    {
        if ($queryData == null) {
            $queryData = array();
        }
        return ReceivedMessage::where(function ($query) use ($queryData) {
            if ($queryData) {
                $this->queryBuilder($query, $queryData);
            }
        }
        )->update($updateData);
    }

    public function deleteItem($id, $queryData = null)
    {
        if ($queryData == null) {
            $queryData = array();
        }
        return ReceivedMessage::where('id', '=', $id)->where(function ($query) use ($queryData) {
            if ($queryData) {
                $this->queryBuilder($query, $queryData);
            }
        }
        )->delete();
    }

    public function deleteItemBy($queryData = null)
    {
        if ($queryData == null) {
            $queryData = array();
        }
        $messagesForDelete = ReceivedMessage::where(function ($query) use ($queryData) {
            if ($queryData) {
                $this->queryBuilder($query, $queryData);
            }
        }
        )->get();
        foreach ($messagesForDelete as $message) {
            if ($message instanceof ReceivedMessage) {
                $message->delete();
            }
        }
        return true;
    }
}
