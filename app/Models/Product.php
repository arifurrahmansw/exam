<?php

namespace App\Models;
use File;
use App\Models\ProductImage;
use App\Models\ProductVariant;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
    protected $fillable = [
        'title', 'sku', 'description'
    ];


    public function getProductList($request, int $per_pro)
    {
        $title          = trim($request->title);
        $_variant        = trim($request->variant);
        $price_from      = trim($request->price_from);
        $price_to      = trim($request->price_to);
        $rows =   $this->select('products.*');
        if(!empty($title)){
            $rows->where(function ($query) use($title) {
                $query->where('products.title', 'like', '%' . $title . '%')
                   ->orWhere('products.description', 'like', '%' . $title . '%');
              });
        }
        if(!empty($request->date)){
            $rows->whereDate('products.created_at','=',Carbon::parse($request->date));
        }
          $data = $rows->groupBy('products.id')->paginate($per_pro);
        foreach($data as $row ){
            $variants = DB::table('product_variant_prices as pvp')->select('pvp.*',
            'pv_1.variant as pv_1_variant',
            'pv_2.variant as pv_2_variant',
            'pv_3.variant as pv_3_variant',
        );
        if(!empty($_variant)){
            $variants =  $variants->where(function ($query) use($_variant) {
                $query->where('pvp.product_variant_one', $_variant)
                   ->orWhere('pvp.product_variant_two', $_variant)
                   ->orWhere('pvp.product_variant_three', $_variant);
              });
        }
        if(!is_null($price_from) && !is_null($price_to)){
            $variants = $variants->where('price','>=',$price_from);
            $variants = $variants->where('price','<=',$price_to);
            //  $variants->whereBetween('price',[$price_from, $price_to]);
        }

        $variants =  $variants->where('pvp.product_id',$row->id)
            ->leftJoin('product_variants as pv_1','pv_1.id','=','pvp.product_variant_one')
            ->leftJoin('product_variants as pv_2','pv_2.id','=','pvp.product_variant_two')
            ->leftJoin('product_variants as pv_3','pv_3.id','=','pvp.product_variant_three')
            ->get();
            $row->variants = $variants;
        }
        // dd($data);
        return $data;
    }





}
