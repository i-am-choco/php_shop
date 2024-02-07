<?php

namespace App\Http\Controllers;

use App\Http\Requests\ProductionListRequest;
use App\Http\Requests\ProductionCreateRequest;
use Illuminate\Http\Request;
use App\Models\Product;
use Illuminate\Support\Facades\DB;

class ProductController extends Controller
{

    /**
     * 查询商品列表
     * @param ProductionListRequest $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function list(ProductionListRequest $request)
    {
        try {
            $page = $request->input("page", 1);
            $page_size = $request->input("page_size", 20);
            $list = Product::query()->paginate($page_size, ['*'], 'page', $page);

            return response()->json([
                "data" => $list,
                "pagination" => [
                    "total" => $list->total(),
                    "current_page" => $list->currentPage(),
                    "page_size" => $list->perPage(),
                ]
            ]);
        } catch (\Exception $e) {
            return response()->json(["message" => $e->getMessage()], 500);
        }

    }


    /**
     * 创建单个商品
     * @param ProductionCreateRequest $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function create(ProductionCreateRequest $request)
    {
        try {

            $product = Product::create([
                'name' => $request->name,
                'description' => $request->input("description", $request->name),
                'price' => $request->price,
            ]);

            return response()->json(['product' => $product], 200);

        } catch (\Exception $e) {
            return response()->json(["message" => $e->getMessage()], 500);
        }
    }

    /**
     * 批量创建商品接口
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function batchCreate(Request $request)
    {
        try {
            DB::transaction(function () use ($request) {
                $request->validate([
                    "products.*.name" => "required|string",
                    "products.*.description" => "max:255|string|nullable",
                    "products.*.price" => "required|string"
                ]);

                $products = collect($request->get('products', []));

                $products->each(function ($product){
                    Product::create([
                        "name" => $product['name'],
                        "description" => $product['description'] ?? $product['name'],
                        "price" => $product['price'],
                    ]);
                });

            }
            );

            return response()->json(["message" => "Success"], 200);


        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(["message" => $e->getMessage()], 500);
        }
    }


    /**
     * 查询商品详情接口
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function detail(Request $request){
        try {
            $request->validate([
                "id" => "required|string"
            ]);

            $product = Product::query()->find($request->id);

            return response(["message" => !$product ? 'Not found product' : $product], 200);

        }catch (\Exception $e)
        {
            return response()->json(["message" => $e->getMessage()], 500);
        }
    }

    public function delete(Request $request)
    {
        try {
            $request->validate([
                "id" => "required|string"
            ]);

            $product = Product::find($request->id);
            if(!$product) {
                return response()->json(['message' => "Not Found Product"], 200);
            }
            $product->delete();
            return  response()->json(["message" => "Success"], 200);

        }catch (\Exception $e)
        {
            return response()->json(['message' => $e->getMessage()], 500);
        }
    }

}
