<?php

declare(strict_types=1);

namespace App\Service;

use DateTime;
use DateTimeImmutable;
use DateTimeInterface;
use DateTimeZone;

class DateService
{
    public function createFromFormat(
        string $format,
        string $data,
        DateTimeZone $timezone = null
    ): DateTimeImmutable|false {
        return DateTimeImmutable::createFromFormat(
            $format,
            $data,
            $timezone ?? $this->getServerTimezone()
        );
    }

    public function createFromAtom(string $date, DateTimeZone $timezone = null): DateTimeImmutable|false
    {
        return $this->createFromFormat(
            DateTimeInterface::ATOM,
            $date,
            $timezone ?? $this->getServerTimezone()
        );
    }

    public function createFromDateString(string $date, DateTimeZone $timezone = null): DateTimeImmutable|false
    {
        return $this->createFromAtom(
            sprintf('%sT00:00:00P', $date),
            $timezone ?? $this->getServerTimezone()
        );
    }

    public function createFromTimeString(
        string $time,
        DateTimeInterface $date = null,
        DateTimeZone $timezone = null
    ): DateTimeImmutable|false {
        if (null === $date) {
            $date = new DateTime('now', $timezone ?? $this->getServerTimezone());
        }

        return $this->createFromAtom(
            sprintf('%sT%s:00P', $date->format('Y-m-d'), $time),
            $date->getTimezone()
        );
    }

    public function getServerTimezone(): DateTimeZone
    {
        return new DateTimeZone('Europe/Paris');
    }
}
