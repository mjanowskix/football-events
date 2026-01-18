<?php

declare(strict_types=1);

namespace App\Application\DTO;

use App\Domain\Entity\FootballEvent;

/**
 * Response DTO for event creation.
 */
final readonly class EventResponse
{
    /**
     * @param array<string, mixed> $data
     */
    private function __construct(
        public string $status,
        public string $message,
        public array $data,
    ) {
    }

    public static function fromEntity(FootballEvent $event): self
    {
        return new self(
            status: 'success',
            message: 'Event saved successfully',
            data: [
                'type' => $event->type()->value,
                'timestamp' => $event->createdAt()->getTimestamp(),
                'event' => $event->toArray(),
            ],
        );
    }

    /**
     * @return array<string, mixed>
     */
    public function toArray(): array
    {
        return [
            'status' => $this->status,
            'message' => $this->message,
            'event' => $this->data,
        ];
    }
}
