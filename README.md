## Bohemia Blog

A Laravel example that implemented some features of Blog.

This example is a collection of backend APIs that was implemented on Laravel 8.x .  

### Important Specifications

1. Generate the username from full surname and 3 letter from first name. The possibility of generating duplicate usernames has been managed.
2. To make user authentication, Laravel Sanctum package has been used. 
3. Blog feed posts list sorted by comments count in descendent order.
4. Use soft-deleted technique to delete posts and comments.
5. Posts and comments can be cleaned from trash bin permanently just by admin.
6. Deleted posts and comments can be restore just by admin.
7. Seeders have been used to generate 50K users, 1K posts and 50 comments for each post.
8. Schedulers has been used to delete posts that are more than 3 hours old.

### Endpoints

1. User Register
2. User Login
3. Save post by admin
4. Save comment on post by user
5. Delete post by admin
6. Delete comment by admin
7. Get list of posts publicly
8. Get list of comments of post publicly
9. Get specific post information publicly
10. Get list of deleted posts by admin
11. Get list of comments of deleted post by admin
12. Get list of deleted comments by admin
13. Restore deleted post by admin
14. Restore deleted comment by admin
15. Clean deleted post permanently by admin
16. Clean deleted comment permanently by admin
