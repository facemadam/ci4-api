<?php

namespace App\Controllers;

use CodeIgniter\RESTful\ResourceController;

class Users extends ResourceController
{
    protected $modelName = 'App\Models\Users';
    protected $format = 'json';

    /**
     * Return an array of resource objects, themselves in array format
     *
     * @return mixed
     */
    public function index()
    {
        return $this->respond($this->model->findAll());
    }

    /**
     * Return the properties of a resource object
     *
     * @return mixed
     */
    public function show($id = null)
    {
        return $this->respond($this->model->find($id));
    }

    /**
     * Return a new resource object, with default properties
     *
     * @return mixed
     */
    public function new()
    {
        return $this->respond($this->model->first());
    }

    /**
     * Create a new resource object, from "posted" parameters
     *
     * @return mixed
     */
    public function create()
    {
        $user = [
            'name' => $this->request->getVar('name'),
            'email' => $this->request->getVar('email'),
            'password' => password_hash(
                $this->request->getVar('password'),
                PASSWORD_DEFAULT
            ),
        ];
        $this->model->save($user);
        return $this->respond(['id' => $this->model->insertID]);
    }

    /**
     * Return the editable properties of a resource object
     *
     * @return mixed
     */
    public function edit($id = null)
    {
        $user = $this->model->find($id);
        return $this->respond($user ? $this->model->allowedFields : []);
    }

    /**
     * Add or update a model resource, from "posted" properties
     *
     * @return mixed
     */
    public function update($id = null)
    {
        $user = $this->model->select($this->model->allowedFields)->find($id);
        $name = $this->request->getVar('name');
        $password = $this->request->getVar('password');
        if (!empty($name)) {
            $user['name'] = $name;
        }
        if (!empty($password)) {
            $user['password'] = password_hash($password, PASSWORD_DEFAULT);
        }
        $this->model->update($id, $user);
        return $this->respond(['id' => $id]);
    }

    /**
     * Delete the designated resource object from the model
     *
     * @return mixed
     */
    public function delete($id = null)
    {
        return $this->respond($this->model->delete($id));
    }
}
