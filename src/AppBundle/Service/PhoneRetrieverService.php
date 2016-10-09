<?php


namespace AppBundle\Service;


use AppBundle\Entity\CallOperation;
use AppBundle\Entity\Contact;
use AppBundle\Entity\Operation;
use AppBundle\Entity\PhoneOwner;
use AppBundle\Entity\SMSOperation;
use AppBundle\Import\Exception\ImportException;
use AppBundle\Import\Factory\ReaderFactory;
use AppBundle\Import\Parser\ParsedOperation;
use AppBundle\Repository\ContactRepository;
use AppBundle\Repository\OperationRepository;
use AppBundle\Repository\PhoneOwnerRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\EntityManagerInterface;

class PhoneRetrieverService
{
    /**
     * @var string
     */
    private $logsUrl;

    /**
     * @var EntityManagerInterface
     */
    private $entityManager;

    /**
     * @var ReaderFactory
     */
    private $readerFactory;

    /**
     * PhoneRetrieverService constructor.
     * @param string $logsUrl
     * @param EntityManagerInterface $entityManager
     * @param ReaderFactory $readerFactory
     */
    public function __construct($logsUrl, EntityManagerInterface $entityManager, ReaderFactory $readerFactory)
    {
        $this->logsUrl = $logsUrl;
        $this->entityManager = $entityManager;
        $this->readerFactory = $readerFactory;
    }

    /**
     * @param string $number
     * @return PhoneOwner|bool
     */
    public function retrievePhoneNumberLogs($number)
    {
        $logLocation = $this->getLogLocation($number);

        try {
            $reader = $this->readerFactory->getReader($logLocation);
            $logOperations = $reader->getOperations();
        } catch (ImportException $e) {
            return false;
        }

        $phoneOwner = $this->getPhoneOwnerRepository()->findOrCreate($number);

        $currentContacts = $this->getContactRepository()->findContactsForOwner($phoneOwner->getId());
        $allContacts = $this->updateCurrentContactsFromLogOperations($currentContacts, $logOperations, $phoneOwner);

        $currentOperations = $this->getOperationRepository()->findOperationsForOwner($phoneOwner->getId());
        $this->createNewOperationsFromLogOperations($currentOperations, $logOperations, $phoneOwner, $allContacts);

        $this->entityManager->flush();

        return $phoneOwner;
    }

    private function getLogLocation($number)
    {
        return strtr($this->logsUrl, [
            '{number}' => $number
        ]);
    }

    /**
     * @return PhoneOwnerRepository
     */
    private function getPhoneOwnerRepository()
    {
        return $this->entityManager->getRepository('AppBundle:PhoneOwner');
    }

    /**
     * @return ContactRepository
     */
    private function getContactRepository()
    {
        return $this->entityManager->getRepository('AppBundle:Contact');
    }

    /**
     * @return OperationRepository
     */
    private function getOperationRepository()
    {
        return $this->entityManager->getRepository('AppBundle:Operation');
    }

    /**
     * @param Contact[]|array $currentContacts
     * @param ParsedOperation[]|ArrayCollection $operations
     * @param PhoneOwner $phoneOwner
     * @return Contact[]|ArrayCollection
     */
    private function updateCurrentContactsFromLogOperations($currentContacts, ArrayCollection $operations, PhoneOwner $phoneOwner)
    {
        $contactsByNumber = new ArrayCollection();

        foreach ($currentContacts as $contact) {
            $contactsByNumber->set($contact->getNumber(), $contact);
        }

        foreach ($operations as $operation) {
            $contactNumber = $operation->getContactNumber();
            $contact = $contactsByNumber->get($contactNumber);

            if (!$contact) {
                $contact = new Contact();
                $contact->setPhoneOwnerId($phoneOwner->getId());
                $contact->setName($operation->getContactName());
                $contact->setNumber($operation->getContactNumber());

                $contactsByNumber->set($contactNumber, $contact);
                $this->entityManager->persist($contact);
            }

            if ($contact->getId() && $operation->getContactName() != $contact->getName()) {
                $contact->setName($operation->getContactName());
                $this->entityManager->persist($contact);
            }
        }

        return $contactsByNumber;
    }

    /**
     * @param Operation[]|array $currentOperations
     * @param ParsedOperation[]|ArrayCollection $logOperations
     * @param PhoneOwner $phoneOwner
     * @param Contact[]|ArrayCollection $allContacts
     */
    private function createNewOperationsFromLogOperations(
        $currentOperations,
        $logOperations,
        $phoneOwner,
        $allContacts
    ) {
        $operationsIndexed = new ArrayCollection();

        foreach ($currentOperations as $operation) {
            $operationsIndexed->set($this->getOperationIdentifier($operation), $operation);
        }

        foreach ($logOperations as $logOperation) {
            $logOperationIdentifier = $this->getLogOperationIdentifier($logOperation);
            $operation = $operationsIndexed->get($logOperationIdentifier);

            if (!$operation) {
                switch ($logOperation->getType()) {
                    case Operation::TYPE_CALL:
                        $operation = new CallOperation();
                        $operation->setDuration($logOperation->getDuration());
                        break;
                    case Operation::TYPE_SMS:
                        $operation = new SMSOperation();
                        break;
                    default:
                        throw new \LogicException(sprintf('Unsupported operation type: %s', $operation));
                }

                $operation->setPhoneOwnerId($phoneOwner->getId());
                $operation->setContact($allContacts->get($logOperation->getContactNumber()));
                $operation->setDirection($logOperation->getDirection());
                $operation->setDate($logOperation->getDate());

                $operationsIndexed->set($logOperationIdentifier, $operation);
                $this->entityManager->persist($operation);
            }
        }
    }

    /**
     * @param Operation $operation
     * @return string
     */
    private function getOperationIdentifier(Operation $operation)
    {
        return md5($operation->getType().$operation->getContact()->getNumber().$operation->getDirection().$operation->getDate()->getTimestamp());
    }

    /**
     * @param ParsedOperation $operation
     * @return string
     */
    private function getLogOperationIdentifier(ParsedOperation $operation)
    {
        return md5($operation->getType().$operation->getContactNumber().$operation->getDirection().$operation->getDate()->getTimestamp());
    }
}