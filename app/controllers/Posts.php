<?php 
 class Posts extends Controller {
     
    public function __construct()
    {
        if(!isset($_SESSION['user_id'])){
            redirect('users/login');
          }

        $this->postModel = $this->model('Post');
        $this->userModel = $this->model('User');
    }



    public function index()
    {
        $posts = $this->postModel->getPosts();
        $data = [
            'posts' =>$posts
        ];
        $this->view('posts/index',$data);
    }

    public function add(){
        if($_SERVER['REQUEST_METHOD'] == 'POST')
        {
            //process form

            //Sanitize Post  Data
            $_POST = filter_input_array(INPUT_POST,FILTER_SANITIZE_STRING);

            $data = [
              'title' => trim($_POST['title']),
              'body' => trim($_POST['body']),
              'user_id'=>$_SESSION['user_id'],
              'title_err' => '',
              'body_err' => ''
          ];
         //validate 
         if(empty($data['title']))
         {
             $data['title_err'] = 'Please Enter A Title';
         }

         if(empty($data['body']))
         {
             $data['body_err'] = 'Please Enter Body';
         }

         
         //make sure error empty

         if(empty($data['title_err']) && empty($data['body_err'])){
             
          //Validated
          
         
         
         //Insert Post
         if($this->postModel->addPost($data)){
               flash('post_msg','Your Post is Added');
               redirect('posts');    
         }else{
                die('Some Thing Went Wrong');
         }

          }else{
            //load views with error
            $this->view('posts/add',$data);
          }

        }  
      
      
    else{
        $data = [
            'title' =>'',
            'body' =>''
        ];
        $this->view('posts/add',$data);
    }
}

    public function show($id){
      $post = $this->postModel->getPostById($id);
      $user = $this->userModel->getUserById($post->user_id);
       $data = [
       
         'post'=> $post,
         'user'=>$user
       ];

       $this->view('posts/show',$data);  
    }


    public function edit($id)
    {
        
        if($_SERVER['REQUEST_METHOD'] == 'POST')
        {
            //process form

            //Sanitize Post  Data
            $_POST = filter_input_array(INPUT_POST,FILTER_SANITIZE_STRING);

            $data = [
              'id' => $id  ,
              'title' => trim($_POST['title']),
              'body' => trim($_POST['body']),
              'user_id'=>$_SESSION['user_id'],
              'title_err' => '',
              'body_err' => ''
          ];
         //validate 
         if(empty($data['title']))
         {
             $data['title_err'] = 'Please Enter A Title';
         }

         if(empty($data['body']))
         {
             $data['body_err'] = 'Please Enter Body';
         }

         
         //make sure error empty

         if(empty($data['title_err']) && empty($data['body_err'])){
             
          //Validated
          
         
         
         //update Post
         if($this->postModel->updatePost($data)){
               flash('post_msg','Your Post is Updated');
               redirect('posts');    
         }else{
                die('Some Thing Went Wrong');
         }

          }else{
            //load views with error
            $this->view('posts/edit',$data);
          }

        }  
      
      
    else{
        $post = $this->postModel->getPostById($id);
        //check for owner
        if($post->user_id != $_SESSION['user_id']){
           redirect('post');
        }
        $data = [
            'id'=>$id,
            'title' =>$post->title ,
            'body' =>$post->body
        ];
        $this->view('posts/edit',$data);
    }

    }

    public function delete($id)
    {
        if($_SERVER['REQUEST_METHOD'] == 'POST')
        {
            $post = $this->postModel->getPostById($id);
            //check for owner
            if($post->user_id != $_SESSION['user_id']){
               redirect('post');
            }
         if( $this->postModel->deletePost($id)){
            flash('post_msg','Your Post is Deleted');
            redirect('posts');
         }else{
             die('Something Went Wrong');
         }
        }else{
            redirect('posts');
        }   
    }


 }