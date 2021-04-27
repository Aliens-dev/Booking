<?php


namespace Tests\Feature\UserAccountTest;


use App\Models\User;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Support\Str;
use Tests\TestCase;

class UserCreateAccountTest extends TestCase
{
    use DatabaseMigrations;

    public function test_a_client_can_create_account()
    {
        $this->withoutExceptionHandling();

        $user = $this->userCollection();
        $response = $this->postJson('/users', $user->toArray());

        $response->assertStatus(201)
            ->assertJson(['success'=> true]);

        $this->assertDatabaseCount('users', 1);
    }

    public function test_user_fname_required() {
        $this->withoutExceptionHandling();

        $user = $this->userCollection();
        $response = $this->postJson('/users', $user->except('fname')->toArray());

        $response->assertStatus(403);
        $response->assertJson(['success' => false]);
        $response->assertJsonValidationErrors('fname');
    }

    public function test_user_lname_required() {
        $this->withoutExceptionHandling();

        $user = $this->userCollection();
        $response = $this->postJson('/users', $user->except('lname')->toArray());

        $response->assertStatus(403);
        $response->assertJson(['success' => false]);
        $response->assertJsonValidationErrors('lname');
    }

    public function test_user_email_required() {
        $this->withoutExceptionHandling();

        $user = $this->userCollection();
        $response = $this->postJson('/users', $user->except('email')->toArray());

        $response->assertStatus(403);
        $response->assertJson(['success' => false]);
        $response->assertJsonValidationErrors('email');
    }

    public function test_user_password_required() {
        $this->withoutExceptionHandling();

        $user = $this->userCollection();
        $response = $this->postJson('/users', $user->except('password')->toArray());

        $response->assertStatus(403);
        $response->assertJson(['success' => false]);
        $response->assertJsonValidationErrors('password');
    }
    public function test_user_password_confirmed_required() {
        $this->withoutExceptionHandling();

        $user = $this->userCollection();
        $response = $this->postJson('/users', $user->except('password_confirmation')->toArray());

        $response->assertStatus(403);
        $response->assertJson(['success' => false]);
        $response->assertJsonValidationErrors('password');
    }
    public function test_user_password_encrypted() {
        $this->withoutExceptionHandling();

        $user = $this->userCollection();
        $response = $this->postJson('/users', $user->toArray());
        $response->assertStatus(201);
        $response->assertJson(['success' => true]);
        $this->assertDatabaseCount('users',1);
        $user['password'] = bcrypt($user['password']);
        $this->assertDatabaseHas('users', [

        ]);
    }

    public function test_user_phone_number_required() {
        $this->withoutExceptionHandling();

        $user = $this->userCollection();
        $response = $this->postJson('/users', $user->except('phone_number')->toArray());

        $response->assertStatus(403);
        $response->assertJson(['success' => false]);
        $response->assertJsonValidationErrors('phone_number');
    }

    public function test_user_dob_required() {
        $this->withoutExceptionHandling();

        $user = $this->userCollection();
        $response = $this->postJson('/users', $user->except('dob')->toArray());

        $response->assertStatus(403);
        $response->assertJson(['success' => false]);
        $response->assertJsonValidationErrors('dob');
    }

    public function test_user_fname_restrictions() {
        $this->withoutExceptionHandling();

        $user = $this->userCollection()->except('fname')->toArray();
        $user['fname'] = 'a';
        $response = $this->postJson('/users', $user);

        $response->assertStatus(403);
        $response->assertJson(['success' => false]);

        $user = $this->userCollection()->except('fname')->toArray();
        $user['fname'] = 'lorem ipsum lorem ipsum lorem ipsum lorem ipsum lorem ipsum lorem ipsum';
        $response = $this->postJson('/users', $user);

        $response->assertStatus(403);

        $response->assertJson(['success' => false]);

        $response->assertJsonValidationErrors('fname');
    }

    public function test_user_lname_restrictions() {
        $this->withoutExceptionHandling();

        $user = $this->userCollection()->except('lname')->toArray();
        $user['lname'] = 'a';
        $response = $this->postJson('/users', $user);

        $response->assertStatus(403);
        $response->assertJson(['success' => false]);

        $user = $this->userCollection()->except('lname')->toArray();
        $user['lname'] = 'lorem ipsum lorem ipsum lorem ipsum lorem ipsum lorem ipsum lorem ipsum';
        $response = $this->postJson('/users', $user);

        $response->assertStatus(403);
        $response->assertJson(['success' => false]);
        $response->assertJsonValidationErrors('lname');
    }
    public function test_user_valide_phone_number() {
        $this->withoutExceptionHandling();

        $user = $this->userCollection()->except('phone_number')->toArray();
        $user['phone_number'] = '07987987';
        $response = $this->postJson('/users', $user);

        $response->assertStatus(403);
        $response->assertJson(['success' => false]);

    }

    public function test_user_email_restriction() {

        $this->withoutExceptionHandling();

        $user = $this->userCollection()->except('email')->toArray();
        $user['email'] = 'adgdgdfg';
        $response = $this->postJson('/users', $user);

        $response->assertStatus(403);
        $response->assertJson(['success' => false]);
        $response->assertJsonValidationErrors('email');
    }
    public function test_user_email_unique() {

        $this->withoutExceptionHandling();

        $user = $this->userCollection()->except('email')->toArray();
        $user['email'] = 'nabil@nabil.com';
        $response = $this->postJson('/users', $user);
        $response->assertStatus(201);
        $response->assertJson(['success' => true]);

        $user2 = $this->userCollection()->except('email')->toArray();
        $user2['email'] = 'nabil@nabil.com';
        $response = $this->postJson('/users', $user2);
        $response->assertStatus(403);
        $response->assertJson(['success' => false]);
        $response->assertJsonValidationErrors('email');
    }
    public function test_cannot_duplicate_email() {
        $this->withoutExceptionHandling();

        $user = $this->userCollection()->toArray();
        $response = $this->postJson('/users', $user);
        $response->assertStatus(201);
        $this->assertDatabaseCount('users',1);

        $response = $this->postJson('/users', $user);
        $response->assertStatus(403);
        $response->assertJsonValidationErrors('email');
        $this->assertDatabaseCount('users',1);
    }

    public function test_user_dob_valid() {
        $this->withoutExceptionHandling();

        $user = $this->userCollection()->except('dob')->toArray();
        $user['dob'] = '09-87-9587';
        $response = $this->postJson('/users', $user);
        $response->assertStatus(403);
        $response->assertJsonValidationErrors('dob');
        $this->assertDatabaseCount('users',0);
    }

    public function test_user_role_required() {
        $this->withoutExceptionHandling();
        $user = $this->userCollection()->except('user_role')->toArray();
        $response = $this->postJson('/users', $user);
        $response->assertStatus(403);
        $response->assertJsonValidationErrors('user_role');
        $this->assertDatabaseCount('users',0);
    }

    public function test_user_role_added_to_user_roles_table() {
        $this->withoutExceptionHandling();
        $user = $this->userCollection()->toArray();
        $response = $this->postJson('/users', $user);
        $response->assertStatus(201);
        $this->assertDatabaseCount('users',1);
    }
    /** @test */
    public function user_role_exists_client_role() {
        $this->withoutExceptionHandling();
        $user = $this->userCollection()->toArray();

        $user['user_role'] = 'not_exist';

        $response = $this->postJson('/users', $user);
        $response->assertStatus(403);
        $response->assertJsonValidationErrors('user_role');
        $this->assertDatabaseCount('users',0);
    }
    /** @test */
    public function user_is_assigned_client_role() {
        $this->withoutExceptionHandling();
        $user = $this->userCollection()->toArray();
        $response = $this->postJson('/users', $user);
        $response->assertStatus(201);
        $this->assertDatabaseCount('users',1);
        $user = User::FindOrFail(1)->first();
        $this->assertEquals('client',$user->user_role);
    }
    /** @test */
    public function user_is_assigned_renter_role() {
        $this->withoutExceptionHandling();
        $user = $this->userCollection()->toArray();
        $user['user_role'] = 'renter';
        $response = $this->postJson('/users', $user);
        $response->assertStatus(201);
        $this->assertDatabaseCount('users',1);
        $user = User::FindOrFail(1)->first();
        $this->assertEquals('renter',$user->user_role);
    }

    private function userCollection() {
        return collect([
            'fname' => 'nabil',
            'lname' => 'merazga',
            'email' => 'nabil@gmail.com',
            'password' => 'password',
            'password_confirmation' => 'password',
            'phone_number' => '0565469531',
            'dob' => '25-06-2000',
            'company' => 'aliensdev',
            'user_role' => 'client'
        ]);
    }
}
