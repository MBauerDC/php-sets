<?php
declare(strict_types=1);

namespace MBauer\PhpSets\contracts;

interface HasElement
{   
    public function hasElement(Element $el): bool;
}