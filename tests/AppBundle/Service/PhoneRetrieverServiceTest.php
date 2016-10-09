<?php


namespace AppBundle\Service;


use AppBundle\Entity\CallOperation;
use AppBundle\Entity\Contact;
use AppBundle\Entity\Operation;
use AppBundle\Entity\PhoneOwner;
use AppBundle\Import\Factory\ReaderFactory;
use AppBundle\Import\Parser\ParsedOperation;
use AppBundle\Import\Reader\ReaderInterface;
use AppBundle\Repository\ContactRepository;
use AppBundle\Repository\OperationRepository;
use AppBundle\Repository\PhoneOwnerRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\EntityManagerInterface;

class PhoneRetrieverServiceTest extends \PHPUnit_Framework_TestCase
{
    const PHONE_OWNER_ID = 1;

    const CONTACT_NUMBER = '600123456';
    const CONTACT_NAME = 'Pepe';

    protected $persistedObjects = [];

    /**
     * @test
     */
    public function retrievePhoneNumberCreatesCorrectLogPath()
    {
        $expectedLogPath = 'http://www.example.com/600123456.log';

        $phoneOwner = new PhoneOwner();
        $phoneOwner->setId(self::PHONE_OWNER_ID);

        $logOperations = new ArrayCollection();
        $existingContacts = new ArrayCollection();
        $existingOperations = new ArrayCollection();

        $entityManager = $this->createEntityManager($phoneOwner, $existingContacts, $existingOperations);
        $readerFactory = $this->createReaderFactory($logOperations, $expectedLogPath);
        $service = new PhoneRetrieverService('http://www.example.com/{number}.log', $entityManager, $readerFactory);

        $service->retrievePhoneNumberLogs('600123456');
    }

    /**
     * @test
     */
    public function retrievePhoneNumberReturnsPhoneOwner()
    {
        $phoneOwner = new PhoneOwner();
        $phoneOwner->setId(self::PHONE_OWNER_ID);

        $logOperations = new ArrayCollection();
        $existingContacts = new ArrayCollection();
        $existingOperations = new ArrayCollection();

        $entityManager = $this->createEntityManager($phoneOwner, $existingContacts, $existingOperations);
        $readerFactory = $this->createReaderFactory($logOperations);
        $service = new PhoneRetrieverService('http://www.example.com', $entityManager, $readerFactory);

        $phoneOwner = $service->retrievePhoneNumberLogs('600123456');

        $this->assertInstanceOf(PhoneOwner::class, $phoneOwner);
    }

    /**
     * @test
     */
    public function retrievePhoneNumberCreatesNewContactAndOperation()
    {
        $phoneOwner = new PhoneOwner();
        $phoneOwner->setId(self::PHONE_OWNER_ID);

        $logOperations = new ArrayCollection();
        $logOperations->set(self::CONTACT_NUMBER, $this->createParsedOperation());

        $existingContacts = new ArrayCollection();
        $existingOperations = new ArrayCollection();

        $entityManager = $this->createEntityManager($phoneOwner, $existingContacts, $existingOperations);
        $readerFactory = $this->createReaderFactory($logOperations);
        $service = new PhoneRetrieverService('http://www.example.com', $entityManager, $readerFactory);

        $service->retrievePhoneNumberLogs('600123456');

        $this->assertEquals(1, count($this->persistedObjects['Contact']));
        $this->assertEquals(1, count($this->persistedObjects['CallOperation']));
    }

    /**
     * @test
     */
    public function retrievePhoneNumberUsesOldContactAndOperation()
    {
        $phoneOwner = new PhoneOwner();
        $phoneOwner->setId(self::PHONE_OWNER_ID);

        $logOperations = new ArrayCollection();
        $logOperations->set(self::CONTACT_NUMBER, $this->createParsedOperation());

        $operation = $this->createCallOperation();
        $operation->setDuration(100);

        $existingContacts = new ArrayCollection();

        $contact = $operation->getContact();
        $contact->setName('Juan');
        $existingContacts->add($contact);

        $existingOperations = new ArrayCollection();
        $existingOperations->add($operation);

        $entityManager = $this->createEntityManager($phoneOwner, $existingContacts, $existingOperations);
        $readerFactory = $this->createReaderFactory($logOperations);
        $service = new PhoneRetrieverService('http://www.example.com', $entityManager, $readerFactory);

        $service->retrievePhoneNumberLogs('600123456');

        $this->assertEquals(self::CONTACT_NAME, $contact->getName());
        $this->assertFalse(isset($this->persistedObjects['CallOperation']));
    }

    /**
     * @param $phoneOwner
     * @param $existingContacts
     * @param $existingOperations
     * @return \PHPUnit_Framework_MockObject_MockObject
     */
    private function createEntityManager($phoneOwner, $existingContacts, $existingOperations)
    {
        $phoneOwnerRepository = $this->createMock(PhoneOwnerRepository::class);
        $phoneOwnerRepository->method('findOrCreate')->willReturn($phoneOwner);

        $contactRepository = $this->createMock(ContactRepository::class);
        $contactRepository->method('findContactsForOwner')->willReturn($existingContacts);

        $operationRepository = $this->createMock(OperationRepository::class);
        $operationRepository->method('findOperationsForOwner')->willReturn($existingOperations);

        $entityManager = $this->createMock(EntityManagerInterface::class);
        $entityManager->method('getRepository')->will(
            $this->returnValueMap([
                ['AppBundle:PhoneOwner', $phoneOwnerRepository],
                ['AppBundle:Contact', $contactRepository],
                ['AppBundle:Operation', $operationRepository],
            ])
        );

        $entityManager->method('persist')->willReturnCallback(function($object) {
            $reflect = new \ReflectionClass($object);

            if (!isset($this->persistedObjects[$reflect->getShortName()])) {
                $this->persistedObjects[$reflect->getShortName()] = [];
            }

            $this->persistedObjects[$reflect->getShortName()][] = $object;
        });

        return $entityManager;
    }

    /**
     * @param $logOperations
     * @param $expectedLogPath
     * @return \PHPUnit_Framework_MockObject_MockObject
     */
    private function createReaderFactory($logOperations, $expectedLogPath = null)
    {
        $reader = $this->createMock(ReaderInterface::class);
        $reader->expects($this->once())->method('getOperations')->willReturn($logOperations);

        $readerFactory = $this->createMock(ReaderFactory::class);

        if ($expectedLogPath) {
            $readerFactory->expects($this->once())->method('getReader')->with($expectedLogPath)->willReturn($reader);
        } else {
            $readerFactory->method('getReader')->willReturn($reader);
        }

        return $readerFactory;
    }

    private function createParsedOperation()
    {
        $parsedOperation = new ParsedOperation();
        $parsedOperation->setType(Operation::TYPE_CALL);
        $parsedOperation->setContactNumber(self::CONTACT_NUMBER);
        $parsedOperation->setContactName(self::CONTACT_NAME);
        $parsedOperation->setDate(new \DateTime());
        $parsedOperation->setDirection(Operation::DIRECTION_INCOMING);

        return $parsedOperation;
    }

    private function createContact()
    {
        $contact = new Contact();
        $contact->setId(1);
        $contact->setPhoneOwnerId(self::PHONE_OWNER_ID);
        $contact->setNumber(self::CONTACT_NUMBER);
        $contact->setName(self::CONTACT_NAME);

        return $contact;
    }

    private function createCallOperation()
    {
        $operation = new CallOperation();
        $operation->setId(1);
        $operation->setContact($this->createContact());
        $operation->setDate(new \DateTime());
        $operation->setDirection(Operation::DIRECTION_INCOMING);

        return $operation;
    }
}
