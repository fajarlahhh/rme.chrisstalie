<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class DatabaseSeeder extends Seeder
{
    private $replace = [' ', '&', '\'', '.', '/'];
    /**
     * Seed the application's database.
     */
    public function run()
    {
        foreach (collect(config('sidebar.menu'))->sortBy('name')->all() as $key => $row) {
            $menu =
                str_replace($this->replace, '', strtolower($row['title']));
            try {
                Permission::create(['name' => $menu]);
            } catch (\Throwable $th) {
            }
            if (!empty($row['sub_menu'])) {
                $this->submenu($row['sub_menu'], $menu);
            }
        }

        $role = ['administrator', 'operator', 'guest'];

        foreach ($role as $key => $subRow) {
            try {
                Role::create(['name' => str_replace($this->replace, '', $row['name']) . '-' . $subRow]);
            } catch (\Throwable $th) {
            }
        }
    }

    private function submenu($menu, $parent)
    {
        foreach ($menu as $key => $row) {
            $menu = $parent . str_replace($this->replace, '', strtolower($row['title']));
            try {
                Permission::create(['name' => $menu]);
            } catch (\Throwable $th) {
            }
            if (!empty($row['sub_menu'])) {
                $this->submenu($row['sub_menu'], $menu);
            }
        }
    }
}
