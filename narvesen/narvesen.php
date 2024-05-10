<?php

function loadProducts()
{
    $products = file_get_contents('products.json');
    return json_decode($products, true);
}

function centsToDollars($cents)
{
    return $cents / 100;
}

function displayProducts($products)
{
    echo "\nHere's our list of products:\n";
    foreach ($products as $product) {
        $priceInDollars = centsToDollars($product['price']);
        echo "ID: " . $product['id'] . ' - ' . $product['name'] . ' - ' . "$" . number_format($priceInDollars, 2) . "\n";
    }
}

function addToBasket(&$basket, &$total, $productID, $products, $quantity)
{
    if ($productID < 1 || $productID > count($products)) {
        echo "Invalid product ID. Please try again.\n";
        return;
    }

    $product = $products[$productID - 1];
    $found = false;

    $productInDollars = centsToDollars($product['price']);

    foreach ($basket as &$item) {
        if ($item['id'] == $productID) {
            $item['quantity'] += $quantity;
            $total += $productInDollars * $quantity;
            echo "\nAdded $quantity " . $product['name'] . " to the basket.\n";
            $found = true;
            break;
        }
    }

    if ($found == false) {
        $product['quantity'] = $quantity;
        $basket[] = $product;
        $total += $productInDollars * $quantity;
        echo "\nAdded $quantity " . $product['name'] . " to the basket.\n";
    }
}

function removeFromBasket(&$basket, &$total, $productID, $removeQuantity)
{
    $itemFound = false;

    foreach ($basket as $index => &$item) {
        if ($item['id'] == $productID) {
            $productInDollars = centsToDollars($item['price']);
            if ($removeQuantity >= $item['quantity']) {
                $total -= $productInDollars * $item['quantity'];
                unset($basket[$index]);
                echo "\nRemoved all of " . $item['name'] . " from the basket.\n";
            } else {
                $item['quantity'] -= $removeQuantity;
                $total -= $productInDollars * $removeQuantity;
                echo "\nRemoved $removeQuantity " . $item['name'] . " from the basket.\n";
            }
            $itemFound = true;
            break;
        }
    }

    if ($itemFound == false) {
        echo "Product not found in the basket.\n";
    }
}

function viewBasket($basket, $total)
{
    echo "\nHere's your basket:\n";
    foreach ($basket as $product) {
        echo $product['id'] . ' - ' . $product['name'] . ' - qty: ' . $product['quantity'] . "\n";
    }
    echo "Total: $" . number_format($total, 2) . "\n";
}

function narvesen($products)
{
    $total = 0;
    $basket = [];

    while (true) {
        displayProducts($products);
        echo "\nWhat would you like to do?\n";
        echo "1. Add to basket\n";
        echo "2. Remove from basket\n";
        echo "3. View basket\n";
        echo "4. Checkout\n";
        echo "5. Exit\n";
        $choice = (int)readline("Please choose an option: ");

        switch ($choice) {
            case 1:
                $productID = (int)readline("Enter the ID of the product you want to add to the basket: ");
                $quantity = (int)readline("Enter the quantity of the product you want to add: ");
                addToBasket($basket, $total, $productID, $products, $quantity);
                break;
            case 2:
                $productID = (int)readline("Enter the ID of the product you want to remove from the basket: ");
                $removeQuantity = (int)readline("Enter the quantity you want to remove: ");
                removeFromBasket($basket, $total, $productID, $removeQuantity);
                break;
            case 3:
                viewBasket($basket, $total);
                break;
            case 4:
                echo "\nTotal: $" . $total . "\n";
                echo "\nThank you for shopping with us!\n";
                break;
            case 5:
                echo "\nThank you for using Narvesen!\n";
                return;
            default:
                echo "Invalid choice. Please try again.\n";
                break;
        }
    }
}

$products = loadProducts();
narvesen($products);