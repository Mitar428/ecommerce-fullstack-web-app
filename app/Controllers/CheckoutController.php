<?php

namespace Ecommerce\Shop\Controllers;

use Ecommerce\Shop\Core\Controller;
use Ecommerce\Shop\Core\Session;
use Ecommerce\Shop\Core\Database;

Class CheckoutController extends Controller{

public function index(){

if(!Session::get('user_id')){
header("Location: /ecommerce-shop/public/login");
exit;
}

$db=Database::getInstance()->getConnection();

$korisnik=Session::get('user_id');

$stmt=$db->prepare("
SELECT 
k.korpa_id,
k.kolicina,
p.proizvod_id,
p.naziv,
p.cena,
p.slika,
(k.kolicina * p.cena) AS ukupno
FROM korpa k
JOIN proizvodi p
ON k.proizvod_id=p.proizvod_id
WHERE k.korisnik_id=?
");

$stmt->execute([$korisnik]);

$items=$stmt->fetchAll();

echo $this->twig->render("Cart/Checkout.html.twig",[
"items"=>$items
]);

}


public function order(){

if(!Session::get('user_id')){
header("Location: /ecommerce-shop/public/login");
exit;
}

$db=Database::getInstance()->getConnection();

$korisnik=Session::get('user_id');

$ime=$_POST['ime'];
$prezime=$_POST['prezime'];
$adresa=$_POST['adresa'];
$grad=$_POST['grad'];
$telefon=$_POST['telefon'];


$stmt=$db->prepare("
SELECT
k.proizvod_id,
k.kolicina,
p.cena
FROM korpa k
JOIN proizvodi p
ON k.proizvod_id=p.proizvod_id
WHERE k.korisnik_id=?
");

$stmt->execute([$korisnik]);

$items=$stmt->fetchAll();


$ukupno=0;

foreach($items as $item){
$ukupno+=$item['cena']*$item['kolicina'];
}



$stmt=$db->prepare("
INSERT INTO porudzbine(
korisnik_id,
ime,
prezime,
adresa,
grad,
telefon,
ukupna_cena,
status
)
VALUES(?,?,?,?,?,?,?,?)
");


$stmt->execute([
$korisnik,
$ime,
$prezime,
$adresa,
$grad,
$telefon,
$ukupno,
"Na čekanju"
]);


$porudzbina_id=$db->lastInsertId();


$stmt=$db->prepare("
INSERT INTO stavke_porudzbine
(
porudzbina_id,
proizvod_id,
kolicina,
cena
)
VALUES(?,?,?,?)
");


foreach($items as $item){

$stmt->execute([
$porudzbina_id,
$item['proizvod_id'],
$item['kolicina'],
$item['cena']
]);

}







header("Location: /ecommerce-shop/public/success");
exit;

}



public function success(){

if(!Session::get('user_id')){
header("Location: /ecommerce-shop/public/login");
exit;
}

echo $this->twig->render("Cart/success.html.twig");

}

}

?>