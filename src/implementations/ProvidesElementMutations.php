<?php
declare(strict_types=1);

namespace MBauer\PhpSets\implementations;

use MBauer\PhpSets\contracts\Element;
use function array_key_exists;

trait ProvidesElementMutations
{
    use HasMutableElements;

    public function addElements(Element ...$els): void
    {
        foreach ($els as $el) {
            $this->elements[$el->getIdentifier()] = $el;
        }
        $this->updateHash();
    }

    public function removeElements(Element ...$els): void
    {
        foreach ($els as $el) {
            $id = $el->getIdentifier();
            if (array_key_exists($id, $this->elements)) {
                unset($this->elements[$id]);
            }
        }
        $this->updateHash();
    }

    public function removeElementsById(string ...$ids): void
    {
        foreach ($ids as $id) {
            if (array_key_exists($id, $this->elements)) {
                unset($this->elements[$id]);
            }
        }
        $this->updateHash();
    }
}
