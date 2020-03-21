<?php


namespace App\Libs\Repositories;


use App\Libs\Interfaces\DefaultInterface;
use App\Models\SentMessage;
use Carbon\Carbon;

class SentMessagesRepository extends DefaultRepository implements DefaultInterface
{

    /**
     * SentMessagesRepository constructor.
     */
    public function __construct()
    {
    }
    public function getItem($id, $queryData = null)
    {
        if ($queryData == null) {
            $queryData = array();
        }
        return SentMessage::where('id', '=', $id)
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
        return SentMessage::where(function ($query) use ($queryData) {
            $this->queryBuilder($query, $queryData);
        })
            ->first();
    }

    public function getAll($queryData = null)
    {
        if ($queryData == null) {
            $queryData = array();
        }
        return SentMessage::where(function ($query) use ($queryData) {
            $this->queryBuilder($query, $queryData);
        })
            ->get();
    }

    public function getAllPaginated($pagination_size = 10, $queryData = null)
    {
        if ($queryData == null) {
            $queryData = array();
        }
        return SentMessage::where(function ($query) use ($queryData) {
            $this->queryBuilder($query, $queryData);
        })
            ->paginate($pagination_size);
    }

    public function addNew($inputData)
    {
        $newSentMessage = new SentMessage();
        $newSentMessage->negarit_client_id = isset($inputData['negarit_client_id']) ? $inputData['negarit_client_id'] : null;
        $newSentMessage->negarit_message_id = isset($inputData['negarit_message_id']) ? $inputData['negarit_message_id'] : null;
        $newSentMessage->message_id = isset($inputData['message_id']) ? $inputData['message_id'] : null;
        $newSentMessage->sent_from = isset($inputData['sent_from']) ? $inputData['sent_from'] : null;
        $newSentMessage->sent_to = isset($inputData['sent_to']) ? $inputData['sent_to'] : null;;
        $newSentMessage->message = isset($inputData['message']) ? $inputData['message'] : null;
        $newSentMessage->is_sent = isset($inputData['is_sent']) ? $inputData['is_sent'] : false;
        $newSentMessage->is_delivered = isset($inputData['is_delivered']) ? $inputData['is_delivered'] : false;
        $newSentMessage->attempts = isset($inputData['attempts']) ? $inputData['attempts'] : 0;
        $newSentMessage->description = isset($inputData['description']) ? $inputData['description'] : null;
        $newSentMessage->process_time = isset($inputData['process_time']) ? $inputData['process_time'] : null;
        $newSentMessage->save();
        return $newSentMessage;
    }

    public function updateItem($id, $updateData, $queryData = null)
    {
        if ($queryData == null) {
            $queryData = array();
        }
        $queryData['id'] = $id;
        return SentMessage::where(function ($query) use ($queryData) {
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
        return SentMessage::where(function ($query) use ($queryData) {
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
        return SentMessage::where('id', '=', $id)->where(function ($query) use ($queryData) {
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
        $messagesForDelete = SentMessage::where(function ($query) use ($queryData) {
            if ($queryData) {
                $this->queryBuilder($query, $queryData);
            }
        }
        )->get();
        foreach ($messagesForDelete as $message) {
            if ($message instanceof SentMessage) {
                $message->delete();
            }
        }
        return true;
    }
}
