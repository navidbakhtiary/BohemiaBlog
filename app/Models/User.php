<?php

namespace App\Models;

use App\Classes\Creator;
use App\Http\Resources\UserResource;
use App\Http\Responses\CreatedResponse;
use App\Interfaces\CreatedModelInterface;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Facades\Hash;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable implements CreatedModelInterface
{
    use HasApiTokens, HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'surname',
        'nickname',
        'email',
        'phone',
        'address',
        'password',
        'city',
        'state',
        'zipcode'
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password'
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
    ];

    public function setPasswordAttribute($value)
    {
        $this->attributes['password'] = Hash::make($value);
    }

    public function admins()
    {
        return $this->hasMany(Admin::class);
    }

    public function sendCreatedResponse()
    {
        return (new CreatedResponse())->sendCreated(
            Creator::createSuccessMessage('user_registered'),
            [
                'user' => new UserResource($this)
            ],
        );
    }
}
