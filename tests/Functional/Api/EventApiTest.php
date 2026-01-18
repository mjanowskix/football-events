<?php

declare(strict_types=1);

namespace App\Tests\Functional\Api;

use Symfony\Bundle\FrameworkBundle\KernelBrowser;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\HttpFoundation\Response;

final class EventApiTest extends WebTestCase
{
    private KernelBrowser $client;

    protected function setUp(): void
    {
        $this->client = static::createClient();
    }

    public function testCreateGoalEvent(): void
    {
        $this->client->request(
            'POST',
            '/api/events',
            [],
            [],
            ['CONTENT_TYPE' => 'application/json'],
            json_encode([
                'type' => 'goal',
                'match_id' => 'test-match',
                'team_id' => 'arsenal',
                'scorer' => 'saka',
                'minute' => 23,
            ]),
        );

        self::assertResponseStatusCodeSame(Response::HTTP_CREATED);

        $response = json_decode($this->client->getResponse()->getContent(), true);
        self::assertSame('success', $response['status']);
        self::assertSame('goal', $response['event']['type']);
    }

    public function testCreateGoalEventWithAssistant(): void
    {
        $this->client->request(
            'POST',
            '/api/events',
            [],
            [],
            ['CONTENT_TYPE' => 'application/json'],
            json_encode([
                'type' => 'goal',
                'match_id' => 'test-match',
                'team_id' => 'arsenal',
                'scorer' => 'saka',
                'assistant' => 'odegaard',
                'minute' => 23,
                'second' => 15,
            ]),
        );

        self::assertResponseStatusCodeSame(Response::HTTP_CREATED);

        $response = json_decode($this->client->getResponse()->getContent(), true);
        self::assertSame('odegaard', $response['event']['event']['assistant']);
    }

    public function testCreateFoulEvent(): void
    {
        $this->client->request(
            'POST',
            '/api/events',
            [],
            [],
            ['CONTENT_TYPE' => 'application/json'],
            json_encode([
                'type' => 'foul',
                'match_id' => 'test-match',
                'team_id' => 'chelsea',
                'player' => 'silva',
                'minute' => 45,
            ]),
        );

        self::assertResponseStatusCodeSame(Response::HTTP_CREATED);

        $response = json_decode($this->client->getResponse()->getContent(), true);
        self::assertSame('success', $response['status']);
        self::assertSame('foul', $response['event']['type']);
    }

    public function testReturnsErrorForInvalidJson(): void
    {
        $this->client->request(
            'POST',
            '/api/events',
            [],
            [],
            ['CONTENT_TYPE' => 'application/json'],
            'invalid json',
        );

        self::assertResponseStatusCodeSame(Response::HTTP_BAD_REQUEST);

        $response = json_decode($this->client->getResponse()->getContent(), true);
        self::assertSame('Invalid JSON', $response['error']);
    }

    public function testReturnsErrorForMissingType(): void
    {
        $this->client->request(
            'POST',
            '/api/events',
            [],
            [],
            ['CONTENT_TYPE' => 'application/json'],
            json_encode(['match_id' => 'test']),
        );

        self::assertResponseStatusCodeSame(Response::HTTP_BAD_REQUEST);

        $response = json_decode($this->client->getResponse()->getContent(), true);
        self::assertSame('Event type is required', $response['error']);
    }

    public function testReturnsErrorForInvalidType(): void
    {
        $this->client->request(
            'POST',
            '/api/events',
            [],
            [],
            ['CONTENT_TYPE' => 'application/json'],
            json_encode([
                'type' => 'invalid',
                'match_id' => 'test',
            ]),
        );

        self::assertResponseStatusCodeSame(Response::HTTP_BAD_REQUEST);
    }

    public function testReturnsErrorForMissingRequiredFields(): void
    {
        $this->client->request(
            'POST',
            '/api/events',
            [],
            [],
            ['CONTENT_TYPE' => 'application/json'],
            json_encode([
                'type' => 'goal',
                'match_id' => 'test',
                // missing team_id, scorer, minute
            ]),
        );

        self::assertResponseStatusCodeSame(Response::HTTP_BAD_REQUEST);
    }
}
