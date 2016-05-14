<?php

namespace Hackathon\BotCommerce\Model\Commands;

class AbstractCommand extends \Magento\Framework\Model\AbstractModel
{
    protected $scopeConfig;
    protected $objectManager;


    public function __construct(
        \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig,
        \Magento\Framework\ObjectManager $objectManager
    ) {
        $this->scopeConfig = $scopeConfig;
        $this->objectManager= $objectManager;
    }

    public function matchKeywords($keywords)
    {
        foreach ($this->_triggerwords as $typeLine) {
            $lineMatch = false;
            foreach ($typeLine as $word) {

                foreach ($keywords as $keyword) {
                    if (strstr($keyword, $word)) { // if the trigger word is in the given keyword line is matched
                        $lineMatch = true;
                        break;
                    }
                }

            }

            if ($lineMatch == false) {
                return false;
            }
        }

        return true;
    }
}