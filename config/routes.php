<?php

use Ecommerce\Shop\Core\Router;
use Ecommerce\Shop\Core\Database;


$db = Database::getInstance()->getConnection();

$router = new Router($db);


// ==========================
// HOME
// ==========================

$router->get('/', 'HomeController', 'index');

$router->get('/o-nama', 'HomeController', 'about');


// ==========================
// ACCOUNT
// ==========================

$router->get('/login', 'AccountController', 'showLogin');
$router->post('/login', 'AccountController', 'login');

$router->get('/registracija', 'AccountController', 'showRegister');
$router->post('/registracija', 'AccountController', 'register');

$router->get('/changepassword', 'AccountController', 'updatePassword');
$router->post('/changepassword', 'AccountController', 'changePassword');

$router->get('/account', 'AccountController', 'account');

$router->get('/account/profile', 'AccountController', 'Myprofile');

$router->get('/account/addresses','AccountController','addresses');

$router->get('/logout', 'AccountController', 'logout');


// ==========================
// PROIZVODI I KATEGORIJE
// ==========================

$router->get('/proizvodi', 'CategoryController', 'categories');

$router->get('/kategorija/{id}', 'CategoryController', 'show');

$router->get('/proizvod/{id}', 'ProductController', 'show');


// ==========================
// KORPA
// ==========================

$router->post('/cart/add', 'CartController', 'add');

$router->get('/korpa', 'CartController', 'index');

$router->get('/cart/remove/{id}', 'CartController', 'remove');


// ==========================
// CHECKOUT
// ==========================

$router->get('/checkout', 'CheckoutController', 'index');

$router->post('/checkout', 'CheckoutController', 'order');

$router->get('/success', 'CheckoutController', 'success');


// ==========================
// PORUDŽBINE
// ==========================

// korisnik
$router->get('/orders', 'OrderController', 'order');

$router->get('/account/orders', 'OrderController', 'orders');

$router->get('/account/orders/remove/{id}','AccountController','removeFromAccount');


// admin porudžbine
$router->get('/admin/orders', 'OrderController', 'orders');

$router->get('/admin/orders/remove/{id}','OrderController','remove');
$router->get('/admin/products/remove/{id}','ProductController','remove');




$router->get('/porudzbina/{id}', 'OrderController', 'show');


// ==========================
// RECENZIJE
// ==========================

$router->post('/review/store/{id}', 'ReviewController', 'store');


// ==========================
// FAVORITI
// ==========================

$router->get('/favoriti', 'WishlistController', 'index');

$router->post('/favoriti/dodaj/{id}', 'WishlistController', 'add');

$router->get('/favoriti/remove/{id}', 'WishlistController', 'remove');


// ==========================
// ADMIN
// ==========================

$router->get('/admin', 'AdminController', 'index');

$router->get('/admin/products', 'AdminController', 'products');

$router->get('/admin/profile','AccountController','profile');


// ADMIN KORISNICI

$router->get('/admin/users', 'AdminController', 'users');

$router->get('/admin/users/edit/{id}', 'AdminController', 'edit');

$router->post('/admin/users/delete/{id}', 'AdminController', 'delete');

$router->post('/admin/users/update/{id}', 'AdminController', 'edituser');


// ADMINI

$router->get(
    '/admin/admins/create',
    'AdminController',
    'createadmin'
);

$router->post(
    '/admin/admins/store',
    'AdminController',
    'storeadmin'
);


// SETTINGS

$router->get('/admin/settings', 'SettingsController', 'index');

$router->post('/admin/settings/update', 'SettingsController', 'update');


// ==========================
// ADMIN PROIZVODI
// ==========================

$router->get('/admin/proizvodi', 'AdminController', 'products');

$router->get(
    '/admin/proizvod/dodaj',
    'AdminController',
    'createProduct'
);

$router->post(
    '/admin/proizvod/dodaj',
    'AdminController',
    'storeProduct'
);


$router->get(
    '/admin/proizvod/izmeni/{id}',
    'AdminController',
    'editProduct'
);

$router->post(
    '/admin/proizvod/izmeni/{id}',
    'AdminController',
    'updateProduct'
);


$router->post(
    '/admin/proizvod/obrisi/{id}',
    'AdminController',
    'deleteProduct'
);


// ==========================
// ADMIN KATEGORIJE
// ==========================

$router->get(
    '/admin/categories',
    'AdminController',
    'categories'
);

$router->post(
    '/admin/kategorija/dodaj',
    'AdminController',
    'storeCategory'
);


// ==========================
// DODAVANJE PROIZVODA
// ==========================

$router->get(
    '/admin/proizvodi/dodaj',
    'AddProductController',
    'index'
);

$router->post(
    '/admin/proizvodi/dodaj',
    'AddProductController',
    'store'
);


return $router;