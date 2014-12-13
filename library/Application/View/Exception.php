<?php
namespace Application\View;

use Application\Application;
use DateTime;

class Exception
{
    /**
     * Line of exception
     * 
     * @var string
     */
    private $line;

    /**
     * File of exception
     * 
     * @var string
     */
    private $file;

    /**
     * Send exception page to client
     * 
     * @param object $exception
     * @param string $type
     * @param string $template
     * @return void
     */
    public function drawExceptionMessage($exception, $type = null, $template = 'Exception')
    {
        // Set headers
        header('HTTP/1.1 502 Bad Gateway', true, 502);

        $this->line = $exception->getLine();
        $this->file = $exception->getFile();

        // Variables to render
        $this->render([
            'message' => str_replace([
                '++',
                '+-+'
            ], [
                '<code style="white-space: normal">', 
                '</code>'
            ], $exception->getMessage()),
            'stacktrace' => $exception->getTrace(),
            'lines'   => $this->fileContent(),
            'data'    => [$this->line, $this->line-1, $this->file],
            'type'    => (string)$type
        ]);

        // Display page
        $this->{$template};

        // Save last error
        $this->saveLastException($exception);
    }

    /**
     * Create exception lines
     *  
     * @return array
     */
    private function fileContent()
    {
        // Load file
        $file = @file($this->file);

        if (count($file) >= 7) {
            $lines[$this->line-4] = (isset($file[$this->line-4]) ? $file[$this->line-4] : null);
            $lines[$this->line-3] = (isset($file[$this->line-3]) ? $file[$this->line-3] : null);
            $lines[$this->line-2] = (isset($file[$this->line-2]) ? $file[$this->line-2] : null);
            $lines[$this->line-1] = (isset($file[$this->line-1]) ? $file[$this->line-1] : null); // Exception line
            $lines[$this->line]   = (isset($file[$this->line])   ? $file[$this->line]   : null);
            $lines[$this->line+1] = (isset($file[$this->line+1]) ? $file[$this->line+1] : null);
            $lines[$this->line+2] = (isset($file[$this->line+2]) ? $file[$this->line+2] : null);            
        } else {
            $lines = ['-'];
        }

        return $lines;
    }

    /**
     * Storage last exception in cache
     * 
     * @param object $exception
     * @return void
     */
    private function saveLastException($exception)
    {
        $_sourceToSave = 'Last exception generated by SimPas Application' . PHP_EOL;
        $_sourceToSave .= '-- Time: ' . (new DateTime)->format('r') . PHP_EOL;
        $_sourceToSave .= '-- Exception message: ' . $exception->getMessage() . PHP_EOL;
        $_sourceToSave .= '-- Exception file: ' . $exception->getFile() . PHP_EOL;
        $_sourceToSave .= '-- Exception line: ' . $exception->getLine();

        @file_put_contents(Application::makePath('storage:last_exception.cgi'), $_sourceToSave);
    }
}
