<?php


namespace AppBundle\Import\Reader;


use AppBundle\Entity\Operation;
use AppBundle\Import\Exception\NoContentsLoadedException;
use AppBundle\Import\Loader\LoaderInterface;
use Doctrine\Common\Collections\ArrayCollection;

class LogReader implements ReaderInterface
{
    /**
     * @var LoaderInterface
     */
    protected $loader;

    /**
     * LogReader constructor.
     * @param LoaderInterface $loader
     */
    public function __construct(LoaderInterface $loader)
    {
        $this->loader = $loader;
    }

    /**
     * @return Operation[]|ArrayCollection
     * @throws NoContentsLoadedException
     */
    public function getOperations()
    {
        $logContent = $this->loader->getContents();

        if (null === $logContent) {
            throw new NoContentsLoadedException('No contents found');
        }

        return $this->parseOperations($logContent);
    }

    /**
     * @param $logContent
     * @return ArrayCollection
     */
    private function parseOperations($logContent)
    {
        $operations = new ArrayCollection();

        foreach ($logContent as $line) {
            $operations->add($this->processLine($line));
        }

        return $operations;
    }

    /**
     * @param $line
     * @return Operation
     */
    private function processLine($line)
    {
        return new Operation();
    }
}