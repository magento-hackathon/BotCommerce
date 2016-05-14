<?php

namespace Hackathon\BotCommerce\Model\Commands;

use Hackathon\BotCommerce\Service\MessageService;

class Orderstatus extends AbstractCommand
{
    /**
     * @var MessageService
     */
    private $messageService;
    
    public function __construct(MessageService $messageService)
    {
        $this->messageService = $messageService;
    }

    /**
     * Get's triggered if one of the words of each line is present
     *
     * @var array
     */
    protected $_triggerwords = [
        ['order','package'],
        ['status','ship']
    ];

    public function executeCommand()
    {
        $status = $this->messageService->getOrderStatus('000000001');

        return new \Magento\Framework\Phrase('You order status is %1', [$status]);
    }
}