<?php

namespace Hackathon\BotCommerce\Model\Commands;

class Orderstatus extends AbstractCommand
{
    /**
     * Get's triggered if one of the words of each line is present
     *
     * @var array
     */
    protected $_triggerwords = [
        ['order','package'],
        ['status','ship']
    ];
}