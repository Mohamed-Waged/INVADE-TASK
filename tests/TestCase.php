<?php

namespace Tests;

use Illuminate\Foundation\Testing\TestCase as BaseTestCase;

abstract class TestCase extends BaseTestCase
{
    use CreatesApplication;

    public function setUp(): void 
    {
        parent::setUp();
        $this->getBearerToken = $this->getBearerToken();
    }

    protected function getAdminBearerToken()
    {
       $data = [
            'email'    => 'user@user.com',
            'password' => 'user'
        ];
        $response = $this->post('/api/v1/login', $data);
        return $response['data']['accessToken'] ?? NULL;
    }
    protected function getBearerToken()
    {
       $data = [
            'email'    => 'user@user.com',
            'password' => 'user'
        ];
        $response = $this->post('/api/v1/login', $data);
        return $response['data']['access_token'] ?? NULL;
    }

}
