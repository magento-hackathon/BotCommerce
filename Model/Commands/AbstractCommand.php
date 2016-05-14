<?php

namespace Hackathon\BotCommerce\Model\Commands;

class AbstractCommand
{
    protected $_triggerwords = [];

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