<?php


namespace AppBundle\Controller;

use AppBundle\Repository\OperationRepository;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;

class PhoneNumberController extends Controller
{
    /**
     * @Route("/phone/{number}", name="phone_number_show")
     * @Template()
     * @return array
     */
    public function showAction($number)
    {
        /** @var \AppBundle\Service\PhoneRetrieverService $phoneRetrieverService */
        $phoneRetrieverService = $this->get('communication.phone_retriever');
        $phoneOwner = $phoneRetrieverService->retrievePhoneNumberLogs($number);

        /** @var OperationRepository $operationRepository */
        $operationRepository = $this->getDoctrine()->getRepository('AppBundle:Operation');
        $operations = $operationRepository->findOperationsForOwnerSortedByContactNameAndDate($phoneOwner->getId());

        $contactsAndOperations = [];

        foreach ($operations as $operation) {
            $contactId = $operation->getContact()->getId();

            if (!isset($contactsAndOperations[$contactId])) {
                $contactsAndOperations[$contactId] = [
                    'contact' => $operation->getContact(),
                    'operations' => []
                ];
            }
            $contactsAndOperations[$contactId]['operations'][] = $operation;
        }

        return [
            'phone_owner'                   => $phoneOwner,
            'contacts_and_operations'       => $contactsAndOperations
        ];
    }
}