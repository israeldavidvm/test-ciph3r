<?php


/**
 * @OA\Schema(
 *     schema="Product",
 *     title="Product",
 *     description="Product model",
 *     @OA\Property(
 *         property="id",
 *         type="integer",
 *         description="The unique identifier of the product"
 *     ),
 *     @OA\Property(
 *         property="name",
 *         type="string",
 *         description="The name of the product"
 *     ),
 *     @OA\Property(
 *         property="description",
 *         type="string",
 *         description="The description of the product"
 *     ),
 *     @OA\Property(
 *         property="price",
 *         type="number",
 *         format="float",
 *         description="The price of the product in the base currency"
 *     ),
 *     @OA\Property(
 *         property="currency_id",
 *         type="integer",
 *         description="The identifier of the base currency"
 *     ),
 *     @OA\Property(
 *         property="tax_cost",
 *         type="number",
 *         format="float",
 *         description="The tax cost of the product"
 *     ),
 *     @OA\Property(
 *         property="manufacturing_cost",
 *         type="number",
 *         format="float",
 *         description="The manufacturing cost of the product"
 *     ),
 *     @OA\Property(
 *         property="created_at",
 *         type="string",
 *         format="date-time",
 *         description="The creation date of the product"
 *     ),
 *     @OA\Property(
 *         property="updated_at",
 *         type="string",
 *         format="date-time",
 *         description="The last update date of the product"
 *     )
 * )
 */
namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Validator;
use Exception;

class Product extends Model
{
	protected $table = 'products';

	protected $casts = [
		'price' => 'float',
		'tax_cost' => 'float',
		'manufacturing_cost' => 'float',
		'currency_id' => 'int'
	];

	protected $fillable = [
		'name',
		'description',
		'price',
		'tax_cost',
		'manufacturing_cost',
		'currency_id'
	];

	public function currency()
	{
		return $this->belongsTo(Currency::class);
	}

	public function product_prices()
	{
		return $this->hasMany(ProductPrice::class);
	}

	    /**
     * Initialize the attributes for the Product model.
     *
     * @param array $attributes
     */
    public function initAttributes($attributes)
    {
        $this->name = $attributes['name'] ?? null;
        $this->description = $attributes['description'] ?? null;
        $this->price = $attributes['price'] ?? null;
        $this->tax_cost = $attributes['tax_cost'] ?? null;
        $this->manufacturing_cost = $attributes['manufacturing_cost'] ?? null;
        $this->currency_id = $attributes['currency_id'] ?? null;
    }

    /**
     * Validate the attributes for the Product model.
     *
     * @param array $attributes
     * @throws Exception
     */
    public static function validateAttributes($attributes)
    {
        $validator = Validator::make($attributes, [
            'name' => [
                'required',
                'string',
                'max:255',
            ],
            'description' => [
                'nullable',
                'string',
                'max:1000',
            ],
            'price' => [
                'required',
                'numeric',
                'min:0',
            ],
            'currency_id' => [
                'required',
                'integer',
                'exists:currencies,id', // Asegúrate de que el ID de la moneda exista
            ],
            'tax_cost' => [
                'nullable',
                'numeric',
                'min:0',
            ],
            'manufacturing_cost' => [
                'nullable',
                'numeric',
                'min:0',
            ],
        ]);

        if ($validator->fails()) {
            throw new Exception(implode(', ', array_map(function ($inputErrors) {
                return implode(', ', $inputErrors);
            }, $validator->errors()->getMessages())));
        }
    }
}
