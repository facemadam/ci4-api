<?php

namespace App\Controllers;

use CodeIgniter\RESTful\ResourceController;
use Firebase\JWT\JWT;

class AuthJwt extends ResourceController
{
    protected $modelName = 'App\Models\Users';
    protected $format = 'json';
    protected $key = '';
    protected $alg = '';

    public function __construct()
    {
        $this->key = getenv('JWT_SECRET');
        $this->alg = getenv('JWT_ALGORITHMS');
    }

    /**
     * Return an array of resource objects, themselves in array format
     *
     * @return mixed
     */
    public function index()
    {
        //
    }

    /**
     * Return the properties of a resource object
     *
     * @return mixed
     */
    public function show($id = null)
    {
        //
    }

    /**
     * Return a new resource object, with default properties
     *
     * @return mixed
     */
    public function new()
    {
        //
    }

    /**
     * Create a new resource object, from "posted" parameters
     *
     * @return mixed
     */
    public function create()
    {
        $user = $this->model
            ->where('email', $this->request->getVar('email'))
            ->first();
        $verify = password_verify(
            $this->request->getVar('password'),
            $user['password']
        );
        if ($user && $verify) {
            $iat = time();
            $payload = [
                'iss' => 'Issuer', // token issuer
                'sub' => 'Subject', // audience
                'aud' => 'Audience', // subject
                'iat' => $iat, // issued
                'exp' => $iat + 3600, // expire
            ];
            $payload['email'] = $user['email'];
            $token = JWT::encode($payload, $this->key, $this->alg);
        }
        return $this->respond(['token' => isset($token) ? $token : null]);
    }

    /**
     * Return the editable properties of a resource object
     *
     * @return mixed
     */
    public function edit($id = null)
    {
        //
    }

    /**
     * Add or update a model resource, from "posted" properties
     *
     * @return mixed
     */
    public function update($id = null)
    {
        //
    }

    /**
     * Delete the designated resource object from the model
     *
     * @return mixed
     */
    public function delete($id = null)
    {
        //
    }
}
