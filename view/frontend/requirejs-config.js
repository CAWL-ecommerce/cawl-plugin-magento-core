let config = {
    config: {
      mixins: {
        'Magento_Checkout/js/view/payment/list': {
          'Cawl_PaymentCore/js/view/payment/list-mixin': true
        },
        'Amasty_CheckoutCore/js/view/payment/list': {
          'Cawl_PaymentCore/js/view/payment/list-mixin': true
        },
        'Cawl_HostedCheckout/js/view/hosted-checkout/worldlinehc-method': {
          'Cawl_PaymentCore/js/view/payment/default-mixin': true
        },
        'Cawl_HostedCheckout/js/view/hosted-checkout/vault': {
          'Cawl_PaymentCore/js/view/payment/default-mixin': true
        },
        'Cawl_RedirectPayment/js/view/redirect-payment/worldlinerp-method': {
          'Cawl_PaymentCore/js/view/payment/default-mixin': true
        },
        'Cawl_RedirectPayment/js/view/redirect-payment/vault': {
          'Cawl_PaymentCore/js/view/payment/default-mixin': true
        },
        'Cawl_CreditCard/js/view/credit-card/worldlinecc-method': {
          'Cawl_PaymentCore/js/view/payment/default-mixin': true
        },
        'Cawl_CreditCard/js/view/credit-card/vault': {
          'Cawl_PaymentCore/js/view/payment/default-mixin': true
        }
      }
    }
};
