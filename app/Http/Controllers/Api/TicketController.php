<?php

namespace App\Http\Controllers\Api;

use Illuminate\Http\Request;
use App\Models\Ticket;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Validator;

class TicketController extends Controller
{
        /**
     * Instantiate a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('admin')->except(['index', 'show']);
    }
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $tickets = Ticket::get();

        if (count($tickets) > 0 ) {
            return response()->json([
                'code' => 202,
                'status' => 'Please select a ticket',
                'data' => $tickets
            ], 202);
        }

        return response()->json([
            'code' => 404,
            'data' => 'ticket not found'
        ]);
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
            'eventName' => 'required',
            'location' => 'required',
            'eventDate' => 'required',
            'eventTime' => 'required',
            'desc' => 'required',
            'quantity' => 'required',
            'price' => 'required',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'code' => 422,
                'status' => 'error',
                'message' => 'data not match with our validation',
                'data' => $validator->errors()
            ], 422);
        }

        $validated = $validator->getData();

        $tickets = Ticket::create($validated);

        return response()->json([
            'code' => 202,
            'status' => 'success',
            'message' => 'ticket successfully created',
            'data' => $tickets
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
        $tickets = Ticket::find($id);

        if (!$tickets) {
            return response()->json([
                'code' => 404,
                'status' => 'error',
                'message' => 'ticket not found in our database'
            ], 404);
        }

                return response()->json([
            'code' => 202,
            'status' => 'success',
            'message' => 'ticket is showed',
            'data' => $tickets
        ], 202);
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
            'eventName' => 'nullable',
            'location' => 'nullable',
            'eventDate' => 'nullable',
            'eventTime' => 'nullable',
            'desc' => 'nullable',
            'quantity' => 'nullable',
            'price' => 'nullable',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'code' => 422,
                'status' => 'error',
                'message' => 'data not match with our validation',
                'data' => $validator->errors()
            ], 422);
        }    

        $validated = $validator->getData();

        $tickets = Ticket::find($id);
        $tickets->update($validated);

                return response()->json([
            'code' => 202,
            'status' => 'success',
            'message' => 'ticket successfully updated',
            'data' => $tickets
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
        $tickets = Ticket::get();   
        
        $tickets = Ticket::where('id', $id)->first();
        if($tickets) {
            $tickets->delete();
            return response()->json([
            'code' => 202,
            'status' => 'success',
            'message' => 'ticket successfully removed',
            'data' => $tickets
        ], 202);
        } else {
            return response()->json([
                'status' => 404,
                'message' => 'id ' . $id . ' tidak ditemukan'
            ], 404);
        }
    }
}