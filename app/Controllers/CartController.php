<?php

namespace Ecommerce\Shop\Controllers;
use Ecommerce\Shop\Core\Session;
use Ecommerce\Shop\Core\Controller;
use Ecommerce\Shop\Core\Database;

class CartController extends Controller
{

    public function add()
    {
        if (!Session::get('user_id')) {
            header("Location: /ecommerce-shop/public/login");
            exit;
        }

        $db = Database::getInstance()->getConnection();

        $korisnik = Session::get('user_id');
        $proizvod = $_POST['proizvod_id'];

        $stmt = $db->prepare("
            SELECT *
            FROM korpa
            WHERE korisnik_id=? AND proizvod_id=?
        ");

        $stmt->execute([$korisnik,$proizvod]);

        if($stmt->fetch()){

            $stmt = $db->prepare("
                UPDATE korpa
                SET kolicina=kolicina+1
                WHERE korisnik_id=? AND proizvod_id=?
            ");

            $stmt->execute([$korisnik,$proizvod]);

        }else{

            $stmt = $db->prepare("
                INSERT INTO korpa
                (korisnik_id,proizvod_id,kolicina)
                VALUES(?,?,1)
            ");

            $stmt->execute([$korisnik,$proizvod]);

        }

        header("Location: /ecommerce-shop/public/korpa");
        exit;
    }
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
            p.naziv,
            p.cena,
            p.slika
            FROM korpa k
            JOIN proizvodi p
            ON k.proizvod_id=p.proizvod_id
            WHERE k.korisnik_id=?
        "
        );

        $stmt->execute([$korisnik]);

        $items=$stmt->fetchAll();
        
        echo $this->twig->render("Cart/index.html.twig",[
            "items"=>$items

        ]);
        
        }

        public function remove($id){

        if(!Session::get('user_id')){
            header("Location: /ecommerce-shop/public/login");
            exit;
        }

        $db=Database::getInstance()->getConnection();
        $korisnik=Session::get('user_id');


       


        $stmt=$db->prepare(
        "
            DELETE FROM korpa WHERE korpa_id=? AND korisnik_id=?
        ");

        $stmt->execute([
            $id,
            $korisnik
        ]);

        header("Location: /ecommerce-shop/public/korpa");
        exit;
}

   
    }
