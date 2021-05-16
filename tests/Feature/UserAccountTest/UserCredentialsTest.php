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
        ])->assertStatus(200)
          ->assertSee('token');
        $this->assertEquals($user->id, Auth::user()->id);
    }
    /** @test */
    public function a_verified_user_email_is_required()
    {
        $this->withoutExceptionHandling();

        $user = Renter::factory()->create();

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
}
