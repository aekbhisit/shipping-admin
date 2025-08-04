<?php

namespace Modules\User\Repositories\Eloquent;

use Modules\User\Repositories\Contracts\RoleRepositoryInterface;
use Modules\User\Entities\Roles;
use Modules\User\Entities\Permissions;
use Modules\User\Entities\RolesAndPermissions;
use Yajra\DataTables\Facades\DataTables;

/**
 * RoleRepository
 * 
 * Purpose: Handle all role-related database operations
 * Replaces: Direct model access in RoleAdminController
 * Pattern: Controller → Repository → Database
 */
class RoleRepository implements RoleRepositoryInterface
{
    protected $model;

    public function __construct(Roles $model)
    {
        $this->model = $model;
    }

    /**
     * Get data for role DataTable
     * Moved from RoleAdminController::datatable_ajax()
     */
    public function getDatatableRoles(array $params)
    {
        //init datatable parameters
        $dt_name_column = array('id', 'name', 'updated_at');
        $dt_order_column = $params['order'][0]['column'] ?? 0;
        $dt_order_dir = $params['order'][0]['dir'] ?? 'desc';
        $dt_start = $params['start'] ?? 0;
        $dt_length = $params['length'] ?? 10;
        $dt_search = $params['search']['value'] ?? '';

        // create role query
        $query = $this->model->newQuery();
        
        // add search query if search exists
        if (!empty($dt_search)) {
            $query = $query->where('name', 'like', "%" . $dt_search . "%");
        }

        // get total count for pagination
        $dt_total = $query->count();
        
        // apply ordering and pagination
        $query = $query->orderBy($dt_name_column[$dt_order_column], $dt_order_dir)
                      ->offset($dt_start)
                      ->limit($dt_length);

        // get roles
        $roles = $query->get();

        // prepare datatable response
        $datatables = Datatables::of($roles)
            ->addIndexColumn()
            ->setRowId('id')
            ->setRowClass('role_row')
            ->setOffset($dt_start)
            ->setTotalRecords($dt_total)
            ->setFilteredRecords($dt_total)
            ->setRowAttr([
                'data-sequence' => function ($record) {
                    return $record->sequence;
                },
            ])
            ->editColumn('updated_at', function ($record) {
                return $record->updated_at->format('Y-m-d H:i:s');
            })
            ->addColumn('manage', function ($record) {
                return $this->getRoleActionButtons($record);
            })
            ->escapeColumns([]);

        return $datatables->make(true);
    }

    /**
     * Generate action buttons for role row
     * Moved from RoleAdminController datatable logic
     */
    protected function getRoleActionButtons($record)
    {
        $action_btn = '<div class="square-buttons d-flex flex-wrap gap-1">';
        
        if (roles('admin.user.role.edit')) {
            if ($record->status == 1) {
                $action_btn .= '<a onclick="setStatus(' . $record->id . ',0)" href="javascript:void(0);" class="btn btn-outline-success"><i class="lni lni-checkmark"></i></a>';
            } else {
                $action_btn .= '<a onclick="setStatus(' . $record->id . ',1)" href="javascript:void(0);" class="btn btn-outline-warning"><i class="lni lni-close"></i></a>';
            }
            $action_btn .= '<a href="' . route('admin.user.role.edit', $record->id) . '" class="btn btn-outline-primary"><i class="lni lni-pencil"></i></a>';
        }
        
        if (roles('admin.user.role.set_delete')) {
            $action_btn .= '<a onclick="setDelete(' . $record->id . ')" href="javascript:void(0);" class="btn btn-outline-danger"><i class="lni lni-trash"></i></a>';
        }
        
        $action_btn .= '</div>';

        return $action_btn;
    }

    /**
     * Find role with permissions loaded
     */
    public function findWithPermissions($id)
    {
        return $this->model->with('permissions')->find($id);
    }

    /**
     * Get all roles for dropdown/select
     */
    public function getAllForSelect()
    {
        return $this->model->where('status', 1)->orderBy('name')->get(['id', 'name']);
    }

    /**
     * Create role with permission assignment
     */
    public function createRoleWithPermissions(array $roleData, array $permissionIds = [])
    {
        $role = $this->model->create($roleData);
        
        if (!empty($permissionIds)) {
            $role->permissions()->sync($permissionIds);
        }
        
        return $role;
    }

    /**
     * Update role's permissions
     */
    public function updateRolePermissions($roleId, array $permissionIds)
    {
        $role = $this->model->find($roleId);
        
        if ($role) {
            $role->permissions()->sync($permissionIds);
            return true;
        }
        
        return false;
    }
} 