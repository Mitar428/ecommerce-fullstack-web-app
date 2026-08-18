<?php

namespace Ecommerce\Shop\Controllers;

use Ecommerce\Shop\Core\Controller;
use Ecommerce\Shop\Core\Session;
use Ecommerce\Shop\Core\Database;


class ProductController extends Controller
{


    public function show($id)
    {


        $db = Database::getInstance()->getConnection();



        // ==========================
        // PROIZVOD
        // ==========================


        $stmt = $db->prepare("

            SELECT

                p.*,

                b.naziv AS brend,

                k.naziv AS kategorija


            FROM proizvodi p


            LEFT JOIN brendovi b

            ON p.brend_id = b.brend_id


            LEFT JOIN kategorije k

            ON p.kategorija_id = k.kategorija_id


            WHERE p.proizvod_id = ?

        ");



        $stmt->execute([$id]);



        $product = $stmt->fetch(\PDO::FETCH_ASSOC);



        if(!$product){

            die("Proizvod ne postoji");

        }





        // ==========================
        // RECENZIJE
        // ==========================


        $reviewStmt = $db->prepare("

            SELECT


                r.review_id,

                r.ocena,

                r.komentar,

                r.datum,


                k.ime,

                k.prezime



            FROM reviews r



            JOIN korisnici k


            ON r.korisnik_id = k.korisnik_id



            WHERE r.proizvod_id = ?



            ORDER BY r.review_id DESC


        ");



        $reviewStmt->execute([$id]);



        $reviews = $reviewStmt->fetchAll(\PDO::FETCH_ASSOC);







        // ==========================
        // RENDER
        // ==========================


        echo $this->twig->render(

            'products/show.html.twig',

            [

                'product'=>$product,

                'reviews'=>$reviews

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

            DELETE FROM proizvodi

            WHERE proizvod_id = ?

        ");



        $stmt->execute([$id]);




        header(

            "Location: /ecommerce-shop/public/admin/products"

        );


        exit;


    }


}

?>