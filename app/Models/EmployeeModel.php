<?php

namespace App\Models;

use CodeIgniter\Model;

class EmployeeModel extends Model
{
    protected $table            = 'employees';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $returnType       = 'array';
    protected $useSoftDeletes   = true;
    protected $protectFields    = true;
    protected $allowedFields    = [
        'employee_code',
        'name',
        'email',
        'gender',
        'position',
        'photo_name',
        'photo_path',
        'photo_mime',
        'photo_size',
        'created_by',
        'updated_by',
        'deleted_by'
    ];

    protected bool $updateOnlyChanged = true;

    // Dates
    protected $useTimestamps = true;
    protected $dateFormat    = 'datetime';
    protected $createdField  = 'created_at';
    protected $updatedField  = 'updated_at';
    protected $deletedField  = 'deleted_at';

    public function datatable()
    {
        return $this->builder()
            ->select("
            employees.*,

            creator.name AS creator,
            editor.name AS editor,
            deleter.name AS deleter
        ")
            ->join(
                'users AS creator',
                'creator.id = employees.created_by',
                'left'
            )
            ->join(
                'users AS editor',
                'editor.id = employees.updated_by',
                'left'
            )
            ->join(
                'users AS deleter',
                'deleter.id = employees.deleted_by',
                'left'
            );
    }
}
