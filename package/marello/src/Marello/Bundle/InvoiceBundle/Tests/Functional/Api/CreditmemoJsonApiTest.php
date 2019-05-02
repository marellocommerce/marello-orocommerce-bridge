<?php

namespace Marello\Bundle\OrderBundle\Tests\Functional\Api;

use Symfony\Component\HttpFoundation\Response;

use Marello\Bundle\OrderBundle\Entity\Order;
use Marello\Bundle\InvoiceBundle\Entity\Invoice;
use Marello\Bundle\CoreBundle\Tests\Functional\RestJsonApiTestCase;
use Marello\Bundle\InvoiceBundle\Tests\Functional\DataFixtures\LoadCreditmemoData;

class CreditmemoJsonApiTest extends RestJsonApiTestCase
{
    const TESTING_ENTITY = 'marellocreditmemos';

    protected function setUp()
    {
        parent::setUp();
        $this->loadFixtures([
            LoadCreditmemoData::class
        ]);
    }

    /**
     * Test cget (getting a list of creditmemos) of Creditmemo entity
     *
     */
    public function testGetListOfCreditmemos()
    {
        $response = $this->cget(['entity' => self::TESTING_ENTITY], []);

        $this->assertJsonResponse($response);
        $this->assertResponseStatusCodeEquals($response, Response::HTTP_OK);
        $this->assertResponseCount(2, $response);
        $this->assertResponseContains('cget_creditmemo_list.yml', $response);
    }

    /**
     * Test get creditmemo by id
     */
    public function testGetCreditmemoById()
    {
        /** @var Invoice $invoice */
        $invoice = $this->getReference('marello_creditmemo_0');
        $response = $this->get(
            ['entity' => self::TESTING_ENTITY, 'id' => $invoice->getId()],
            []
        );

        $this->assertJsonResponse($response);
        $this->assertResponseStatusCodeEquals($response, Response::HTTP_OK);
        $this->assertResponseContains('get_creditmemo_by_id.yml', $response);
    }

    /**
     * Test get creditmemo by invoiceNumber
     */
    public function testGetCreditmemoByInvoiceNumber()
    {
        /** @var Invoice $invoice */
        $invoice = $this->getReference('marello_creditmemo_1');
        $response = $this->get(
            ['entity' => self::TESTING_ENTITY, 'id' => $invoice->getId()],
            [
                'filter' => ['invoiceNumber' =>  $invoice->getInvoiceNumber() ]
            ]
        );

        $this->assertJsonResponse($response);
        $this->assertResponseStatusCodeEquals($response, Response::HTTP_OK);
        $this->assertResponseContains('get_creditmemo_by_invoiceNumber.yml', $response);
    }

    /**
     * Test cget (getting a list of creditmemos) of Creditmemo entity filter by order id
     *
     */
    public function testGetListOfCreditmemosFilteredByOrder()
    {
        /** @var Order $order */
        $order = $this->getReference('marello_order_1');

        $response = $this->cget(
            ['entity' => self::TESTING_ENTITY],
            [
                'filter' => ['order' =>  $order->getId() ]
            ]
        );

        $this->assertJsonResponse($response);
        $this->assertResponseStatusCodeEquals($response, Response::HTTP_OK);
        $this->assertResponseCount(1, $response);
        $this->assertResponseContains('cget_creditmemo_list_by_order.yml', $response);
    }
}
