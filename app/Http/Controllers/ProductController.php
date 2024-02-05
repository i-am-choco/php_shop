<?php

namespace App\Http\Controllers;

use App\Http\Requests\ProductionListRequest;
use App\Http\Requests\ProductionCreateRequest;
use Illuminate\Http\Request;
use App\Models\Product;
use mysql_xdevapi\Exception;
use Illuminate\Validation\ValidationException;

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
        }catch (\Exception $e )
        {
            return  response()->json(["message" => $e->getMessage()], 500);
        }

    }

    public function create(ProductionCreateRequest $request)
    {
        try {
            return response()->json(["message" => $request->get('name')], 200);

        }catch (\Exception $e)
        {
            return response()->json(["message" => $e->validator->getMessageBag()->all()], 500);
        }
    }
}
