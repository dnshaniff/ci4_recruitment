<?php

namespace App\Controllers;

use App\Controllers\BaseController;
use App\Models\UserModel;
use Config\Database;
use Exception;
use Throwable;

class UserController extends BaseController
{
    protected $userModel;

    public function __construct()
    {
        $this->userModel = new UserModel();
    }

    public function index()
    {
        return view('users/index', ['title' => 'User Management']);
    }

    public function datatable()
    {
        $request = service('request');

        $draw   = (int) $request->getGet('draw');
        $start  = (int) $request->getGet('start');
        $length = (int) $request->getGet('length');

        $search = $request->getGet('search');
        $searchValue = trim($search['value'] ?? '');

        $baseQuery = $filteredQuery = $this->userModel->datatable();

        if (!is_admin()) {
            $baseQuery->where('users.deleted_at', null);

            $filteredQuery->where('users.deleted_at', null);
        }

        $totalRecords = clone $baseQuery;
        $recordsTotal = $totalRecords->countAllResults();

        $filteredQuery = clone $baseQuery;
        if ($searchValue !== '') {
            $filteredQuery->groupStart()
                ->like('users.name', $searchValue)
                ->orLike('users.email', $searchValue)
                ->groupEnd();
        }


        $recordsFiltered = clone $filteredQuery;
        $recordsFiltered = $recordsFiltered->countAllResults();

        $users = $filteredQuery->orderBy('users.id', 'DESC')->limit($length, $start)->get()->getResultArray();

        $data = [];
        foreach ($users as $key => $user) {
            $data[] = [
                'fake_id' => $start + $key + 1,
                'id' => $user['id'],
                'name' => $user['name'],
                'email' => $user['email'],
                'creator' => $user['creator'],
                'editor' => $user['editor'],
                'deleter' => $user['deleter'],
                'deleted_at' => $user['deleted_at'],
                'created_at' => $user['created_at'],
                'updated_at' => $user['updated_at'],
            ];
        }

        return $this->response->setJSON([
            'draw' => $draw,
            'recordsTotal' => $recordsTotal,
            'recordsFiltered' => $recordsFiltered,
            'data' => $data,
        ]);
    }

    public function store()
    {
        if (!$this->request->isAJAX()) {
            return $this->response->setJSON(['status' => 'error', 'message' => 'Invalid request'])->setStatusCode(400);
        }

        $rules = [
            'name' => [
                'rules' => 'required|min_length[4]',
                'errors' => [
                    'required' => 'Name is required',
                    'min_length' => 'Name minimum 4 characters',
                ],
            ],
            'email' => [
                'rules' => 'required|valid_email|is_unique[users.email]',
                'errors' => [
                    'required' => 'Email is required',
                    'valid_email' => 'Email format is invalid',
                    'is_unique' => 'Email already exists',
                ],
            ],
            'password' => [
                'rules' => 'required|min_length[8]|regex_match[/^(?=.*[a-z])(?=.*[A-Z]).+$/]',
                'errors' => [
                    'required' => 'Password is required',
                    'min_length' => 'Password minimum 8 characters',
                    'regex_match' =>
                    'Password must contain uppercase and lowercase letters',
                ],
            ],
        ];

        if (!$this->validate($rules)) {
            return $this->response->setJSON(['status' => 'error', 'message' => 'Validation failed', 'errors' => $this->validator->getErrors()])->setStatusCode(422);
        }

        $db = Database::connect();

        try {
            $db->transBegin();

            $this->userModel->insert([
                'name' => $this->request->getPost('name'),
                'email' => $this->request->getPost('email'),
                'password' => password_hash($this->request->getPost('password'), PASSWORD_DEFAULT),
                'created_by' => auth_user()['id'],
                'updated_by' => auth_user()['id'],
            ]);

            if ($db->transStatus() === false) {
                throw new Exception('Database transaction failed');
            }

            $db->transCommit();

            return $this->response->setJSON(['status' => 'success', 'message' => 'User created successfully'])->setStatusCode(201);
        } catch (Throwable $e) {
            $db->transRollback();

            log_message('error', $e->getMessage());

            return $this->response->setJSON(['status' => 'error', 'message' => 'Failed to create user'])->setStatusCode(500);
        }
    }

    public function show($id)
    {
        $user = $this->userModel->find($id);

        if (!$user) {
            return $this->response->setJSON(['status' => 'error', 'message' => 'User not found',])->setStatusCode(404);
        }

        return $this->response->setJSON([
            'id' => $user['id'],
            'name' => $user['name'],
            'email' => $user['email'],
        ], 200);
    }

    public function update($id)
    {
        $user = $this->userModel->find($id);

        if (!$user) {
            return $this->response->setJSON(['status' => 'error', 'message' => 'User not found',])->setStatusCode(404);
        }

        $rules = [
            'name' => [
                'rules' => 'required|min_length[4]',
                'errors' => [
                    'required' => 'Name is required',
                    'min_length' => 'Name minimum 4 characters',
                ],
            ],
            'email' => [
                'rules' => "required|valid_email|is_unique[users.email,id,{$id}]",
                'errors' => [
                    'required' => 'Email is required',
                    'valid_email' => 'Email format is invalid',
                    'is_unique' => 'Email already exists',
                ],
            ],
        ];

        $password = $this->request->getPost('password');

        if (!empty($password)) {
            $rules['password'] = [
                'rules' => 'permit_empty|min_length[8]|regex_match[/^(?=.*[a-z])(?=.*[A-Z]).+$/]',
                'errors' => [
                    'min_length' => 'Password minimum 8 characters',
                    'regex_match' => 'Password must contain uppercase and lowercase letters',
                ],
            ];
        }

        if (!$this->validate($rules)) {
            return $this->response->setJSON(['status' => 'error', 'message' => 'Validation failed', 'errors' => $this->validator->getErrors()])->setStatusCode(422);
        }

        $db = Database::connect();

        try {
            $db->transBegin();

            $data = [
                'name' => $this->request->getPost('name'),
                'email' => $this->request->getPost('email'),
                'updated_by' => auth_user()['id'],
            ];

            if (!empty($password)) {
                $data['password'] = password_hash($password, PASSWORD_DEFAULT);
            }

            $this->userModel->update($id, $data);

            if ($db->transStatus() === false) {
                throw new Exception('Database transaction failed');
            }

            $db->transCommit();

            return $this->response->setJSON(['status' => 'success', 'message' => 'User updated successfully'])->setStatusCode(200);
        } catch (Throwable $e) {
            $db->transRollback();

            log_message('error', $e->getMessage());

            return $this->response->setJSON(['status' => 'error', 'message' => 'Failed to update user'])->setStatusCode(500);
        }
    }

    public function delete($id)
    {
        $user = $this->userModel->find($id);

        if (!$user) {
            return $this->response->setJSON(['status' => 'error', 'message' => 'User not found',])->setStatusCode(404);
        }

        $this->userModel->update($id, ['deleted_by' => auth_user()['id']]);

        $this->userModel->delete($id);

        return $this->response->setJSON(['status' => 'success', 'message' => 'User deleted successfully'], 200);
    }

    public function restore($id)
    {
        $user = $this->userModel->withDeleted()->find($id);

        if (!$user) {
            return $this->response->setJSON(['status' => 'error', 'message' => 'User not found'])->setStatusCode(404);
        }

        if ($user['deleted_at'] === null) {
            return $this->response->setJSON(['status' => 'info', 'message' => 'User is not deleted']);
        }

        $this->userModel->builder()->where('id', $id)->update(['deleted_at' => null, 'deleted_by' => null]);

        return $this->response->setJSON(['status' => 'success', 'message' => 'User restored successfully'], 200);
    }

    public function force($id)
    {
        $user = $this->userModel->withDeleted()->find($id);

        if (!$user) {
            return $this->response->setJSON(['status' => 'error', 'message' => 'User not found'])->setStatusCode(404);
        }

        if ($user['deleted_at'] === null) {
            return $this->response->setJSON(['status' => 'info', 'message' => 'User is not deleted']);
        }

        $this->userModel->delete($id, true);

        return $this->response->setJSON(['status' => 'success', 'message' => 'User permanently deleted'], 200);
    }
}
