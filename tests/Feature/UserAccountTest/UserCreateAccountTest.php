<?php


namespace Tests\Feature\UserAccountTest;


use App\Models\Renter;
use App\Models\User;
use Illuminate\Auth\Events\Registered;
use Illuminate\Foundation\Auth\EmailVerificationRequest;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Tests\TestCase;

class UserCreateAccountTest extends TestCase
{
    use DatabaseMigrations;

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
        $this->assertDatabaseHas('users', []);
    }

    public function test_user_phone_number_optional() {
        $this->withoutExceptionHandling();

        $user = $this->userCollection();
        $response = $this->postJson('/users', $user->except('phone_number')->toArray());

        $response->assertStatus(201);
        $response->assertJson(['success' => true]);
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
/*
    public function a_verification_email_is_sent_when_registering() {

        $data = $this->userCollection()->toArray();
        $this->json('post','/users',$data);
        $user = User::find(1);
        $this->assertFalse($user->hasVerifiedEmail());
    }
*/
    /** @test */
    public function a_renter_profile_pic_uploaded() {
        $data = $this->userCollection()->toArray();
        $this->json('post','/users',$data);
        $user = User::find(1);
        $this->assertDatabaseHas('users',
            [
                'profile_pic' => 'uploads/renter/1/profile.jpg'
            ]
        );
    }
    /** @test */
    public function a_renter_id_pic_uploaded() {
        $data = $this->userCollection()->toArray();
        $this->json('post','/users',$data);
        $user = User::find(1);
        $this->assertDatabaseHas('users',
            [
                'identity_pic' => 'uploads/renter/1/id.jpg'
            ]
        );
    }
    /** @test */
    public function a_client_profile_pic_uploaded() {
        $data = $this->userCollection()->toArray();
        $data['user_role'] = 'client';
        $this->json('post','/users',$data);
        $user = User::find(1);
        $this->assertDatabaseHas('users',
            [
                'profile_pic' => 'uploads/client/1/profile.jpg'
            ]
        );
    }
    /** @test */
    public function a_client_id_pic_uploaded() {
        $data = $this->userCollection()->toArray();
        $data['user_role'] = 'client';
        $this->json('post','/users',$data);
        $user = User::find(1);
        $this->assertDatabaseHas('users',
            [
                'identity_pic' => 'uploads/client/1/id.jpg'
            ]
        );
    }

    /** @test */
    public function get_all_users()
    {
        Renter::factory(10)->create();
        $this->json('get', route('users.index'))
            ->assertStatus(200)
            ->assertJsonCount(2);
    }

    /** @test */
    public function get_specific_user()
    {
        $user = Renter::factory()->create();

        $this->json('get', route('users.show', $user->id))
            ->assertStatus(200)
            ->assertJsonCount(2)
            ->assertSee($user->fname);
    }
    /** @test */
    public function get_not_existing_user()
    {
        $user = Renter::factory()->create();

        $this->json('get', route('users.show', 2))
            ->assertStatus(403);
    }

    private function userCollection() {
        Storage::fake('users');
        $profile_pic = UploadedFile::fake()->image('profile.jpg');
        $id_pic = UploadedFile::fake()->image('id.jpg');

        return collect([
            'fname' => 'nabil',
            'lname' => 'merazga',
            'email' => 'nabil@gmail.com',
            'password' => 'password',
            'password_confirmation' => 'password',
            'phone_number' => '0565469531',
            'dob' => '25-06-2000',
            'company' => 'aliensdev',
            'user_role' => 'renter',
            'profile_pic' => $profile_pic,
            'identity_pic' => $id_pic
        ]);
    }
}
