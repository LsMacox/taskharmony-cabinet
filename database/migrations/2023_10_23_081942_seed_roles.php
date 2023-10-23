<?php

use Illuminate\Database\Migrations\Migration;
use Spatie\Permission\Models\Role;

return new class extends Migration
{
    public array $roles = [
        'Super admin',
        'Employee'
    ];

    /**
     * Run the migrations.
     */
    public function up(): void
    {
        foreach ($this->roles as $role) {
            Role::create(['name' => $role, 'guard' => 'web']);
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        foreach ($this->roles as $role) {
            Role::query()
                ->where('name', $role)
                ->where('guard', 'web')
                ->delete();
        }
    }
};
