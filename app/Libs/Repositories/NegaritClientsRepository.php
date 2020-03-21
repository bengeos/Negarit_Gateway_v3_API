<?php


namespace App\Libs\Repositories;


use App\Libs\Interfaces\DefaultInterface;
use App\Models\NegaritClient;

class NegaritClientsRepository extends DefaultRepository implements DefaultInterface
{
    public function getItem($id, $queryData = null)
    {
        if ($queryData == null) {
            $queryData = array();
        }
        return NegaritClient::where('id', '=', $id)
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
        return NegaritClient::where(function ($query) use ($queryData) {
            $this->queryBuilder($query, $queryData);
        })
            ->first();
    }

    public function getAll($queryData = null)
    {
        if ($queryData == null) {
            $queryData = array();
        }
        return NegaritClient::where(function ($query) use ($queryData) {
            $this->queryBuilder($query, $queryData);
        })
            ->get();
    }

    public function getAllPaginated($pagination_size = 10, $queryData = null)
    {
        if ($queryData == null) {
            $queryData = array();
        }
        return NegaritClient::where(function ($query) use ($queryData) {
            $this->queryBuilder($query, $queryData);
        })
            ->paginate($pagination_size);
    }

    public function addNew($inputData)
    {
        $newNegaritClient = new NegaritClient();
        $newNegaritClient->gateway_code = isset($inputData['gateway_code']) ? $inputData['gateway_code'] : $this->getRandomString(32);
        $newNegaritClient->company_name = isset($inputData['company_name']) ? $inputData['company_name'] : null;
        $newNegaritClient->company_id = isset($inputData['company_id']) ? $inputData['company_id'] : null;
        $newNegaritClient->unique_code = isset($inputData['unique_code']) ? $inputData['unique_code'] : $this->getRandomString(16);;
        $newNegaritClient->incoming_rate = isset($inputData['incoming_rate']) ? $inputData['incoming_rate'] : 0;
        $newNegaritClient->outgoing_rate = isset($inputData['outgoing_rate']) ? $inputData['outgoing_rate'] : 0;
        $newNegaritClient->short_code = isset($inputData['short_code']) ? $inputData['short_code'] : null;
        $newNegaritClient->port_type = isset($inputData['port_type']) ? $inputData['port_type'] : 'MOBILE_ORIGINATED';
        $newNegaritClient->save();
        return $newNegaritClient;
    }

    public function updateItem($id, $updateData, $queryData = null)
    {
        if ($queryData == null) {
            $queryData = array();
        }
        $queryData['id'] = $id;
        return NegaritClient::where(function ($query) use ($queryData) {
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
        return NegaritClient::where(function ($query) use ($queryData) {
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
        return NegaritClient::where('id', '=', $id)->where(function ($query) use ($queryData) {
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
        $clientsForDelete = NegaritClient::where(function ($query) use ($queryData) {
            if ($queryData) {
                $this->queryBuilder($query, $queryData);
            }
        }
        )->get();
        foreach ($clientsForDelete as $client) {
            if ($client instanceof NegaritClient) {
                $client->delete();
            }
        }
        return true;
    }
}
