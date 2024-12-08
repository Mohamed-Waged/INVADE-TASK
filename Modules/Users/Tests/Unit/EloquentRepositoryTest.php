<?php

namespace Modules\Users\Tests\Unit;

use Tests\TestCase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;

class EloquentRepositoryTest extends TestCase
{
    //use RefreshDatabase;
    protected $apiURL = '/api/v1/backend/users';

    //
    public function test_api_list()
    {
        $this->withHeaders([
            'Authorization' => 'Bearer ' . $this->getBearerToken(),
            'Accept'        => 'application/json'
        ]);
        $response = $this->get($this->apiURL);

        $this->assertEquals(200, $response->status());
    }

    public function test_api_show()
    {
        $this->withHeaders([
            'Authorization' => 'Bearer ' . $this->getBearerToken(),
            'Accept'        => 'application/json'
        ]);
        $response = $this->get($this->apiURL);
        $encryptId = $response['data']['rows'][0]['encryptId'] ?? NULL;

        $this->withHeaders([
            'Authorization' => 'Bearer ' . $this->getBearerToken(),
            'Accept'        => 'application/json'
        ]);
        $response2 = $this->get($this->apiURL.'/'.$encryptId);

        $this->assertEquals(200, $response2->status());
    }
    //
}
