<?php
declare(strict_types=1);

namespace Cawl\PaymentCore\Model\SurchargingCreditmemo;

use Cawl\PaymentCore\Api\Data\SurchargingCreditmemoInterface;
use Cawl\PaymentCore\Api\Data\SurchargingCreditmemoInterfaceFactory;
use Cawl\PaymentCore\Api\SurchargingCreditmemoRepositoryInterface;
use Cawl\PaymentCore\Model\SurchargingCreditmemo\ResourceModel\SurchargingCreditmemo as ResourceModel;
use Cawl\PaymentCore\Model\SurchargingCreditmemo\ResourceModel\SurchargingCreditmemo\CollectionFactory;

class SurchargingCreditmemoRepository implements SurchargingCreditmemoRepositoryInterface
{
    /**
     * @var ResourceModel
     */
    private $surchargingCreditmemoResource;

    /**
     * @var SurchargingCreditmemoInterfaceFactory
     */
    private $surchargingCreditmemoFactory;

    /**
     * @var CollectionFactory
     */
    private $surchargingCreditmemoCollectionFactory;

    /**
     * @var array
     */
    private $storedSurchargingCreditmemo = [];

    public function __construct(
        ResourceModel $surchargingCreditmemoResource,
        SurchargingCreditmemoInterfaceFactory $surchargingCreditmemoFactory,
        CollectionFactory $surchargingCreditmemoCollectionFactory
    ) {
        $this->surchargingCreditmemoResource = $surchargingCreditmemoResource;
        $this->surchargingCreditmemoFactory = $surchargingCreditmemoFactory;
        $this->surchargingCreditmemoCollectionFactory = $surchargingCreditmemoCollectionFactory;
    }

    public function save(SurchargingCreditmemoInterface $surchargingCreditmemo): SurchargingCreditmemoInterface
    {
        $this->surchargingCreditmemoResource->save($surchargingCreditmemo);

        return $surchargingCreditmemo;
    }

    public function getByCreditmemoId(int $creditmemoId): SurchargingCreditmemoInterface
    {
        if (empty($this->storedSurchargingCreditmemo[$creditmemoId])) {
            $surchargingCreditmemo = $this->surchargingCreditmemoFactory->create();
            $this->surchargingCreditmemoResource->load(
                $surchargingCreditmemo,
                $creditmemoId,
                SurchargingCreditmemoInterface::CREDITMEMO_ID
            );
            $this->storedSurchargingCreditmemo[$creditmemoId] = $surchargingCreditmemo;
        }

        return $this->storedSurchargingCreditmemo[$creditmemoId];
    }

    public function getItemsByQuoteId(int $quoteId): array
    {
        $surchargingCreditmemoCollection = $this->surchargingCreditmemoCollectionFactory->create();
        $surchargingCreditmemoCollection->addFieldToFilter(SurchargingCreditmemoInterface::QUOTE_ID, $quoteId);

        return $surchargingCreditmemoCollection->getItems();
    }
}
