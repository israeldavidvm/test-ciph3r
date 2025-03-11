<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Models\Product;
use App\Models\ProductPrice;
use Illuminate\Http\Request;

class ProductController extends Controller
{
    /**
     * @OA\Get(
     *     path="/api/v1/products",
     *     tags={"Product"},
     *     security={{"sanctum": {}}},  
     *     summary="List all products",
     *     @OA\Response(
     *         response=200,
     *         description="A list of products",
     *     )
     * )
     */
    public function index()
    {
        return response()->json(Product::paginate(10), 200);
    }

    /**
     * @OA\Post(
     *     path="/api/v1/products",
     *     tags={"Product"},
     *     security={{"sanctum": {}}},  
     *     summary="Create a new product",
     *     @OA\RequestBody(
     *         required=true,
     *     ),
     *     @OA\Response(
     *         response=201,
     *         description="Product created successfully",
     *     ),
     *     @OA\Response(
     *         response=400,
     *         description="Invalid input"
     *     )
     * )
     */
    public function store(Request $request)
    {

        Product::validateAttributes($request->all());

        $product = Product::create($request->all());
        return response()->json($product, 201);
    }

    /**
     * @OA\Get(
     *     path="/api/v1/products/{id}",
     *     tags={"Product"},
     *     security={{"sanctum": {}}},  
     *     summary="Get a product by ID",
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Product found",
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Product not found"
     *     )
     * )
     */
    public function show($id)
    {
        $product = Product::find($id);
        if (!$product) {
            return response()->json(['message' => 'Not Found'], 404);
        }
        return response()->json($product, 200);
    }

    /**
     * @OA\Put(
     *     path="/api/v1/products/{id}",
     *     tags={"Product"},
     *     security={{"sanctum": {}}},  
     *     summary="Update a product by ID",
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\RequestBody(
     *         required=true,
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Product updated successfully",
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Product not found"
     *     )
     * )
     */
    public function update(Request $request, $id)
    {

        Product::validateAttributes($request->all());

        $product = Product::find($id);
        if (!$product) {
            return response()->json(['message' => 'Not Found'], 404);
        }
        $product->update($request->all());
        return response()->json($product, 200);
    }

    /**
     * @OA\Delete(
     *     path="/api/v1/products/{id}",
     *     tags={"Product"},
     *     security={{"sanctum": {}}},  
     *     summary="Delete a product by ID",
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Product deleted successfully"
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Product not found"
     *     )
     * )
     */
    public function destroy($id)
    {
        $product = Product::find($id);
        if (!$product) {
            return response()->json(['message' => 'Not Found'], 404);
 }
        $product->delete();
        return response()->json(['message' => 'Deleted'], 200);
    }

    /**
     * @OA\Get(
     *     path="/api/v1/products/{id}/prices",
     *     tags={"Product"},
     *     security={{"sanctum": {}}},  
     *     summary="Get the list of prices for a product",
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="List of prices for the product",
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Product not found"
     *     )
     * )
     */
    public function getPrices($id)
    {
        $product = Product::find($id);
        if (!$product) {
            return response()->json(['message' => 'Not Found'], 404);
        }
        return response()->json($product->product_prices, 200);
    }

    /**
     * @OA\Post(
     *     path="/api/v1/products/{id}/prices",
     *     tags={"Product"},
     *     security={{"sanctum": {}}},  
     *     summary="Create a new price for a product",
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\RequestBody(
     *         required=true,
     *     ),
     *     @OA\Response(
     *         response=201,
     *         description="Price created successfully",
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Product not found"
     *     )
     * )
     */
    public function storePrice(Request $request, $id)
    {

        $product = Product::find($id);
        if (!$product) {
            return response()->json(['message' => 'Not Found'], 404);
        }

        $request->merge(['product_id' => $id]);

        ProductPrice::validateAttributes($request->all());

        $price = new ProductPrice();
        $price->initAttributes($request->all());
        $price->save();
        
        return response()->json($price, 201);
    }
}