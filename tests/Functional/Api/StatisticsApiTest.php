<?php

declare(strict_types=1);

namespace App\Tests\Functional\Api;

use Symfony\Bundle\FrameworkBundle\KernelBrowser;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\HttpFoundation\Response;

final class StatisticsApiTest extends WebTestCase
{
    private KernelBrowser $client;

    protected function setUp(): void
    {
        $this->client = static::createClient();
    }

    public function testGetMatchStatistics(): void
    {
        // First create some events
        $this->createGoalEvent('stat-match', 'arsenal', 'saka', 10);
        $this->createFoulEvent('stat-match', 'chelsea', 'silva', 20);

        $this->client->request('GET', '/api/statistics?match_id=stat-match');

        self::assertResponseIsSuccessful();

        $response = json_decode($this->client->getResponse()->getContent(), true);
        self::assertSame('stat-match', $response['match_id']);
        self::assertArrayHasKey('statistics', $response);
    }

    public function testGetTeamStatistics(): void
    {
        $this->createGoalEvent('team-stat-match', 'arsenal', 'saka', 10);
        $this->createGoalEvent('team-stat-match', 'arsenal', 'odegaard', 30);

        $this->client->request('GET', '/api/statistics?match_id=team-stat-match&team_id=arsenal');

        self::assertResponseIsSuccessful();

        $response = json_decode($this->client->getResponse()->getContent(), true);
        self::assertSame('team-stat-match', $response['match_id']);
        self::assertSame('arsenal', $response['team_id']);
    }

    public function testReturnsErrorWithoutMatchId(): void
    {
        $this->client->request('GET', '/api/statistics');

        self::assertResponseStatusCodeSame(Response::HTTP_BAD_REQUEST);

        $response = json_decode($this->client->getResponse()->getContent(), true);
        self::assertSame('match_id is required', $response['error']);
    }

    public function testReturnsEmptyStatisticsForNonexistentMatch(): void
    {
        $this->client->request('GET', '/api/statistics?match_id=nonexistent');

        self::assertResponseIsSuccessful();

        $response = json_decode($this->client->getResponse()->getContent(), true);
        self::assertEmpty($response['statistics']);
    }

    private function createGoalEvent(string $matchId, string $teamId, string $scorer, int $minute): void
    {
        $this->client->request(
            'POST',
            '/api/events',
            [],
            [],
            ['CONTENT_TYPE' => 'application/json'],
            json_encode([
                'type' => 'goal',
                'match_id' => $matchId,
                'team_id' => $teamId,
                'scorer' => $scorer,
                'minute' => $minute,
            ]),
        );
    }

    private function createFoulEvent(string $matchId, string $teamId, string $player, int $minute): void
    {
        $this->client->request(
            'POST',
            '/api/events',
            [],
            [],
            ['CONTENT_TYPE' => 'application/json'],
            json_encode([
                'type' => 'foul',
                'match_id' => $matchId,
                'team_id' => $teamId,
                'player' => $player,
                'minute' => $minute,
            ]),
        );
    }
}
