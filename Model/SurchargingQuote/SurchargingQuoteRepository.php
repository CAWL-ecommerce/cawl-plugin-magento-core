<?php
declare(strict_types=1);

namespace Cawl\PaymentCore\Model\SurchargingQuote;

use Magento\Sales\Api\OrderRepositoryInterface;
use Cawl\PaymentCore\Api\Data\SurchargingQuoteInterface;
use Cawl\PaymentCore\Api\Data\SurchargingQuoteInterfaceFactory;
use Cawl\PaymentCore\Api\SurchargingQuoteRepositoryInterface;
use Cawl\PaymentCore\Model\SurchargingQuote\ResourceModel\SurchargingQuote as SurchargingQuoteResource;

class SurchargingQuoteRepository implements SurchargingQuoteRepositoryInterface
{
    /**
     * @var OrderRepositoryInterface
     */
    private $orderRepository;

    /**
     * @var SurchargingQuoteResource
     */
    private $surchargingQuoteResource;

    /**
     * @var SurchargingQuoteInterfaceFactory
     */
    private $surchargingQuoteFactory;

    /**
     * @var array
     */
    private $storedSurchargingQuote = [];

    public function __construct(
        OrderRepositoryInterface $orderRepository,
        SurchargingQuoteResource $surchargingQuoteResource,
        SurchargingQuoteInterfaceFactory $surchargingQuoteFactory
    ) {
        $this->orderRepository = $orderRepository;
        $this->surchargingQuoteResource = $surchargingQuoteResource;
        $this->surchargingQuoteFactory = $surchargingQuoteFactory;
    }

    public function save(SurchargingQuoteInterface $surchargingQuoteEntity): SurchargingQuoteInterface
    {
        $this->surchargingQuoteResource->save($surchargingQuoteEntity);

        return $surchargingQuoteEntity;
    }

    public function getByQuoteId(int $quoteId): SurchargingQuoteInterface
    {
        if (empty($this->storedSurchargingQuote[$quoteId])) {
            $surchargingQuote = $this->surchargingQuoteFactory->create();
            $this->surchargingQuoteResource->load($surchargingQuote, $quoteId, SurchargingQuoteInterface::QUOTE_ID);
            $this->storedSurchargingQuote[$quoteId] = $surchargingQuote;
        }

        return $this->storedSurchargingQuote[$quoteId];
    }

    public function getByOrderId(int $orderId): SurchargingQuoteInterface
    {
        $order = $this->orderRepository->get($orderId);

        return $this->getByQuoteId((int)$order->getQuoteId());
    }

    public function deleteByQuoteId(int $quoteId): void
    {
        $surchargingQuote = $this->getByQuoteId($quoteId);
        if ($surchargingQuote->getId()) {
            $this->surchargingQuoteResource->delete($surchargingQuote);
        }
    }
}
