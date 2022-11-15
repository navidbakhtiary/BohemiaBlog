<?php

namespace App\Models;

use App\Classes\Creator;
use App\Http\Resources\PostResource;
use App\Http\Responses\CreatedResponse;
use App\Interfaces\CreatedModelInterface;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Post extends Model implements CreatedModelInterface
{
    use HasFactory;

    protected $fillable = [
        'admin_id', 'subject', 'content'
    ];

    public function author()
    {
        return $this->belongsTo(Admin::class, 'admin_id')->first()->belongsTo(User::class, 'user_id');
    }

    public function comments()
    {
        return $this->hasMany(Comment::class);
    }

    public function sendCreatedResponse()
    {
        return (new CreatedResponse())->sendCreated(
            Creator::createSuccessMessage('post_saved'),
            [
                'post' => new PostResource($this)
            ],
        );
    }
}
