<?php
declare(strict_types=1);

namespace Cawl\PaymentCore\Service\Payment;

use Magento\Framework\Exception\LocalizedException;
use OnlinePayments\Sdk\Domain\CancelPaymentResponse;
use Psr\Log\LoggerInterface;
use Cawl\PaymentCore\Api\Service\Payment\CancelPaymentServiceInterface;
use Cawl\PaymentCore\Api\ClientProviderInterface;
use Cawl\PaymentCore\Model\Config\WorldlineConfig;
use OnlinePayments\Sdk\Domain\CancelPaymentRequest;

/**
 * @link https://support.direct.ingenico.com/en/documentation/api/reference/#tag/Payments/operation/CancelPaymentApi
 */
class CancelPaymentService implements CancelPaymentServiceInterface
{
    /**
     * @var ClientProviderInterface
     */
    private $clientProvider;

    /**
     * @var WorldlineConfig
     */
    private $worldlineConfig;

    /**
     * @var LoggerInterface
     */
    private $logger;

    public function __construct(
        ClientProviderInterface $clientProvider,
        WorldlineConfig $worldlineConfig,
        LoggerInterface $logger
    ) {
        $this->clientProvider = $clientProvider;
        $this->worldlineConfig = $worldlineConfig;
        $this->logger = $logger;
    }

    /**
     * Cancel payment by payment id
     *
     * @param string $paymentId
     * @param int|null $storeId
     * @return CancelPaymentResponse
     * @throws LocalizedException
     */
    public function execute(string $paymentId, ?int $storeId = null): CancelPaymentResponse
    {
        try {
            return $this->clientProvider->getClient($storeId)
                ->merchant($this->worldlineConfig->getMerchantId($storeId))
                ->payments()
                ->cancelPayment($paymentId, new CancelPaymentRequest());
        } catch (\Exception $e) {
            $this->logger->debug($e->getMessage());
            throw new LocalizedException(__('CancelPaymentApi has failed. Please contact the provider.'));
        }
    }
}
