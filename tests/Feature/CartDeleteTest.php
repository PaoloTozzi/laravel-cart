<?php

use Binafy\LaravelCart\Models\Cart;
use Binafy\LaravelCart\Models\CartItem;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Artisan;
use Tests\SetUp\Models\Product;
use Tests\SetUp\Models\User;
use function Pest\Laravel\assertDatabaseCount;
use function Pest\Laravel\assertDatabaseHas;
use function PHPUnit\Framework\assertInstanceOf;

/*
 * Use `RefreshDatabase` for delete migration data for each test.
 */
uses(RefreshDatabase::class);

test('can remove an item from the cart', function () {
    $user = User::query()->create(['name' => 'Milwad', 'email' => 'milwad.dev@gmail.comd']);
    $product1 = Product::query()->create(['title' => 'Product 1']);
    $product2 = Product::query()->create(['title' => 'Product 2']);
    $product3 = Product::query()->create(['title' => 'Product 3']);
    $product4 = Product::query()->create(['title' => 'Product 4']);

    $items = [
        [
            'itemable' => $product1,
            'quantity' => 2,
        ],
        [
            'itemable' => $product2,
            'quantity' => 1,
        ],
        [
            'itemable' => $product3,
            'quantity' => 5,
        ],
        [
            'itemable' => $product4,
            'quantity' => 3,
        ],
    ];

    // Store items to cart
    $cart = Cart::query()->firstOrCreate(['user_id' => $user->id]);
    $cart->storeItems($items);

    // Delete Item from cart
    $cart->removeItem($product1);

    // DB Assertions
    assertDatabaseCount('carts', 1);
    assertDatabaseCount('cart_items', 3);

    $cart->removeItem($product2);
    assertDatabaseCount('cart_items', 2);

    $cart->removeItem($product1);
    assertDatabaseCount('cart_items', 2);
});

test('can empty the cart', function() {
    $user = User::query()->create(['name' => 'Milwad', 'email' => 'milwad.dev@gmail.comd']);
    $product1 = Product::query()->create(['title' => 'Product 1']);
    $product2 = Product::query()->create(['title' => 'Product 2']);
    $product3 = Product::query()->create(['title' => 'Product 3']);
    $product4 = Product::query()->create(['title' => 'Product 4']);

    $items = [
        [
            'itemable' => $product1,
            'quantity' => 2,
        ],
        [
            'itemable' => $product2,
            'quantity' => 1,
        ],
        [
            'itemable' => $product3,
            'quantity' => 5,
        ],
        [
            'itemable' => $product4,
            'quantity' => 3,
        ],
    ];

    // Store items to cart
    $cart = Cart::query()->firstOrCreate(['user_id' => $user->id]);
    $cart->storeItems($items);

    assertDatabaseCount('cart_items', 4);

    // Remove all items from cart
    $cart->emptyCart();
    assertDatabaseCount('cart_items', 0);
});
