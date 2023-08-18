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

class OrderController extends Controller
{
    public function checkOrder(Request $request) {
        $order_items = $request->input('order_items');
        $username = $request->input('username');

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
        $out_of_stock_items = [];

        foreach ($order_items as $item) { // Checking for the availibility of the product by checking its stock.
            $product = Product::find($item['product_id']);
            if ($product->stock_quantity < $item['quantity']) {
                $out_of_stock_items[] = [
                    'product_title' => $product->title,
                    'message' => "Item is out of stock!"
                ];
            }
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

        $discount = $this->applyBestCampaign($order_items, $total_price);

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
            'total_price' => $total_price
        ], 200);
    }

    public function applyBestCampaign($order_items, $total_price){
        $campaigns = Campaign::all();
        $discountAmounts = []; 

        foreach ($campaigns as $campaign) {
            switch($campaign->id){
                case 1: // Sabahattin Ali'nin Roman kitaplarında 2 üründen 1 tanesi bedava
                    $counter = 0;
                    $cheapest_book = null;
                    foreach ($order_items as $item) {
                        $product = Product::find($item['product_id']);
                        if($product-> author_id == 3 && $product-> category_id == 1){
                            $counter += $item['quantity'];
                            if($cheapest_book==null || $product->list_price < $cheapest_book)
                                $cheapest_book = $product->list_price;
                        }
                    }
                    if($counter >= 2){
                        $discountAmounts[] = $cheapest_book;
                    }
                    break;
                case 2: //  
                    $total_discount = 0;
                    foreach ($order_items as $item) {
                        $product = Product::find($item['product_id']);
                        $author = Author::where('id', $product -> author_id) -> first();
                        if($author->is_local){
                            $discount = 5 * ($product->list_price) / 100; // %5 discount
                            $total_discount += $discount * $item['quantity'];
                        }    
                    }
                    $discountAmounts[] = $total_discount;
                    
                    break;
                case 3:
                    $total_discount = 0;
                    if($total_price >= 200){
                        $total_discount = 5 * $total_price / 100;
                    }
                    $discountAmounts[] = $total_discount;

                    break;
                default:
                    break;
            }
        }
        $maxDiscount = max($discountAmounts);

        return $maxDiscount;
    }

    public function applyCampaign(){
        // This function apply campaign to the user's orders and calculates the discounted price.
    }
    
}