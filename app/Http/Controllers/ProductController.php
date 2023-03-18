<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\Variant;
use Illuminate\Support\Str;
use App\Models\ProductImage;
use Illuminate\Http\Request;
use App\Models\ProductVariant;
use Illuminate\Support\Facades\DB;
use App\Models\ProductVariantPrice;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\File;

class ProductController extends Controller
{
    public $product;
    public function __construct(
        Product $product
    )
    {
        $this->product= $product;
    }

    public function index(Request $request)
    {
        $per_pro = 20;
        $product = $this->product->getProductList($request,$per_pro);
        $variants = Variant::with('variant_child')->get();
        foreach ($variants as $key => $variant) {
            $variant->child = ProductVariant::where('variant_id',$variant->id)->get();
        }
        // dd($variants);

        return view('products.index',compact('variants'))->withData($product);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Contracts\Foundation\Application|\Illuminate\Contracts\View\Factory|\Illuminate\Http\Response|\Illuminate\View\View
     */
    public function create()
    {
        $variants = Variant::all();
        return view('products.create', compact('variants'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function store(Request $request)
    {

        DB::beginTransaction();
        try {
            $path = 'assets/uploads/products/';
            $product                   = new Product();
            $product->title            = $request->title;
            $product->sku             = Str::lower(Str::slug($request->title));
            $product->description      = $request->description;
            $product->created_at       = date('Y-m-d H:i:s');
            $product->save();


            if (!is_null($request->file('product_image')))
            {
                $count = count($request->file('product_image'));
                for ($i = 0;$i < $count;$i++)
                {
                    if (!empty($request->file('product_image') [$i]))
                    {
                        if (!File::exists($path)) {
                            File::makeDirectory($path, 0755, true);
                        }
                        $image = $request->file('product_image') [$i];
                        $extension = $image->getClientOriginalExtension();
                        $base_name = preg_replace('/\..+$/', '', $image->getClientOriginalName());
                        $base_name = explode(' ', $base_name);
                        $base_name = implode('-', $base_name);
                        $feature_image = $base_name . "-" . uniqid() . '.'.$extension;
                        $image->move($path, $feature_image);
                        $image_name = $path . '/' . $feature_image;
                        $pro_allery = new ProductImage();
                        $pro_allery->product_id  = $product->id;
                        $pro_allery->file_path = $image_name;
                        $pro_allery->thumbnail = 1;
                        $pro_allery->created_at =date('Y-m-d H:i:s');
                        $pro_allery->save();
                    }
                }
            }

        } catch (\Exception $e) {
            dd($e->getMessage());
            DB::rollback();
            return redirect()->route('product.index');
        }
        DB::commit();
        return redirect()->route('product.index');

    }


    /**
     * Display the specified resource.
     *
     * @param \App\Models\Product $product
     * @return \Illuminate\Http\Response
     */
    public function show($product)
    {

    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param \App\Models\Product $product
     * @return \Illuminate\Http\Response
     */
    public function edit(Product $product)
    {
        $variants = Variant::all();
        return view('products.edit', compact('variants'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param \Illuminate\Http\Request $request
     * @param \App\Models\Product $product
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Product $product)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param \App\Models\Product $product
     * @return \Illuminate\Http\Response
     */
    public function destroy(Product $product)
    {
        //
    }
}
