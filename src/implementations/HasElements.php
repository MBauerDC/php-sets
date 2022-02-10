<?php
declare(strict_types=1);

namespace MBauer\PhpSets\implementations;

use Psalm\Pure;
use function array_key_exists;
use function array_keys;
use function array_map;
use function implode;

trait HasElements
{
    protected array $elements = [];

    protected ?array $elementIdMemo = null;

    protected string $hash;

    protected function updateHash(): void
    {
        $stringToHash = implode('|', $this->getElementIds());
        $this->hash = hash('murmur3f', $stringToHash);
    }

    protected function updateElementIdMemo(): void
    {
        $keys = array_keys($this->elements);
        $this->elementIdMemo = array_map(static fn(mixed $key): string => (string)$key, $keys);
    }

    /**
     * @return string[]
     */
    #[Pure]
    public function getElementIds(): array
    {
        if (null === $this->elementIdMemo) {
            $this->updateElementIdMemo();
        }
        return $this->elementIdMemo;
    }

}