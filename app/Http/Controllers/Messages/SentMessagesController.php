<?php

namespace App\Http\Controllers\Messages;

use App\Http\Controllers\Controller;
use App\Libs\Repositories\SentMessagesRepository;
use App\Models\NegaritClient;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Http\Request;

class SentMessagesController extends Controller
{
    protected $sentMessagesRepo;

    /**
     * SentMessagesController constructor.
     * @param SentMessagesRepository $repository
     */
    public function __construct(SentMessagesRepository $repository)
    {
        $this->sentMessagesRepo = $repository;
    }

    public function getSentMessagesList()
    {
        try {
            $this->authorize('view', new NegaritClient());
            $clients = $this->sentMessagesRepo->getAll();
            return response()->json(['status' => true, 'message' => 'sent messages fetched successfully', 'result' => $clients, 'error' => null], 200);
        } catch (AuthorizationException $e) {
            return response()->json(['status' => false, 'message' => $e->getMessage(), 'result' => null, 'error' => $e->getCode()], 500);
        }
    }

    public function getSentMessagesPaginated()
    {
        try {
            $PAGINATE_NUM = request()->input('PAGINATE_SIZE') ? request()->input('PAGINATE_SIZE') : 10;
            $this->authorize('view', new NegaritClient());
            $clients = $this->sentMessagesRepo->getAllPaginated($PAGINATE_NUM);
            return response()->json(['status' => true, 'message' => 'sent messages fetched successfully', 'result' => $clients, 'error' => null], 200);
        } catch (AuthorizationException $e) {
            return response()->json(['status' => false, 'message' => $e->getMessage(), 'result' => null, 'error' => $e->getCode()], 500);
        }
    }

}
