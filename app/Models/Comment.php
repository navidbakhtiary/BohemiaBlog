<?php

namespace App\Models;

use App\Classes\Creator;
use App\Http\Resources\CommentResource;
use App\Http\Responses\CreatedResponse;
use App\Interfaces\CreatedModelInterface;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Comment extends Model implements CreatedModelInterface
{
    use HasFactory;

    protected $fillable = [
        'post_id', 'user_id', 'content'
    ];

    public function post()
    {
        return $this->belongsTo(Post::class);
    }
    
    public function sendCreatedResponse()
    {
        return (new CreatedResponse())->sendCreated(
            Creator::createSuccessMessage('comment_saved'),
            [
                'comment' => new CommentResource($this)
            ],
        );
    }
}
