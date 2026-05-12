<?php

namespace App\Controllers;

use App\Controllers\BaseController;
use App\Models\EmployeeModel;
use App\Models\UserModel;

class DashboardController extends BaseController
{
    protected $userModel;

    protected $employeeModel;

    public function __construct()
    {
        $this->userModel = new UserModel();

        $this->employeeModel = new EmployeeModel();
    }

    public function index()
    {
        $totalUsers = $this->userModel->countAllResults();

        $totalEmployees = $this->employeeModel->countAllResults();

        return view('dashboard/index', ['totalUsers' => $totalUsers, 'totalEmployees' => $totalEmployees]);
    }
}
