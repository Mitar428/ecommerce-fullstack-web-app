<?php

namespace Ecommerce\Shop\Controllers;


use Ecommerce\Shop\Core\Controller;
use Ecommerce\Shop\Core\Session;
use Ecommerce\Shop\Core\Database;



class ReviewController extends Controller
{


 
 public function store($id)
{


    if(!Session::get('user_id')){

        header("Location: /ecommerce-shop/public/login");
        exit;

    }


    $db = Database::getInstance()->getConnection();


    $korisnik = Session::get('user_id');


    $ocena = $_POST['ocena'];

    $komentar = $_POST['komentar'];



    $stmt = $db->prepare("

        INSERT INTO reviews
        (
            korisnik_id,
            proizvod_id,
            ocena,
            komentar
        )

        VALUES
        (?,?,?,?)

    ");



    $stmt->execute([

        $korisnik,
        $id,
        $ocena,
        $komentar

    ]);



    header(
        "Location: /ecommerce-shop/public/proizvod/".$id
    );

    exit;

}






    /*
        Prikaz recenzija proizvoda
    */


    public function productReviews($id)
    {


        $db = Database::getInstance()->getConnection();



        $stmt = $db->prepare("

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



            WHERE r.proizvod_id=?


            ORDER BY r.review_id DESC


        ");



        $stmt->execute([$id]);



        return $stmt->fetchAll(\PDO::FETCH_ASSOC);


    }






    /*
        Brisanje recenzije (ADMIN)
    */


    public function delete($id)
    {


        if(!Session::get('user_id')){

            header("Location: /ecommerce-shop/public/login");
            exit;

        }



        $db = Database::getInstance()->getConnection();



        $stmt = $db->prepare("

            DELETE FROM reviews

            WHERE review_id=?

        ");



        $stmt->execute([$id]);




        header("Location: /ecommerce-shop/public/admin/reviews");

        exit;


    }




}

?>