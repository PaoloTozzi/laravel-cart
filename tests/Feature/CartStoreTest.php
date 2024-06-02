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

test('can store product in cart', function () {
    $user = User::query()->create(['name' => 'Milwad', 'email' => 'milwad.dev@gmail.comd']);
    $product = Product::query()->create(['title' => 'Product 1']);

    $cart = Cart::query()->firstOrCreate(['user_id' => $user->id]);
    $cartItem = new CartItem([
        'itemable_id' => $product->id,
        'itemable_type' => $product::class,
        'quantity' => 1,
    ]);

    $cart->items()->save($cartItem);

    // Assertions
    assertInstanceOf($product::class, $cartItem->itemable()->first());

    // DB Assertions
    assertDatabaseCount('carts', 1);
    assertDatabaseCount('cart_items', 1);
    assertDatabaseHas('cart_items', [
        'itemable_id' => $product->id,
        'itemable_type' => $product::class,
        'quantity' => 1,
    ]);
});

test('can store product in cart with custom table name from config', function () {
    config()->set([
        'laravel-cart.carts.table' => 'custom_carts',
        'laravel-cart.cart_items.table' => 'custom_cart_items',
    ]);

    Artisan::call('migrate:refresh');

    $user = User::query()->create(['name' => 'Milwad', 'email' => 'milwad.dev@gmail.comd']);
    $product = Product::query()->create(['title' => 'Product 1']);

    $cart = Cart::query()->firstOrCreate(['user_id' => $user->id]);

    $cartItem = new CartItem([
        'itemable_id' => $product->id,
        'itemable_type' => $product::class,
        'quantity' => 1,
    ]);

    $cart->items()->save($cartItem);

    // Assertions
    assertInstanceOf($product::class, $cartItem->itemable()->first());

    // DB Assertions
    assertDatabaseCount('custom_carts', 1);
    assertDatabaseCount('custom_cart_items', 1);
    assertDatabaseHas('custom_cart_items', [
        'itemable_id' => $product->id,
        'itemable_type' => $product::class,
        'quantity' => 1,
    ]);
});

test('can store product in cart with firstOrCreateWithItems scope', function () {
    $user = User::query()->create(['name' => 'Milwad', 'email' => 'milwad.dev@gmail.comd']);
    $product = Product::query()->create(['title' => 'Product 1']);

    $cart = Cart::query()->firstOrCreateWithStoreItems($product, 1, $user->id);

    // DB Assertions
    assertDatabaseCount('carts', 1);
    assertDatabaseCount('cart_items', 1);
    assertDatabaseHas('cart_items', [
        'itemable_id' => $product->id,
        'itemable_type' => $product::class,
        'quantity' => 1,
    ]);
});
