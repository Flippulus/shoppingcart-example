<?php


/**
 *
 * Class ShoppingCartModel
 *
 *
 * Holds all functions about the shopping cart itself.
 */
class ShoppingCartModel
{
    private $cartProducts;

    /**
     * Adds a product to the shopping cart.
     *
     * @param $product an array containing the product data: product name, product_id, product price (without vat), product vat, product discount
     * @param int $amount The amount of items to add to the cart
     * @return bool true on success, false on failure
     */
    public function addProductToCart($product, $amount)
    {
        $shoppingCart = $this->_shoppingCart();

        if (isset($shoppingCart[$product['product_id']])) {
            $shoppingCart[$product['product_id']]['amount'] += $amount;
        } else {
            $shoppingCart[$product['product_id']] = array(
                'name' => $product['name'],
                'discount' => isset($product['discount']) ? $product['discount'] : 0,
                'product_id' => $product['product_id'],
                'price_without_vat' => $product['price'],
                'price_with_vat' => $this->_calculatePriceWithVat($product['price'], $product['vat']),
                'vat' => $product['vat'],
            );
        }

        $this->_saveShoppingCart($shoppingCart);

        return true;
    }

    /**
     * Get the total prices.
     *
     * @return array $totals an array containing the following values:
     * - total_price_without_discount
     * - total_price_without_vat
     * - total_price_with_vat
     * - total_price_vat
     */
    public function getTotals()
    {
        $cartProducts = $this->_shoppingCart();

        $totals = $this->_calculateTotalPrices($cartProducts);

        return $totals;
    }

    /**
     * Returns the shopping cart.
     *
     * @return array
     */
    public function getShoppingCart()
    {
        return $this->_shoppingCart();
    }

    /**
     * Updates a product discount.
     *
     * @param int $product_id
     * @param float $discount_percentage
     * @return bool true on success, false on failure
     */
    public function updateProductDiscount($product_id, $discount_percentage)
    {
        $cartProducts = $this->_shoppingCart();

        if (isset($cartProducts[$product_id])) {
            $cartProducts[$product_id]['discount'] = $discount_percentage;
            $this->_saveShoppingCart($cartProducts);
            return true;
        }

        return false;
    }

    /**
     * Returns the price, with discount calculated, or the clean price, if no discount.
     *
     * @param float $price
     * @param int $discount_percentage
     * @return float $discountedPrice
     */
    protected function _calculateDiscountedPrice($price, $discount_percentage)
    {
        if (!$discount_percentage) {
            return $price;
        }

        if ($discount_percentage < 1) {
            return $price - ($price * $discount_percentage);
        }

        return $price - ($price * $discount_percentage / 100);
    }

    /**
     * Calculates the product price with vat and returns it.
     *
     * @param float $price The price of the product
     * @param float $vat The vat percentage
     * @return float
     */
    protected function _calculatePriceWithVat($price, $vat)
    {
        if (is_numeric($vat) && is_numeric($price)) {
            if ($vat < 1) {
                return $price * ($vat + 1);
            }

            return $price * (($vat / 100) + 1);
        }

        return 0;
    }

    /**
     * Calculates the total prices
     *
     * @param array $cartProducts
     * @return array
     */
    protected function _calculateTotalPrices($cartProducts)
    {
        $total_without_discount = 0;
        $total_without_vat = 0;
        $total_with_vat = 0;

        foreach ($cartProducts as $cartProduct) {
            $price_without_vat = $this->_calculateDiscountedPrice($cartProduct['price_without_vat'], $cartProduct['discount']);
            $price_with_vat = $this->_calculatePriceWithVat($price_without_vat, $cartProduct['vat']);

            $total_without_discount = $cartProduct['price_without_vat'];
            $total_without_vat += $price_without_vat;
            $total_with_vat += $price_with_vat;
        }

        return array(
            'total_price_without_discount' => $total_without_discount,
            'total_price_without_vat' => $total_without_vat,
            'total_price_with_vat' => $total_with_vat,
            'total_price_vat' => $total_with_vat - $total_without_vat,
        );
    }

    /**
     * Updates the shopping cart session variable.
     *
     * @param array $shoppingCart
     * @return bool true on success, false on failure
     */
    protected function _saveShoppingCart($shoppingCart)
    {
        $this->cartProducts = $shoppingCart;
        if (isset($_SESSION)) {
            $_SESSION['shoppingCart'] = $this->cartProducts;
            return true;
        }

        return false;
    }

    /**
     * Returns an array containing cart products.
     * Fetches the session variable if it is not yet set.
     *
     * @return array cartProducts
     */
    protected function _shoppingCart()
    {
        if ($this->cartProducts) {
            return $this->cartProducts;
        }

        if (isset($_SESSION) && isset($_SESSION['shoppingCart'])) {
            $this->cartProducts = $_SESSION['shoppingCart'];
            return $this->cartProducts;
        }

        $_SESSION['shoppingCart'] = array();
        $this->cartProducts = array();
        return $this->cartProducts;
    }
} 