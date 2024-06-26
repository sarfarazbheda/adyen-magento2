<?php
/**
 *
 * Adyen Payment module (https://www.adyen.com/)
 *
 * Copyright (c) 2023 Adyen NV (https://www.adyen.com/)
 * See LICENSE.txt for license details.
 *
 * Author: Adyen <magento@adyen.com>
 */

namespace Adyen\Payment\Model\Config\Backend;

use Adyen\Payment\Helper\PaymentMethods;
use Magento\Framework\App\Cache\TypeListInterface;
use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Framework\App\Config\Value;
use Magento\Framework\Data\Collection\AbstractDb;
use Magento\Framework\Model\Context;
use Magento\Framework\Model\ResourceModel\AbstractResource;
use Magento\Framework\Registry;

class PaymentMethodsStatus extends Value
{
    protected PaymentMethods $paymentMethodsHelper;

    public function __construct(
        Context $context,
        Registry $registry,
        ScopeConfigInterface $config,
        TypeListInterface $cacheTypeList,
        PaymentMethods $paymentMethodsHelper,
        AbstractResource $resource = null,
        AbstractDb $resourceCollection = null,
        array $data = []
    ) {
        $this->paymentMethodsHelper = $paymentMethodsHelper;

        parent::__construct($context, $registry, $config, $cacheTypeList, $resource, $resourceCollection, $data);
    }

    public function afterSave(): PaymentMethodsStatus
    {
        $this->paymentMethodsHelper->togglePaymentMethodsActivation(
            (bool) $this->getValue(),
            $this->getScope(),
            $this->getScopeId()
        );

        return parent::afterSave();
    }

    /**
     * @inheritDoc
     * @return PaymentMethodsStatus
     */
    public function afterDelete() : PaymentMethodsStatus
    {
        $this->paymentMethodsHelper->removePaymentMethodsActivation($this->getScope(), $this->getScopeId());
        return parent::afterDelete();
    }
}
