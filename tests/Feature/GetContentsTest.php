<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Sanctum\Sanctum;
use App\Models\{Content, Role, User};
use Tests\TestCase;

class GetContentsTest extends TestCase
{
    use RefreshDatabase;

    /**
     * Contents were successfully fetched
     */
    public function test_contents_were_successfully_fetched()
    {
        $userRole = Role::factory()
                    ->user()
                    ->create();
        $adminRole = Role::factory()
                        ->administrator()
                        ->create();
        $admin = User::factory()
                    ->administrators()
                    ->create();
        $content = Content::factory()
                        ->create();

        Sanctum::actingAs($admin, ['*']);
        $response = $this->getJson(route('admin.contents.index'));

        $response->assertOk();
    }
}
