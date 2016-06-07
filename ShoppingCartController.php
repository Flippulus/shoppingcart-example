<?php
use ShoppingCartModel;

/**
 *
 * Class ShoppingCartController
 *
 */
class ShoppingCartController
{
    private $shoppingCartModel;

    function index()
    {
        //Show the page here.
    }

    /**
     * Adds one product to the shopping cart.
     *
     * @param array $productData
     */
    function addProductToCart($productData)
    {
        $success = $this->_shoppingCart()->addProductToCart($productData, 1);
    }

    /**
     * Adds products to the shopping cart.
     *
     * @param array $products
     */
    function addProductsToCart($products)
    {
        $success = true;
        $model = $this->_shoppingCart();

        foreach ($products as $productData) {
            if (!$model->addProductToCart($productData, 1)) {
                $success = false;
            }
        }
    }

    /**
     * Adds a discount to a certain product.
     *
     * @param $product_id
     * @param $discount_percentage
     */
    function addDiscountToCart($product_id, $discount_percentage)
    {
        $success = $this->_shoppingCart()->updateProductDiscount($product_id, $discount_percentage);
    }

    /**
     * Adds a multitude of discounts to products in the shopping cart.
     *
     * @param $discounts
     */
    function addDiscountsToCart($discounts)
    {
        $success = true;
        $model = $this->_shoppingCart();

        foreach ($discounts as $product_id => $discount_percentage) {
            if (!$this->_shoppingCart()->updateProductDiscount($product_id, $discount_percentage)) {
                $success = false;
            }
        }
    }

    /**
     * Show the shopping cart.
     */
    function showCart()
    {
        $products = $this->_shoppingCart()->getShoppingCart();
        $totals = $this->_shoppingCart()->getTotals();

        //TODO show products & totals
    }

    /**
     * Returns (and if needed creates shoppingCartModel)
     *
     * @return ShoppingCartModel
     */
    protected function _shoppingCart()
    {
        if (!$this->shoppingCartModel) {
            $this->shoppingCartModel = new ShoppingCartModel();
        }

        return $this->shoppingCartModel;
    }
} 