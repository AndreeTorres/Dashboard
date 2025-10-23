<?php

namespace Database\Seeders;

use BezhanSalleh\FilamentShield\Support\Utils;
use Illuminate\Database\Seeder;
use Spatie\Permission\PermissionRegistrar;

class ShieldSeeder extends Seeder
{
    public function run(): void
    {
        app()[PermissionRegistrar::class]->forgetCachedPermissions();

        $rolesWithPermissions = '[
            {
                "name": "admin",
                "guard_name": "web",
                "permissions": [
                    "view_role",
                    "view_any_role",
                    "create_role",
                    "update_role",
                    "delete_role",
                    "delete_any_role",

                    "view_user",
                    "view_any_user",
                    "create_user",
                    "update_user",
                    "restore_user",
                    "restore_any_user",
                    "replicate_user",
                    "reorder_user",
                    "delete_user",
                    "delete_any_user",
                    "force_delete_user",
                    "force_delete_any_user",
                    "view_table",
                    "view_any_table",
                    "create_table",
                    "update_table",
                    "restore_table",
                    "restore_any_table",
                    "replicate_table",
                    "reorder_table",
                    "delete_table",
                    "delete_any_table",
                    "force_delete_table",
                    "force_delete_any_table",
                    "view_supply",
                    "view_any_supply",
                    "create_supply",
                    "update_supply",
                    "restore_supply",
                    "restore_any_supply",
                    "replicate_supply",
                    "reorder_supply",
                    "delete_supply",
                    "delete_any_supply",
                    "force_delete_supply",
                    "force_delete_any_supply",
                    "force_delete_any_supply",
                    "force_delete_any_product",
                    "view_product",
                    "view_any_product",
                    "create_product",
                    "update_product",
                    "restore_product",
                    "restore_any_product",
                    "replicate_product",
                    "reorder_product",
                    "delete_product",
                    "delete_any_product",
                    "force_delete_product",
                    "force_delete_any_product",
                    "force_delete_any_product",
                    "view_order",
                    "view_any_order",
                    "create_order",
                    "update_order",
                    "restore_order",
                    "restore_any_order",
                    "replicate_order",
                    "reorder_order",
                    "delete_order",
                    "delete_any_order",
                    "force_delete_order",
                    "force_delete_any_order",
                    "view_expense",
                    "view_any_expense",
                    "create_expense",
                    "update_expense",
                    "restore_expense",
                    "restore_any_expense",
                    "replicate_expense",
                    "reorder_expense",
                    "delete_expense",
                    "delete_any_expense",
                    "force_delete_expense",
                    "force_delete_any_expense",
                    "view_supply::category",
                    "view_any_supply::category",
                    "create_supply::category",
                    "update_supply::category",
                    "restore_supply::category",
                    "restore_any_supply::category",
                    "replicate_supply::category",
                    "reorder_supply::category",
                    "delete_supply::category",
                    "delete_any_supply::category",
                    "force_delete_supply::category",
                    "view_product::subcategory",
                    "view_any_product::subcategory",
                    "create_product::subcategory",
                    "update_product::subcategory",
                    "restore_product::subcategory",
                    "restore_any_product::subcategory",
                    "replicate_product::subcategory",
                    "reorder_product::subcategory",
                    "delete_product::subcategory",
                    "delete_any_product::subcategory",
                    "force_delete_product::subcategory",
                    "view_product::category",
                    "view_any_product::category",
                    "create_product::category",
                    "update_product::category",
                    "restore_product::category",
                    "restore_any_product::category",
                    "replicate_product::category",
                    "reorder_product::category",
                    "delete_product::category",
                    "delete_any_product::category",
                    "force_delete_product::category",

                    "page_StatisticsPage",

                    "widget_StatisticsExpensesWidget",
                    "widget_StatisticsSalesWidget",
                    "widget_StatisticsCrossMetricsWidget",
                    "widget_SalesVsExpensesChart",
                    "widget_MonthlyComparisonChart",
                    "widget_ExpenseDistributionChart",
                    "widget_PerformanceRadarChart",
                    "widget_SalesDistributionChart",
                    "widget_TopSubcategoriesChart",
                    "widget_TopProductsChart",
                    "widget_PerformanceMetricsExplanationWidget"
                ]
            },
            {
                "name": "normal",
                "guard_name": "web",
                "permissions": [
                    "view_order",
                    "view_any_order",
                    "create_order",
                    "update_order",
                    "restore_order",
                    "restore_any_order",
                    "replicate_order",
                    "reorder_order",
                    "delete_order",
                    "delete_any_order",
                    "force_delete_order",
                    "force_delete_any_order"
                ]
            }
        ]';

        if (! json_decode($rolesWithPermissions)) {
            throw new \Exception('Invalid JSON string in ShieldSeeder.php');
        }

        $directPermissions = '[]';

        static::makeRolesWithPermissions($rolesWithPermissions);
        static::makeDirectPermissions($directPermissions);

        $this->command->info('Shield Seeding Completed.');
    }

    protected static function makeRolesWithPermissions(string $rolesWithPermissions): void
    {
        if (! blank($rolePlusPermissions = json_decode($rolesWithPermissions, true))) {
            /** @var Model $roleModel */
            $roleModel = Utils::getRoleModel();
            /** @var Model $permissionModel */
            $permissionModel = Utils::getPermissionModel();

            foreach ($rolePlusPermissions as $rolePlusPermission) {
                $role = $roleModel::firstOrCreate([
                    'name' => $rolePlusPermission['name'],
                    'guard_name' => $rolePlusPermission['guard_name'],
                ]);

                if (! blank($rolePlusPermission['permissions'])) {
                    $permissionModels = collect($rolePlusPermission['permissions'])
                        ->map(fn ($permission) => $permissionModel::firstOrCreate([
                            'name' => $permission,
                            'guard_name' => $rolePlusPermission['guard_name'],
                        ]))
                        ->all();

                    $role->syncPermissions($permissionModels);
                }
            }
        }
    }

    public static function makeDirectPermissions(string $directPermissions): void
    {
        if (! blank($permissions = json_decode($directPermissions, true))) {
            /** @var Model $permissionModel */
            $permissionModel = Utils::getPermissionModel();

            foreach ($permissions as $permission) {
                if ($permissionModel::whereName($permission)->doesntExist()) {
                    $permissionModel::create([
                        'name' => $permission['name'],
                        'guard_name' => $permission['guard_name'],
                    ]);
                }
            }
        }
    }
}
