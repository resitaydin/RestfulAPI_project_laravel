<?php

namespace App\Http\Controllers;

use App\Models\Author;
use App\Models\Product;
use App\Models\Campaign;
use Illuminate\Http\Request;

class CampaignController extends Controller
{
    public function applyBestCampaign($order_items, $total_price){
        $campaigns = Campaign::all();
        $discountAmounts = []; 

        foreach ($campaigns as $campaign) {
            $min_condition = $campaign -> min_condition;
            $gift_condition = $campaign -> gift_condition;

            switch($campaign->discount_type){
                case 1: // Sabahattin Ali'nin Roman kitaplarında 2 üründen 1 tanesi bedava
                    $author_id = $campaign -> conditions["author_id"];
                    $category_id = $campaign -> conditions["category_id"];
                    
                    $counter = 0;
                    $cheapest_book = null;
                    foreach ($order_items as $item) {
                        $product = Product::find($item['product_id']);
                        if($product-> author_id == $author_id && $product-> category_id == $category_id){
                            $counter += $item['quantity'];
                            if($cheapest_book==null || $product->list_price < $cheapest_book)
                                $cheapest_book = $product->list_price;
                        }
                    }
                    if($counter >= $min_condition){
                        $discountAmounts[] = $cheapest_book * $gift_condition;
                    }
                    break;
                        
                case 2: 
                    if($min_condition > 0){ // belli bi sipariş tutarı üstünde %lik indirim.
                        $total_discount = 0;
                        if($total_price >= $min_condition){
                            $total_discount = $gift_condition * $total_price / 100;
                        }
                        $discountAmounts[] = $total_discount;
                    }
                    else if($min_condition==0) { // yüzde indirim yerli veya yabancı yazarlarda
                        $author_local = $campaign->conditions["author_local"];
                        $total_discount = 0;
                        foreach ($order_items as $item) {
                            $product = Product::find($item['product_id']);
                            $author = Author::find($product->author_id);
                            if($author->is_local == $author_local){
                                $discount = $gift_condition * ($product->list_price) / 100; // %gift condition discount
                                $total_discount += $discount * $item['quantity'];
                            }
                        }
                        $discountAmounts[] = $total_discount;
                        
                    }
                    break;

                default:
                    break;
            }
        }
        dd($discountAmounts);
        return max($discountAmounts);
        // Returning the max of discounts which is the most profitable one for the customer.
    }
    
}
