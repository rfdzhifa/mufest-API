<?php

namespace App\Http\Controllers\Api;

use Illuminate\Http\Request;
use App\Models\Ticket;
use App\Models\Order;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Validator;

class OrderController extends Controller
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
        $orders = [];
        $user = auth('sanctum')->user();

        if ($user->isAdmin == 'true') {
            $orders = Order::latest()->get();
        } elseif ($user->isAdmin == 'false') {
            $orders = Order::where('orderBy', $user->id)->latest()->get();
        }

        return response()->json([
            'code' => 202,
            'status' => 'success',
            'message' => 'data successfully accepted',
            'data' => $orders
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
            'ticketID' => 'required',
            'name' => 'required',
            'email' => 'required',
            'phone' => 'required',
            'quantity' => 'required',
        ]);

        $tickets = Ticket::where('id', $request->ticketID)->first();

        $orders = Order::create([
            'ticketID' => $tickets->id,
            'orderBy' => auth('sanctum')->user()->id,
            'name' => $request->name,
            'email' => $request->email,
            'phone' => $request->phone,
            'quantity' => $request->phone,
            'total' => $request->quantity * $tickets->price,
            'bookingCode' => 'TCX' . mt_rand(10000, 99999) . mt_rand(100, 999),
        ]);

        return response()->json([
            'code' => 202,
            'status' => 'success',
            'message' => 'order successfully created',
            'data' => $orders
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
        $orders = Order::find($id);

        if (!$orders) {
            return response()->json([
                'code' => 404,
                'status' => 'error',
                'message' => 'order data not found in our database'
            ], 404);
        }

        if ($user->isAdmin == 'false') {
            if ($orders->orderBy == $user->id) {
                return response()->json([
                    'code' => 206,
                    'status' => 'success',
                    'message' => 'data successfully showed',
                    'data' => $orders
                ], 206);
            }

            return response()->json([
                'code' => 403,
                'status' => 'error',
                'message' => 'order data is not yours'
            ], 403);
        }
        return response()->json([
            'code' => 206,
            'status' => 'success',
            'message' => 'data successfully showed',
            'data' => $orders
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
            'name' => 'nullable',
            'email'=> 'nullable',
            'phone'=> 'nullable',
            'quantity'=> 'nullable',
        ]);

        $validated = $validator->getData();

        $orders = Order::find($id);
        $orders->update($validated);

        return response()->json([
            'code' => 202,
            'status' => 'success',
            'message' => 'order data successfully updated',
            'data' => $orders
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
        $orders = Order::get();   
        
        $orders = Order::where('id', $id)->first();
        if($orders) {
            $orders->delete();
            return response()->json([
            'code' => 202,
            'status' => 'success',
            'message' => 'ticket successfully removed',
            'data' => $orders
        ], 202);
        } else {
            return response()->json([
                'status' => 404,
                'message' => 'id ' . $id . ' tidak ditemukan'
            ], 404);
        }
    }
}