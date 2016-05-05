<?php
/*
Mygateway Payment Controller
By: Junaid Bhura
www.junaidbhura.com
*/

class Faspaycc_BcaCc_PaymentController extends Mage_Core_Controller_Front_Action {
    // The redirect action is triggered when someone places an order
    protected $_redirectBlockType = 'bcacc/processing';
    protected $_thanksBlockType	= 'bcacc/thanks';

    protected function _expireAjax(){
        if (!$this->getCheckout()->getQuote()->hasItems()) {
            $this->getResponse()->setHeader('HTTP/1.1','403 Session Expired');
            exit;
        }
    }

    public function formAction() {
        $this->loadLayout();
        $this->renderLayout();
    }

    public function getCheckout(){
        return Mage::getSingleton('checkout/session');
    }

    public function redirectAction() {


        $session = $this->getCheckout();
        $session->setFaspayccQuoteId($session->getQuoteId());
        $session->setFaspayccRealOrderId($session->getLastRealOrderId());

        $order = Mage::getModel('sales/order');
        $order->loadByIncrementId($session->getLastRealOrderId());
        //$order->addStatusToHistory(Mage_Sales_Model_Order::STATE_HOLDED, Mage::helper('bcacc')->__('Customer was redirected to Bank URL.'));
        $order->addStatusHistoryComment('Customer was redirected to Bank URL.');
        $order->save();

        $this->getResponse()->setBody(
            $this->getLayout()
                ->createBlock($this->_redirectBlockType)
                ->setOrder($order)
                ->toHtml()
        );

        $session->unsQuoteId();
    }

    public function thanksAction() {

        $data = $this->getRequest()->getParams();
        $status = $this->processValidate(); // process the callback

        if (array_key_exists('validate', $status)) {

            $this->norouteAction();

        }else {

            $response = $this->processResponse();
            $status	= $response['status'];
            $trxid	= $response['order_id'];

            $template = "bcacc/thanks";
            //Get current layout state
            $this->loadLayout();

            $block = $this->getLayout()->createBlock($template)->setData($response);
            //$block->setData($response);
            $this->getLayout()->getBlock('root')->setTemplate('page/1column.phtml');
            $this->getLayout()->getBlock('content')->append($block);
            $this->_initLayoutMessages('core/session');
            $this->renderLayout();
        }

    }

    public function callbackAction() {

        $data = $this->getRequest()->getParams();

        //Mage::log($data);
        $order		= Mage::getModel('sales/order')->loadByIncrementId($data["MERCHANT_TRANID"]);

        $status = $this->processValidate();

        if (array_key_exists('validate', $status)) {

            $msg	= $status['validate_message'];
            $order_status = Mage::helper('bcacc')->__($msg);
            $order->addStatusToHistory(Mage_Sales_Model_Order::STATE_PENDING_PAYMENT, $order_status);
            Mage::helper('bcacc')->resp_faspay("faspay_error",$data,$msg);
            $order->save();

        } else {


            $response = $this->processResponse();

            if ($response['status']=='SUCCESS') {


                $session = $this->getCheckout();
                $session->unsFaspayccRealOrderId();
                $session->getQuote()->setIsActive(false)->save();

                $invoice = $order->prepareInvoice()
                    ->setTransactionId($order->getId())
                    ->addComment('Payment successfully processed by faspay.')
                    ->register()
                    ->pay();

                $transaction_save = Mage::getModel('core/resource_transaction')
                    ->addObject($invoice)
                    ->addObject($invoice->getOrder());

                $transaction_save->save();
                $invoice->sendEmail();

                $order_status = Mage::helper('bcacc')->__('Faspay has processed the payment.');
                $order->addStatusToHistory(Mage_Sales_Model_Order::STATE_PROCESSING, $order_status,true);
                $order->save();

            }elseif ($response['status']=='FAILED') {

                $session = $this->getCheckout();
                $session->unsFaspayccRealOrderId();
                $session->getQuote()->setIsActive(false)->save();


            }elseif ($response['status']=='PENDING') {

                $session = $this->getCheckout();
                $session->unsFaspayccRealOrderId();
                $session->getQuote()->setIsActive(false)->save();
                $order_status = Mage::helper('bcacc')->__('Payment Pending.');
                $order->addStatusToHistory(Mage_Sales_Model_Order::STATE_PENDING_PAYMENT, $order_status);
                $order->save();

            }



        }

    }
    protected function processValidate(){

        $data = $this->getRequest()->getParams();

        $pass		= Mage::getStoreConfig('payment/bcacc/password');
        $sigres		= strtoupper(sha1(strtoupper('##'.$data['MERCHANTID'].'##'.$pass.'##'.$data["MERCHANT_TRANID"].'##'.$data["AMOUNT"].'##'.$data["TXN_STATUS"].'##')));

        $status = array();

        if (!isset($data['MERCHANTID']) || !isset($data['MERCHANT_TRANID']) || !isset($data['AMOUNT']) || !isset($data['TXN_STATUS']) ) {

            $status['validate']='failed';
            $status['validate_message']='Emty MID/TRANID/AMOUNT/STATUS';
             var_dump($status);die();
        }

        if (!($data['MERCHANTID']) || !($data['MERCHANT_TRANID']) || !($data['AMOUNT']) || !($data['TXN_STATUS']) ) {

            $status['validate']='failed';
            $status['validate_message']='Not set MID/TRANID/AMOUNT/STATUS';

        }

        $order		= Mage::getModel('sales/order')->loadByIncrementId($data["MERCHANT_TRANID"]);
        if(!$order->getId()){

            $status['validate']='failed';
            $status['validate_message']='Magento Transaction ID Empty';

        }

        /*if ($this->getCheckout()->getFaspayccRealOrderId() != $data['MERCHANT_TRANID']) {

            $status['validate']='failed';
            $status['validate_message']='Magento Transaction ID Not Match';

        }*/
        if ( empty($data['SIGNATURE']) ) {

            $status['validate']='failed';
            $status['validate_message']='Signature is Emty';

        }

        if( $data['SIGNATURE']!=$sigres ) {

            $status['validate']='failed';
            $status['validate_message']='Signature not match';


        }

       

        return $status;


    }

    public function processResponse() {

        $data = $this->getRequest()->getParams();
        $order		= Mage::getModel('sales/order')->loadByIncrementId($data["MERCHANT_TRANID"]);
        $void		= Mage::getStoreConfig('payment/bcacc/autovoid');

        if( $data["TXN_STATUS"]=="F" ) {

            Mage::helper('bcacc')->resp_faspay("faspay_report",$data,"pending");
            goto fail;

        }elseif ( $data["TXN_STATUS"]=="N" ) {

            $payInst	= $order->getPayment()->getMethodInstance();
            $a 			= $payInst->requery($data);

            if ($a["TXN_STATUS"] =="RC") {
                goto fail;
            }else
                if ($a["TXN_STATUS"] == "A") {

                    Mage::helper('bcacc')->resp_faspay("faspay_report",$a,"pending");
                    goto pending;

                }
                elseif ($data["TXN_STATUS"]=="V") {


                    Mage::helper('bcacc')->resp_faspay("faspay_void",$data,"canceled");
                    goto fail;

                }


        }elseif( $void == "1" && ( $data["TXN_STATUS"]=="C" || $data["TXN_STATUS"]=="S" ) && strtoupper($data["EXCEED_HIGH_RISK"])=="YES" ) {

            $payInst	= $order->getPayment()->getMethodInstance();
            $a 			= $payInst->requeryVoid($data);
            Mage::helper('bcacc')->resp_faspay("faspay_log",$a);

            if($a["TXN_STATUS"] == "V") {
                Mage::helper('bcacc')->resp_faspay("faspay_void",$a);
                goto fail;
            } else {
                goto success;
            }
        }elseif ( $data["TXN_STATUS"]=="A" && strtoupper($data["EXCEED_HIGH_RISK"])=="NO" ) {



            $payInst	= $order->getPayment()->getMethodInstance();
            $a 			= $payInst->requery($data);

            if($a["TXN_STATUS"] == "A" || $a["TXN_STATUS"] == "CRC" || $a["TXN_STATUS"] == "CF")  {

                Mage::helper('bcacc')->resp_faspay("faspay_report",$a,"pending");
                goto pending;
            }elseif ($a["TXN_STATUS"] == "C") {
                goto success;
            }

        }elseif ( $data["TXN_STATUS"]=="A" && strtoupper($data["EXCEED_HIGH_RISK"])=="YES" ) {

            $payInst	= $order->getPayment()->getMethodInstance();
            $a 			= $payInst->requery($data);

            if($a["TXN_STATUS"] == "A") {

                //Mage::helper('bcacc')->resp_faspay("faspay_report",$a,"pending");
                $order_status= "Need manual Payment Review";
                $order->addStatusToHistory(Mage_Sales_Model_Order::STATE_PAYMENT_REVIEW, $order_status);
                $order->save();
                goto pending;
            }elseif ($a["TXN_STATUS"] == "C") {
                if ($void == "1") {

                    $payInst	= $order->getPayment()->getMethodInstance();
                    $a 			= $payInst->requeryVoid($data);
                    Mage::helper('bcacc')->resp_faspay("faspay_log",$a);

                    if($a["TXN_STATUS"] == "V") {

                        $order->loadByIncrementId($a["MERCHANT_TRANID"]);

                        Mage::helper('bcacc')->resp_faspay("faspay_void",$a);
                        $order_status = Mage::helper('bcacc')->__('Payment is Auto Void');
                        $order->addStatusToHistory(Mage_Sales_Model_Order::STATE_CANCELED, $order_status);
                        $order->cancel();
                        goto fail;
                    } else {
                        goto success;
                    }

                } else {
                    goto success;
                }
            }


        }elseif ( $data["TXN_STATUS"]=="C" || $data["TXN_STATUS"]=="S" ) {
            goto success;
        }

        fail :
        $response=array();
        $response['status']="FAILED";
        $response['order_id']=$data["MERCHANT_TRANID"];
        return $response;

        pending:
        $response=array();
        $response['status']="PENDING";
        $response['order_id']=$data["MERCHANT_TRANID"];
        return $response;

        success :
        $response=array();
        $response['status']="SUCCESS";
        $response['order_id']=$data["MERCHANT_TRANID"];
        return $response;


    }

    public function responseAction() {
        $orderId = Mage::getSingleton('checkout/session')->getLastRealOrderId();
        $order = Mage::getModel('sales/order');
        $order->loadByIncrementId($orderId);
        $order->setState(Mage_Sales_Model_Order::STATE_PROCESSING, true, 'Faspay has processed the payment.');

        $order->sendNewOrderEmail();
        $order->setEmailSent(true);

        $order->save();

        Mage::getSingleton('checkout/session')->unsQuoteId();

        Mage_Core_Controller_Varien_Action::_redirect('checkout/onepage/success', array('_secure'=>true));
    }
    // The cancel action is triggered when an order is to be cancelled
    public function cancelAction() {
        if (Mage::getSingleton('checkout/session')->getLastRealOrderId()) {
            $order = Mage::getModel('sales/order')->loadByIncrementId(Mage::getSingleton('checkout/session')->getLastRealOrderId());
            if($order->getId()) {
                // Flag the order as 'cancelled' and save it
                $order->cancel()->setState(Mage_Sales_Model_Order::STATE_CANCELED, true, 'Gateway has declined the payment.')->save();
            }
        }
    }
}