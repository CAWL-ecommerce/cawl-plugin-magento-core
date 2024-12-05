<?php
declare(strict_types=1);

namespace Cawl\PaymentCore\Model\Fraud;

use Magento\Framework\Exception\AlreadyExistsException;
use Cawl\PaymentCore\Api\Data\FraudInterface;
use Cawl\PaymentCore\Api\Data\FraudInterfaceFactory;
use Cawl\PaymentCore\Api\FraudRepositoryInterface;
use Cawl\PaymentCore\Api\PaymentRepositoryInterface;
use Cawl\PaymentCore\Model\Fraud\ResourceModel\Fraud as FraudResource;

/**
 * Repository for fraud entity
 */
class FraudRepository implements FraudRepositoryInterface
{
    /**
     * @var FraudInterfaceFactory
     */
    private $fraudFactory;

    /**
     * @var FraudResource
     */
    private $fraudResource;

    /**
     * @var PaymentRepositoryInterface
     */
    private $paymentRepository;

    public function __construct(
        FraudInterfaceFactory $fraudFactory,
        FraudResource $fraudResource,
        PaymentRepositoryInterface $paymentRepository
    ) {
        $this->fraudFactory = $fraudFactory;
        $this->fraudResource = $fraudResource;
        $this->paymentRepository = $paymentRepository;
    }

    /**
     * Save fraud entity
     *
     * @param FraudInterface $fraudEntity
     * @return FraudInterface
     * @throws AlreadyExistsException
     */
    public function save(FraudInterface $fraudEntity): FraudInterface
    {
        $this->fraudResource->save($fraudEntity);

        return $fraudEntity;
    }

    public function getByIncrementId(string $incrementId): FraudInterface
    {
        $payment = $this->paymentRepository->get($incrementId);
        $fraudEntity = $this->fraudFactory->create();
        $this->fraudResource->load($fraudEntity, $payment->getEntityId(), FraudInterface::WORLDLINE_PAYMENT_ID);

        return $fraudEntity;
    }
}
