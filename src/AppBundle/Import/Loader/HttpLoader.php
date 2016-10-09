<?php


namespace AppBundle\Import\Loader;


class HttpLoader implements LoaderInterface
{
    protected $url;

    /**
     * HttpLoader constructor.
     * @param $url
     */
    public function __construct($url)
    {
        $this->url = $url;
    }

    /**
     * @return array
     */
    public function getContents()
    {
        if (filter_var($this->url, FILTER_VALIDATE_URL) === false) {
            throw new \LogicException('The url is not valid');
        }

        $fileContents = $this->downloadFile();

        return $this->fileToArray($fileContents);
    }

    /**
     * @param string $fileContents
     * @return array
     */
    private function fileToArray($fileContents)
    {
        $lines = preg_split ('/$\R?^/m', $fileContents);
        $cleanedLines = [];

        foreach ($lines as $line) {
            $line = trim($line);

            if (strlen($line) > 0) {
                $cleanedLines[] = $line;
            }
        }

        return $cleanedLines;
    }

    /**
     * @return string
     */
    protected function downloadFile()
    {
        $ch = curl_init();

        curl_setopt($ch, CURLOPT_URL, $this->url);
        curl_setopt($ch, CURLOPT_VERBOSE, 1);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_HTTP_VERSION, CURL_HTTP_VERSION_1_1);
        curl_setopt($ch, CURLOPT_HEADER, 0);

        $file = curl_exec($ch);

        curl_close($ch);

        return $file;
    }
}