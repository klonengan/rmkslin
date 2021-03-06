<?php
class Faspaycc_BcaCc_Model_Standard extends Mage_Payment_Model_Method_Abstract {
	protected $_code = 'bcacc';
	
	protected $_formBlockType = 'bcacc/form';
	protected $_infoBlockType = 'bcacc/info';

	protected $_isInitializeNeeded      = true;
	protected $_canUseInternal          = true;
	protected $_canUseForMultishipping  = false;
	
	
	
	public function getOrder(){
		if (!$this->_order) {
			$paymentInfo = $this->getInfoInstance();
			$this->_order = Mage::getModel('sales/order')
							->loadByIncrementId($paymentInfo->getOrder()->getRealOrderId());
		}
		return $this->_order;
	}

	public function getOrderPlaceRedirectUrl() {
		//return Mage::getUrl('bcacc/payment/redirect');
		return Mage::getUrl('bcacc/payment/redirect', array('_secure' => true));
	}

	public function getRedirectBlockType(){
		return $this->_redirectBlockType;
	}

	public function getUrl(){
		$server		= Mage::getStoreConfig('payment/bcacc/server_stage');
		$rd = $server == 1 ?
			"https://ucdev.faspay.co.id/payment/PaymentInterface.jsp" :
			"https://uc.faspay.co.id/payment/PaymentInterface.jsp";
		
		return $rd;
	}

	public function getFormFields(){
		
		
		$lastOrderId = Mage::getSingleton('checkout/session')->getLastRealOrderId();		
		$order		= Mage::getModel('sales/order')->loadByIncrementID($lastOrderId);
		$customerId = Mage::getSingleton('customer/session')->getCustomer()->getId();
   		$customer = Mage::getModel('customer/customer')->load($customerId);
   		$customerAddressId = $customer->getDefaultBilling();
 
   		$address  = Mage::getModel('customer/address')->load($customerAddressId);

		$_totalData		= $order->getData();
		$billing		= $order->getBillingAddress();
		$shipping		= $order->getShippingAddress();
		$amount			= $_totalData['base_grand_total'];

		$payment = $order->getPayment();
		$data= Mage::getSingleton('core/session')->getMydata();
		$card_num=$data['cc_number'];
		$card_cid=$data['cc_cid'];
		$card_name 		= $payment->getData('cc_owner');
		$card_types 	= substr($payment->getData('cc_type'),0,1);
		$exp_year 		= $payment->getData('cc_exp_year');
		
		if($payment->getData('cc_exp_month') < 10){
    		$exp_month = '0'.$payment->getData('cc_exp_month');
		}
		else{
    		$exp_month = $payment->getData('cc_exp_month');
		}
		
		$cvv 			= $payment->getData('cc_cid');
		
		
		if( Mage::getStoreConfig('payment/bcacc/active') == 1 ){
			$merchant 		= Mage::getStoreConfig('payment/bcacc/merchant_name');
			$mid			= Mage::getStoreConfig('payment/bcacc/mid');
			$pas			= Mage::getStoreConfig('payment/bcacc/password');
			$payment_type	= Mage::getStoreConfig('payment/bcacc/payment_type');
			$ind			= Mage::getStoreConfig('payment/bcacc/payment_indicator');
			$crt			= Mage::getStoreConfig('payment/bcacc/payment_criteria');
		}else{
			echo "Mohon Aktifkan Payment Channel Faspay Credit Card ".ucfirst($bank); exit;
		}

		$signaturecc	= sha1('##'.strtoupper($mid).'##'.strtoupper($pas).'##'.$lastOrderId.'##'.number_format($amount,'2','.','').'##'.'0'.'##');
		
		$post = array(
			"PAYMENT_METHOD"                  	=> '1',
            "TRANSACTIONTYPE"                 	=> $payment_type,
			"MERCHANTID" 						=> $mid,
			"MERCHANT_TRANID"					=> $lastOrderId,
			"TXN_PASSWORD"						=> $pas,
			"CURRENCYCODE"						=> 'IDR',
			"AMOUNT"							=> number_format($amount,'2','.',''),
			"CUSTNAME"							=> $customer->getName(),
			"CUSTEMAIL"							=> $customer->getEmail(),
			"DESCRIPTION"						=> 'Pembelian Barang di'.$merchant,
			"RESPONSE_TYPE"                   	=> '2',
			"RETURN_URL" 						=> Mage::getBaseUrl().'bcacc/payment/thanks',
			"SIGNATURE" 						=> $signaturecc,
			"BILLING_ADDRESS"					=> $billing->getData('street'),
			"BILLING_ADDRESS_CITY"				=> $billing->getData('city'),
			"BILLING_ADDRESS_REGION"			=> $billing->getData('region'),
			"BILLING_ADDRESS_STATE"				=> 'INDONESIA',
			"BILLING_ADDRESS_POSCODE"			=> $billing->getData('postcode'),
			"BILLING_ADDRESS_COUNTRY_CODE"		=> $billing->getData('country_id'),
			"RECEIVER_NAME_FOR_SHIPPING"		=> $shipping->getData('firstname').' '.$shipping->getData('lastname'),
			"SHIPPING_ADDRESS" 					=> $shipping->getData('street'),
			"SHIPPING_ADDRESS_CITY" 			=> $shipping->getData('city'),
			"SHIPPING_ADDRESS_REGION"			=> $shipping->getData('region'),
			"SHIPPING_ADDRESS_STATE"			=> 'INDONESIA',
			"SHIPPING_ADDRESS_POSCODE"			=> $shipping->getData('postcode'),
			"SHIPPING_ADDRESS_COUNTRY_CODE"		=> $shipping->getData('country_id'),
			"SHIPPINGCOST"						=> number_format($_totalData['shipping_amount'],'2','.',''),
			"PHONE_NO" 							=> $address->getTelephone(),
			"PYMT_IND"							=> $ind,
			"PYMT_CRITERIA"						=> $crt,
			"SHOPPER_IP"                      	=> Mage::helper('core/http')->getRemoteAddr(),
			"CARDNO"                          	=> $card_num,
            "CARDNAME"                        	=> $card_name,
            "CARDTYPE"                        	=> $card_types,
            "EXPIRYMONTH"                     	=> $exp_month,
            "EXPIRYYEAR"                      	=> $exp_year,
            "CARDCVC"                         	=> $card_cid,
            "CARD_ISSUER_BANK_COUNTRY_CODE"   	=> '',

		);
		//Mage::helper('bcacc')->dump($post); exit;
        return $post;
	}

	public function requery($data){

		$pass	= Mage::getStoreConfig('payment/bcacc/password');
		$sigcc	= sha1('##'.strtoupper($data["MERCHANTID"]).'##'.strtoupper($pass).'##'.$data["MERCHANT_TRANID"].'##'.$data["AMOUNT"].'##0##');
		$post = array(
			"TRANSACTIONTYPE"		=> '4',
			"MERCHANTID" 			=> $data["MERCHANTID"],
			"MERCHANT_TRANID"		=> $data["MERCHANT_TRANID"],
			"AMOUNT"				=> $data["AMOUNT"],
			"RESPONSE_TYPE"			=> '3',
			"SIGNATURE"				=> $sigcc
		);
		
		$a	= Mage::helper('bcacc')->inquiry($post);
		
		return $a;
	}
	
	public function requeryVoid($data){
		$pass	= Mage::getStoreConfig('payment/bcacc/password');
		$sigvoid	= sha1('##'.strtoupper($data["MERCHANTID"]).'##'.strtoupper($pass).'##'.$data["MERCHANT_TRANID"].'##'.$data["AMOUNT"].'##'.$data["TRANSACTIONID"].'##');
		$post = array(
			"PAYMENT_METHOD"		=> '1',
			"TRANSACTIONTYPE"		=> '10',
			"MERCHANTID"			=> $data["MERCHANTID"],
			"MERCHANT_TRANID"		=> $data["MERCHANT_TRANID"],
			"TRANSACTIONID"			=> $data["TRANSACTIONID"],
			"AMOUNT"				=> $data["AMOUNT"],
			"RESPONSE_TYPE"			=> '3',
			"SIGNATURE"				=> $sigvoid
		);
		$a	= Mage::helper('bcacc')->void($post);
		
		return $a;
	}

	
}
?>