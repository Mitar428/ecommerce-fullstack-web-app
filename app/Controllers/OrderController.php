<?php

namespace Ecommerce\Shop\Controllers;

use Ecommerce\Shop\Core\Controller;
use Ecommerce\Shop\Core\Session;
use Ecommerce\Shop\Core\Database;


class OrderController extends Controller
{


    public function order()
    {

        if(!Session::get('user_id')){
            header("Location: /ecommerce-shop/public/login");
            exit;
        }


        $db = Database::getInstance()->getConnection();

        $korisnik = Session::get('user_id');


        $stmt = $db->prepare("
            SELECT
                p.porudzbina_id,
                p.ukupna_cena,
                p.status,
                p.datum,

                sp.kolicina,
                sp.cena,

                pr.naziv,
                pr.slika

            FROM porudzbine p

            JOIN stavke_porudzbine sp
            ON p.porudzbina_id = sp.porudzbina_id

            JOIN proizvodi pr
            ON sp.proizvod_id = pr.proizvod_id

            WHERE p.korisnik_id=?

            ORDER BY p.porudzbina_id DESC
        ");


        $stmt->execute([$korisnik]);


        $order = $stmt->fetchAll(\PDO::FETCH_ASSOC);



        echo $this->twig->render(
            "Cart/order.html.twig",
            [
                "order"=>$order
            ]
        );

    }







    public function orders()
    {

        if(!Session::get('user_id')){

            header("Location: /ecommerce-shop/public/login");
            exit;

        }



        $db = Database::getInstance()->getConnection();


        $korisnik = Session::get('user_id');



        $stmt = $db->prepare("
            SELECT uloga
            FROM korisnici
            WHERE korisnik_id=?
        ");


        $stmt->execute([$korisnik]);


        $user = $stmt->fetch(\PDO::FETCH_ASSOC);



        if(!$user){

            die("Korisnik nije pronađen");

        }




        // ADMIN

        if($user['uloga'] == 'ADMIN'){


            $stmt = $db->prepare("

                SELECT

                    p.porudzbina_id,
                    p.ime,
                    p.prezime,
                    p.adresa,
                    p.grad,
                    p.telefon,
                    p.ukupna_cena,
                    p.status,
                    p.datum,

                    sp.kolicina,
                    sp.cena,

                    pr.naziv,
                    pr.slika


                FROM porudzbine p


                JOIN stavke_porudzbine sp
                ON p.porudzbina_id = sp.porudzbina_id


                JOIN proizvodi pr
                ON sp.proizvod_id = pr.proizvod_id


                ORDER BY p.porudzbina_id DESC

            ");


            $stmt->execute();



            $orders = $stmt->fetchAll(\PDO::FETCH_ASSOC);



            echo $this->twig->render(
                "admin/orders.html.twig",
                [
                    "orders"=>$orders,
                    "admin"=>true
                ]
            );


            return;

        }







        // KUPAC


        $stmt = $db->prepare("

            SELECT

                p.porudzbina_id,
                p.ukupna_cena,
                p.status,
                p.datum,

                sp.kolicina,
                sp.cena,

                pr.naziv,
                pr.slika


            FROM porudzbine p


            JOIN stavke_porudzbine sp
            ON p.porudzbina_id = sp.porudzbina_id


            JOIN proizvodi pr
            ON sp.proizvod_id = pr.proizvod_id


            WHERE p.korisnik_id=?


            ORDER BY p.porudzbina_id DESC

        ");



        $stmt->execute([$korisnik]);



        $orders = $stmt->fetchAll(\PDO::FETCH_ASSOC);



        echo $this->twig->render(
            "Cart/order.html.twig",
            [
                "order"=>$orders,
                "admin"=>false
            ]
        );

    }









    public function remove($id)
    {


        if(!Session::get('user_id')){

            header("Location: /ecommerce-shop/public/login");
            exit;

        }



        $db = Database::getInstance()->getConnection();



        $stmt = $db->prepare("

            DELETE FROM porudzbine

            WHERE porudzbina_id=?

        ");



        $stmt->execute([
            $id
        ]);



        header("Location: /ecommerce-shop/public/admin/orders");

        exit;

    }


}

?>