<?php
declare(strict_types=1);

namespace Cawl\PaymentCore\Api\Service;

use OnlinePayments\Sdk\Merchant\Products\GetPaymentProductsParams;

interface GetPaymentProductsRequestBuilderInterface
{
    public function build(?int $storeId = null): GetPaymentProductsParams;
}
