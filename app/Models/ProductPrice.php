<?php
/**
 * @OA\Schema(
 *     schema="ProductPrice",
 *     title="ProductPrice",
 *     description="Product Price model",
 *     @OA\Property(
 *         property="id",
 *         type="integer",
 *         description="The unique identifier of the product price"
 *     ),
 *     @OA\Property(
 *         property="product_id",
 *         type="integer",
 *         description="The identifier of the product"
 *     ),
 *     @OA\Property(
 *         property="currency_id",
 *         type="integer",
 *         description="The identifier of the currency"
 *     ),
 *     @OA\Property(
 *         property="price",
 *         type="number",
 *         format="float",
 *         description="The price of the product in the specified currency"
 *     ),
 *     @OA\Property(
 *         property="created_at",
 *         type="string",
 *         format="date-time",
 *         description="The creation date of the product price"
 *     ),
 *     @OA\Property(
 *         property="updated_at",
 *         type="string",
 *         format="date-time",
 *         description="The last update date of the product price"
 *     )
 * )
 */
namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Validator;
use Exception;

class ProductPrice extends Model
{
	protected $table = 'product_prices';

	protected $casts = [
		'product_id' => 'int',
		'currency_id' => 'int',
		'price' => 'float'
	];

	protected $fillable = [
		'product_id',
		'currency_id',
		'price'
	];

	public function product()
	{
		return $this->belongsTo(Product::class);
	}

	public function currency()
	{
		return $this->belongsTo(Currency::class);
	}

	    /**
     * Initialize the attributes for the ProductPrice model.
     *
     * @param array $attributes
     */
    public function initAttributes($attributes)
    {
        $this->product_id = $attributes['product_id'] ?? null;
        $this->currency_id = $attributes['currency_id'] ?? null;
        $this->price = $attributes['price'] ?? null;
    }

    /**
     * Validate the attributes for the ProductPrice model.
     *
     * @param array $attributes
     * @throws Exception
     */
    public static function validateAttributes($attributes)
    {
        $validator = Validator::make($attributes, [
            'product_id' => [
                'required',
                'integer',
                'exists:products,id', // Asegúrate de que el ID del producto exista
            ],
            'currency_id' => [
                'required',
                'integer',
                'exists:currencies,id', // Asegúrate de que el ID de la moneda exista
            ],
            'price' => [
                'required',
                'numeric',
                'min:0', // El precio no puede ser negativo
            ],
        ]);

        if ($validator->fails()) {
            throw new Exception(implode(', ', array_map(function ($inputErrors) {
                return implode(', ', $inputErrors);
            }, $validator->errors()->getMessages())));
        }
    }
}
