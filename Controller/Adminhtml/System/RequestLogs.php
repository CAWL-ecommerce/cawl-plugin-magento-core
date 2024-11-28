<?php
declare(strict_types=1);

namespace Worldline\PaymentCore\Controller\Adminhtml\System;

use Magento\Backend\App\Action;
use Magento\Backend\Model\View\Result\Page;
use Magento\Framework\App\Action\HttpGetActionInterface;
use Magento\Framework\Controller\ResultFactory;
use Magento\Framework\Controller\ResultInterface;

class RequestLogs extends Action implements HttpGetActionInterface
{
    /**
     * Authorization level of a basic admin session
     *
     * @see _isAllowed()
     */
    public const ADMIN_RESOURCE = 'Cawl_PaymentCore::request_logs';

    public function execute(): ResultInterface
    {
        /** @var Page $resultPage */
        $resultPage = $this->resultFactory->create(ResultFactory::TYPE_PAGE);
        $resultPage->initLayout();
        $resultPage->setActiveMenu('Cawl_PaymentCore::request_logs');
        $resultPage->getConfig()->getTitle()->prepend(__('Cawl Request Logs'));

        return $resultPage;
    }
}
