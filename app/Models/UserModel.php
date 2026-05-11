<?php

namespace App\Models;

use CodeIgniter\Model;

class UserModel extends Model
{
    protected $table            = 'users';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $returnType       = 'array';
    protected $useSoftDeletes   = true;
    protected $protectFields    = true;
    protected $allowedFields    = [
        'name',
        'email',
        'password',
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
            users.*,

            creator.name AS creator,
            editor.name AS editor,
            deleter.name AS deleter
        ")
            ->join(
                'users AS creator',
                'creator.id = users.created_by',
                'left'
            )
            ->join(
                'users AS editor',
                'editor.id = users.updated_by',
                'left'
            )
            ->join(
                'users AS deleter',
                'deleter.id = users.deleted_by',
                'left'
            );
    }
}
