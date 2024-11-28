let config = {
    map: {
        '*': {
            checkConnection: 'Cawl_PaymentCore/js/testconnection'
        }
    },
    config: {
        mixins: {
            'mage/validation': {
                'Cawl_PaymentCore/js/system/config/validation-mixin': true
            }
        }
    }
};
