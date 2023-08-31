<?php

namespace Usercom\Analytics\Setup\Patch\Data;

use Magento\Customer\Model\Customer;
use Magento\Customer\Model\ResourceModel\Attribute;
use Magento\Customer\Setup\CustomerSetupFactory;
use Magento\Customer\Setup\Patch\Data\UpdateIdentifierCustomerAttributesVisibility;
use Magento\Framework\Setup\ModuleDataSetupInterface;
use Magento\Framework\Setup\Patch\DataPatchInterface;


/**
 * Class AddUserComUserIdAttribute
 *
 * @author Piotr Niewczas <piotr.niewczas@movecloser.pl>
 */
class AddUserComKeyAttribute implements DataPatchInterface
{
    /**
     * @var ModuleDataSetupInterface
     */
    private $moduleDataSetup;

    /**
     * @var CustomerSetupFactory
     */
    private $customerSetupFactory;

    /**
     * @var Attribute
     */
    private $attributeResource;

    /**
     * @param ModuleDataSetupInterface $moduleDataSetup
     * @param CustomerSetupFactory $customerSetupFactory
     * @param Attribute $attributeResource
     */
    public function __construct(
        ModuleDataSetupInterface $moduleDataSetup,
        CustomerSetupFactory $customerSetupFactory,
        Attribute $attributeResource
    ) {
        $this->moduleDataSetup      = $moduleDataSetup;
        $this->customerSetupFactory = $customerSetupFactory;
        $this->attributeResource    = $attributeResource;
    }

    /**
     * @inheritdoc
     */
    public static function getDependencies()
    {
        return [
            UpdateIdentifierCustomerAttributesVisibility::class,
        ];
    }

    /**
     * @inheritdoc
     * @throws \Magento\Framework\Exception\AlreadyExistsException
     */
    public function apply()
    {
        $customerSetup = $this->customerSetupFactory->create(['setup' => $this->moduleDataSetup]);

        /**
         * Add attribute
         */
        $customerSetup->addAttribute(
            Customer::ENTITY,
            'usercom_key',
            [
                'type'         => 'varchar',
                'label'        => 'UserCom Key',
                'input'        => 'text',
                'backend_type' => 'varchar',
                'source'       => null,
                'position'     => 101,
                'required'     => false,
                'system'       => false,
                'default'      => null
            ]
        );

        /**
         * Fetch the newly created attribute and set options to be used in forms
         */
        $userComUserIdAttribute = $customerSetup->getEavConfig()->getAttribute(
            Customer::ENTITY,
            'usercom_key'
        );

        $userComUserIdAttribute->setData(
            'used_in_forms',
            ['adminhtml_customer']
        );

        $this->attributeResource->save($userComUserIdAttribute);

        return $this;
    }

    /**
     * @inheritdoc
     */
    public function getAliases()
    {
        return [];
    }
}
