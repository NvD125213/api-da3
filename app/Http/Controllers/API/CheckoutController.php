<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\OrderDetails;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\DB;



class CheckoutController extends Controller
{
    private $orderdetail;
    public function __construct(OrderDetails $orderdetail) {
        $this -> orderdetail = $orderdetail;
    }

    public function create(Request $request) {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:191',
            'email' => 'required|string|max:191',
            'phone' => 'required|string|max:10',
            'address' => 'required|string|max:191',
            'address_city' => 'required|string|max:191',
           
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => 422,
                'errors' => $validator->messages()
            ]);
        } else {
            $user = auth('sanctum')->user();
            if (!$user) {
                return response()->json([
                    'status' => 401,
                    'message' => 'Unauthorized'
                ]);
            }

            $order = new Order;
            $order->user_id = $user->id;
            $order->name = $request->name;
            $order->email = $request->email;
            $order->phone = $request->phone;
            $order->address = $request->address;
            $order->address_city = $request->address_city;
            $order->status = 0;
            $order->save();

            foreach ($request->items as $item) {
                $data = [
                    'order_id' => $order->id,
                    'product_id' => $item['id'],
                    'quantity' => $item['quantity'],
                    'price' => $item['price'],
                ];
                $this->orderdetail->create($data);
            }

            return response()->json([
                'status' => 200,
                'message' => 'Thêm đơn hàng thành công!',
            ]);
        }
    }

    public function getOrderUnconfimred(Request $request) {
        $page = $request->input('page', 1);
        $pageSize = $request->input('pageSize', 10);
        $totalPages = 0;
        $orders = DB::select('CALL GetOrdersUncormfirmedPaginate(?, ?, @totalPages)', [$page, $pageSize]);
        $totalPagesResult = DB::select('SELECT @totalPages AS totalPages');
        $totalPages = $totalPagesResult[0]->totalPages;
        return response()->json([
            'orders' => $orders,
            'totalPages' => $totalPages,
        ]);
    }
    
    public function getOrdersCormfirmed(Request $request) {
        $page = $request->input('page', 1);
        $pageSize = $request->input('pageSize', 10);
        $totalPages = 0;
        $orders = DB::select('CALL GetOrdersCormfirmedPaginate(?, ?, @totalPages)', [$page, $pageSize]);
        $totalPagesResult = DB::select('SELECT @totalPages AS totalPages');
        $totalPages = $totalPagesResult[0]->totalPages;
        return response()->json([
            'orders' => $orders,
            'totalPages' => $totalPages,
        ]);
    }
    
    public function getOrderDetails($orderId)
    {
        $orderDetails = DB::select('CALL GetOrderDetails(?)', array($orderId));        
        return response()->json($orderDetails);
    }

    public function confirmOrder(Request $request)
    {
        $orderId = $request->input('orderId');
        $result = DB::select('CALL ConfirmOrder(?)', [$orderId]);
        if($result[0]->message === 'Đơn hàng đã được xác nhận.') {
            return response()->json([
                'status' => 200,
                'message' => 'Xác nhận thành công!'
            ]);

        }
        else {
            return response()->json([
                'status' => 401,
            ]);

        }
    }

    public function getOrderByUserID(Request $request) {
        $userId = $request->user()->id;
        $page = $request->query('page'); 
        $pageSize = $request->query('pageSize'); 
        $totalPages = 0;    
        $orders = DB::select('CALL GetOrdersByUserId(?, ?, ?, @totalPages)', [$userId, $page, $pageSize]);
        $totalPages = DB::select('SELECT @totalPages as totalPages')[0]->totalPages;

        return response()->json([
            'orders' => $orders,
            'totalPages' => $totalPages
        ]);
    }

}
