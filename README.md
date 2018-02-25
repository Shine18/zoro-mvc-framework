# zoro-mvc-framework
A PHP based mvc framework with Full Documentation



## Introduction

              Zoro is an MVC framework written in PHP. It provides easy routing to create any PHP/website quickly. It is based on MVC (Model View Controller) concept.
              You can create entities as models and use them anywhere as an object. Models are directly saved into database on a function call. It is designed to provide you the ease of writing code, easy structure, and reusability.
            

## Requirements

- Apache Server (PHP >= 7.0)
- MySQl Database

## Installation

### Download

              To install:
              
1. Download the zip file
2. Extract into a folder
3. Copy the contents to your server (along with .htaccess)
4. Next step is to [configure](#configure)

### Folder Structure

    zoro-framework/
    ├── assets/
    │   ├── css
    │   └── js
    |
    ├── controllers/
    │   └── WebController.php
    │                
    ├── inc/
    │   ├── init.php
    │   ├── load.php
    │   ├── functions.php
    │   ├── config.php
    │   ├── routes.php
    │   └── process.php
    │
    ├── lib/
    │   ├── Cookie.class.php
    │   ├── Logger.class.php
    │   ├── SQL.class.php
    │   └── WebOptions.class.php
    │
    ├── logs/
    ├── models/
    │   └── Model.php
    │
    ├── public/
    │   ├── images/
    │   └── files/
    │
    ├── views/
    │   ├── layouts/
    │   │    ├── application.php
    │   └── partials/
    │
    ├── .htaccess
    └── index.php

### Configure

              First, open *inc/config.php* in any code editor, and add the values of following constants:
            

**HOST**

                Set it to your domain with http/https protocol e.g https://www.xyz.com or in case, you're working on local environment in xyz folder, then it will be http://localhost/xyz/
              

**For SQL connection, set these config variables:**

1. SQL_HOST
2. SQL_USERNAME
3. SQL_PASSWORD
4. SQL_DATABASE

### Getting Started

#### 1. Create a route

                  Open *inc/routes.php*. and write this code at the end, to create a route.
                  
    <?php 
    
    get("/test/",function(){
      TestingController::index();
    });
    
    ?>

This means that whenever you navigate to /test/, it will run the index function in the TestingController

To know more about routes, navigate to [routing](#routing)

#### 2. Create the controller

                  Create the file TestingController.php in the controllers folder,
                  and put the following code in it.
                  
    <?php 
    
    class TestingController extends WebController{
      public static function index(){
        static::render();
      }
    }
    
    ?>

Every controller should extend from WebController,  render is called here in index function, it will get the respective view from views folder and render it in the browser.

                To learn more about controllers, navigate to [Controllers](#controllers)

#### 3. Create the view

- Navigate to views folder
- Create a folder named "Testing". (Folder names are based on controller naems)
- Create a file named "index.php" in *Testing* folder. (File names are based on function names)

                  Put the following code in that file:
                  
    <h1>Testing ZORO MVC Framework</h1>

                To learn more about views, navigate to [Views](Views)

#### 4. Now head to *http://your-host/test/* in your browser

## Routing

Basic Routing

                  You can create routes in *inc/routes.php*

                  Two functions are available to create routes, post and get
                  You can create any route and pass the function in second parameter, which will run whenever this route is requested
                  
    get("/test",function(){
      
    });
    
    post("/test",function(){
      
    });

URL Parameters

If you want to pass parameters in URL, add your parameter in the route with a colon (:), like this

    get("/post/:handle",function($args){
      echo $args["handle"];
    }):

Any parameters you will pass in route, will be available in function in $args array

Route Names and Paths

Any route you will create, will be available to you as a function in the whole application to output that specfic route url anywhere. 
For example, following routes are created: 

    get("/post/add/",function(){
    
    });
    get("/post/edit/:id",function(){
      
    });
    get("/post/delete/:id",function(){
      
    });

Now, you can call the following functions anywhere to output the specific route/link/path in your application.

    post_add_path();  // will return /post/add/

    post_edit_path( array( "id" => 2) );   // will return /post/edit/2

    post_delete_path( array("id" => 2) );  // will return /post/delete/2

Note: You will have to pass the URI params in array to the functions

                Hint: *You can check/output $GLOBALS['route_paths'] to see, which path functions are available to you.*

Custom Paths

If you want to give custom name to any route, or to make the same post/get routes different, you can pass the route name as the third parameter, like this:

    get("/post/add",function(){
    
    },"add_post");
    
    post("/post/add",function(){
      
    },"save_post");

Now these two paths will be available to you, as the two functions named:

    add_post_path();

    save_post_path();

404 path

                You must defined a route named 'error_404', so the framework can redirect any invalid request to that route. Define a 404 route like this:
                
    get("/404",function(){
      // Handle 404 request here
    },"error_404");

Note: You must name the 404 route *'error_404'*

## Controllers

Basic

Controllers are placed in *controllers* folder. You can put the main data manipulation and other complex logic in controllers.

Every Controller should extend from *WebController* class

Member function of the controller class are called actions

Every action in the controller should be declared as static, so you can call it in the *route.php* without creating an object

A basic controller with an action named *index* will look like this

    class HomeController extends WebController{
      public static function index(){
    
      }
    }

Render View

To render the output in your action, call the render function at the end of your action like this:

    class HomeController extends WebController{
      public static function index(){
        // other stuff here
        static::render();
      }
    }

*render* will look in the views folder, and it will automatically render the *home/index.php* template in the browser. 
 This will use the default *application* layout

                Wanna learn more about views, head to the [Views section](#views)

Custom views

Calling render in any action will find the default view, for example if you call *render* in index function of HomeController, it will look for a file named *index* in the *home* folder.
                

To use any custom view, pass the view path as the first parameter to render

Suppose we have a view file like this *`views/other/home.php`, we use that view like this:*

    class HomeController extends WebController{
      public static function index(){
        //other stuff to do here
        static::render("other/home");
      }
    }

Custom Layout

Calling render in any action will use the default layout `application`

Suppose you have another layout `page-layout.php` in layouts folder

To use any other layout, pass an array in second parameter like this:

    class HomeController extends WebController{
      public static function index(){
        // other stuff here
        static::render(null,array("layout" => "page-layout") );
      }
    }

If you want to use a custom layout for all the actions in a controller, simply declare a static member variable named layout in that controller and assign it the value of your new layout, e.g: 

    class HomeController extends WebController{
      public static $layout = "page-layout";
    
      public static function index(){
        // other stuff here
        static::render( );
      }
    }

                Learn about layouts [here](#layouts)

## Views

Views reside in the `views` folder

Layouts

Layouts are the files which contain universal code which outputs on every page, for example, header, sidebar, footer etc.

Layouts reside in `views/layouts/` folder.

                For example: 
                
    <!DOCTYPE html>
    <html>
      <head>
        <meta charset="UTF-8" />
        <title>Document</title>
      </head>
      <body>
        <header></header>
    
        <main role="main">
          [@content]
        </main>
    
        <footer></footer>
      </body>
    </html>
    

`[@content]` is important in the layout, because the respective view will be rendered where it is placed.

Creating Views: Guidelines

View files should be created in `views` folder.

View files should have `.php` extension

Variables in Views

MVC is made to display dynamic content based upon the request, so if you wanna access the data variables in your views, you can pass them along to render method in second parameter (array).

For example:

We have this code in controller.

    class HomeController extends WebController{
      public static function index(){
        $post = array(
          "title"   => "Using the framework",
          "content" => "Lorem ipsum dolor sit amet, consectetur adipisicing elit. At, iste."
        );
        static::render(null,array( "post" => $post ) );
      }
    }

Now, we can access the `$post` variable in our view like this:

    <h1><?php echo $post["title"]; ?></h1>
    <p><?php echo $post["content"]; ?>

Partials

#### What are partials

The code which is used repeatedly in views is placed in partials, Partials are placed in `views/partials/` folder.

#### Including Partials in views

Suppose you have a partial file in `views/partials/home/header.php`, use the `render_partial` funtion to include that in your view anywhere.

    <?php render_partial(array("path" => "home/header")); ?>

#### Using variables in partials

To use variables in partials, pass the variables along with the path in the array like this:

    <?php render_partial(array("path" => "home/header", "menu" => $menu)); ?>

## Models

Basic

Every entity in your application will be created as model.

Model classes are placed in `modes/` folder.

Every model class should extend from `Model` class

You should name your model file same as the class name, otherwise model won't load.

A very basic 'Book' model will look like this:

    class Book extends Model{
      public function __construct($args=[]){
        // properties go here
        parent::__construct($args);
      }
    }

Note:*In every model's constructor, you should call the parent constructor and pass the arguments to the parent constructor, so it can self assign the properties on the creation of new object of that model.*

Adding Properties

To add properties to your model, you can use the `add_property` method.

`add_property` accepts these parameters:

- property_name (required): Name of the property, same as the column name in sql database
- property_type (required): Type of the property, accepted: (string, integer, boolean, datetime)
- is_protected (optional): If it can be changed or not (default = false)
- is_primary_key (optional): If it is a primary key (default = false)
- is_unique (optional): If it is a unique property (default = false)
- is_encrypted (optional): If it is an encrypted field (default = false)

`add_property($property_name, $property_type, $is_protected, $is_primary, $is_unique, $is_encrypted)`
Suppose, we have a `User` model class like this:

    class User extends Model{
      public function __construct($args=[]){
        $this->add_property("id","integer",true,true,false,false);
        $this->add_property("username","string",false,false,true,false);
        $this->add_property("first_name","string");
        $this->add_property("last_name","string");
        $this->add_property("password","string",false,false,false,true); // password will be automatically encrypted
        $this->add_property("email","string");
      }
    }

Note: You should first create table in sql database, then create these properties according to that table

Encryption of Properties

When you specify encryption for any property in `add_property` method, zoro used it's default encryption method and saves that property value after encryption in database. It uses default md5 encryption. If you want to use your custom encryption for any model, define a method named `encrypt` in your model, like this:
-
                
    class User extends Model{
      public function __construct($args=[]){
        $this->add_property("id","integer",true,true,false,false);
        $this->add_property("username","string",false,false,true,false);
        $this->add_property("first_name","string");
        $this->add_property("last_name","string");
        $this->add_property("password","string",false,false,false,true); // password will be automatically encrypted
        $this->add_property("email","string");
      }
      public function encrypt($value){
        return md5($value);
      }
    }

`encrypt` should take one parameter and return that in encrypted form.

Model Operations

Suppose, we have a model `Book`, with the id,title and author properties, we can perform following operations, on it.

#### Create

    $book = new Book();

                or 
                
    $book = new Book(array(
      "title" => "Learn Programming",
      "author"=> "John Doe"
    ))

#### Accessing/Modifying Properties

    $book->title = "Learn programming in 24 hours";
    echo $book->title;

#### Saving object

    $book->save();   # saves object in database

#### Deleting object

    $book->destroy(); # deletes the object from database

#### Copying object

    $other_book = $book->clone(); 

`clone` returns a copy of object, it does not save the object in database. You will have to call `save` on the clone object to save it in database.

Retrieving objects from database

#### all

Calling all will return all objects of the respective model from database

    $books = Book::all();

#### find

You can use `find` method to search the objects from database with id

    $book = Book::find(2);  // will retrieve the book with id 2

#### find_by

You can search an object with value of another column instead of searching by id.

    $book = Book::find_by("author","John Doe");  // will retrieve book which has author john doe

Note:*If multiple results, it will return an array of objects.*

Load

`load` is use to load the copy of the object from database, overwriting your non-saved changes. 
For example:

    $book = Book::find_by("title","Programming in C"); 
    $book->title = "Programming in C++"; 
    
    $book->load(); // This will load the title again from database

## Libraries

- Libraries are placed in `libs/` folder
- Libraries file name should be the same as class name but with `.class.php` extesnion, e.g SQL.class.php
- Library class name should end with Lib, e.g SQLLib
- If upper naming conventions are not followed, it will prevent the library to load in the framework

SQL Library

                As zoro is built in PHP 7.0, it uses PDO to interact with sql database. 
                
### Methods:

*select*

                    You can do select query by simply calling the select method. It accepts the following paramters: 
`select( $table_name,$columns,$where=null,$args=null,$query_order="DESC" )`

- table_name: Table name
- columns: array of columns to select
- where: array of columns if you want to specify a condition
- args: array of values of the columns specified in where parameter
- query_order: ASC or DESC

    // SELECT * FROM books; 
    $books = SQLLib::select( "books","*"); 
    
    // SELECT * FROM books WHERE author='John Doe';
    $books = SQLLib::select("books","*",array("author"),array("John Doe")); 
    
    // SELECT title,author FROM books; 
    $books = SQLLib::select("books",array('title','author'));

*insert*

                    Insert data in table. It accepts following parameters: 
`insert($table_name,$columns,$values)`

- table_name: Table Name
- columns: Array of column names to insert
- values: Values to insert in the columns specified in second parameter

    // INSERT INTO books (id,title,author) VALUES(null,"Learn Programming", "John Doe"); 
    
    SQLLib::insert("books",array("id","title","author"),array(null,"Learn Programming","John Doe"));

*update*

                    Update column based on row id.  
`update($table_name, $id, $col, $val)`

- table_name: Table name 
- id: id of the row for which to update
- col: column name of which value to update
- val: new value

    // UPDATE books SET title="Learn Programming in 24 hours" WHERE id=2 
    
    SQLLib::update("books",2,"title","Learn Programming in 24 hours");

*update_where*

                    Update column based on column you specify.  
`update_where($table_name,$col,$val,$col_where,$val_where)`

- table_name: Table name 
- col: column name of which value to update
- val: new value
- col_where: condition column to specify in where clause
- val_where: value of condition column to specify in where clause

    // UPDATE books SET title="Learn Programming in 24 hours" WHERE title="Learn Programming" 
    
    SQLLib::update_where("books","title","Learn Programming in 24 hours","title","Learn Programming");

*delete*

                    Delete column based on id you specify.  
`delete($table_name,$id)`

- table_name: Table name 
- id: id of the row to delete

    // DELETE FROM books WHERE id=2 
    
    SQLLib::delete("books",2);

*delete_where*

                    Delete column based on column you specify.  
`delete_where($table_name,$col_where,$val_where)`

- table_name: Table name 
- col_where: condition column to specify in where clause
- val_where: value of condition column to specify in where clause

    // DELETE FROM books WHERE title="Learn Programming"
    
    SQLLib::delete_where("books","title","Learn Programming");

Options Library

It is use to save your application's global and general options/values in database.

Options library is available to you in the whole framework (Controllers and views) in the `$options` variable. 

                  You can call following methods on `$options` variable:

*save_option*

Save any option/value in database. It accepts two parameters: 
`save_option($name,$value)`

- $name: Key of the option ( Should be without spaces, use underscores and lowcase letters )
- $value: Value of the option

    // Saving the site title in database to show on homepage 
    
    $options->save_option("site_title","Learn Programming");

*get_option*

Get any option/value from database. It accepts one parameter: 
`get_option($name)`

- $name: Key of the option

    // Getting the site title from database to show on homepage 
    
    echo $options->get_option("site_title");

Cookies Library

                  Cookies Library is available in controllers, to save/retrieve cookies from user. 

                  It is important that you should set cookies before sending any data to browser.
                

*set*

Sets the cookie in user's browser 
`set($key, $value, $seconds = 86400)`

- key: Key of the cookie
- value: Value of the cookie to save
- seconds: Time for cookie to expire in seconds

    CookieLib::set('token',"2398dfs97df9sdf9",3600);

*get*

Get the cookie from user's browser 
`get($key)`

- key: Key of the cookie

    CookieLib::get('token');

*delete*

Delete the cookie from user's browser 
`delete($key)`

- key: Key of the cookie

    CookieLib::delete('token');

*flush*

Flushes/Deletes all the cookies from user's browser 
`get($key)`

    CookieLib::flush();

