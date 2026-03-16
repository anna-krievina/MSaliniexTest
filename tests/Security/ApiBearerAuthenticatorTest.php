<?php

namespace App\Tests\Security;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class ApiBearerAuthenticatorTest extends WebTestCase
{
    public function testRequestWithoutTokenReturns401(): void
    {
        $client = static::createClient();

        $client->request('GET', '/api/stations');

        $this->assertResponseStatusCodeSame(401);
    }
}
