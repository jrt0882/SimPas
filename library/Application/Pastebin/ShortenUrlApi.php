<?php
namespace Application\Pastebin;

use Application\FileManager\FileManager;

class ShortenUrlApi
{
    /**
    * Bit.ly Username
    * 
    * @var string
    */
    private $bitly_username = 'simpasapplication';

    /**
    * Bit.ly API Key
    * 
    * @var string
    */
    private $bitly_api_key = 'R_f3518c22e6f64286a06cb7b1306e4702';

    /**
     * File manager
     * 
     * @var object
     */
    private $file_manager;

    /**
    * Construct
    *  
    * @return void
    */
    public function __construct()
    {
        $this->file_manager = new FileManager();
    }

    /**
    * Shorten URL
    * 
    * @param string $long_url 
    * @return array|bool
    */
    public function shorten($long_url)
    {
        // Get API
        $response = $this->file_manager->getContentsFromUrl(sprintf('http://api.bit.ly/shorten?version=2.0.1&longUrl=%s&login=%s&apiKey=%s', 
            urlencode($long_url), $this->bitly_username, $this->bitly_api_key));

        // Decode from JSON
        $response = json_decode($response, true);

        if($response == null || $response['errorCode'] !== 0) {
            return false;
        }

        $short_url = null;

        // Fetch short URL
        foreach($response['results'] as $site) {
            $short_url = $site['shortUrl'];
        }
        
        return $short_url;
    }
}
