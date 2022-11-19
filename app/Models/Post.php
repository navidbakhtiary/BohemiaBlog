<?php

namespace App\Models;

use App\Classes\Creator;
use App\Http\Resources\PostResource;
use App\Http\Responses\CreatedResponse;
use App\Http\Responses\OkResponse;
use App\Interfaces\CreatedModelInterface;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\DB;

class Post extends Model implements CreatedModelInterface
{
    use HasFactory, SoftDeletes;

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

    public function commentsCount()
    {
        return $this->comments()->count();
    }

    public function scopeListItem()
    {
        return $this->select(['id', 'admin_id', 'subject', 'updated_at', DB::raw("SUBSTR(content, 1, 250) as summary")]);
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

    public function sendDeletedResponse()
    {
        return (new OkResponse())->sendOk(
            Creator::createSuccessMessage('post_deleted')
        );
    }
}
