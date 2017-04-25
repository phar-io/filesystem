<?php
namespace PharIo\FileSystem;

class LastModifiedDate {
    /**
     * @var \DateTimeImmutable
     */
    private $dateTime;

    /**
     * @param \DateTimeImmutable $dateTime
     */
    public function __construct(\DateTimeImmutable $dateTime) {
        $this->dateTime = $dateTime;
    }

    /**
     * @param int $timestamp
     * @throws FilenameException
     * @return LastModifiedDate
     */
    public static function fromTimestamp($timestamp) {
        $dateTime = \DateTimeImmutable::createFromFormat('U', $timestamp);
        if (false === $dateTime) {
            throw new FilenameException('Invalid last modified date');
        }

        return new self($dateTime);
    }

    /**
     * @param \DateTimeImmutable $date
     * @return bool
     */
    public function isOlderThan(\DateTimeImmutable $date) {
        return $this->dateTime < $date;
    }
}
