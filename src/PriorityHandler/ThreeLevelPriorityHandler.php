<?php

namespace ReputationVIP\QueueClient\PriorityHandler;

use ReputationVIP\QueueClient\PriorityHandler\Priority\Priority;

class ThreeLevelPriorityHandler extends StandardPriorityHandler
{
    public function __construct()
    {
        parent::__construct();
        $this->priorities = [];
        $this->add(new Priority('HIGH', 0));
        $default = new Priority('MID', 100);
        $this->add($default);
        $this->add(new Priority('LOW', 200));
        $this->setDefault($default);
    }
}
