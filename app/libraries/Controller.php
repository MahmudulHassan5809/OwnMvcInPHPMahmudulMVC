<?php 

  /*
  *Base Controller
  *LOads the models and views
  */

  class Controller
  {
      //Load Model

      public function model($model)
      {
          //Require model file

          require_once '../app/models/'.$model.'.php';

          //Instantiate model
       return new $model();
      }

      //Load view

      public function view($view , $data=[])
      {
         //Check For The view Files
         if(file_exists('../app/views/'.$view.'.php')){
             require_once '../app/views/'.$view.'.php';
         }else{
             //View doesnot exists
             die('View Does Not Exists');
         }
      }
  }