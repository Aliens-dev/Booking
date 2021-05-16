<?php


namespace Tests\Feature\UserAccountTest;


use App\Models\Renter;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class UserUpdateAccountTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function a_user_update_firstname_required() {
        $this->withoutExceptionHandling();
        $user = Renter::factory()->create();
        $this->actingAs($user);
        $user = $user->toArray();
        $user['fname'] = '';
        $response = $this->json('patch','/users/'. $user['id'], $user);
        $response->assertStatus(403);
        $this->assertDatabaseCount('users',1);
    }
    /** @test */
    public function a_user_update_lastname_required() {
        $this->withoutExceptionHandling();
        $user = Renter::factory()->create();
        $this->actingAs($user);
        $user = $user->toArray();
        $user['lname'] = '';
        $response = $this->json('patch','/users/'. $user['id'], $user);
        $response->assertStatus(403);
        $this->assertDatabaseCount('users',1);
    }
    /** @test */
    public function a_user_update_email_required() {
        $this->withoutExceptionHandling();
        $user = Renter::factory()->create();
        $this->actingAs($user);
        $user = $user->toArray();
        $user['email'] = '';
        $response = $this->json('patch','/users/'. $user['id'], $user);
        $response->assertStatus(403);
        $this->assertDatabaseCount('users',1);
    }
    /** @test */
    public function a_user_update_password_required() {
        $this->withoutExceptionHandling();
        $user = Renter::factory()->create();
        $this->actingAs($user);
        $user = $user->toArray();
        $user['password'] = '';
        $response = $this->json('patch','/users/'. $user['id'], $user);
        $response->assertStatus(403);
        $this->assertDatabaseCount('users',1);
    }

    /** @test */
    public function a_user_update_dob_required() {
        $this->withoutExceptionHandling();
        $user = Renter::factory()->create();
        $this->actingAs($user);
        $user = $user->toArray();
        $user['dob'] = '';
        $response = $this->json('patch','/users/'. $user['id'], $user);
        $response->assertStatus(403);
        $this->assertDatabaseCount('users',1);
    }
    /** @test */
    public function a_user_update_phone_number_required() {
        $this->withoutExceptionHandling();
        $user = Renter::factory()->create();
        $this->actingAs($user);
        $user = $user->toArray();
        $user['phone_number'] = '';
        $response = $this->json('patch','/users/'. $user['id'], $user);
        $response->assertStatus(403);
        $this->assertDatabaseCount('users',1);
    }
    /** @test */
    public function a_user_cannot_update_to_existed_email_required() {
        $this->withoutExceptionHandling();
        $user = Renter::factory()->create();
        $user2 = Renter::factory()->create();
        $this->actingAs($user2);
        $user = $user->toArray();
        $user['email'] = $user2->email;
        $user['password'] = 'password';
        $user['password_confirmation'] = 'password';
        $response = $this->json('patch','/users/'. $user['id'], $user);
        $response->assertStatus(403);
        $response->assertJsonValidationErrors('email');
    }

    /** @test */
    public function a_user_cannot_update_others_profile()
    {
        $this->withoutExceptionHandling();

        $user1 = Renter::factory()->create();
        $user2 = Renter::factory()->create();
        $response = $this->actingAs($user1)->json('patch', '/users/'. $user2->id,$this->userArray()->toArray());
        $response->assertStatus(401);

    }

    /** @test */
    public function an_unauthenticated_user_cannot_update_others_profile()
    {
        $user1 = Renter::factory()->create();

        $response = $this->json('patch', '/users/'. $user1->id,$this->userArray()->toArray());
        $response->assertStatus(401);
        $this->assertDatabaseHas('users',[
            'fname' => $user1->fname
        ]);
    }
    /** @test */
    public function a_user_can_update_his_pic() {
        $this->withoutExceptionHandling();

        Storage::fake('users');
        $profile_pic = UploadedFile::fake()->image('profile.jpg');
        $id_pic = UploadedFile::fake()->image('id.jpg');

        $data= [
            'fname' => 'nabil',
            'lname' => 'merazga',
            'email' => 'nabil@gmail.com',
            'password' => 'password',
            'password_confirmation' => 'password',
            'phone_number' => '0565469531',
            'dob' => '25-06-2000',
            'user_role' => 'renter',
            'profile_pic' => $profile_pic,
            'identity_pic' => $id_pic
        ];
        $this->postJson('/users', $data);

        $user = User::find(1);
        $profile_pic = UploadedFile::fake()->image('profile1.jpg');
        $id_pic = UploadedFile::fake()->image('id1.jpg');
        $data = $user->toArray();
        $data['password'] = 'password';
        $data['password_confirmation'] = 'password';
        $data['profile_pic'] = $profile_pic;
        $data['identity_pic'] = $id_pic;
        $response = $this->actingAs($user)->json('patch','/users/'. $user['id'], $data);
        $response->assertStatus(200);

        $this->assertDatabaseMissing('users',['profile_pic' => "uploads/renter/1/profile.jpg"]);
        $this->assertDatabaseMissing('users',['identity_pic' => "uploads/renter/1/id.jpg"]);

        $this->assertDatabaseHas('users',['profile_pic' => "uploads/renter/1/profile1.jpg"]);
        $this->assertDatabaseHas('users',['identity_pic' => "uploads/renter/1/id1.jpg"]);

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
            'company' => 'aliensdev',
        ]);
    }
}
