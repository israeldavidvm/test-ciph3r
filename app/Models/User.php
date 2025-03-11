<?php

/**
 * @OA\Schema(
 * schema="User",
 * title="User",
 * description="User model"
 *  * @OA\Property(
     * property="id",
     * type="integer",
     * description="The id"
     * ),
     * @OA\Property(
     * property="name",
     * type="string",
     * description="The user name"
     * ),
     * @OA\Property(
     * property="email",
     * type="string",
     * description="The email"
     * ),
     * @OA\Property(
     * property="email_verified_at",
     * type="string",
     * format="date-time",
     * description="The email verification timestamp"
     * ),
     * @OA\Property(
     * property="remember_token",
     * type="string",
     * description="The remember token"
     * ),
     * @OA\Property(
     * property="created_at",
     * type="string",
     * format="date-time",
     * description="Fecha de creación"
     * ),
     * @OA\Property(
     * property="updated_at",
     * type="string",
     * format="date-time",
     * description="Fecha de actualización"
     * )
 * )
 */
namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;
use Exception;
use Filament\Models\Contracts\FilamentUser;
use Filament\Panel;
/**
 * Class User
 *
 * @property int $id
 * @property string $name
 * @property string $email
 * @property Carbon|null $email_verified_at
 * @property string $password
 * @property string|null $remember_token
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 *
 * @package App\Models
 */
class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable;

    protected $table = 'users';

    protected $casts = [
        'email_verified_at' => 'datetime'
    ];

    protected $hidden = [
        'password',
        'remember_token'
    ];

    protected $fillable = [
        'name',
        'email',
        'email_verified_at',
        'password',
        'remember_token'
    ];

	public function initAttributes($attributes) {

		$this->name=$attributes['name'];
        $this->email=$attributes['email'];
        $this->password=$attributes['password'];
      
    }

	public static function validateAttributes($attributes) {

        $validator = Validator::make($attributes, 
        [
            'name' => [
                'required',
                'max:255',
                'regex:/^[\p{L} ]+$/u', // Solo letras y espacios
            ],
            'email' => [
                'required',
                'max:255',
                'email', // Formato de email válido
            ],
            'password' => [
                'required',
                'max:255',
                'min:8', // Longitud mínima
                'regex:/[a-z]/', // Al menos una letra minúscula
                'regex:/[A-Z]/', // Al menos una letra mayúscula
                'regex:/[0-9]/', // Al menos un número
                'regex:/[@$!%*?&]/', // Al menos un carácter especial
            ],
            'action' => [
                'required',
                Rule::in(['create', 'update', 'updateOrCreate']),
            ]
        ])->sometimes('name', 'unique:users,name', function ($input) {
            return strtolower($input->action) == 'create';
        })->sometimes('email', 'unique:users,email', function ($input) {
            return strtolower($input->action) == 'create';
        });
    
        if ($validator->fails()) {
            throw new Exception(implode(', ',array_map(function ($inputErrors){

                return implode(', ',$inputErrors);

            },$validator->errors()->getMessages())));

         }
    }
}