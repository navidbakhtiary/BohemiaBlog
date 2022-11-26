<?php

namespace App\Models;

use App\Classes\Creator;
use App\Http\Resources\DeletedPostInformationResource;
use App\Http\Resources\PostInformationResource;
use App\Http\Resources\PostResource;
use App\Http\Responses\CreatedResponse;
use App\Http\Responses\OkResponse;
use App\Interfaces\CreatedModelInterface;
use App\Interfaces\DeletedModelInterface;
use App\Interfaces\ShowModelInterface;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\DB;

class Post extends Model implements CreatedModelInterface, DeletedModelInterface, ShowModelInterface
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

    public function deletedComments()
    {
        return $this->hasMany(Comment::class)->onlyTrashed();
    }

    public function scopeDeletedListItem()
    {
        return $this->select(['id', 'admin_id', 'subject', 'created_at', 'deleted_at', DB::raw("SUBSTR(content, 1, 250) as summary")]);
    }

    public function scopeListItem()
    {
        return $this->select(['id', 'admin_id', 'subject', 'updated_at', DB::raw("SUBSTR(content, 1, 250) as summary")]);
    }

    public function sendCleanedResponse()
    {
        return (new OkResponse())->sendOk(
            Creator::createSuccessMessage('deleted_post_permanently_cleaned')
        );
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

    public function sendDeletedInformationResponse()
    {
        return (new OkResponse())->sendOk(
            Creator::createSuccessMessage('deleted_post_got'),
            ['deleted post' => new DeletedPostInformationResource($this)]
        );
    }

    public function sendDeletedResponse()
    {
        return (new OkResponse())->sendOk(
            Creator::createSuccessMessage('post_deleted')
        );
    }

    public function sendInformationResponse()
    {
        return (new OkResponse())->sendOk(
            Creator::createSuccessMessage('post_got'), ['post' => new PostInformationResource($this)]
        );
    }

    public function sendRestoredResponse()
    {
        return (new OkResponse())->sendOk(
            Creator::createSuccessMessage('deleted_post_restored'),
            [
                'restored post' => new PostInformationResource($this)
            ],
        );
    }
}
