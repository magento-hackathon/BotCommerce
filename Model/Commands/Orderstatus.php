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

    public function executeCommand()
    {
        $orderInstance = $this->objectManager->get('Magento\Sales\Model\Order');
        $orderObject = $orderInstance->load(1);

        return new \Magento\Phrase('You order status is %1', [$orderObject->getStatus]);
    }
}