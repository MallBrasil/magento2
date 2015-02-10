<?php
/**
 * Copyright © 2015 Magento. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Magento\Customer\Block\Adminhtml\Edit\Tab;

use Magento\Customer\Api\CustomerRepositoryInterface;
use Magento\Customer\Api\Data\CustomerInterfaceFactory;
use Magento\Customer\Controller\RegistryConstants;

/**
 * Magento\Customer\Block\Adminhtml\Edit\Tab\View
 *
 * @SuppressWarnings(PHPMD.CouplingBetweenObjects)
 * @magentoAppArea adminhtml
 */
class ViewTest extends \PHPUnit_Framework_TestCase
{
    /** @var  \Magento\Backend\Block\Template\Context */
    private $_context;

    /** @var  \Magento\Framework\Registry */
    private $_coreRegistry;

    /** @var  CustomerDataBuilder */
    private $_customerFactory;

    /** @var  CustomerRepositoryInterface */
    private $_customerRepository;

    /** @var \Magento\Framework\Store\StoreManagerInterface */
    private $_storeManager;

    /** @var \Magento\Framework\ObjectManagerInterface */
    private $_objectManager;

    /** @var \Magento\Framework\Reflection\DataObjectProcessor */
    private $_dataObjectProcessor;

    /** @var  View */
    private $_block;

    public function setUp()
    {
        $this->_objectManager = \Magento\TestFramework\Helper\Bootstrap::getObjectManager();

        $this->_storeManager = $this->_objectManager->get('Magento\Store\Model\StoreManager');
        $this->_context = $this->_objectManager->get(
            'Magento\Backend\Block\Template\Context',
            ['storeManager' => $this->_storeManager]
        );

        $this->_customerFactory = $this->_objectManager->get('Magento\Customer\Api\Data\CustomerInterfaceFactory');
        $this->_coreRegistry = $this->_objectManager->get('Magento\Framework\Registry');
        $this->_customerRepository = $this->_objectManager->get(
            'Magento\Customer\Api\CustomerRepositoryInterface'
        );
        $this->_dataObjectProcessor = $this->_objectManager->get('Magento\Framework\Reflection\DataObjectProcessor');

        $this->_block = $this->_objectManager->get(
            'Magento\Framework\View\LayoutInterface'
        )->createBlock(
            'Magento\Customer\Block\Adminhtml\Edit\Tab\View',
            '',
            [
                'context' => $this->_context,
                'registry' => $this->_coreRegistry
            ]
        );
    }

    public function tearDown()
    {
        $this->_coreRegistry->unregister(RegistryConstants::CURRENT_CUSTOMER_ID);
    }

    public function testGetTabLabel()
    {
        $this->assertEquals(__('Customer View'), $this->_block->getTabLabel());
    }

    public function testGetTabTitle()
    {
        $this->assertEquals(__('Customer View'), $this->_block->getTabTitle());
    }

    /**
     * @magentoDataFixture Magento/Customer/_files/customer.php
     */
    public function testCanShowTab()
    {
        $this->_loadCustomer();
        $this->assertTrue($this->_block->canShowTab());
    }

    public function testCanShowTabNot()
    {
        $this->_createCustomer();
        $this->assertFalse($this->_block->canShowTab());
    }

    /**
     * @magentoDataFixture Magento/Customer/_files/customer.php
     */
    public function testIsHiddenNot()
    {
        $this->_loadCustomer();
        $this->assertFalse($this->_block->isHidden());
    }

    public function testIsHidden()
    {
        $this->_createCustomer();
        $this->assertTrue($this->_block->isHidden());
    }

    /**
     * @return \Magento\Customer\Api\Data\CustomerInterface
     */
    private function _createCustomer()
    {
        /** @var \Magento\Customer\Api\Data\CustomerInterface $customer */
        $customer = $this->_customerFactory->create()->setFirstname(
            'firstname'
        )->setLastname(
            'lastname'
        )->setEmail(
            'email@email.com'
        );
        $data = ['account' => $this->_dataObjectProcessor
            ->buildOutputDataArray($customer, 'Magento\Customer\Api\Data\CustomerInterface'), ];
        $this->_context->getBackendSession()->setCustomerData($data);
        return $customer;
    }

    /**
     * @return \Magento\Customer\Api\Data\CustomerInterface
     */
    private function _loadCustomer()
    {
        /** @var \Magento\Customer\Api\Data\CustomerInterface $customer */
        $customer = $this->_customerRepository->getById(1);
        $data = ['account' => $this->_dataObjectProcessor
            ->buildOutputDataArray($customer, 'Magento\Customer\Api\Data\CustomerInterface'), ];
        $this->_context->getBackendSession()->setCustomerData($data);
        $this->_coreRegistry->register(RegistryConstants::CURRENT_CUSTOMER_ID, $customer->getId());
        return $customer;
    }

    protected function dataToString($data)
    {
        return parent::dataToString($data); // TODO: Change the autogenerated stub
    }
}
