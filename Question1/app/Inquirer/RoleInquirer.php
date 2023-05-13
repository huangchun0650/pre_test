<?php

namespace App\Inquirer;

use App\Inquirer\Criteria\OrderBy\Models\RoleOrderByCriteria;
use Illuminate\Support\Collection;
use App\Model\Ecsite;
use App\Model\User;
use Spatie\Permission\Models\Role;

class RoleInquirer extends Inquirer
{
    public function __construct()
    {
        $this->table(Role::class);
        $this->pushCriteria(RoleOrderByCriteria::class);
    }

    /**
     * 選擇 Admin 的角色
     *
     * @return Inquirer
     */
    public function selectAdminRole(): Inquirer
    {
        return $this->selectConditions(['guard_name' => User::ROLE_ADMIN]);
    }

    /**
     * 選擇 Ecsite 的角色
     *
     * @return Inquirer
     */
    public function selectEcSiteRole(): Inquirer
    {
        return $this->selectConditions(['guard_name' => Ecsite::ROLE_ECSITE]);
    }

    /**
     * 選擇 Admin (employee + admin) 的角色
     *
     * @return Inquirer
     */
    public function selectCreatedTypeAdminRole(): Inquirer
    {
        return $this->selectConditions(['created_type' => [User::ROLE_ADMIN, User::ROLE_EMPLOYEE]]);
    }

    /**
     * 選擇 代理 的角色
     *
     * @return Inquirer
     */
    public function selectCreatedTypeAgentRole(): Inquirer
    {
        return $this->selectConditions(['created_type' => [User::ROLE_AGENT]]);
    }

    /**
     * 取得有權限的角色
     *
     * @return Collection
     */
    public function getRoleFromPermissions($permissions) : Collection
    {
        return $this->with('permissions', function ($query) use ($permissions) {
                $query->whereIn('id', $permissions);
            })
            ->whereHas('permissions', function ($query) use ($permissions) {
                $query->whereIn('id', $permissions);
            })->get()->pluck('permissions.*.pivot')->collapse();
    }
}

