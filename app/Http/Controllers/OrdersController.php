<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Order;
use App\Quote;
use Auth;
use Illuminate\Support\Facades\DB;


class OrdersController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    { 
        $orders = DB::table('orders')->get();
        return view('order.index', ['orders' => $orders]);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        return view('order.create');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $request->validate([
            'link.*' => 'required|min:7',
            'qty.*' => 'required|min:1|numeric',
            'description.*' => 'nullable'
        ]);
        $order = new Order;
        $order->status = 0;
        $order->user()->associate(Auth::id());
        $order->save();
        // $quote  = new Quote;
        // $quote->order_id = $order->id;
        // $quote->link = $request->link;
        // $quote->qty = $request->qty;
        // $quote->description = $request->description;

        $order_id = $order->id;
        $link = $request->link;
        $qty = $request->qty;
        $des = $request->description;

        $count = count($link);

        for ($i = 0; $i < $count; $i++) {
            $data = array(
                'order_id' => $order_id,
                'link' => $link[$i],
                'qty' => $qty[$i],
                'description' => $des[$i],
                'created_at' => new \DateTime(),
                'updated_at' => new \DateTime()
            );

            $insertData[] = $data;
        }

        Quote::insert($insertData);

        return redirect()->action('OrdersController@index');
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $order = Order::findOrFail($id);
        return view('order.show')->with('order', $order);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        // $order = Order::findOrFail($id);

        // $request->validate([
        //     'status' => 'required'
        // ]);

        // $order->status = $request->status;
        // $order->update();
    }

    public function status_change(Request $request)
    {
        $id = $request->id;
        $order = Order::findOrFail($id);
        if($order->status < 4){
            $order->status = 8;
            $order->update();
        }
        echo "status Changes";
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $order = Order::findOrFail($id);
        if($order->status == 0){
            $order->delete();
        }
        
    }
}
