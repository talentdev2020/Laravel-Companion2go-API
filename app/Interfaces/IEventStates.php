<?php

namespace App\Interfaces;


/**
 * Interface IEventStates
 * @package App\Interfaces
 */
interface IEventStates
{
    const STATE_NEW = 1;
    const STATE_ACCEPTED = 2;
    const STATE_REJECTED = 3;
    const STATE_DELETED = 4;
    const STATE_HIDDEN = 5;
}


