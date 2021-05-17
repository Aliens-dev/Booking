<?php


namespace Facilities;


use App\Models\Admin;
use App\Models\Client;
use App\Models\Facility;
use App\Models\Renter;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class FacilitiesTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp(); // TODO: Change the autogenerated stub
    }

    /** @test */
    public function getting_all_facilities()
    {
        Facility::factory(2)->create();
        $response = $this->json('get','/facilities')
            ->assertStatus(200)
            ->assertSee(['success' => true])
            ->assertSee('data')
            ->assertJsonCount(2);

        $this->assertEquals(count($response->json(['data'])),2);
    }
    /** @test */
    public function an_admin_can_add_a_facility()
    {
        $admin = Admin::factory()->create();
        $data = [
            'title' => 'Facility',
            'description' => 'description',
            'title_ar' => 'Facility',
            'description_ar' => 'description',
        ];
        $this->actingAs($admin)->json('post','/facilities', $data)
            ->assertStatus(201);
        $this->assertDatabaseCount('facilities',1);
    }
    /** @test */
    public function a_renter_cannot_add_a_facility()
    {
        $renter = Renter::factory()->create();
        $data = [
            'title' => 'Facility',
            'description' => 'description',
            'title_ar' => 'Facility',
            'description_ar' => 'description',
        ];
        $this->actingAs($renter)->json('post','/facilities', $data)
            ->assertStatus(401);
        $this->assertDatabaseCount('facilities',0);
    }
    /** @test */
    public function a_client_cannot_add_a_facility()
    {
        $client = Client::factory()->create();
        $data = [
            'title' => 'Facility',
            'description' => 'description',
            'title_ar' => 'Facility',
            'description_ar' => 'description',
        ];
        $this->actingAs($client)->json('post','/facilities', $data)
            ->assertStatus(401);
        $this->assertDatabaseCount('facilities',0);
    }
    /** @test */
    public function a_facility_title_is_required_if_sent()
    {
        $admin = Admin::factory()->create();
        $data = [
            'title' => '',
        ];
        $this->actingAs($admin)->json('post','/facilities', $data)
            ->assertStatus(403);
        $this->assertDatabaseCount('facilities',0);
    }
    /** @test */
    public function an_admin_can_update_a_facility()
    {
        $admin = Admin::factory()->create();
        $facility = Facility::factory()->create();
        $data = [
            'title' => 'Facility',
            'description' => 'description',
            'title_ar' => 'Facility',
            'description_ar' => 'description',
        ];
        $this->actingAs($admin)->json('patch','/facilities/' . $facility->id, $data)
            ->assertStatus(200);
        $this->assertDatabaseCount('facilities',1);
        $this->assertDatabaseHas('facilities',['title' => 'Facility']);
    }
    /** @test */
    public function on_facility_update_title_is_required()
    {
        $admin = Admin::factory()->create();
        $facility = Facility::factory()->create();
        $data = [
            'title' => '',
        ];
        $this->actingAs($admin)->json('patch','/facilities/' . $facility->id, $data)
            ->assertStatus(403);
        $this->assertDatabaseCount('facilities',1);
        $this->assertDatabaseHas('facilities',['title' => $facility->title]);
    }
    /** @test */
    public function an_admin_can_delete_a_facility()
    {
        $this->withoutExceptionHandling();
        $admin = Admin::factory()->create();
        $facility = Facility::factory()->create();
        $this->actingAs($admin)->json('delete','/facilities/'.$facility->id)
            ->assertStatus(200);
        $this->assertDatabaseCount('facilities',0);
    }
    /** @test */
    public function a_client_cannot_delete_a_facility()
    {
        $this->withoutExceptionHandling();
        $client = Client::factory()->create();
        $facility = Facility::factory()->create();
        $this->actingAs($client)->json('delete','/facilities/'.$facility->id)
            ->assertStatus(401);
        $this->assertDatabaseCount('facilities',1);
    }
}
