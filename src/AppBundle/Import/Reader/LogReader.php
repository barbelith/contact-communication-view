<?php


namespace AppBundle\Import\Reader;


use AppBundle\Entity\Operation;
use AppBundle\Import\Exception\NoContentsLoadedException;
use AppBundle\Import\Loader\LoaderInterface;
use AppBundle\Import\Mapping\LogOperationMapping;
use AppBundle\Import\Parser\LogOperationParser;
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

        $parser = new LogOperationParser(new LogOperationMapping());

        foreach ($logContent as $line) {
            $operation = $parser->parseItem($line);

            if ($operation) {
                $operations->add($operation);
            }

        }

        return $operations;
    }
}