<?php

namespace App\Http\Controllers\Api;

use Illuminate\Http\Request;
use App\Models\Order;
use App\Models\Transaction;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Validator;

class TransactionController extends Controller
{
    /**
     * Instantiate a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('admin')->except(['store', 'index', 'show']);
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $transaction = [];
        $user = auth('sanctum')->user();

        if ($user->isAdmin == 'true') {
            $transaction = Transaction::latest()->get();
        } elseif ($user->isAdmin == 'false') {
            $transaction = Transaction::where('transactionBy', $user->id)->latest()->get();
        }

        return response()->json([
            'code' => 202,
            'status' => 'success',
            'message' => 'data successfully accepted',
            'data' => $transaction
        ], 202);
    }

    // /**
    //  * Show the form for creating a new resource.
    //  *
    //  * @return \Illuminate\Http\Response
    //  */
    // public function create()
    // {
    //     //
    // }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'bookingCode' => 'required',
        ]);

        $orders = Order::where('bookingCode', $request->bookingCode)->first();

        $transaction = Transaction::create([
            'transactionBy' => auth('sanctum')->user()->id,
            'bookingCode' => $orders->bookingCode,
            'total' => $orders->total
        ]);

        return response()->json([
            'code' => 202,
            'status' => 'success',
            'message' => 'transaction successfully created',
            'data' => $transaction
        ], 202);
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $user = auth('sanctum')->user();
        $transaction = Transaction::find($id);

        if (!$transaction) {
            return response()->json([
                'code' => 404,
                'status' => 'error',
                'message' => 'transaction data not found in our database'
            ], 404);
        }

        if ($user->isAdmin == 'false') {
            if ($transaction->transactionBy == $user->id) {
                return response()->json([
                    'code' => 206,
                    'status' => 'success',
                    'message' => 'data successfully showed',
                    'data' => $transaction
                ], 206);
            }

            return response()->json([
                'code' => 403,
                'status' => 'error',
                'message' => 'transaction data is not yours'
            ], 403);
        }

        return response()->json([
            'code' => 206,
            'status' => 'success',
            'message' => 'data successfully showed',
            'data' => $transaction
        ], 206);
    }

    // /**
    //  * Show the form for editing the specified resource.
    //  *
    //  * @param  int  $id
    //  * @return \Illuminate\Http\Response
    //  */
    // public function edit($id)
    // {
    //     //
    // }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        $validator = Validator::make($request->all(), [
            'alreadyPaid?'=> 'required',
        ]);

        $validated = $validator->getData();

        $transaction = Transaction::find($id);
        $transaction->update($validated);

        return response()->json([
            'code' => 202,
            'status' => 'success',
            'message' => 'transaction data successfully updated',
            'data' => $transaction
        ], 202);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $transaction = Transaction::get();   
        
        $transaction = Transaction::where('id', $id)->first();
        if($transaction) {
            $transaction->delete();
            return response()->json([
            'code' => 202,
            'status' => 'success',
            'message' => 'ticket successfully removed',
            'data' => $transaction
        ], 202);
        } else {
            return response()->json([
                'status' => 404,
                'message' => 'id ' . $id . ' tidak ditemukan'
            ], 404);
        }
    }
}