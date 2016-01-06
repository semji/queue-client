<?php

namespace ReputationVIP\QueueClient\PriorityHandler;

class ThreeLevelPriorityHandler extends StandardPriorityHandler
{
    /**
     * @var int
     */
    protected $defaultIndex = 1;

    /**
     * @var []
     */
    protected $priorities = [
        'HIGH',
        'MID',
        'LOW'
    ];
}
