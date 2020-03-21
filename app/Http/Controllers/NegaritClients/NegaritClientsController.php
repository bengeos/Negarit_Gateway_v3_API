<?php

namespace App\Http\Controllers\NegaritClients;

use App\Http\Controllers\Controller;
use App\Libs\Repositories\NegaritClientsRepository;
use App\Models\City;
use App\Models\NegaritClient;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class NegaritClientsController extends Controller
{
    protected $negaritClientsRepo;

    /**
     * NegaritClientsController constructor.
     * @param NegaritClientsRepository $repository
     */
    public function __construct(NegaritClientsRepository $repository)
    {
        $this->middleware('auth:api');
        $this->negaritClientsRepo = $repository;
    }

    public function getClientsList()
    {
        try {
            $this->authorize('view', new NegaritClient());
            $clients = $this->negaritClientsRepo->getAll();
            return response()->json(['status' => true, 'message' => 'clients fetched successfully', 'result' => $clients, 'error' => null], 200);
        } catch (AuthorizationException $e) {
            return response()->json(['status' => false, 'message' => $e->getMessage(), 'result' => null, 'error' => $e->getCode()], 500);
        }
    }

    public function getClientsPaginated()
    {
        try {
            $PAGINATE_NUM = request()->input('PAGINATE_SIZE') ? request()->input('PAGINATE_SIZE') : 10;
            $this->authorize('view', new NegaritClient());
            $clients = $this->negaritClientsRepo->getAllPaginated($PAGINATE_NUM);
            return response()->json(['status' => true, 'message' => 'clients fetched successfully', 'result' => $clients, 'error' => null], 200);
        } catch (AuthorizationException $e) {
            return response()->json(['status' => false, 'message' => $e->getMessage(), 'result' => null, 'error' => $e->getCode()], 500);
        }
    }

    public function createClient()
    {
        try {
            $this->authorize('create', new NegaritClient());
            $credential = request()->all();
            $rule = ['company_name' => 'required', 'short_code' => 'required'];
            $validator = Validator::make($credential, $rule);
            if ($validator->fails()) {
                $error = $validator->messages();
                return response()->json(['status' => false, 'message' => 'please provide necessary information', 'result' => null, 'error' => $error], 500);
            }
            $newClient = $this->negaritClientsRepo->addNew($credential);
            if ($newClient) {
                return response()->json(['status' => true, 'message' => 'client created successfully', 'result' => $newClient, 'error' => null], 200);
            } else {
                return response()->json(['status' => false, 'message' => 'whoops! something went wrong! try again', 'result' => null, 'error' => 'something went wrong! try again'], 500);
            }
        } catch (AuthorizationException $e) {
            return response()->json(['status' => false, 'message' => $e->getMessage(), 'result' => null, 'error' => $e->getCode()], 500);
        } catch (\Exception $e) {
            return response()->json(['status' => false, 'message' => $e->getMessage(), 'result' => null, 'error' => $e->getCode()], 500);
        }
    }

    public function updateClient()
    {
        try {
            $this->authorize('update', new NegaritClient());
            $credential = request()->all();
            $rule = ['id' => 'required'];
            $validator = Validator::make($credential, $rule);
            if ($validator->fails()) {
                $error = $validator->messages();
                return response()->json(['status' => false, 'message' => 'please provide necessary information', 'result' => null, 'error' => $error], 500);
            }
            $updatedClientStatus = $this->negaritClientsRepo->updateItem($credential['id'], $credential);
            if ($updatedClientStatus) {
                $updatedClient = $this->negaritClientsRepo->getItem($credential['id']);
                return response()->json(['status' => true, 'message' => 'client updated successfully', 'result' => $updatedClient, 'error' => null], 200);
            } else {
                return response()->json(['status' => false, 'message' => 'whoops! something went wrong! try again', 'result' => null, 'error' => null], 500);
            }
        } catch (AuthorizationException $e) {
            return response()->json(['status' => false, 'message' => $e->getMessage(), 'result' => null, 'error' => $e->getCode()], 500);
        } catch (\Throwable $e) {
            return response()->json(['status' => false, 'message' => $e->getMessage(), 'result' => null, 'error' => $e->getCode()], 500);
        }
    }

    public function deleteClient($id)
    {
        try {
            $this->authorize('delete', new City());
            $queryData = array();
            $status = $this->negaritClientsRepo->deleteItem($id, $queryData);
            if ($status) {
                return response()->json(['status' => true, 'message' => 'client deleted successfully', 'result' => null, 'error' => null], 200);
            } else {
                return response()->json(['status' => false, 'message' => 'whoops! unable to delete this city', 'result' => null, 'error' => 'failed to delete the city'], 500);
            }
        } catch (AuthorizationException $e) {
            return response()->json(['status' => false, 'message' => $e->getMessage(), 'result' => null, 'error' => $e->getCode()], 500);
        } catch (\Throwable $e) {
        }
    }


}
