<?php

namespace App\Controllers;

use App\Controllers\BaseController;
use CodeIgniter\API\ResponseTrait;
use App\Models\UserModel;
use Firebase\JWT\JWT;

class User extends BaseController
{
    use ResponseTrait;

    protected $userModel;

    public function __construct()
    {
        $this->userModel = new UserModel();
    }

    public function index()
    {
        return $this->respond(['users' => $this->userModel->findAll()], 200);
    }

    public function register()
    {
        $rules = [
            'name' => ['rules' => 'required|min_length[3]|max_length[255]'],
            'email' => [
                'rules' =>
                    'required|min_length[4]|max_length[255]|valid_email|is_unique[users.email]',
            ],
            'password' => ['rules' => 'required|min_length[8]|max_length[255]'],
            'password_confirm' => [
                'label' => 'confirm password',
                'rules' => 'matches[password]',
            ],
        ];

        if ($this->validate($rules)) {
            $data = [
                'name' => $this->request->getVar('name'),
                'email' => $this->request->getVar('email'),
                'password' => password_hash(
                    $this->request->getVar('password'),
                    PASSWORD_DEFAULT
                ),
            ];
            $this->userModel->save($data);
            return $this->respond(
                ['message' => 'Registered Successfully'],
                200
            );
        } else {
            $response = [
                'errors' => $this->validator->getErrors(),
                'message' => 'Invalid Inputs',
            ];
            return $this->fail($response, 409);
        }
    }

    public function login()
    {
        $email = $this->request->getVar('email');
        $password = $this->request->getVar('password');

        $user = $this->userModel->where('email', $email)->first();
        if (is_null($user)) {
            return $this->respond(
                ['error' => 'Invalid username or password.'],
                401
            );
        }

        $pwd_verify = password_verify($password, $user['password']);
        if (!$pwd_verify) {
            return $this->respond(
                ['error' => 'Invalid username or password.'],
                401
            );
        }

        $key = getenv('JWT_SECRET');
        $iat = time(); // current timestamp value
        $exp = $iat + 3600;
        $payload = [
            'iss' => 'Issuer', // token issuer
            'sub' => 'Subject', // audience
            'aud' => 'Audience', // subject
            'exp' => $exp, // expiration time of token
            'iat' => $iat, //time the JWT issued at
            'email' => $user['email'],
        ];
        $token = JWT::encode($payload, $key, 'HS256');

        $response = [
            'message' => 'Login Succesful',
            'token' => $token,
        ];

        return $this->respond($response, 200);
    }
}
