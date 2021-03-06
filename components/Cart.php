<?php namespace OFFLINE\Mall\Components;

use Auth;
use Flash;
use OFFLINE\Mall\Classes\Exceptions\OutOfStockException;
use OFFLINE\Mall\Models\Cart as CartModel;
use OFFLINE\Mall\Models\CartProduct;
use OFFLINE\Mall\Models\GeneralSettings;
use OFFLINE\Mall\Models\ShippingMethod;
use Request;
use Session;

class Cart extends MallComponent
{
    public $cart;
    public $defaultMinQuantity = 1;
    public $defaultMaxQuantity = 100;
    public $showDiscountApplier = true;
    public $productPage;

    public function componentDetails()
    {
        return [
            'name'        => 'offline.mall::lang.components.cart.details.name',
            'description' => 'offline.mall::lang.components.cart.details.description',
        ];
    }

    public function defineProperties()
    {
        return [
            'showDiscountApplier' => [
                'type'    => 'checkbox',
                'name'    => 'offline.mall::lang.components.cart.properties.showDiscountApplier.name',
                'default' => 1,
            ],
        ];
    }

    public function init()
    {
        $this->addComponent(DiscountApplier::class, 'discountApplier', []);
    }

    public function onRun()
    {
        $this->addJs('assets/pubsub.js');
        $this->setData();
    }

    public function onUpdateQuantity()
    {
        $id = $this->decode(input('id'));

        // Make sure the product is actually in the logged
        // in user's shopping cart.
        $cart    = CartModel::byUser(Auth::getUser());
        $product = CartProduct
            ::whereHas('cart', function ($query) use ($cart) {
                $query->where('id', $cart->id);
            })
            ->where('id', $id)
            ->firstOrFail();

        try {
            $cart->setQuantity($product->id, (int)input('quantity'));
        } catch (OutOfStockException $e) {
            Flash::error(trans('offline.mall::lang.common.out_of_stock', ['quantity' => $e->product->stock]));

            return;
        } finally {
            $this->setData();
        }
    }

    public function onRemoveProduct()
    {
        $id = $this->decode(input('id'));

        // Make sure the product is actually in the logged
        // in user's shopping cart.
        $cart    = CartModel::byUser(Auth::getUser());
        $product = CartProduct
            ::whereHas('cart', function ($query) use ($cart) {
                $query->where('id', $cart->id);
            })
            ->where('id', $id)
            ->firstOrFail();

        $cart->removeProduct($product);

        $this->setData();
    }

    protected function setData()
    {
        $cart = CartModel::byUser(Auth::getUser());
        $cart->load(['products', 'products.custom_field_values', 'discounts']);
        if ($cart->shipping_method_id === null) {
            $cart->setShippingMethod(ShippingMethod::getDefault());
        }

        $this->setVar('cart', $cart);
        $this->setVar('productPage', GeneralSettings::get('product_page'));
        $this->setVar('showDiscountApplier', $this->property('showDiscountApplier'));
    }
}
