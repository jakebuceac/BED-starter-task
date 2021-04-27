# BED New Starter Project
## Brief
Created an API for powering a blog.
 
This API would power the public facing blog and its admin panel.

Just focused on the API for now.

## Requirements
This API can:
- Be a RESTful JSON API
    - Can handle 404s and 405s
- Has user, post, and tag models
    - Users have many posts
    - Posts have and belong to many tags
    - Uses foreign keys and configure model relationships
    - Posts body of posts are written in Markdown
    - Uses Laravel's resource classes for transforming the post's Markdown into HTML on the fly
 - Allow users to log in
    - There is an endpoint for logging in with an email address and a password
    - If the credentials are valid then a token is then generated
    - This token is then be passed in subsequent HTTP requests using the Authorization header
    - Uses Laravel Sanctum for generating the tokens
 - Allows certain users to do certain things
    - Guests are be able to read posts, users, and tags
    - Users can do this as well but can also create, update, and delete their own posts (but not another user's posts)
    - Users can also create, read, and update tags
    - Users can delete a tag too (only if it's not in use by any posts though)
    - Uses Laravel's policies for enforcing this logic
 - Has tests
    - Every endpoint has at least one feature test
    - Uses factories to populate the database
 - Redis
    - Laravel's cache is configured to use Redis
    - Laravel's queues are configured to use Redis
- Commands
    - There is a scheduled command which invalidates tokens which have expired
- Jobs
    - There is a queued job for sending an email to a user when they log in
    - This email is a security email (similar to the one you'd get from Google after a new log in)
    - In it displays the IP address from which the log in request originated
    - Used MailHog to simulate an SMTP server
