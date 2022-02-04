<?php
declare(strict_types=1);

namespace MBauer\PhpSets\contracts;

interface HasElementById
{   
    public function hasElementById(string $id): bool;
}