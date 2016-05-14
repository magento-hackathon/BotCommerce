<?php

namespace TextProcessing;

class Wrapper
{
    protected $language;

    protected $body;

    const APIURL = 'http://text-processing.com/api/%s/';

    const OUTPUT_TAGGED = 'tagged';
    const OUTPUT_SEXPR = 'sexpr';
    const OUTPUT_IOB = 'iob';

    public function __construct($body, $language)
    {
        $this->language = $language;
        $this->body = $body;
    }

    protected function _call($api, $postFields)
    {
        $apiUrl = sprintf(self::APIURL, $api);

        //open connection
        $ch = curl_init();

        //set the url, number of POST vars, POST data
        curl_setopt($ch,CURLOPT_URL, $apiUrl);
        curl_setopt($ch,CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch,CURLOPT_POST, true);
        curl_setopt($ch,CURLOPT_POSTFIELDS, http_build_query($postFields, '', '&'));

        //execute post
        $result = curl_exec($ch);

        //close connection
        curl_close($ch);

        return json_decode($result, true);
    }

    public function getPOS($output)
    {
        $result = $this->_call('tag', [
            'output' => $output,
            'text' => $this->body,
            'language' => $this->language
        ]);

        return $result;
    }

    public function getNER()
    {
        $result = $this->_call('phrases', [
            'text' => $this->body,
            'language' => $this->language
        ]);

        return $result;
    }

    public function getStem()
    {
        $result = $this->_call('stem', [
            'text' => $this->body
        ]);

        return $result;
    }

    public function getSentiment()
    {
        $result = $this->_call('sentiment', [
            'text' => $this->body,
            'language' => $this->language
        ]);

        return $result;
    }

}