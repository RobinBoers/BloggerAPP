# BloggrAPP
Webapp to manage, create and publish blogposts, using blogspot. It uses the Google Client Library for PHP to manage your Blogger account

# Features
- Manage posts: Publish, create drafts or delete posts 
- Manage comments: Delete, aprove and add comments
- Download posts and comments as HTML files for an save backup

# How to use?
1. Download the latest release
2. Copy the files over to your server
3. Login with your Google account
4. Enter your blog address
5. You're ready to go!

# How does the Blogger API work?
First you will have to authenticate the user, using oauth2.
After that you will need to get an blog, by URI, or by blogID.
Then you can get posts from the blog. 
If you want to edit, update or revert blogposts to draft you will need to have a postID

You can find templates for these actions in the templates folder.
