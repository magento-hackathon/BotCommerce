<?php

namespace Hackathon\BotCommerce\Model\Commands;

use Hackathon\BotCommerce\Service\MessageService;

class Productstock extends AbstractCommand
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
        ['product'],
        ['stock']
    ];

    public function executeCommand($body, $words)
    {
        $sku = '001';
        $stock = $this->messageService->getProductStockStatus($ku);

        return new \Magento\Framework\Phrase('The stock for %1 is %2', [$sku, $stock]);
    }
}