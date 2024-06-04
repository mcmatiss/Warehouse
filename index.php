<?php
require 'vendor/autoload.php';
require_once 'app/Product.php';
require_once 'app/Warehouse.php';

use App\Product;
use App\Warehouse;
use LucidFrame\Console\ConsoleTable;
use PHP_Parallel_Lint\PhpConsoleColor\ConsoleColor;

function isInputValid(int $userInput, int $minValue, int $maxValue): bool
{
    return $userInput >= $minValue && $userInput <= $maxValue;
}

$consoleColor = new ConsoleColor();
$warehouse = new Warehouse();


if (file_exists('storedProducts.json')) {
    $products = json_decode(file_get_contents('storedProducts.json'));
    foreach ($products as $product) {
        $warehouse->addProduct($product);
    }
    var_dump($products);
}

$users = json_decode(file_get_contents('users.json'));

$userIsValid = false;
do {
    echo "Please login.\n";
    $user = readline("Username: ");
    foreach ($users as $user) {
        if ($user->username === $user) {
            $userIsValid = true;
        }
    }
    if (!$userIsValid) {
        readline("Wrong username. Press any key to continue...");
    }
} while (false);


do {
    $table = new ConsoleTable();
    $table
        ->addHeader("Index")
        ->addHeader("Id")
        ->addHeader("Name")
        ->addHeader("Units")
        ->addHeader("Created at")
        ->addHeader("Last updated at")
        ->setPadding(2)
        ->addRow()
    ;
    if ($warehouse->products()) {
        foreach ($warehouse->products() as $index => $product) {
            $table
                ->addColumn(($index + 1) . ".", 0, $index)
                ->addColumn($product->getId(), 1, $index)
                ->addColumn($product->getName(), 2, $index)
                ->addColumn($product->getUnits(), 3, $index)
                ->addColumn($product->getCreatedAt(), 4, $index)
                ->addColumn($product->getUpdatedAt(), 5, $index)
            ;
            if($product->getUnits() === 0)
            {
                $table->addColumn($consoleColor->apply(
                    "color_160",
                    $product->getUnits()), 3, $index);
            }
        }
    }
    $table->display();

    echo "\n1. Add product.\n";
    if ($warehouse->products())
    {
        echo "2. Withdraw product.\n" .
            "3. Change product name.\n" .
            "4. Change product unit amount.\n" .
            "5. Remove product.\n"
        ;
    } else {
        echo $consoleColor->apply(
            "color_240",
            "2. Withdraw product.\n" .
            "3. Change product name.\n" .
            "4. Change product unit amount.\n" .
            "5. Remove product.\n"
        );
    }
    echo "6. Exit.\n";

    $mainMenuChoice = (int) readline("Main Menu Choice: ");
    switch ($mainMenuChoice) {
        case 1:
            $productName = readline("Product name: ");
            $productInStock = (int) readline("Units in stock: ");
            if($productInStock <= 0)
            {
                readline(
                    'Invalid input. Amount of units in stock cant be less than 1. Press any key to continue...'
                );
                break;
            }
            $warehouse->addProduct(new Product($productName, $productInStock));
            break;
        case 2:
            $productChoice = (int) readline("Enter product index: ");
            if(! isInputValid($productChoice, 1, count($warehouse->products())))
            {
                readline('Invalid input. Press any key to continue...');
                break;
            }
            $withdrawAmount = (int) readline("Withdraw Amount: ");
            if(! isInputValid($withdrawAmount, 0, $warehouse->products()[$productChoice-1]->getUnits()))
            {
                readline('Invalid input. Press any key to continue...');
                break;
            }
            $warehouse->products()[$productChoice-1]->withdrawUnits($withdrawAmount);
            $warehouse->products()[$productChoice-1]->update();
            // TODO: LOG: $user - withdrew $withdrawAmount units of $warehouse->products()[$productChoice-1]->getName() id: $warehouse->products()[$productChoice-1]->getId() at $warehouse->products()[$productChoice-1]->$product->getUpdatedAt()
            break;
        case 3:
            $productChoice = (int) readline("Enter product index: ");
            if(! isInputValid($productChoice, 1, count($warehouse->products())))
            {
                readline('Invalid input. Press any key to continue...');
                break;
            }
            $productName = readline("Product name: ");
            $previousProductName = $warehouse->products()[$productChoice-1]->getName();
            $warehouse->products()[$productChoice-1]->setName($productName);
            $warehouse->products()[$productChoice-1]->update();
            // TODO: LOG: $user - changed the name of $previousProductName id: $warehouse->products()[$productChoice-1]->getId() to $warehouse->products()[$productChoice-1]->getName() at $warehouse->products()[$productChoice-1]->$product->getUpdatedAt()
            break;
        case 4:
            $productChoice = (int) readline("Enter product index: ");
            if(! isInputValid($productChoice, 1, count($warehouse->products())))
            {
                readline('Invalid input. Press any key to continue...');
                break;
            }
            $productInStock = (int) readline("Units in stock: ");
            if($productInStock <= 0)
            {
                readline(
                    'Invalid input. Amount of units in stock cant be less than 1. Press any key to continue...'
                );
                break;
            }
            $previousUnitAmount = $warehouse->products()[$productChoice-1]->getUnits();
            $warehouse->products()[$productChoice-1]->setUnits($productInStock);
            $warehouse->products()[$productChoice-1]->update();
            // TODO: LOG: $user - changed $warehouse->products()[$productChoice-1]->getName() id: $warehouse->products()[$productChoice-1]->getId() amount of units from $previousUnitAmount to $warehouse->products()[$productChoice-1]->getUnits() at $warehouse->products()[$productChoice-1]->$product->getUpdatedAt()
            break;
        case 5:
            $productChoice = (int) readline("Enter product index: ");
            if(! isInputValid($productChoice, 1, count($warehouse->products())))
            {
                readline('Invalid input. Press any key to continue...');
                break;
            }
            $warehouse->products()[$productChoice-1]->update();
            // TODO: LOG: $user - removed $warehouse->products()[$productChoice-1]->getName() id: $warehouse->products()[$productChoice-1]->getId() at $warehouse->products()[$productChoice-1]->$product->getUpdatedAt()
            $warehouse->removeProduct($productChoice-1);
            break;
        case 6:
            $storedProducts = $warehouse->jsonSerialize();
            file_put_contents("storedProducts.json", $storedProducts);
            return false;
        default:
            readline('Invalid input. Press any key to continue...');
    }
} while (true);

// TODO Serialize and Un-serialize warehouse data

// TODO Create an "users" / " customers "  list with access codes.

// TODO Each time customer launches the application he must enter his "code" to gain access to the application.

// TODO When there are changes made to the amount of the product or the product is edited,
//  there must be a log of what changes were made.
