<?php

declare(strict_types=1);

namespace App\Contracts\Api;

interface EditTaskModelInterface
{
    public function getStatus(): string;

    public function getPriority(): int;

    public function getTitle(): string;

    public function getDescription(): string;

    public function getParentId(): ?int;

    public function getUserId(): ?int;

    public function getFinishedAt(): ?string;
}
