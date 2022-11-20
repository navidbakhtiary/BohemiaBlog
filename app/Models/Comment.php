<?php

namespace App\Models;

use App\Classes\Creator;
use App\Http\Resources\CommentResource;
use App\Http\Responses\CreatedResponse;
use App\Http\Responses\OkResponse;
use App\Interfaces\CreatedModelInterface;
use App\Interfaces\DeletedModelInterface;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Comment extends Model implements CreatedModelInterface, DeletedModelInterface
{
    use HasFactory, SoftDeletes;

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

    public function sendDeletedResponse()
    {
        return (new OkResponse())->sendOk(
            Creator::createSuccessMessage('comment_deleted')
        );
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
