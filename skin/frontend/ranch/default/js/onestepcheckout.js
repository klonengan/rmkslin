jQuery(document).ready(function(){

	if(initBillingForm == 1){
		initAddressForm(true); // Billing Form
	}
	initAddressForm(false); // Init Shipping Address Form

	customerInformationSection();
	paymentMethodSection();
	shippingInformationSection();
	shippingMethodSection();
	orderReviewSection();
});

/* Customer Information */
function customerInformationSection(){
	var defSelectOption = '<option value="">---</option>';

	// Event focusout for street input field
	jQuery('input.billing-streets').each(function(){
		var idx =  jQuery("input.billing-streets").index(this);
		jQuery(this).on("focusout", function(){
			if(jQuery('input[name="billing[ship_to_this_address]"]').prop("checked") == true){
				jQuery("input#shipping\\:street" + (idx+1)).val(jQuery(this).val());
			}
		});
	});
	// Event change for province combo box field
	jQuery("#billing\\:region").on("change", function(){
		if(jQuery('input[name="billing[ship_to_this_address]"]').prop("checked") == true){
			jQuery("#shipping\\:region").html(jQuery(this).html()).val(jQuery(this).val());
			jQuery("#shipping\\:province_code").val(jQuery("#billing\\:region option:selected").attr("data-code"));
			jQuery("#shipping\\:city").html(defSelectOption).val("");
			jQuery("#shipping\\:regency_code").val("");
			jQuery("#shipping\\:subdistrict").html(defSelectOption).val("");
			jQuery("#shipping\\:subdistrict_code").val("");
			jQuery('input[name="shipping[postcode]"]').val("");
		}
	});
	// Event change for province combo box field
	jQuery("#billing\\:city").on("change", function(){
		if(jQuery('input[name="billing[ship_to_this_address]"]').prop("checked") == true){
			jQuery("#shipping\\:city").html(jQuery(this).html()).val(jQuery(this).val());
			jQuery("#shipping\\:regency_code").val(jQuery("#billing\\:city option:selected").attr("data-code"));
			jQuery("#shipping\\:subdistrict").html(defSelectOption).val("");
			jQuery("#shipping\\:subdistrict_code").val("");
			jQuery('input[name="shipping[postcode]"]').val("");
		}
	});
	// Event change for province combo box field
	jQuery("#billing\\:subdistrict").on("change", function(){
		if(jQuery('input[name="billing[ship_to_this_address]"]').prop("checked") == true){
			jQuery("#shipping\\:subdistrict").html(jQuery(this).html()).val(jQuery(this).val());
			jQuery("#shipping\\:subdistrict_code").val(jQuery("#billing\\:subdistrict option:selected").attr("data-code"));
			jQuery('input[name="shipping[postcode]"]').val("");
		}
	});
	// Event focusout for postcode input field
	jQuery('input[name="billing[postcode]"]').on("focusout", function(){
		if(jQuery('input[name="billing[ship_to_this_address]"]').prop("checked") == true){
			jQuery('input[name="shipping[postcode]"]').val(jQuery('input[name="billing[postcode]"]').val());
		}
	});
	// Event click for suggestion input field
	jQuery("#billing\\:postcodeBox .suggestion-postcode").on("click", "a", function(){
		if(jQuery('input[name="billing[ship_to_this_address]"]').prop("checked") == true){
			jQuery('input[name="shipping[telephone]"]').val(jQuery('input[name="billing[telephone]"]').val());
		}
	});
	// Event focusout for telephone input field
	jQuery('input[name="billing[telephone]"]').on("focusout", function(){
		if(jQuery('input[name="billing[ship_to_this_address]"]').prop("checked") == true){
			jQuery('input[name="shipping[telephone]"]').val(jQuery('input[name="billing[telephone]"]').val());
		}
	});
	// Event focusout for fax input field
	jQuery('input[name="billing[fax]"]').on("focusout", function(){
		if(jQuery('input[name="billing[ship_to_this_address]"]').prop("checked") == true){
			jQuery('input[name="shipping[fax]"]').val(jQuery('input[name="billing[fax]"]').val());
		}
	});
	// Event focusout for mobile_phone input field
	jQuery('input[name="billing[mobile_phone]"]').on("focusout", function(){
		if(jQuery('input[name="billing[ship_to_this_address]"]').prop("checked") == true){
			jQuery('input[name="shipping[mobile_phone]"]').val(jQuery('input[name="billing[mobile_phone]"]').val());
		}
	});
	// Event click for "Ship to this address" checkbox field
	jQuery('input[name="billing[ship_to_this_address]"]').on("click", function(){
		_resetShippingMethodOption();
		if(jQuery(this).prop("checked") == true){
			jQuery("#shipping-new-address-form select").addClass("select-disabled").prop("disabled", false);
			jQuery("#shipping-new-address-form input[type=text]").addClass("input-disabled").prop("disabled", false);
			jQuery("#shipping-form-masking").show();
			for(i = 1; i <= 10; i++) {
				if(jQuery("input#billing\\:street"+i).length){
					jQuery("input#shipping\\:street"+i).val(jQuery("input#billing\\:street"+i).val());
				}
				else{
					break;
				}
			};

			jQuery("#shipping\\:prefix").html(jQuery("#billing\\:prefix").html()).val(jQuery("#billing\\:prefix").val());
			jQuery("#shipping\\:firstname").val(jQuery("#billing\\:firstname").val());
			jQuery("#shipping\\:middlename").val(jQuery("#billing\\:middlename").val());
			jQuery("#shipping\\:lastname").val(jQuery("#billing\\:lastname").val());
			jQuery("#shipping\\:suffix").html(jQuery("#billing\\:suffix").html()).val(jQuery("#billing\\:suffix").val());

			jQuery("#shipping\\:region").html(jQuery("#billing\\:region").html()).val(jQuery("#billing\\:region").val());
			jQuery("#shipping\\:province_code").val(jQuery("#billing\\:region option:selected").attr("data-code"));

			jQuery("#shipping\\:city").html(jQuery("#billing\\:city").html()).val(jQuery("#billing\\:city").val());
			jQuery("#shipping\\:regency_code").val(jQuery("#billing\\:city option:selected").attr("data-code"));

			jQuery("#shipping\\:subdistrict").html(jQuery("#billing\\:subdistrict").html()).val(jQuery("#billing\\:subdistrict").val());
			jQuery("#shipping\\:subdistrict_code").val(jQuery("#billing\\:subdistrict option:selected").attr("data-code"));

			jQuery('input[name="shipping[postcode]"]').val(jQuery('input[name="billing[postcode]"]').val());

			jQuery('input[name="shipping[telephone]"]').val(jQuery('input[name="billing[telephone]"]').val());
			jQuery('input[name="shipping[fax]"]').val(jQuery('input[name="billing[fax]"]').val());
			jQuery('input[name="shipping[mobile_phone]"]').val(jQuery('input[name="billing[mobile_phone]"]').val());
		}
		else{
			jQuery("#shipping-new-address-form select").removeClass("select-disabled");
			jQuery("#shipping-new-address-form input[type=text]").removeClass("input-disabled");
			jQuery("#shipping-form-masking").hide();
			for(i = 1; i <= 10; i++) {
				if(jQuery("input#billing\\:street"+i).length){
					jQuery("input#shipping\\:street"+i).val("");
				}
				else{
					break;
				}
			};
			jQuery("#shipping\\:province_code").val("");
			jQuery("#shipping\\:regency_code").val("");
			jQuery("#shipping\\:subdistrict_code").val("");
			jQuery('input[name="shipping[postcode]"]').val("");
			jQuery('input[name="shipping[telephone]"]').val("");
			jQuery('input[name="shipping[fax]"]').val("");
			jQuery('input[name="shipping[mobile_phone]"]').val("");

			initAddressForm(false);
		}
	});
}

/* Payment Method */
function paymentMethodSection(){
	jQuery("#p_method_banktransfer").prop("checked", true);
	jQuery("#payment_form_banktransfer").show();
	jQuery('input[name="payment[method]"]').on("click", function(){
		var paymentMethod = jQuery(this).val();
		jQuery("#checkout-payment-method-load dd > ul, #checkout-payment-method-load dd > div").hide();
		jQuery("#payment_form_"+paymentMethod).show();
	});
}

/* Shipping Method */
function shippingInformationSection(){
	// Select shipping address
	jQuery('input[name="shipping[shipping_address_id]"]').on("click", function(){
		jQuery('input[name="shipping[is_shipping_insurance]"]').val(0);
		_resetShippingMethodOption();
		if(jQuery(this).val() != ""){
			jQuery("#shipping-new-address-form").hide();
		}
		else{
			jQuery("#shipping-new-address-form").show();
		}
	});

	jQuery('select[name="shipping[region]"]').on("change", function(){
		_resetShippingMethodOption();
	});

	jQuery('select[name="shipping[city]"]').on("change", function(){
		_resetShippingMethodOption();
	});

	jQuery('select[name="shipping[subdistrict]"]').on("change", function(){
		_resetShippingMethodOption();
	});
		
}

/* Shipping Method */
function shippingMethodSection(){
	// _reloadShippingMethod();
	_initShippingMethodOption();
	jQuery("a#reload-shipping-method-button").unbind("click").on("click", function(){
		if(billingForm.validator.validate() && shippingForm.validator.validate()){
			_reloadShippingMethod();
		}
	});
	jQuery("body").on("click", "input[name=shipping_method]", function(){
		_setInsuranceCheckboxState(true);
	});
	_insuranceCheckboxClick();
}

/* Order Review */
function orderReviewSection(){
	jQuery("button#order_submit_button").unbind("click").on("click", function(){
		_placeOrder();
	});
	jQuery("a#reload-order-review-button").unbind("click").on("click", function(){
		_updateOrderReview();
	});
}

function _saveBilling(data){
    jQuery.ajax({
        url: urlSaveBilling,
        type: "POST",
        data: data
    });
}

function _saveShipping(data){
    jQuery.ajax({
        url: urlSaveShipping,
        type: "POST",
        data: data
    });
}

function _setInsuranceCheckboxState(state){
	if(state == true){
		jQuery(".shippingjne-insurance").removeClass("disabled");
		jQuery("#insurance_shippingjne").removeAttr("disabled");
		_insuranceCheckboxClick();
	}
	else{
		jQuery(".shippingjne-insurance").addClass("disabled");
		jQuery("#insurance_shippingjne").attr("disabled", "disabled").prop("checked", false).unbind("click").off("click");
		jQuery('input[name="shipping[is_shipping_insurance]"]').val(0);
	}
}

function _insuranceCheckboxClick(){
	jQuery("body").on("click", "#insurance_shippingjne", function(){
		if(jQuery("input[name=shipping_method]").is(":checked")){
			_resetOrderReview(3);
			if(jQuery(this).prop("checked") == true){
				jQuery('input[name="shipping[is_shipping_insurance]"]').val(1);
			}
			else{
				jQuery('input[name="shipping[is_shipping_insurance]"]').val(0);
			}
		}
		else{
			_resetOrderReview(2);
			jQuery(this).prop("checked", false);
			jQuery('input[name="shipping[is_shipping_insurance]"]').val(0);
		}
	});
	jQuery("body").on("click", "input[name=shipping_method]", function(){
		_resetOrderReview(3);
	});
}

function _reloadShippingMethod(){
	_resetOrderReview(1);
	var billingData = jQuery("form#co-billing-form").serialize();
	var shippingData = jQuery("form#co-shipping-form").serialize();
	formData = billingData+'&'+shippingData;
    jQuery.ajax({
        url: urlUpdateShippingMethod,
        type: "POST",
        data: formData,
        beforeSend: function(){
        	jQuery("#checkout-load-shipping_method").html("").hide();
        	jQuery("#reload-shipping-method").hide();
        	jQuery("#shipping-method-loading").show();
        },
        success:function(response){
        	res = jQuery.parseJSON(response);
        	if(typeof res.error !== 'undefined' && typeof res.message !== 'undefined' && typeof res.message !== ''){
        		_shippingMethodNotFound();
        	}
        	else{
        		jQuery("#checkout-load-shipping_method").html(res.update_step.shipping_method);
        		if(typeof res.shippingmethod !== 'undefined' && res.shippingmethod !== ''){
        			_setInsuranceCheckboxState(true);
        		}
        		else{
        			if(jQuery("input[name=shipping_method]").is(":checked")){
        				_setInsuranceCheckboxState(true);
        			}
        			else{
        				_setInsuranceCheckboxState(false);
        			}
        		}

        	}
        	jQuery("#checkout-load-shipping_method").show();
        	jQuery("#shipping-method-loading").hide();

        	_updateOrderReview();
        	
        }
    });
}

function _resetShippingMethodOption(){
	jQuery("#checkout-load-shipping_method").html("").hide();
	jQuery("#reload-shipping-method").show();
	_resetOrderReview(2);
}

function _initShippingMethodOption(){
	jQuery("#checkout-load-shipping_method").html("").hide();
	jQuery("#reload-shipping-method").hide();
	jQuery("#shipping-method-loading").show();
	_resetOrderReview(1);
}

function _shippingMethodNotFound(){
	jQuery("#reload-shipping-method").show();
	jQuery("#checkout-load-shipping_method").html('<p class="no-shipping">'+noShippingLabel+'</p>');
	_resetOrderReview(2);
}

function _resetOrderReview(type){
	jQuery("#checkout-load-review").html("").hide();

	jQuery("#order_submit_button").unbind("click");
	jQuery("#checkout-review-submit").hide();
	jQuery("#advice-required-entry-aggreements").hide();

	if(type == 1){
		jQuery("#order-review-loading").show();
		jQuery("#order-review-message").hide();
		jQuery("#reload-order-review-button").unbind("click");
		jQuery("#reload-order-review").hide();
	}
	else if(type == 2){
		jQuery("#order-review-loading").hide();
		jQuery("#order-review-message").show();
		jQuery("#reload-order-review-button").unbind("click");
		jQuery("#reload-order-review").hide();
	}
	else if(type == 3){
		jQuery("#order-review-loading").hide();
		jQuery("#order-review-message").hide();
		jQuery("#reload-order-review").show();
		jQuery("#reload-order-review-button").unbind("click").on("click", function(){
			_updateOrderReview();
		});
	}
}

function _unsetOrderReview(html){
	jQuery("#checkout-load-review").html(html).show();

	orderReviewSection();

	jQuery("#checkout-review-submit").show();

	jQuery("#order-review-loading").hide();
	jQuery("#order-review-message").hide();
	jQuery("#reload-order-review").hide();
}

function _updateOrderReview(){
	var billingData = jQuery("form#co-billing-form").serialize();
	var paymentMethodData = jQuery("form#co-payment-form").serialize();
	var shippingData = jQuery("form#co-shipping-form").serialize();
	var shippingMethodData = jQuery("form#co-shipping-method-form").serialize();
	formData = billingData+'&'+paymentMethodData+'&'+shippingData+'&'+shippingMethodData;
	jQuery.ajax({
        url: urlUpdateOrderReview,
        type: "POST",
        data: formData,
        beforeSend: function(){
        	_resetOrderReview(1);
        },
        success:function(response){
        	res = jQuery.parseJSON(response);
        	if(typeof res.error !== 'undefined' && typeof res.message !== 'undefined' && typeof res.message !== ''){
        	}
        	else{
        		if(typeof res.shippingmethod !== 'undefined' && res.shippingmethod !== ''){
        			_unsetOrderReview(res.update_step.review);
        		}
        		else{
        			_resetOrderReview(2);
        		}
        		
        	}
        	
        }
    });
}

function _placeOrder(){
	var billingData = jQuery("form#co-billing-form").serialize();
	var paymentMethodData = jQuery("form#co-payment-form").serialize();
	var shippingData = jQuery("form#co-shipping-form").serialize();
	var shippingMethodData = jQuery("form#co-shipping-method-form").serialize();
	var agreementData = jQuery("form#checkout-agreements").serialize();
	formData = billingData+'&'+paymentMethodData+'&'+shippingData+'&'+shippingMethodData+'&'+agreementData;
	jQuery.ajax({
        url: urlPlaceOrder,
        type: "POST",
        data: formData,
        beforeSend: function(){
        	jQuery("#bg-masking, #bg-loading").show();
        },
        success:function(response){
        	res = jQuery.parseJSON(response);
        	
        	if(typeof res.error !== 'undefined'){
        		if(typeof res.obj !== 'undefined'){
        			if(res.obj === 'aggreement'){
        				jQuery("#advice-required-entry-aggreements").show();
        			}
        		}
        		jQuery("#bg-masking, #bg-loading").hide();
        	}
        	else{
        		var redirectUrl = '';
        		if(typeof res.redirect !== 'undefined' && res.redirect !== ''){
        			redirectUrl = res.redirect;
        		}
        		window.location = redirectUrl;
        	}
        	
        }
    });
}

