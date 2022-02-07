<?php
declare(strict_types=1);

namespace MBauer\PhpSets\implementations;

enum ElementBehavior
{
    case DATA_COPY_ASSIGNMENT;
    case DATA_CLONE_WHERE_POSSIBLE;
    case DATA_COPY_WITH_WEAKREF;
}