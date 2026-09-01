<?php

namespace Tests\Feature;

use App\Livewire\UserManagement;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Livewire\Livewire;
use Tests\TestCase;

class UserManagementTest extends TestCase
{
    use RefreshDatabase;

    public function test_only_admins_can_open_user_management(): void
    {
        $this->actingAs(User::factory()->manager()->create())->get('/users')->assertForbidden();
        $this->actingAs(User::factory()->sales()->create())->get('/users')->assertForbidden();
        $this->actingAs(User::factory()->admin()->create())->get('/users')->assertOk();
    }

    public function test_admin_can_create_a_user(): void
    {
        Livewire::actingAs(User::factory()->admin()->create())
            ->test(UserManagement::class)
            ->call('openCreate')
            ->set('name', 'Nova Vendedora')
            ->set('email', 'nova@crm.com')
            ->set('password', 'senha-secreta')
            ->set('role', User::ROLE_SALES)
            ->call('save')
            ->assertHasNoErrors()
            ->assertSet('showForm', false);

        $user = User::where('email', 'nova@crm.com')->firstOrFail();

        $this->assertSame(User::ROLE_SALES, $user->role);
        $this->assertTrue(Hash::check('senha-secreta', $user->password));
    }

    public function test_user_creation_is_validated(): void
    {
        $admin = User::factory()->admin()->create();

        Livewire::actingAs($admin)
            ->test(UserManagement::class)
            ->set('name', 'ab')
            ->set('email', $admin->email)
            ->set('password', '123')
            ->set('role', 'root')
            ->call('save')
            ->assertHasErrors(['name', 'email', 'password', 'role']);
    }

    public function test_admin_can_update_user_without_changing_password(): void
    {
        $admin  = User::factory()->admin()->create();
        $target = User::factory()->sales()->create();
        $hash   = $target->password;

        Livewire::actingAs($admin)
            ->test(UserManagement::class)
            ->call('editUser', $target->id)
            ->set('name', 'Nome Editado')
            ->set('role', User::ROLE_MANAGER)
            ->call('save')
            ->assertHasNoErrors();

        $target->refresh();

        $this->assertSame('Nome Editado', $target->name);
        $this->assertSame(User::ROLE_MANAGER, $target->role);
        $this->assertSame($hash, $target->password);
    }

    public function test_admin_can_delete_another_user_but_not_himself(): void
    {
        $admin  = User::factory()->admin()->create();
        $target = User::factory()->sales()->create();

        Livewire::actingAs($admin)
            ->test(UserManagement::class)
            ->call('deleteUser', $target->id)
            ->call('deleteUser', $admin->id);

        $this->assertDatabaseMissing('users', ['id' => $target->id]);
        $this->assertDatabaseHas('users', ['id' => $admin->id]);
    }
}
