# reactPhpBlogTemplateBackend
Please see the corresponding frontend repository [here](https://github.com/emily-daitch/reactPhpBlogTemplate2).

# Getting started
If you are using XAMPP as discussed in the frontend repository, you will want to put the contents of this repository uner the same C:\(Path to your XAMPP installation)/xampp/htdocs/reactPhpBlogTemplate2 folder.
<br/><br/>
This is the PHP backend portion of the React/PHP blog template. The .htaccess file is an Apache configuration file which instructs the server to direct requests to routing.php. Under the controllers directory you will find a PostsController and GoogleController.<br/>
The PostsController can be used to fetch example/test posts from https://jsonplaceholder.typicode.com/posts in order to populate your database for testing. It also controls fetching existing posts to be displayed on the frontend.<br/>
<br/>
Under the services directory DB.php handles the connection to your database. You will want a file (protected from access, of course) in which to keep your database credentials so that DB.php can authenticate. Do not commit this file to your fork of this repository.<br/>
-- Work in progress. --<br/>

# Setting up your database with XAMPP
-- Work in progress. --<br/>

# Setting up your database with Hostinger (for a live site)
-- Work in progress. --<br/>

# GoogleController
The GoogleController supports a feature I implemented for my personal site which is not part of the typical use case for portfolio/blog sites. I have a page where I display exercise data from an app called Strava, and on this page I show a map of one of the routes that I exercised on. <br/>
The GoogleController contains an endpoint which takes a "polyline" defining a route and returns a google "static map", which is a photo that displays the route given. If you want to try this out you will need to create a Google developer profile and set up API keys. 
