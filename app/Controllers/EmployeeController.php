<?php

namespace App\Controllers;

use  App\Controllers\BaseController;
use App\Models\EmployeeModel;
use Config\Database;
use Exception;
use Throwable;

class EmployeeController extends BaseController
{
    protected $employeeModel;

    public function __construct()
    {
        $this->employeeModel = new EmployeeModel();
    }

    public function index()
    {
        return view('employees/index', ['title' => 'Employee Management']);
    }

    public function datatable()
    {
        $request = service('request');

        $draw   = (int) $request->getGet('draw');
        $start  = (int) $request->getGet('start');
        $length = (int) $request->getGet('length');

        $search = $request->getGet('search');
        $searchValue = trim($search['value'] ?? '');

        $baseQuery = $filteredQuery = $this->employeeModel->datatable();

        if (!is_admin()) {
            $baseQuery->where('employees.deleted_at', null);

            $filteredQuery->where('employees.deleted_at', null);
        }

        $totalRecords = clone $baseQuery;
        $recordsTotal = $totalRecords->countAllResults();

        $filteredQuery = clone $baseQuery;
        if ($searchValue !== '') {
            $filteredQuery->groupStart()
                ->like('employees.name', $searchValue)
                ->orLike('employees.email', $searchValue)
                ->groupEnd();
        }


        $recordsFiltered = clone $filteredQuery;
        $recordsFiltered = $recordsFiltered->countAllResults();

        $employees = $filteredQuery->orderBy('employees.id', 'DESC')->limit($length, $start)->get()->getResultArray();

        $data = [];
        foreach ($employees as $key => $employee) {
            $data[] = [
                'fake_id' => $start + $key + 1,
                'id' => $employee['id'],
                'employee_code' => $employee['employee_code'],
                'photo_path' => $employee['photo_path'],
                'photo_name' => $employee['photo_name'],
                'name' => $employee['name'],
                'email' => $employee['email'],
                'position' => $employee['position'],
                'gender' => $employee['gender'],
                'creator' => $employee['creator'],
                'editor' => $employee['editor'],
                'deleter' => $employee['deleter'],
                'deleted_at' => $employee['deleted_at'],
                'created_at' => $employee['created_at'],
                'updated_at' => $employee['updated_at'],
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
        $rules = [
            'employee_code' => [
                'rules' => 'required|is_unique[employees.employee_code]',
                'errors' => [
                    'required' => 'Employee code is required',
                    'is_unique' => 'Employee code already exists',
                ],
            ],
            'name' => [
                'rules' => 'required|min_length[3]',
                'errors' => [
                    'required' => 'Name is required',
                    'min_length' => 'Name minimum 3 characters',
                ],
            ],
            'email' => [
                'rules' => 'required|valid_email|is_unique[employees.email]',
                'errors' => [
                    'required' => 'Email is required',
                    'valid_email' => 'Invalid email address',
                    'is_unique' => 'Email already exists',
                ],
            ],
            'gender' => [
                'rules' => 'required|in_list[Male,Female]',
                'errors' => [
                    'required' => 'Gender is required',
                    'in_list' => 'Invalid gender selected',
                ],
            ],
            'position' => [
                'rules' => 'required',
                'errors' => [
                    'required' => 'Position is required',
                ],
            ],
            'photo' => [
                'rules' => 'uploaded[photo]' . '|max_size[photo,300]' . '|mime_in[photo,image/jpg,image/jpeg]',
                'errors' => [
                    'uploaded' => 'Photo is required',
                    'max_size' => 'Photo maximum 300KB',
                    'mime_in' => 'Photo must be JPG/JPEG',
                ],
            ],
        ];

        if (!$this->validate($rules)) {
            return $this->response->setJSON(['status' => 'error', 'message' => 'Validation failed', 'errors' => $this->validator->getErrors()])->setStatusCode(422);
        }

        $photo = $this->request->getFile('photo');

        $randomName = $photo->getRandomName();
        $originalName = $photo->getClientName();
        $mimeType = $photo->getMimeType();
        $size = $photo->getSize();

        $relativePath = 'uploads/employees/' . $randomName;

        $absolutePath = FCPATH . $relativePath;

        $db = Database::connect();

        try {
            $db->transBegin();

            $photo->move(FCPATH . 'uploads/employees', $randomName);

            $this->employeeModel->insert([
                'employee_code' => $this->request->getPost('employee_code'),
                'name' => $this->request->getPost('name'),
                'email' => $this->request->getPost('email'),
                'gender' => $this->request->getPost('gender'),
                'position' => $this->request->getPost('position'),
                'photo_name' => $originalName,
                'photo_path' => $relativePath,
                'photo_mime' => $mimeType,
                'photo_size' => $size,
                'created_by' => auth_user()['id'],
                'updated_by' => auth_user()['id'],
            ]);

            if ($db->transStatus() === false) {
                throw new Exception('Database transaction failed');
            }

            $db->transCommit();

            return $this->response->setJSON(['status' => 'success', 'message' => 'Employee created successfully'])->setStatusCode(201);
        } catch (Throwable $e) {
            $db->transRollback();

            if (file_exists($absolutePath)) {
                unlink($absolutePath);
            }

            log_message('error', $e->getMessage());

            return $this->response->setJSON(['status' => 'error', 'message' => 'Failed to create employee'])->setStatusCode(500);
        }
    }

    public function show($id)
    {
        $employee = $this->employeeModel->find($id);

        if (!$employee) {
            return $this->response->setJSON(['status' => 'error', 'message' => 'Employee not found'])->setStatusCode(404);
        }

        return $this->response->setJSON([
            'id' => $employee['id'],
            'employee_code' => $employee['employee_code'],
            'name' => $employee['name'],
            'email' => $employee['email'],
            'gender' => $employee['gender'],
            'position' => $employee['position'],
            'photo_path' => $employee['photo_path'],
        ]);
    }

    public function update($id)
    {
        $employee = $this->employeeModel->find($id);

        if (!$employee) {
            return $this->response->setJSON(['status' => 'error', 'message' => 'Employee not found'])->setStatusCode(404);
        }

        $rules = [
            'employee_code' => [
                'rules' =>  "required|is_unique[employees.employee_code,id,{$id}]",
                'errors' => [
                    'required' => 'Employee code is required',
                    'is_unique' => 'Employee code already exists',
                ],
            ],
            'name' => [
                'rules' => 'required|min_length[3]',
                'errors' => [
                    'required' => 'Name is required',
                    'min_length' => 'Name minimum 3 characters',
                ],
            ],
            'email' => [
                'rules' => "required|valid_email|is_unique[employees.email,id,{$id}]",
                'errors' => [
                    'required' => 'Email is required',
                    'valid_email' => 'Invalid email address',
                    'is_unique' => 'Email already exists',
                ],
            ],
            'gender' => [
                'rules' => 'required|in_list[Male,Female]',
                'errors' => [
                    'required' => 'Gender is required',
                    'in_list' => 'Invalid gender selected',
                ],
            ],
            'position' => [
                'rules' => 'required',
                'errors' => [
                    'required' => 'Position is required',
                ],
            ],
            'photo' => [
                'rules' => 'permit_empty' . '|max_size[photo,300]' . '|mime_in[photo,image/jpg,image/jpeg]',
                'errors' => [
                    'max_size' => 'Photo maximum 300KB',
                    'mime_in' => 'Photo must be JPG/JPEG',
                ],
            ],
        ];

        if (!$this->validate($rules)) {
            return $this->response->setJSON(['status' => 'error', 'message' => 'Validation failed', 'errors' => $this->validator->getErrors()])->setStatusCode(422);
        }

        $db = Database::connect();

        $photo = $this->request->getFile('photo');

        $newPhotoUploaded = $photo && $photo->isValid();

        $oldPhotoPath = FCPATH . $employee['photo_path'];

        $newAbsolutePath = null;

        try {
            $db->transBegin();

            $updateData = [
                'employee_code' => $this->request->getPost('employee_code'),
                'name' => $this->request->getPost('name'),
                'email' => $this->request->getPost('email'),
                'gender' => $this->request->getPost('gender'),
                'position' => $this->request->getPost('position'),
                'updated_by' => auth_user()['id'],
            ];

            if ($newPhotoUploaded) {
                $randomName = $photo->getRandomName();

                $relativePath = 'uploads/employees/' . $randomName;

                $newAbsolutePath = FCPATH . $relativePath;

                $originalName = $photo->getClientName();
                $mimeType = $photo->getMimeType();
                $size = $photo->getSize();

                $photo->move(FCPATH . 'uploads/employees', $randomName);

                $updateData['photo_name'] = $originalName;
                $updateData['photo_path'] = $relativePath;
                $updateData['photo_mime'] = $mimeType;
                $updateData['photo_size'] = $size;
            }

            $this->employeeModel->update($id, $updateData);

            if ($db->transStatus() === false) {
                throw new Exception('Database transaction failed');
            }

            $db->transCommit();

            if ($newPhotoUploaded && file_exists($oldPhotoPath)) {
                unlink($oldPhotoPath);
            }

            return $this->response->setJSON(['status' => 'success', 'message' => 'Employee updated successfully'])->setStatusCode(200);
        } catch (Throwable $e) {
            $db->transRollback();

            if ($newAbsolutePath && file_exists($newAbsolutePath)) {
                unlink($newAbsolutePath);
            }

            log_message('error', $e->getMessage());

            return $this->response->setJSON(['status' => 'error', 'message' => 'Failed to update employee'])->setStatusCode(500);
        }
    }

    public function delete($id)
    {
        $employee = $this->employeeModel->find($id);

        if (!$employee) {
            return $this->response->setJSON(['status' => 'error', 'message' => 'Employee not found'])->setStatusCode(404);
        }

        $this->employeeModel->update($id, ['deleted_by' => auth_user()['id']]);

        $this->employeeModel->delete($id);

        return $this->response->setJSON(['status' => 'success', 'message' => 'Employee deleted successfully'])->setStatusCode(200);
    }

    public function restore($id)
    {
        $employee = $this->employeeModel->withDeleted()->find($id);

        if (!$employee) {
            return $this->response->setJSON(['status' => 'error', 'message' => 'Employee not found'])->setStatusCode(404);
        }

        if ($employee['deleted_at'] === null) {
            return $this->response->setJSON(['status' => 'info', 'message' => 'Employee is not deleted']);
        }

        $this->employeeModel->builder()->where('id', $id)->update(['deleted_at' => null, 'deleted_by' => null]);

        return $this->response->setJSON(['status' => 'success', 'message' => 'Employee restored successfully'])->setStatusCode(200);
    }

    public function force($id)
    {
        $employee = $this->employeeModel->withDeleted()->find($id);

        if (!$employee) {
            return $this->response->setJSON(['status' => 'error', 'message' => 'Employee not found'])->setStatusCode(404);
        }

        if ($employee['deleted_at'] === null) {
            return $this->response->setJSON(['status' => 'info', 'message' => 'Employee is not deleted']);
        }

        $photoPath = FCPATH . $employee['photo_path'];

        $db = Database::connect();

        try {
            $db->transBegin();

            $this->employeeModel->delete($id, true);

            if ($db->transStatus() === false) {
                throw new Exception('Database transaction failed');
            }

            $db->transCommit();

            if (!empty($employee['photo_path']) && file_exists($photoPath)) {
                unlink($photoPath);
            }

            return $this->response->setJSON(['status' => 'success', 'message' => 'Employee permanently deleted'])->setStatusCode(200);
        } catch (Throwable $e) {
            $db->transRollback();

            log_message('error', $e->getMessage());

            return $this->response->setJSON(['status' => 'error', 'message' => 'Failed to permanently delete employee'])->setStatusCode(500);
        }
    }
}
