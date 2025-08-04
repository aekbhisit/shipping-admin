<?php

namespace Modules\User\Repositories\Eloquent;

use Modules\User\Repositories\Contracts\PermissionRepositoryInterface;
use Modules\User\Entities\Permissions;
use Yajra\DataTables\Facades\DataTables;

/**
 * PermissionRepository
 * 
 * Purpose: Handle all permission-related database operations
 * Replaces: Direct model access in PermissionAdminController
 * Pattern: Controller → Repository → Database
 */
class PermissionRepository implements PermissionRepositoryInterface
{
    protected $model;

    public function __construct(Permissions $model)
    {
        $this->model = $model;
    }

    /**
     * Get data for permission DataTable
     * Moved from PermissionAdminController::datatable_ajax()
     */
    public function getDatatablePermissions(array $params)
    {
        //init datatable parameters
        $dt_name_column = array('id', 'name', 'group', 'module', 'page', 'action', 'route_name', 'updated_at');
        $dt_order_column = $params['order'][0]['column'] ?? 0;
        $dt_order_dir = $params['order'][0]['dir'] ?? 'desc';
        $dt_start = $params['start'] ?? 0;
        $dt_length = $params['length'] ?? 10;
        $dt_search = $params['search']['value'] ?? '';

        // create permission query
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

        // get permissions
        $permissions = $query->get();

        // prepare datatable response
        $datatables = Datatables::of($permissions)
            ->addIndexColumn()
            ->setRowId('id')
            ->setRowClass('permission_row')
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
                return $this->getPermissionActionButtons($record);
            })
            ->escapeColumns([]);

        return $datatables->make(true);
    }

    /**
     * Generate action buttons for permission row
     * Moved from PermissionAdminController datatable logic
     */
    protected function getPermissionActionButtons($record)
    {
        $action_btn = '<div class="square-buttons d-flex flex-wrap gap-1">';
        
        if (roles('admin.user.permission.edit')) {
            $action_btn .= '<a href="' . route('admin.user.permission.edit', $record->id) . '" class="btn btn-outline-primary"><i class="lni lni-pencil"></i></a>';
        }
        
        if (roles('admin.user.permission.set_delete')) {
            $action_btn .= '<a onclick="setDelete(' . $record->id . ')" href="javascript:void(0);" class="btn btn-outline-danger"><i class="lni lni-trash"></i></a>';
        }
        
        $action_btn .= '</div>';

        return $action_btn;
    }

    /**
     * Find permission with roles loaded
     */
    public function findWithRoles($id)
    {
        return $this->model->with('roles')->find($id);
    }

    /**
     * Get all permissions for dropdown/select
     */
    public function getAllForSelect()
    {
        return $this->model->orderBy('name')->get(['id', 'name']);
    }

    /**
     * Get permissions grouped by category
     */
    public function getPermissionsByCategory()
    {
        return $this->model->orderBy('group')->orderBy('name')->get()->groupBy('group');
    }
} 