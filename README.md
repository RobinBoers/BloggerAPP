# BloggrAPP
Webapp to manage, create and publish blogposts, using blogspot. It uses the [Google Client Library for PHP](https://github.com/googleapis/google-api-php-client "Google Client Library for PHP") to manage your Blogger account

## Features
- Manage posts: Publish, or delete posts 

### Work in progress
These features will be available soon:
- Create drafts
- Manage comments: Delete, aprove and add comments
- Download posts and comments as HTML files for an save backup

## How to use?
1. Download the latest release
2. Copy the files over to your server
3. Go to [https://console.developers.google.com/](https://console.developers.google.com/)
4. Add an project, and tap "Credentials"
5. Tap "Create data" and create an Client-ID OAuth
6. Choose the "Webapp" and enter the path to the files you just copied in the "Authorized diversion URIs" textbox
7. Hit create, and hit ok.
8. Click on the Client-ID you just generated.
9. Click "download JSON"
10. Download the file, and rename it to 'client_secret.json' and copy it to the folder where you also copied the other files to.
11. You're ready to go!

## How does the Blogger API work?
First you will have to authenticate the user, using oauth2.
After that you will need to get an blog, by URI, or by blogID.
Then you can get posts from the blog. 
If you want to edit, update or revert blogposts to draft you will need to have a postID

You can find templates for these actions in the templates folder.

