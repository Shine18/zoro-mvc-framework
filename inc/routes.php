<?php
  # define your routes here
  get("/",function(){
  	echo "Welcome, You're running Zoro MVC Framework";
  });
  get("/404",function(){
  	WebController::error_404();
  },"error_404");

  get("/testing/",function(){
  	TestingController::index();
  	var_dump($GLOBALS['route_paths']);
  });
  post("/testing",function(){

  });
?>
