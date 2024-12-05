<?php
declare(strict_types=1);

namespace Cawl\PaymentCore\Model\RefundRequest;

use Exception;
use Magento\Framework\Exception\CouldNotSaveException;
use Cawl\PaymentCore\Api\Data\RefundRequestInterface;
use Cawl\PaymentCore\Api\Data\RefundRequestInterfaceFactory;
use Cawl\PaymentCore\Api\RefundRequestRepositoryInterface;
use Cawl\PaymentCore\Model\RefundRequest\ResourceModel\RefundRequest as RefundRequestResource;
use Cawl\PaymentCore\Model\RefundRequest\ResourceModel\RefundRequest\Collection;
use Cawl\PaymentCore\Model\RefundRequest\ResourceModel\RefundRequest\CollectionFactory;

class RefundRequestRepository implements RefundRequestRepositoryInterface
{
    /**
     * @var RefundRequestResource
     */
    private $refundRequestResource;

    /**
     * @var CollectionFactory
     */
    private $collectionFactory;

    public function __construct(
        RefundRequestResource $refundRequestResource,
        CollectionFactory $collectionFactory
    ) {
        $this->refundRequestResource = $refundRequestResource;
        $this->collectionFactory = $collectionFactory;
    }

    public function getListByIncrementId(string $incrementId): array
    {
        /** @var Collection $collection */
        $collection = $this->collectionFactory->create();
        $collection->addFieldToFilter(RefundRequestInterface::INCREMENT_ID, ['eq' => $incrementId]);

        return $collection->getItems();
    }

    public function getByIncrementIdAndAmount(string $incrementId, int $amount): RefundRequestInterface
    {
        /** @var Collection $collection */
        $collection = $this->collectionFactory->create();
        $collection->addFieldToFilter(RefundRequestInterface::INCREMENT_ID, ['eq' => $incrementId]);
        $collection->addFieldToFilter(RefundRequestInterface::AMOUNT, ['eq' => $amount]);
        $collection->addFieldToFilter(RefundRequestInterface::REFUNDED, ['eq' => 0]);

        return $collection->getLastItem();
    }

    /**
     * @param RefundRequestInterface $refundRequest
     * @return RefundRequestInterface
     * @throws CouldNotSaveException
     */
    public function save(RefundRequestInterface $refundRequest): RefundRequestInterface
    {
        try {
            $this->refundRequestResource->save($refundRequest);
        } catch (Exception $exception) {
            throw new CouldNotSaveException(__('Could not save request to refund: %1', $exception->getMessage()));
        }

        return $refundRequest;
    }
}
