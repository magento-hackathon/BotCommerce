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
        preg_match('/\[(.*?)\]/', $body, $matches);
        $sku = $matches[1];

        if (isset($matches[1])) {
            $stock = $this->messageService->getProductStockStatus($sku);

            return new \Magento\Framework\Phrase('The product %1 is %2', [$sku, $stock]);
        }

        return new \Magento\Framework\Phrase('Please provide a product sku in your message (eg. [abc123def456])', []);
    }
}
