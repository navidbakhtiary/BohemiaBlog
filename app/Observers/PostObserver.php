<?php

namespace App\Observers;

use App\Models\Post;

class PostObserver
{
    /**
     * Handle the user "created" event.
     *
     * @param  \App\Models\user  $user
     * @return void
     */
    public function deleting(Post $post)
    {
        $post->comments()->delete();
    }
}
