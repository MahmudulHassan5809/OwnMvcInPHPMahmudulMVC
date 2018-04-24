<?php 
   class Users extends Controller {
    
      public function __construct()
      {
            $this->userModel = $this->model('User');
      }

      public function register()
      {
          if($_SERVER['REQUEST_METHOD'] == 'POST')
          {
              //process form

              //Sanitize Post  Data
              $_POST = filter_input_array(INPUT_POST,FILTER_SANITIZE_STRING);

              $data = [
                'name' => trim($_POST['name']),
                'email' => trim($_POST['email']),
                'password'=> trim($_POST['password']),
                'confirm_password'=> trim($_POST['confirm_password']),
                'name_err' => '',
                'email_err' => '',
                'password_err' => '',
                'confirm_password_err' => ''
            ];
           //validate 
           if(empty($data['email']))
           {
               $data['email_err'] = 'Please Enter A Email';
           }else {
            if($this->userModel->findUserByEmail($data['email'])){
                $data['email_err'] = 'Email is already Taken';
            }
         }

           if(empty($data['name']))
           {
               $data['name_err'] = 'Please Enter A name';
           }

           if(empty($data['password']))
           {
               $data['password_err'] = 'Please Enter Password';
           }else if(strlen($data['password']<6)){
            $data['password_err'] = 'Password Must Be Greater than Six Char';
           }
          
           
           if(empty($data['confirm_password']))
           {
               $data['confirm_password_err'] = 'Please Enter Confirm Password';
           }else if($data['password'] != $data['confirm_password']){
            $data['confirm_password_err'] = 'Password Doesnot Match';
           }
           
           //make sure error empty

           if(empty($data['email_err']) && empty($data['name_err']) && empty($data['password_err']) && empty($data['confirm_password_err']) ){
               
            //Validated
            
            //hash password
           $data['password'] = password_hash($data['password'],PASSWORD_DEFAULT);
           
           //Register User
           if($this->userModel->register($data)){
                 flash('register_success','You are registered');
                 redirect('users/login');    
           }else{
                  die('S0ome Thing Went Wrong');
           }

            }else{
              //load views with error
              $this->view('users/register',$data);
            }
 
          }
          else{
              //load form
              $data = [
                  'name' => '',
                  'email' => '',
                  'password'=> '',
                  'confirm_password'=> '',
                  'name_err' => '',
                  'email_err' => '',
                  'password_err' => '',
                  'confirm_password_err' => ''
              ];

              //load view
              $this->view('users/register',$data);
          }
      }

      public function login()
      {
        if($_SERVER['REQUEST_METHOD'] == 'POST')
        {
            //process form

            
              //Sanitize Post  Data
              $_POST = filter_input_array(INPUT_POST,FILTER_SANITIZE_STRING);
              
              $data = [
                            
                 'email' => trim($_POST['email']),
                'password'=> trim($_POST['password']),
                'email_err' => '',
                'password_err' => '',
                ];

            if(empty($data['email']))
              {
                 $data['email_err'] = 'Please Enter A Email';
              }

              
            if(empty($data['password']))
            {
               $data['password_err'] = 'Please Enter password';
            }
            //Check For user/email
            if($this->userModel->findUserByEmail($data['email'])){

            }else{
                $data['password_err'] = 'No User Found';
            }

            //make sure error empty

        if(empty($data['email_err'])  && empty($data['password_err'])){
             //Validated
             
             $loggedInUser = $this->userModel->login($data['email'],$data['password']);
             if($loggedInUser){
                 $this->createUserSession($loggedInUser);
             }else{
                 $data['password_err']= 'Password Incoreect';
                 $this->view('users/login',$data);
             }

         }else{
           //load views with error
           $this->view('users/login',$data);
         }
               
        }
        else{
            //load form
            $data = [
                'email' => '',
                'password'=> '',
                'email_error' => '',
                'password_error' => ''
                
            ];

            //load view
            $this->view('users/login',$data);
        }

      }

      public function createUserSession($user)
      {
          $_SESSION['user_id'] = $user->id ;
          $_SESSION['user_email'] = $user->email ;
          $_SESSION['user_name'] = $user->name ;

          redirect('pages/index');
      }

      public function logout()
      {
          unset($_SESSION['user_id']);
          unset($_SESSION['user_email']);
          unset($_SESSION['user_name']);
          session_destroy();
          redirect('posts/index');
      }

      

   }