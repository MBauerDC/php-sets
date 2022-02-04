<?php
declare(strict_types=1);

namespace MBauer\PhpSets\contracts;


interface Element
{   /** 
    * @return string Identifier - can be id, hash etc
    */
    public function getIdentifier(): string;

    public function clone(): Element;
}