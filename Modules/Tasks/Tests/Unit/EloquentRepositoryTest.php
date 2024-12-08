<?php

namespace Modules\Tasks\Tests\Unit;

use Tests\TestCase;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;

class EloquentRepositoryTest extends TestCase
{
    //use RefreshDatabase;
    protected $apiURL = '/api/v1/tasks';

    // Test Index - Get all tasks
    public function test_api_index()
    {
        $this->withHeaders([
            'Authorization' => 'Bearer ' . $this->getAdminBearerToken(),
            'Accept'        => 'application/json',
            'appId'         => env('APP_ID')
        ]);
        $response = $this->get($this->apiURL);

        $this->assertEquals(200, $response->status());
    }

    // Test Store - Missing fields
    public function test_api_store_validation_missing_fields()
    {
        $this->withHeaders([
            'Authorization' => 'Bearer ' . $this->getAdminBearerToken(),
            'Accept'        => 'application/json',
            'appId'         => env('APP_ID')
        ]);

        // Test with missing 'title' field
        $request = [
            'id' => 1,
            'categoryId' => 1,
            'description' => 'test title',
            'status' => 'completed'
        ];
        $response = $this->post($this->apiURL, $request);

        $this->assertEquals(422, $response->status()); // Expecting a validation error
    }

    // Test Store - Invalid category_id
    public function test_api_store_invalid_category_id()
    {
        $this->withHeaders([
            'Authorization' => 'Bearer ' . $this->getAdminBearerToken(),
            'Accept'        => 'application/json',
            'appId'         => env('APP_ID')
        ]);

        // Attempt to store a task with an invalid category_id
        $request = [
            'categoryId' => 10000,
            'title' => 'books',
            'description' => "body",
            'status' => "completed",
        ];
        $response = $this->post($this->apiURL, $request);

        $this->assertEquals(422, $response->status()); // Expecting a validation error
    }

    // Test Store - Valid data
    public function test_api_store_with_valid_data()
    {
        $this->withHeaders([
            'Authorization' => 'Bearer ' . $this->getAdminBearerToken(),
            'Accept'        => 'application/json',
            'appId'         => env('APP_ID')
        ]);

        $request = [
            'id' =>  1,
            'categoryId' => 1,
            'title' => 'title',
            'description' => "body",
            'status' => "completed",
        ];
        $response = $this->post($this->apiURL, $request);
        $this->assertEquals(201, $response->status());
    }

    // Test Show - Invalid EncryptId
    public function test_api_show_invalid_encryptId()
    {
        $this->withHeaders([
            'Authorization' => 'Bearer ' . $this->getAdminBearerToken(),
            'Accept'        => 'application/json',
            'appId'         => env('APP_ID')
        ]);

        // Attempt to retrieve a tasks with an invalid encryptId
        $invalidEncryptId = 'invalid-id';
        $response = $this->get($this->apiURL . '/' . $invalidEncryptId);

        $this->assertEquals(404, $response->status());
    }

    // Test Show - Valid EncryptId
    public function test_api_show_valid_encryptId()
    {
        $this->withHeaders([
            'Authorization' => 'Bearer ' . $this->getAdminBearerToken(),
            'Accept'        => 'application/json',
            'appId'         => env('APP_ID')
        ]);
        $response = $this->get($this->apiURL);
        $encryptId = $response['data']['rows'][0]['encryptId'] ?? NULL;

        // Attempt to retrieve a task with an valid encryptId
        $response2 = $this->get($this->apiURL . '/' . $encryptId);

        $this->assertEquals(200, $response2->status());
    }

    // Test Update - Invalid EncryptId
    public function test_api_update_invalid_encryptId()
    {
        $this->withHeaders([
            'Authorization' => 'Bearer ' . $this->getAdminBearerToken(),
            'Accept'        => 'application/json',
            'appId'         => env('APP_ID')
        ]);

        // Prepare updated data
        $updateRequest = [
            'id' =>  1,
            'categoryId' => 1,
            'title' => 'title',
            'description' => "body",
            'status' => 'pending'
        ];

        // Attempt to update a task with an invalid encryptId
        $invalidEncryptId = 'invalid-id';
        $response = $this->put($this->apiURL . '/' . $invalidEncryptId, $updateRequest);
        $this->assertEquals(500, $response->status());
    }

    // Test Update - Valid data
    public function test_api_update()
    {
        $this->withHeaders([
            'Authorization' => 'Bearer ' . $this->getAdminBearerToken(),
            'Accept'        => 'application/json',
            'appId'         => env('APP_ID')
        ]);

        // First, create a task to ensure there's something to update
        $request = [
            'id' =>  1,
            'categoryId' => 1,
            'title' => 'title',
            'description' => "body",
            'status' => 'pending'
        ];
        $this->post($this->apiURL, $request);

        // Retrieve the created task to get its encryptId
        $response = $this->get($this->apiURL);
        $encryptId = $response['data']['rows'][0]['encryptId'] ?? NULL;

        // Prepare updated data
        $updateRequest = [
            'id' =>  1,
            'categoryId' => 1,
            'title' => 'new',
            'description' => "new",
            'status' => 'completed'
        ];

        // Attempt to update the task using the valid encryptId
        $response2 = $this->put($this->apiURL . '/' . $encryptId, $updateRequest);
        $this->assertEquals(200, $response2->status());
    }

    // Test Destroy - Invalid EncryptId
    public function test_api_destroy_invalid_encryptId()
    {
        $this->withHeaders([
            'Authorization' => 'Bearer ' . $this->getAdminBearerToken(),
            'Accept'        => 'application/json',
            'appId'         => env('APP_ID')
        ]);

        // Attempt to retrieve a task with an invalid encryptId
        $invalidEncryptId = 'invalid-id';
        $response = $this->delete($this->apiURL . '/' . $invalidEncryptId);

        $this->assertEquals(500, $response->status());
    }

    // Test Destroy - Valid EncryptId
    public function test_api_destroy_valid_encryptId()
    {
        $this->withHeaders([
            'Authorization' => 'Bearer ' . $this->getAdminBearerToken(),
            'Accept'        => 'application/json',
            'appId'         => env('APP_ID')
        ]);

        // First, create a task to ensure there's something to delete
        $request = [
            'id' =>  1,
            'category_id' => 1,
            'title' => 'title',
            'description' => "body",
            'status' => "completed",
        ];
        $this->post($this->apiURL, $request);

        // Retrieve the created task to get its encryptId
        $response = $this->get($this->apiURL);
        $encryptId = $response['data']['rows'][0]['encryptId'] ?? NULL;

        // Attempt to delete the task using the valid encryptId
        $response2 = $this->delete($this->apiURL . '/' . $encryptId);
        // Assert that the response status is 200 (OK)
        $this->assertEquals(200, $response2->status());
    }

}
