<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Order;
use App\Models\Author;

use App\Models\Product;
use App\Models\Campaign;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use App\Http\Controllers\CampaignController;

class OrderController extends Controller
{

    public function validateOrder($order_items, $username){
        $validator = Validator::make([
            'order_items' => $order_items,
            'username' => $username
        ], [
            'order_items' => 'required|array',
            'username' => 'required|exists:users,username',
            'order_items.*.product_id' => 'required|exists:products,id',
            'order_items.*.quantity' => 'required|integer|min:1',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'message' => 'Validation failed.',
                'errors' => $validator->errors(),
            ], 422);
        }
    }

    public function checkOrder(Request $request) {

        $order_items = $request->input('order_items');
        $username = $request->input('username');

        $validationResult = $this->validateOrder($order_items, $username);

        if ($validationResult instanceof JsonResponse) {
            // Return the response object from the validateOrder method
            return $validationResult;
        }

        $out_of_stock_items = [];
        $not_enough_stock_items = [];

        foreach ($order_items as $item) { // Checking for the availibility of the product by checking its stock.
            $product = Product::find($item['product_id']);
            if ($product->stock_quantity < $item['quantity']) {
                if($product->stock_quantity == 0){
                    $out_of_stock_items[] = [
                        'product_title' => $product->title,
                        'message' => "Item is out of stock!"
                    ];
                }
                $not_enough_stock_items[] = [
                    'product_title' => $product->title,
                    'message' => "Not enough stock for your order!"
                ];
            }
        }

        if (!empty($not_enough_stock_items)) { // If any of the items are out of stock.
            return response()->json([
                'message' => "Some items are not enough to order!",
                'not_enough_stock_items' => $not_enough_stock_items
            ], 404);
        }

        if (!empty($out_of_stock_items)) { // If any of the items are out of stock.
            return response()->json([
                'message' => "Some items are out of stock!",
                'out_of_stock_items' => $out_of_stock_items
            ], 404);
        }
        return $order_items;
    }

    public function createOrder(Request $request){
        $order_items = $this -> checkOrder($request);

        if ($order_items instanceof JsonResponse) {
            // Return the response object from the checkOrder method
            return $order_items;
        }

        $total_price = 0;

        $username = $request->input('username');

        foreach ($order_items as $item) {
         
            $product = Product::find($item['product_id']);
            if (!$product) {
                return response()->json([
                    'message' => "Product not found!"
                ], 404);
            }
            
            $product->stock_quantity -= $item['quantity'];
            $product->save();
            $total_price += $product->list_price * $item['quantity'];
        }

        if($total_price < 50){
            $total_price += 10; // shipping cost(cargo)
        }

        $discount = (new CampaignController)->applyBestCampaign($order_items, $total_price);

        $discountedPrice = $total_price - $discount;
        
        $user = User::where('username', $username)->first();

        $order = $user->getOrder()->create([
            'total_price' => $total_price,
            'discounted_price' => $discountedPrice
        ]);

        foreach ($order_items as $item) {
            $product = Product::find($item['product_id']);

            $order->getProduct()->attach($product->id,[
                'product_price' => $product->list_price,
                'product_quantity' => $item['quantity'],
                'created_at' => now(),
                'updated_at' => now()
            ]);
        }

        return response()->json([
            'message' => "$username's order is successful!",
            'total_price' => $discountedPrice
        ], 200);
    }
}