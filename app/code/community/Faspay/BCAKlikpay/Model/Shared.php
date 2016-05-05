<?php
/**
 * Magento
 *
 * @author    Faspay http://faspay.mediaindonusa.com <cs@mediaindonusa.com>
 * @copyright Copyright (C) 2013 MediaIndonusa. (http://faspay.mediaindonusa.com)
 *
**/

class Faspay_BCAKlikpay_Model_Shared extends Mage_Payment_Model_Method_Abstract
{
	/**
	* payment id assigned by Faspay
	*
	* @var string [a-z0-9_]
	**/
	protected $_code = 'BCAKlikpay_shared';

	protected $_formBlockType = 'BCAKlikpay/form';
	protected $_infoBlockType = 'BCAKlikpay/info';

	protected $_isGateway				= true;
	protected $_canAuthorize			= false;
	protected $_canCapture				= true;
	protected $_canCapturePartial		= false;
	protected $_canRefund				= false;
	protected $_canVoid					= false;
	protected $_canUseInternal			= false;
	protected $_canUseCheckout			= true;
	protected $_canUseForMultishipping	= true;

	protected $_paymentMethod			= 'shared';
	protected $_defaultLocale			= 'en';
	protected $_supportedLocales		= array('en');

	protected $_order;
	private $channel_uid				= "";
	private $signature					= "";

	public function getOrder(){
		if (!$this->_order) {
			$paymentInfo = $this->getInfoInstance();
			$this->_order = Mage::getModel('sales/order')
							->loadByIncrementId($paymentInfo->getOrder()->getRealOrderId());
		}
		return $this->_order;
	}

	public function getOrderPlaceRedirectUrl(){
		  return Mage::getUrl('BCAKlikpay/payment/redirect');
	}

	public function cancel(Varien_Object $payment){
		$payment->setStatus(self::STATUS_DECLINED)
			->setLastTransId($this->getTransactionId());

		return $this;
	}
	
	public function capture(Varien_Object $payment, $amount){
		$payment->setStatus(self::STATUS_APPROVED)
			->setLastTransId($this->getTransactionId());

		return $this;
	}
	
	public function getRedirectBlockType(){
		return $this->_redirectBlockType;
	}

	public function getPaymentMethodType(){
		return $this->_paymentMethod;
	}

	public function getUrl($paychannel,$method="postdata",$data=null){
		$server		= Mage::getStoreConfig('payment/'.$paychannel.'/server_stage');
		$rd			= "";
		
		if($method=="postdata"){
			$rd = $server == 1 ?
				"http://dev.faspay.co.id/pws/300002/183xx00010100000" :
				"https://web.faspay.co.id/pws/300002/383xx00010100000";
		}elseif($method=="redirect"){	
			if($paychannel=="BCAKlikpay"){
				$serverbca	= Mage::getStoreConfig('payment/BCAKlikpay/server_stage_bca');
				
				$rdbca	= $serverbca == 1 ?
					"https://202.6.215.230:8081/purchasing/purchase.do?action=loginRequest" :
					"http://dev.faspay.co.id/redirectbca";
				
				$rd	= $server == 1 ? 
					$rdbca :
					'https://klikpay.klikbca.com/purchasing/purchase.do?action=loginRequest';
			}elseif($paychannel=="faspay_permatanet"){
				$rd = $server == 1 ?
				"http://dev.faspay.co.id/permatanet/payment" :
				"https://web.faspay.co.id/permatanet/payment";
			}
			else{
				$sg = $this->signature;
				$qs = 'trx_id='.$data['trx_id'].'&merchant_id='.$data['merchant_id'].'&bill_no='.$data['bill_no'];
				$rd = $server == 1 ?
					"http://dev.faspay.co.id/pws/100003/0830000010100000/$sg?$qs" :
					"https://web.faspay.co.id/pws/100003/2830000010100000/$sg?$qs";
			}
		}elseif($method=="inquirystatus"){
			$rd = $server == 1 ?
				"http://dev.faspay.co.id/pws/100004/183xx00010100000" : 
				"https://web.faspay.co.id/pws/100004/383xx00010100000";
		}
		return $rd;
	}
	
	public function getLocale(){
		$locale = explode('_', Mage::app()->getLocale()->getLocaleCode());
		if (is_array($locale) && !empty($locale) && in_array($locale[0], $this->_supportedLocales))
			return $locale[0];
		else
			return $this->getDefaultLocale();
	}

	public function getFormFields(){
		$response	= Mage::helper("BCAKlikpay")->_xml2array($this->post_data());
		$status		= Mage::getStoreConfig('payment/BCAKlikpay/order_status');
		$post		= array_merge(array('signature'=>$this->signature,'payment_channel'=>$this->channel_uid,'status'=>$status),$response);
		//Mage::helper("faspay")->dump($post,true);
		
		Mage::helper('BCAKlikpay')->resp_faspay('post_data', $post);
		
		$paychannel	= Mage::helper('BCAKlikpay')->getPaymentChannel();
		if($paychannel=="BCAKlikpay"){
			$post	= $this->bcaklikpay_postdata($response);
		}elseif($paychannel=="faspay_permatanet"){
			$post 	= $this->permatanet_postdata($response);
		}
		
        return $post;
	}
	
	private function post_data(){
		$lastOrderId = Mage::getSingleton('checkout/session')->getLastRealOrderId();
		
		$merchantid	= Mage::getStoreConfig('Service/settings/faspay_id');
		$merchantus	= Mage::getStoreConfig('Service/settings/merchant_id');
		$merchant	= Mage::getStoreConfig('Service/settings/merchant_name');
		$password	= Mage::getStoreConfig('Service/settings/merchant_password');
		$customer	= Mage::getSingleton('customer/session')->getCustomer();
		$order		= Mage::getModel('sales/order')->loadByIncrementID($lastOrderId);
		$items		= $order->getAllVisibleItems();
		$tenorSesion= Mage::getSingleton('core/session')->getMyArray();
		
		$_totalData	= $order->getData();
		$billing	= $order->getBillingAddress();
		$shipping	= $order->getShippingAddress();
		
		$paychannel	= Mage::helper('BCAKlikpay')->getPaymentChannel();
		$channel	= Mage::getStoreConfig('payment/'.$paychannel.'/title');
		$chan_uid	= 405;
		
		$url		= $this->getUrl($paychannel,"postdata");
		$this->channel_uid	= $chan_uid;
		
		$amount		= $_totalData['base_grand_total']*100;
		$ship		= $order->getShippingAmount()*100;
		$billgross	= ($_totalData['base_subtotal']*100) + ($_totalData['discount_amount']*100);	
		$expired	= date("Y-m-d H:i:s", strtotime(date("Y-m-d H:i:s", strtotime($_totalData["updated_at"])) . " +".Mage::getStoreConfig('payment/'.$paychannel.'/order_expire')." hour"));	
		
		
		
		if( Mage::getStoreConfig('payment/'.$paychannel.'/active') !== "1" ){
			echo "Mohon Aktifkan Payment Channel Faspay ".ucfirst($channel); exit;
		}
		
		$str 			= "$merchantus$password$lastOrderId";
		$signature 		= sha1(md5($str));
		$this->signature	= $signature;
		
		$xml  = '<?xml version="1.0" encoding="utf-8"?>' . "\n";
		$xml .= "<faspay>" . "\n";
		$xml .= "<request>Post Data Transaksi</request>" . "\n";
		$xml .= "<merchant_id>".$merchantid."</merchant_id>" . "\n";
		$xml .= "<merchant>".$merchant."</merchant>" . "\n";
		$xml .= "<bill_no>".$lastOrderId."</bill_no>" . "\n";
		$xml .= "<bill_reff>".$lastOrderId."</bill_reff>" . "\n";
		$xml .= "<bill_date>".$order['created_at']."</bill_date>" . "\n";
		$xml .= "<bill_expired>".$expired."</bill_expired>" . "\n";
		$xml .= "<bill_desc>Pembelian Barang</bill_desc>" . "\n";
		$xml .= "<bill_currency>IDR</bill_currency>" . "\n";
		$xml .= "<bill_gross>".$billgross."</bill_gross>" . "\n";	
		$xml .= "<bill_tax>0</bill_tax>" . "\n";
		$xml .= "<bill_miscfee>".$ship."</bill_miscfee>" . "\n";
		$xml .= "<bill_total>".$amount."</bill_total>" . "\n";
		$xml .= "<cust_no>".$customer->getId()."</cust_no>" . "\n";
		$xml .= "<cust_name>".$customer->getName()."</cust_name>" . "\n";
		$xml .= "<payment_channel>".$chan_uid."</payment_channel>" . "\n";
		$xml .= "<bank_userid></bank_userid>" . "\n";
		$xml .= "<msisdn></msisdn>" . "\n";
		$xml .= "<email>".$customer->getEmail()."</email>" . "\n";
		$xml .= "<terminal>10</terminal>" . "\n";
		$xml .= "<billing_address>".$billing->getStreetFull()."</billing_address>" . "\n";
		$xml .= "<billing_address_city>".$billing->getcity()."</billing_address_city>"."\n";
		$xml .= "<billing_address_region>".$billing->getregion()."</billing_address_region>"."\n";  
		$xml .= "<billing_address_state>Indonesia</billing_address_state>"."\n";  
		$xml .= "<billing_address_poscode>".$billing->getpostcode()."</billing_address_poscode>"."\n";
		$xml .= "<billing_address_country_code>".$billing->getcountry_id()."</billing_address_country_code>"."\n";  
		$xml .= "<receiver_name_for_shipping>".$shipping->getfirstname()."</receiver_name_for_shipping>"."\n";  
		$xml .= "<shipping_address>".$shipping->getStreetFull()."</shipping_address>"."\n";
		$xml .= "<shipping_address_city>".$shipping->getcity()."</shipping_address_city>"."\n";
		$xml .= "<shipping_address_region>".$shipping->getregion()."</shipping_address_region>"."\n";
		$xml .= "<shipping_address_state>Indonesia</shipping_address_state>"."\n";
		$xml .= "<shipping_address_poscode>".$shipping->getpostcode()."</shipping_address_poscode>"."\n";
		
		$statusPayType	= 1;
		$indexStatus	= 1;
		$index			= 0;
		$last			= 0;
		$countercicilan	= 0;
		
		Mage::getSingleton('core/session')->setData("shipping_amount", $ship);
		//Mage::helper('faspay')->dump($tenorSesion,true);
		//Mage::helper('faspay')->dump($xml,true);
		
		if ($chan_uid == 405){
			foreach($items as $item) {
				if($index == 0){
					if($tenorSesion['payment_tenor_'.$index]== '00'){
						$statusPayType = 1;
						$last = 1;
					}else{
						$statusPayType = 2;
						$last = 2;
						$countercicilan++;
					}
				}else{
					if($tenorSesion['payment_tenor_'.$index]== '00'){
						$statusPayType = 1;
					}else{
						$statusPayType = 2;
						$countercicilan++;
					}
				}
				if($last != $statusPayType){
					$last = 3;
				}
				$index++;
			}
			if($last == 1){
				$xml .= "<pay_type>01</pay_type>" . "\n";
				Mage::getSingleton('core/session')->setData("pay_type", '01');
			}else if($last == 2 ){
				$xml .= "<pay_type>02</pay_type>" . "\n";
				Mage::getSingleton('core/session')->setData("pay_type", '02');
			}else{
				$mixed	= Mage::getStoreConfig('payment/BCAKlikpay/order_mixed');
				
				if($mixed==1){
					$xml .= "<pay_type>03</pay_type>" . "\n";
					Mage::getSingleton('core/session')->setData("pay_type", '03');
				}else{
					$base_url	= Mage::getBaseUrl();
					echo "<script language=\"Javascript\">\n";
					echo "window.alert('Pembelian Tidak Bisa Dilakukan Dengan Sebagian Cicilan dan Sebagian Full Payment');";
					echo "<script language='javascript'>window.location ='".$base_url."checkout/cart/'</script>";
					echo "</script>";
					exit;
				}
			}
		}else{
			$xml .= "<pay_type>01</pay_type>" . "\n";
			Mage::getSingleton('core/session')->setData("pay_type", 01);
		}
		
		$index = 0;
		$a3bulan	= Mage::getStoreConfig('payment/BCAKlikpay/installment_3_month');
		$a6bulan	= Mage::getStoreConfig('payment/BCAKlikpay/installment_6_month');
		$a12bulan	= Mage::getStoreConfig('payment/BCAKlikpay/installment_12_month');
		$a24bulan	= Mage::getStoreConfig('payment/BCAKlikpay/installment_24_month');
		
		$m3bulan	= Mage::getStoreConfig('payment/BCAKlikpay/min_price_3_month');
		$m6bulan	= Mage::getStoreConfig('payment/BCAKlikpay/min_price_6_month');
		$m12bulan	= Mage::getStoreConfig('payment/BCAKlikpay/min_price_12_month');
		$m24bulan	= Mage::getStoreConfig('payment/BCAKlikpay/min_price_24_month');
		
		foreach ($items as $item) {
			$qty	= $item->getQtyToInvoice();
			$prices	= ($item->getPrice()*100)-($item->getDiscount_amount()*100);
			$price	= $prices * $qty;
			
			$xml .= "<item>" . "\n";
			$xml .= "<product>".str_replace("&","-", $item->getName())."</product>" . "\n";
			$xml .= "<qty>".$item->getQtyToInvoice()."</qty>" . "\n";
			$xml .= "<amount>".$price."</amount>" . "\n";
			
			if($chan_uid == 405){
				$tenorSesion = Mage::getSingleton('core/session')->getMyArray();
				//Mage::helper("faspay")->dump($tenorSesion,true);
				
				if($tenorSesion['payment_tenor_'.$index] == '03' && $a3bulan==1 && $m3bulan < $price){
					echo "<script language=\"Javascript\">\n";
					echo "window.alert('Nominal Pembelian Item Dengan Cicilan 3 Bulan Minimal Rp '". number_format($m3bulan,'2',',','.') .");";
					echo "<script language='javascript'>window.location ='".$base_url."checkout/cart/'</script>";
					echo "</script>";
				}elseif($tenorSesion['payment_tenor_'.$index] == '06' && $a6bulan==1 && $m6bulan < $price){
					echo "<script language=\"Javascript\">\n";
					echo "window.alert('Nominal Pembelian Item Dengan Cicilan 6 Bulan Minimal Rp '". number_format($m6bulan,'2',',','.') .");";
					echo "<script language='javascript'>window.location ='".$base_url."checkout/cart/'</script>";
					echo "</script>";
				}elseif($tenorSesion['payment_tenor_'.$index] == '12' && $a12bulan==1 && $m12bulan < $price){
					echo "<script language=\"Javascript\">\n";
					echo "window.alert('Nominal Pembelian Item Dengan Cicilan 12 Bulan Minimal Rp '". number_format($m12bulan,'2',',','.') .");";
					echo "<script language='javascript'>window.location ='".$base_url."checkout/cart/'</script>";
					echo "</script>";
				}elseif($tenorSesion['payment_tenor_'.$index] == '24' && $a24bulan==1 && $m24bulan < $price){
					echo "<script language=\"Javascript\">\n";
					echo "window.alert('Nominal Pembelian Item Dengan Cicilan 24 Bulan Minimal Rp '". number_format($m24bulan,'2',',','.') .");";
					echo "<script language='javascript'>window.location ='".$base_url."checkout/cart/'</script>";
					echo "</script>";
				}
				
				$test = "'payment_tenor_".$index."'";
				if($tenorSesion['payment_tenor_'.$index]== '00'){
					$xml .= "<payment_plan>01</payment_plan>" . "\n";
					$xml .= "<tenor>00</tenor>" . "\n";
					$xml .= "<merchant_id>".Mage::getStoreConfig('payment/BCAKlikpay/mid_fullpayment')."</merchant_id>" . "\n";
				}else if($tenorSesion['payment_tenor_'.$index] == '03' && $a3bulan==1){
					$xml .= "<payment_plan>02</payment_plan>" . "\n";
					$xml .= "<tenor>03</tenor>" . "\n";
					$xml .= "<merchant_id>".Mage::getStoreConfig('payment/BCAKlikpay/mid_3_month')."</merchant_id>" . "\n";
				}else if($tenorSesion['payment_tenor_'.$index] == '06' && $a6bulan==1){
					$xml .= "<payment_plan>02</payment_plan>" . "\n";
					$xml .= "<tenor>06</tenor>" . "\n";
					$xml .= "<merchant_id>".Mage::getStoreConfig('payment/BCAKlikpay/mid_6_month')."</merchant_id>" . "\n";
				}else if($tenorSesion['payment_tenor_'.$index] == '12' && $a12bulan==1){
					$xml .= "<payment_plan>02</payment_plan>" . "\n";
					$xml .= "<tenor>12</tenor>" . "\n";
					$xml .= "<merchant_id>".Mage::getStoreConfig('payment/BCAKlikpay/mid_12_month')."</merchant_id>" . "\n";
				}else if($tenorSesion['payment_tenor_'.$index] == '24' && $a24bulan==1){
					$xml .= "<payment_plan>02</payment_plan>" . "\n";
					$xml .= "<tenor>24</tenor>" . "\n";
					$xml .= "<merchant_id>".Mage::getStoreConfig('payment/BCAKlikpay/mid_24_month')."</merchant_id>" . "\n";
				}else{
					$xml .= "<merchant_id>01</merchant_id>" . "\n";
					$xml .= "<payment_plan>01</payment_plan>" . "\n";
				}
			}else{
				$xml .= "<merchant_id>-</merchant_id>" . "\n";
				$xml .= "<tenor>00</tenor>" . "\n";
				$xml .= "<payment_plan>01</payment_plan>" . "\n";
			}
			$xml .= "</item>" . "\n";
			$index++;
		}
		$xml .= "<reserve1></reserve1>" . "\n";
		$xml .= "<reserve2></reserve2>" . "\n";
		$xml .= "<signature>".$signature."</signature>" . "\n";
		$xml .= "</faspay>" . "\n";
		
		if($countercicilan > 5) {
			echo "<script language=\"Javascript\">\n";
			echo "window.alert('Pembelian dengan Cicilan Tidak Bisa Lebih dari 5 Jenis Barang');";
			echo "</script>";
			exit;
		}
		
		$response	= $this->_prep_data($xml,$url);
		//Mage::helper("BCAKlikpay")->dump($response,true);
		
		return $response;
	}
	
	private function bcaklikpay_postdata($rsp){
		$order			= Mage::getModel('sales/order')->loadByIncrementId($rsp["bill_no"]);
		$_totalData		= $order->getData();		
		$amount			= number_format($_totalData['base_subtotal'],2,'.','');
		$lastOrderDate	= $_totalData['created_at'];
		$paytype		= Mage::getSingleton('core/session')->getData("pay_type");
		$shippingamount	= Mage::getSingleton('core/session')->getData("shipping_amount");
		
		$dat = array(
			'klikPayCode'		=> Mage::getStoreConfig('payment/BCAKlikpay/klikpaycode'),
			'transactionNo'		=> $rsp['trx_id'],
			'totalAmount'		=> $amount,
			'currency'			=> 'IDR',
			'payType'			=> $paytype,
			'callback'			=> Mage::getBaseUrl()."BCAKlikpay/payment/thanks?trx_id=$rsp[trx_id]&bill_no=$rsp[bill_no]",
			'transactionDate'	=> date('d/m/Y H:i:s',strtotime($lastOrderDate)),
			'descp'				=> 'Pembelian Barang di(' .Mage::getStoreConfig('payment/BCAKlikpay/merchant_name'). ')',
			'miscFee'			=> number_format(($shippingamount/100),2,'.',''),
			'signature'			=> Mage::helper('BCAKlikpay')->_bcakp_signature_ori($rsp['trx_id'])
		);
		
		return $dat;
	}
	
	public function check_bcakpsignature($trx_id){
		$sql = "select a.subtotal, a.created_at from sales_flat_order a, order_payment_faspay b where a.increment_id = b.order_id and b.trx_id = '$trx_id'";
		$connection = Mage::getSingleton('core/resource')->getConnection('core_write');
		$rsp = $connection->fetchAll($sql);	

		return $rsp;
	}
	
	public function inquiry_status($data){
		//Mage::helper('faspay')->dump($data,true);
		$trx_id		= $data['trx_id'];
		$bill_no	= $data['bill_no'];
		$channel	= Mage::helper("faspay")->getOrderInfobyId($trx_id);
		$merchantID	= Mage::getStoreConfig('faspay/settings/faspay_id');
		$server		= $this->getUrl("faspay_".$channel["payment_channel"], "inquirystatus");
		
		//Mage::helper('faspay')->dump($server,true);
		
		$signature	= sha1(md5(Mage::getStoreConfig('faspay/settings/merchant_id').Mage::getStoreConfig('faspay/settings/merchant_password').$bill_no));
		
		$xml .= "<faspay>" . "\n";
		$xml .= "<request>Inquiry Status Payment</request>" . "\n";
		$xml .= "<trx_id>$trx_id</trx_id>" . "\n";
		$xml .= "<merchant_id>$merchantID</merchant_id>" . "\n";
		$xml .= "<bill_no>$bill_no</bill_no>" . "\n";
		$xml .= "<signature>$signature</signature>" . "\n";
		$xml .= "</faspay>" . "\n";
		
		$proses	= $this->_prep_data($xml,$server);
		
		$p = xml_parser_create();
		xml_parse_into_struct($p, $proses, $vals, $index);
		xml_parser_free($p);

		for($i=0;$i<count($vals)&&$i<=20;$i++){
			if($i % 2 == 1){
				$a = $vals[15]['value'];		
			}
		}
		
		return $a;
	}
	
	private function _prep_data($data,$url){
		
		$c = curl_init ($url);
		curl_setopt ($c, CURLOPT_POST, true);
		curl_setopt ($c, CURLOPT_HTTPHEADER, array('Content-Type: text/xml'));
		curl_setopt ($c, CURLOPT_POSTFIELDS, $data);
		curl_setopt ($c, CURLOPT_RETURNTRANSFER, true);
		curl_setopt ($c, CURLOPT_SSL_VERIFYPEER, false);
		$response = curl_exec ($c);
		curl_close ($c);
		
		return $response;
	}
		
	public function faspay_signature($source) {
		return base64_encode(Mage::helper("BCAKlikpay")->hex2bin(sha1($source)));
	}
}
?>