<?php


namespace Tests\Feature\UserAccountTest;


use App\Models\Renter;
use Illuminate\Auth\AuthenticationException;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class UserDeleteAccountTest extends TestCase {
    use RefreshDatabase;

    /** @test */

    public function a_user_can_delete_his_account()
    {
        $this->withoutExceptionHandling();

        $user = Renter::factory()->create();
        $response = $this->actingAs($user)->json('DELETE', '/users/'. $user->id);
        $response->assertStatus(200);
        $this->assertDatabaseCount('users',0);
        $this->assertDatabaseMissing('users', $user->toArray());
    }

    /** @test */

    public function a_user_cannot_delete_others_account()
    {
        $this->withoutExceptionHandling();

        $user = Renter::factory()->create();
        $user2 = Renter::factory()->create();

        $response = $this->actingAs($user)->json('DELETE', '/users/'. $user2->id);
        $response->assertStatus(403);
        $this->assertDatabaseCount('users',2);
        $this->assertDatabaseHas('users', ['id' => $user2->id]);
    }

    /** @test */

    public function an_unauthenticated_user_cannot_delete_others_account()
    {
        $user = Renter::factory()->create();
        $response = $this->json('DELETE', '/users/'. $user->id);
        $response->assertStatus(401);
        $this->assertDatabaseCount('users',1);
        $this->assertDatabaseHas('users', ['id' => $user->id]);
    }
}
