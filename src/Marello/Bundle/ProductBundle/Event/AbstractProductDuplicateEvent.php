<?php

namespace Marello\Bundle\ProductBundle\Event;

use Marello\Bundle\ProductBundle\Entity\Product;
use Symfony\Component\EventDispatcher\Event;

abstract class AbstractProductDuplicateEvent extends Event
{
    /**
     * @var Product
     */
    protected $product;

    /**
     * @var Product
     */
    protected $sourceProduct;

    /**
     * @param Product $product
     * @param Product $sourceProduct
     */
    public function __construct(Product $product, Product $sourceProduct)
    {
        $this->product = $product;
        $this->sourceProduct = $sourceProduct;
    }

    /**
     * @return Product
     */
    public function getProduct()
    {
        return $this->product;
    }

    /**
     * @return Product
     */
    public function getSourceProduct()
    {
        return $this->sourceProduct;
    }
}
