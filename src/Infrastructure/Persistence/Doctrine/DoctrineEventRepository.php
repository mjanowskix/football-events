<?php

declare(strict_types=1);

namespace App\Infrastructure\Persistence\Doctrine;

use App\Domain\Entity\FootballEvent;
use App\Domain\Repository\EventRepositoryInterface;
use App\Domain\ValueObject\EventId;
use App\Domain\ValueObject\MatchId;
use App\Domain\ValueObject\TeamId;
use Doctrine\ORM\EntityManagerInterface;

final readonly class DoctrineEventRepository implements EventRepositoryInterface
{
    public function __construct(
        private EntityManagerInterface $entityManager,
    ) {
    }

    public function save(FootballEvent $event): void
    {
        $this->entityManager->persist($event);
        $this->entityManager->flush();
    }

    public function findById(EventId $id): ?FootballEvent
    {
        return $this->entityManager->find(FootballEvent::class, $id);
    }

    public function findByMatch(MatchId $matchId): array
    {
        return $this->entityManager
            ->getRepository(FootballEvent::class)
            ->findBy(['matchId' => $matchId], ['occurredAt' => 'ASC']);
    }

    public function findByMatchAndTeam(MatchId $matchId, TeamId $teamId): array
    {
        return $this->entityManager
            ->getRepository(FootballEvent::class)
            ->findBy(
                ['matchId' => $matchId, 'teamId' => $teamId],
                ['occurredAt' => 'ASC'],
            );
    }

    public function findAll(): array
    {
        return $this->entityManager
            ->getRepository(FootballEvent::class)
            ->findBy([], ['createdAt' => 'DESC']);
    }
}
