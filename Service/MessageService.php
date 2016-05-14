<?php

namespace Hackathon\BotCommerce\Service;

use Magento\Catalog\Api\ProductRepositoryInterface;
use Magento\CatalogInventory\Api\StockStateInterface;
use Magento\Framework\Api\SearchCriteriaBuilder;
use Magento\Sales\Api\OrderRepositoryInterface;

class MessageService
{
    /**
     * @var OrderRepositoryInterface
     */
    private $orderRepository;

    /**
     * @var ProductRepositoryInterface
     */
    private $productRepository;

    /**
     * @var SearchCriteriaBuilder
     */
    private $criteriaBuilder;

    /**
     * @var StockStateInterface
     */
    private $stockState;

    public function __construct(
        SearchCriteriaBuilder $criteriaBuilder,
        OrderRepositoryInterface $orderRepository,
        ProductRepositoryInterface $productRepository,
        StockStateInterface $stockState
    ) {
        $this->stockState = $stockState;
        $this->criteriaBuilder = $criteriaBuilder;
        $this->orderRepository = $orderRepository;
        $this->productRepository = $productRepository;
    }

    /**
     * @param string $incrementId
     * @return string
     */
    public function getOrderStatus($incrementId)
    {
        $this->criteriaBuilder->addFilter('increment_id', $incrementId);

        $orders = $this->orderRepository->getList(
            $this->criteriaBuilder->create()
        );

        $order = current($orders->getItems());

        return $order->getStatus();
    }

    /**
     * @param string $sku
     * @return string
     */
    public function getProductStockStatus($sku)
    {
        $product = $this->productRepository->get($sku);

        return ($this->stockState->verifyStock($product->getId()) ? 'In Stock' : 'Out Of Stock');
    }
}
