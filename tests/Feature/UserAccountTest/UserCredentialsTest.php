<?php


namespace Tests\Feature\UserAccountTest;


use App\Models\Renter;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Auth;
use Tests\TestCase;

class UserCredentialsTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function a_verified_user_can_login_and_get_token()
    {
        $this->withoutExceptionHandling();

        $user = Renter::factory()->create();

        $this->json('post', '/login', [
            'email' => $user->email,
            'password' => 'password'
        ])->assertStatus(200);
        $this->assertEquals($user->id, Auth::user()->id);
    }
    /** @test */
    public function a_verified_user_email_is_required()
    {
        $this->withoutExceptionHandling();

        Renter::factory()->create();

        $this->json('post', '/login', [
            'password' => 'password'
        ])
            ->assertStatus(403)
            ->assertJsonValidationErrors('email');
    }
    /** @test */
    public function a_verified_user_password_is_required()
    {
        $this->withoutExceptionHandling();

        $user = Renter::factory()->create();

        $this->json('post', '/login', [
            'email' => $user->email,
        ])
            ->assertStatus(403)
            ->assertJsonValidationErrors('password');
    }
    /** @test */
    public function a_verified_user_remember_me_adds_ttl()
    {
        $this->withoutExceptionHandling();

        $user = Renter::factory()->create();

        $this->json('post', '/login', [
            'email' => $user->email,
            'password' => 'password',
            'remember' => true,
        ])
            ->assertStatus(200);
        $this->assertTrue(auth()->payload()['exp'] > 60*24*29);
    }

    /** @test */
    public function a_user_can_logout_after_login()
    {
        $this->withoutExceptionHandling();

        $user = Renter::factory()->create();

        $response=  $this->json('post', '/login', [
            'email' => $user->email,
            'password' => 'password',
            'remember' => true,
        ])
            ->assertStatus(200);
        $token = $response['message']['token'];
        $this->withHeaders([
            'Authorization'=> 'bearer ' . $token
        ])->json('post','/logout')->assertStatus(200);
        $this->assertNull(Auth::user());
    }

    /** @test */
    public function a_token_is_invalid_after_logout()
    {
        $user = Renter::factory()->create();
        $response = $this->json('post', '/login', [
            'email' => $user->email,
            'password' => 'password',
            'remember' => true,
        ])->assertStatus(200);
        $token = $response['message']['token'];
        $this->withHeaders([
            'Authorization'=> 'bearer ' . $token
        ])->json('post','/logout')->assertStatus(200);
        $this->withHeaders([
            'Authorization'=> 'bearer ' . $token
        ])->json('post','/logout')
            ->assertStatus(401);
    }
}
