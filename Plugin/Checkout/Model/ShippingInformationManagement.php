<?php                                                         

namespace Usercom\Analytics\Plugin\Checkout\Model;                        

class ShippingInformationManagement
{
    protected $helper;
    protected $usercom; 
    protected $cart;
    protected $session;


    public function __construct(
        \Usercom\Analytics\Helper\Data $helper,
        \Usercom\Analytics\Helper\Usercom $usercom,
        \Magento\Checkout\Model\Cart $cart,
        \Magento\Checkout\Model\Session $session
    ){
        $this->helper = $helper;
        $this->usercom = $usercom;
        $this->cart = $cart;
        $this->session = $session;
    }
    /**
     * @param \Magento\Checkout\Model\ShippingInformationManagement $subject
     * @param $cartId
     * @param \Magento\Checkout\Api\Data\ShippingInformationInterface $addressInformation
     */
    public function beforeSaveAddressInformation(
        \Magento\Checkout\Model\ShippingInformationManagement $subject,
        $cartId,
        \Magento\Checkout\Api\Data\ShippingInformationInterface $addressInformation
    ) {
        if( !$this->helper->isModuleEnabled() || !($usercomCustomerId = $this->usercom->getUsercomCustomerId()) )
            return;

        $shippingAddress = $addressInformation->getShippingAddress();
        $data = array(
            "first_name" =>  $shippingAddress->getFirstName(),
            "last_name" => $shippingAddress->getLastName(),
            "email" =>  $this->session->getGuestCustomerEmail(),
            "phone_number" => $shippingAddress->getTelephone(),
            "region" => $shippingAddress->getRegion(),
            "street" => implode(",",$shippingAddress->getStreet()),
            "postcode" => $shippingAddress->getPostcode(),
            "city" => $shippingAddress->getCity(),
            "company" => $shippingAddress->getCompany(),
        );

        $this->usercom->updateCustomer($usercomCustomerId,$data);



       $user = $this->usercom->getCustomerById($usercomCustomerId); 
        if (empty($user->companies) && ($companyName = $shippingAddress->getCompany()) && ($company = $this->usercom->createCompany(array("name"=>$companyName))) && isset($company->id)){
            $this->usercom->companyAddMember($company->id, array("user_id"=>$usercomCustomerId));
        }   


        $items = "";
        foreach ($this->cart->getQuote()->getAllVisibleItems() as $item)
            $items .= $item->getName().",";
        $items = trim($items,","); 

        $data = array(
            "user_id" => $usercomCustomerId,
            "name" => "order",
            "timestamp" => time(),
            "data" => array(
                "step" => 2,
                "first_name" =>  $shippingAddress->getFirstName(),
                "last_name" => $shippingAddress->getLastName(),
                "email" =>  $this->session->getGuestCustomerEmail(),
                "phone_number" => $shippingAddress->getTelephone(),
                "items" => $items,
                "region" => $shippingAddress->getRegion(),
                "street" => implode(",",$shippingAddress->getStreet()),
                "postcode" => $shippingAddress->getPostcode(),
                "city" => $shippingAddress->getCity(),
                "company" => $shippingAddress->getCompany()
            )
        );

        $this->usercom->createEvent($data);
    }

}
