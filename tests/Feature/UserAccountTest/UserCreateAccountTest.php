<?php


namespace Tests\Feature\UserAccountTest;


use App\Models\Role;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Tests\TestCase;

class UserCreateAccountTest extends TestCase
{
    use DatabaseMigrations;

    public function test_a_client_can_create_account()
    {
        $this->withoutExceptionHandling();

        $user = $this->userArray();
        $role = $this->create_role('client');
        $response = $this->postJson('/users', $user->toArray());

        $response->assertStatus(200)
            ->assertJson(['success'=> true]);

        $this->assertDatabaseCount('users', 1);
    }

    public function test_user_fname_required() {
        $this->withoutExceptionHandling();

        $user = $this->userArray();
        $response = $this->postJson('/users', $user->except('fname')->toArray());

        $response->assertStatus(403);
        $response->assertJson(['success' => false]);
        $response->assertJsonValidationErrors('fname');
    }

    public function test_user_lname_required() {
        $this->withoutExceptionHandling();

        $user = $this->userArray();
        $response = $this->postJson('/users', $user->except('lname')->toArray());

        $response->assertStatus(403);
        $response->assertJson(['success' => false]);
        $response->assertJsonValidationErrors('lname');
    }

    public function test_user_email_required() {
        $this->withoutExceptionHandling();

        $user = $this->userArray();
        $response = $this->postJson('/users', $user->except('email')->toArray());

        $response->assertStatus(403);
        $response->assertJson(['success' => false]);
        $response->assertJsonValidationErrors('email');
    }

    public function test_user_password_required() {
        $this->withoutExceptionHandling();

        $user = $this->userArray();
        $response = $this->postJson('/users', $user->except('password')->toArray());

        $response->assertStatus(403);
        $response->assertJson(['success' => false]);
        $response->assertJsonValidationErrors('password');
    }
    public function test_user_password_confirmed_required() {
        $this->withoutExceptionHandling();

        $user = $this->userArray();
        $response = $this->postJson('/users', $user->except('password_confirmation')->toArray());

        $response->assertStatus(403);
        $response->assertJson(['success' => false]);
        $response->assertJsonValidationErrors('password');
    }

    public function test_user_phone_number_required() {
        $this->withoutExceptionHandling();

        $user = $this->userArray();
        $response = $this->postJson('/users', $user->except('phone_number')->toArray());

        $response->assertStatus(403);
        $response->assertJson(['success' => false]);
        $response->assertJsonValidationErrors('phone_number');
    }
    public function test_user_address_required() {
        $this->withoutExceptionHandling();

        $user = $this->userArray();
        $response = $this->postJson('/users', $user->except('address')->toArray());

        $response->assertStatus(403);
        $response->assertJson(['success' => false]);
        $response->assertJsonValidationErrors('address');
    }
    public function test_user_dob_required() {
        $this->withoutExceptionHandling();

        $user = $this->userArray();
        $response = $this->postJson('/users', $user->except('dob')->toArray());

        $response->assertStatus(403);
        $response->assertJson(['success' => false]);
        $response->assertJsonValidationErrors('dob');
    }

    public function test_user_fname_restrictions() {
        $this->withoutExceptionHandling();

        $user = $this->userArray()->except('fname')->toArray();
        $user['fname'] = 'a';
        $response = $this->postJson('/users', $user);

        $response->assertStatus(403);
        $response->assertJson(['success' => false]);

        $user = $this->userArray()->except('fname')->toArray();
        $user['fname'] = 'lorem ipsum lorem ipsum lorem ipsum lorem ipsum lorem ipsum lorem ipsum';
        $response = $this->postJson('/users', $user);

        $response->assertStatus(403);

        $response->assertJson(['success' => false]);

        $response->assertJsonValidationErrors('fname');
    }

    public function test_user_lname_restrictions() {
        $this->withoutExceptionHandling();

        $user = $this->userArray()->except('lname')->toArray();
        $user['lname'] = 'a';
        $response = $this->postJson('/users', $user);

        $response->assertStatus(403);
        $response->assertJson(['success' => false]);

        $user = $this->userArray()->except('lname')->toArray();
        $user['lname'] = 'lorem ipsum lorem ipsum lorem ipsum lorem ipsum lorem ipsum lorem ipsum';
        $response = $this->postJson('/users', $user);

        $response->assertStatus(403);
        $response->assertJson(['success' => false]);
        $response->assertJsonValidationErrors('lname');
    }
    public function test_user_valide_phone_number() {
        $this->withoutExceptionHandling();

        $user = $this->userArray()->except('phone_number')->toArray();
        $user['phone_number'] = '07987987';
        $response = $this->postJson('/users', $user);

        $response->assertStatus(403);
        $response->assertJson(['success' => false]);

    }

    public function test_user_email_restriction() {

        $this->withoutExceptionHandling();

        $user = $this->userArray()->except('email')->toArray();
        $user['email'] = 'adgdgdfg';
        $response = $this->postJson('/users', $user);

        $response->assertStatus(403);
        $response->assertJson(['success' => false]);
        $response->assertJsonValidationErrors('email');
    }

    public function test_cannot_duplicate_email() {
        $this->withoutExceptionHandling();

        $user = $this->userArray()->toArray();
        $role = $this->create_role('client');
        $response = $this->postJson('/users', $user);
        $response->assertStatus(200);
        $this->assertDatabaseCount('users',1);

        $response = $this->postJson('/users', $user);
        $response->assertStatus(403);
        $response->assertJsonValidationErrors('email');
        $this->assertDatabaseCount('users',1);
    }

    public function test_user_dob_valid() {
        $this->withoutExceptionHandling();

        $user = $this->userArray()->except('dob')->toArray();
        $user['dob'] = '09-87-9587';
        $response = $this->postJson('/users', $user);
        $response->assertStatus(403);
        $response->assertJsonValidationErrors('dob');
        $this->assertDatabaseCount('users',0);
    }

    public function test_role_name_required() {
        $this->withoutExceptionHandling();
        $user = $this->userArray()->except('role_name')->toArray();
        $response = $this->postJson('/users', $user);
        $response->assertStatus(403);
        $response->assertJsonValidationErrors('role_name');
        $this->assertDatabaseCount('users',0);
    }

    public function test_role_name_added_to_user_roles_table() {
        $this->withoutExceptionHandling();
        $user = $this->userArray()->toArray();
        $this->create_role('client');
        $response = $this->postJson('/users', $user);
        $response->assertStatus(200);
        $this->assertDatabaseCount('users',1);
    }
    public function test_role_name_exists_in_user_roles_table() {
        $this->withoutExceptionHandling();
        $user = $this->userArray()->toArray();
        $this->create_role('clients');
        $response = $this->postJson('/users', $user);
        $response->assertStatus(403);
        $response->assertJsonValidationErrors('role_name');
        $this->assertDatabaseCount('users',0);
    }
    private function create_role($role_name) {
        $role = new Role();
        $role->role_name = $role_name;
        $role->save();
        return $role;
    }
    private function userArray() {
        return collect([
            'fname' => 'nabil',
            'lname' => 'merazga',
            'email' => 'nabil@gmail.com',
            'password' => 'password',
            'password_confirmation' => 'password',
            'phone_number' => '0565469531',
            'dob' => '25-06-2000',
            'address' => '40 logs',
            'company' => 'aliensdev',
            'role_name' => 'client'
        ]);
    }
}
