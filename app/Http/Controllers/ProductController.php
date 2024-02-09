<?php

namespace App\Http\Controllers;

use App\Exports\ProductExport;
use App\Http\Requests\ProductionListRequest;
use App\Http\Requests\ProductionCreateRequest;
use App\Imports\ProductImport;
use Illuminate\Http\Request;
use App\Models\Product;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Facades\Excel;

class ProductController extends Controller
{

    /**
     * 查询商品列表
     * @param ProductionListRequest $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function list(Request $request)
    {
        try {
            $page = $request->input("page", 1);
            $page_size = $request->input("page_size", 20);
            $list = Product::query()
                ->when($request->filled('description'), function ($query) use ($request) {
                    $query->where('description', 'like', '%' . $request->input('description') . '%');
                })
                ->when($request->filled('price'), function ($query) use ($request) {
                    $query->where('price', $request->input('price'));
                })
                ->when($request->filled('start_date') && $request->filled('end_date'), function ($query) use ($request){
                    $query->whereDate('created_at', '>=', $request->input('start_date'))
                        ->whereDate('created_at', '<=', $request->input('end_date'));
                })
                ->paginate($page_size, ['*'], 'page', $page);

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

                $products->each(function ($product) {
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
    public function detail(Request $request)
    {
        try {
            $request->validate([
                "id" => "required|string"
            ]);

            $product = Product::query()->find($request->id);

            return response(["message" => !$product ? 'Not found product' : $product], 200);

        } catch (\Exception $e) {
            return response()->json(["message" => $e->getMessage()], 500);
        }
    }

    /**
     * 刪除功能
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function delete(Request $request)
    {
        try {
            $request->validate([
                "id" => "required|string"
            ]);

            $product = Product::findOrFail($request->id);

            $product->delete();
            return response()->json(["message" => "Success"], 200);

        } catch (\Exception $e) {
            return response()->json(['message' => $e->getMessage()], 500);
        }
    }

    public function import(Request $request)
    {
        try {

            $request->validate([
                'file' => 'required|file|mimes:xlsx,xls',
            ]);

            $file = $request->file("file");
            Excel::import(new ProductImport(), $file);

            return response()->json(["message" => "Success"], 200);
        } catch (\Exception $e) {
            return response()->json(["message" => $e->getMessage()], 500);
        }

    }

    public function export()
    {
        try {
            return Excel::download(new ProductExport(), 'file.xlsx');
        } catch (\Exception $e) {
            return response()->json(["message" => $e->getMessage()], 500);
        }
    }


    public function update(Request $request)
    {
        try {
            DB::transaction(function () use ($request){
                $request->validate([
                    'id' => "required|string",
                    "name" => "nullable|string",
                    "description" => "string|max:255|nullable",
                    'price' => "string|nullable",
                ]);

                $product = Product::findOrFail($request->id);

                $product->update([
                    'name' => $request->name,
                    'description' => $request->description,
                    'price' => $request->price,
                ]);
            });

            return response()->json(['product' => 'Success'], 200);

        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(['message' => $e->getMessage()], 500);
        }
    }
}
