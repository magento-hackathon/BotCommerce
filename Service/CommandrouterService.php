<?php

namespace Hackathon\BotCommerce\Service;

use Hackathon\BotCommerce\Model\Commands\Orderstatus;
use Hackathon\BotCommerce\Model\Commands\Productstock;
use Hackathon\BotCommerce\Wrapper;
use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Framework\ObjectManagerInterface;
use Magento\Store\Model\ScopeInterface;

class CommandrouterService
{
    /**
     * @var ScopeConfigInterface
     */
    protected $scopeConfig;

    /**
     * @var ObjectManagerInterface
     */
    protected $objectManager;

    /**
     * @param ObjectManagerInterface $objectManager
     * @param ScopeConfigInterface $scopeConfig
     */
    public function __construct(
        ObjectManagerInterface $objectManager,
        ScopeConfigInterface $scopeConfig
    ) {
        $this->objectManager = $objectManager;
        $this->scopeConfig = $scopeConfig;
    }

    /**
     * @param string $message
     * @return string
     */
    public function processMessage($message)
    {
        $languageIso2 = substr($this->scopeConfig->getValue(
            'general/locale/code',
            ScopeInterface::SCOPE_STORE
        ), 0, 2);
        $language = Wrapper::getLanguage($languageIso2);

        $textProcessing = new Wrapper($message, $language);

        $ner = $textProcessing->getNER();

        $filterNERlist = [];
        foreach ($ner as $type) {
            $filterNERlist = array_merge(array_values($type), $filterNERlist);
        }

        $pos = $textProcessing->getPOS($textProcessing::OUTPUT_TAGGED);

        preg_match_all('/(\w+)\/(\w+)/i', $pos['text'], $matches);

        $textTypes = [];
        $i = 0;
        while ($i < count($matches[0])) {
            $type = $matches[2][$i];
            $value = $matches[1][$i];

//            if (in_array($value, $filterNERlist)) { // @todo NER gave false positives
//                $i++;
//                continue;
//            }

            if (!isset($textTypes[$type])) {
                $textTypes[$type] = [];
            }

            if (in_array($type, ['V'])) {
                $stem = new Wrapper($value, $language);

                $value = $stem->getStem()['text'];
            }

            $textTypes[$type][] = $value;
            $i++;
        }

        $executeList = $this->matchCommand($textTypes['NN']); // @todo check what types should be given here, for now just nouns
        $content = [];

        foreach ($executeList as $instance) {
            $content[] = $instance->executeCommand($message, $textTypes);
        }

        return implode("\n", $content);
    }

    public function getAllCommands()
    {
        /*
         * @todo make this dynamic with the array defined in a XML node that allows for easily adding more commands
         */
        return [
            'orderstatus' => [
                'class' => Orderstatus::class,
                'priority' => 1
            ],
            'productstock' => [
                'class' => Productstock::class,
                'priority' => 3
            ]
        ];
    }

    public function matchCommand($keywords)
    {
        $commands = $this->getAllCommands();
        $executeList = [];

        foreach ($commands as $command) {
            $instance = $this->objectManager->create($command['class']);
            if ($instance->matchKeywords($keywords)) { // check if the given keywords match the trigger words for the command
                $executeList[] = $instance;
            }
        }

        return $executeList;
    }
}
