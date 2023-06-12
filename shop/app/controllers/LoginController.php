<?php

namespace MyApp\Controllers;

use Phalcon\Mvc\Controller;

session_start();
class LoginController extends Controller
{
    public function indexAction()
    {

        //  default
    }
    public function loginAction()
    {
        if ($this->request->isPost()) {
            $email = $this->request->getPost('email');
            $pass = $this->request->getPost("password");
            $role = $this->request->getPost("role");

            $collection = $this->mongo->Users;
            $data = $collection->findOne(["email" => $email, "password" => $pass]);
            $admin = $data['role'];
            $name = $data['name'];
            if ($admin) {
                $this->logger->excludeAdapters(['error']);
                $this->logger->info("Login details are $email , $role ,$name");
                $this->response->redirect('login');
            } else {
                $this->logger->excludeAdapters(['login']);
                $this->logger->info("Authentication Failed");
                $this->response->redirect('signup');
              
            }
        }
    }
}
