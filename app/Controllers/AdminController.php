<?php

namespace Ecommerce\Shop\Controllers;

use Ecommerce\Shop\Core\Session;
use Ecommerce\Shop\Core\Controller;
use Ecommerce\Shop\Models\User;
use Ecommerce\Shop\Models\Products;
use Ecommerce\Shop\Models\Orders;
use Ecommerce\Shop\Models\Category;
use Ecommerce\Shop\Core\Database;


class AdminController extends Controller
{
    public function index(): void
{
    $userModel = new User();
    $productModel = new Products();
    $orderModel = new Orders();
    $categoryModel = new Category();

    $data = [
        'users_count' => $userModel->count(),
        'products_count' => $productModel->count(),
        'orders_count' => $orderModel->count(),
        'categories_count' => $categoryModel->count(),
        
    ];




    $db = Database::getInstance()->getConnection();


    $stmt = $db->prepare("
        SELECT SUM(ukupna_cena) 
        FROM porudzbine
    ");

    $stmt->execute();

    $data['revenue'] = $stmt->fetchColumn() ?? 0;



    
    $stmt = $db->prepare("
        SELECT
            porudzbina_id AS id,
            ime AS customer_name,
            telefon AS email,
            status,
            ukupna_cena AS total
        FROM porudzbine
        ORDER BY datum DESC
        LIMIT 1
    ");

    $stmt->execute();

    $data['orders'] = $stmt->fetchAll(\PDO::FETCH_ASSOC);



    
    $stmt = $db->prepare("
        SELECT
            proizvod_id AS id,
            p.naziv AS name,
            p.cena AS price,
            k.naziv AS category
            FROM proizvodi p
            LEFT JOIN kategorije k
            ON p.kategorija_id = k.kategorija_id
            ORDER BY p.proizvod_id DESC
            LIMIT 1
    ");

    $stmt->execute();

    $data['latest_products'] = $stmt->fetchAll(\PDO::FETCH_ASSOC);


    echo $this->twig->render(
        'Admin/dashboard.html.twig',
        $data
    );}



    public function logout()
    {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }

        session_unset();
        session_destroy();

        header("Location: /login");
        exit;
    }




    public function createadmin()
    {
        echo $this->twig->render(
            'Admin/createadmin.html.twig'
        );
    }





    public function storeadmin()
    {

        $ime = trim($_POST['ime']);
        $prezime = trim($_POST['prezime']);
        $email = trim($_POST['email']);
        $lozinka = $_POST['lozinka'];
        $telefon = trim($_POST['telefon']);
        $adresa = trim($_POST['adresa']);
        $grad = trim($_POST['grad']);
        $postanskiBroj = trim($_POST['postanski_broj']);


        if(
            empty($ime) ||
            empty($prezime) ||
            empty($email) ||
            empty($lozinka)
        ){
            die('Sva obavezna polja moraju biti popunjena');
        }



        $stmt = $this->db->prepare("
            INSERT INTO korisnici(
                ime,
                prezime,
                email,
                lozinka,
                telefon,
                adresa,
                grad,
                postanski_broj,
                uloga
            )
            VALUES(?, ?, ?, ?, ?, ?, ?, ?, 'ADMIN')
        ");



        $stmt->execute([
            $ime,
            $prezime,
            $email,
            password_hash($lozinka, PASSWORD_DEFAULT),
            $telefon,
            $adresa,
            $grad,
            $postanskiBroj
        ]);



        header("Location:" . APP_URL . "/admin");
        exit;

    }


    public function users()
    {

        $stmt = $this->db->prepare("
            SELECT * 
            FROM korisnici 
            ORDER BY korisnik_id DESC
        ");


        $stmt->execute();


        $users = $stmt->fetchAll(\PDO::FETCH_ASSOC);



        echo $this->twig->render(
            'Admin/users.html.twig',
            [
                'users' => $users
            ]
        );

    }


    public function delete($id)
    {

        $stmt = $this->db->prepare("
            DELETE FROM korisnici 
            WHERE korisnik_id=?
        ");


        $stmt->execute([$id]);



        header("Location:" . APP_URL . "/admin/users");
        exit;

    }


    public function edit($id)
    {

        $stmt = $this->db->prepare("
            SELECT *
            FROM korisnici
            WHERE korisnik_id=?
        ");



        $stmt->execute([$id]);



        $user = $stmt->fetch(\PDO::FETCH_ASSOC);



        if(!$user){

            die("Korisnik nije pronadjen");

        }




        echo $this->twig->render(
            'Admin/user_edit.html.twig',
            [
                'user'=>$user
            ]
        );

    }

    public function edituser($id)
    {

        $stmt = $this->db->prepare("
            UPDATE korisnici
            SET
                ime=?,
                prezime=?,
                email=?,
                telefon=?,
                adresa=?,
                grad=?,
                postanski_broj=?,
                uloga=?
            WHERE korisnik_id=?
        ");



        $stmt->execute([

            $_POST['ime'],
            $_POST['prezime'],
            $_POST['email'],
            $_POST['telefon'],
            $_POST['adresa'],
            $_POST['grad'],
            $_POST['postanski_broj'],
            $_POST['uloga'],
            $id

        ]);



        header("Location:" . APP_URL . "/admin/users");
        exit;

    }

    public function orders()


{  

    $db = Database::getInstance()->getConnection();


    $stmt = $db->prepare("

     SELECT

        p.porudzbina_id,
        p.status,
        p.datum,
        p.ukupna_cena,

        sp.kolicina,
        sp.cena,
        p.ime,
        p.prezime,
        p.adresa,
        p.grad,
        p.telefon,

        pr.naziv,
        pr.slika


    FROM porudzbine p


    JOIN stavke_porudzbine sp
    ON p.porudzbina_id = sp.porudzbina_id


    JOIN proizvodi pr
    ON sp.proizvod_id = pr.proizvod_id


    ORDER BY p.datum DESC
    

    ");
    


    $stmt->execute();


    $orders = $stmt->fetchAll(\PDO::FETCH_ASSOC);

  

    echo $this->twig->render(
        'admin/orders.html.twig',
        [
            'orders' => $orders
        ]
    );

}



public function products()
{

    $stmt = $this->db->prepare("
        SELECT *
        FROM proizvodi
        ORDER BY proizvod_id DESC
    ");

    $stmt->execute();


    $products = $stmt->fetchAll(\PDO::FETCH_ASSOC);



    echo $this->twig->render(
        'Admin/products.html.twig',
        [
            'products'=>$products
        ]
    );

}

public function categories()
{

    $db = Database::getInstance()->getConnection();


    $stmt = $db->prepare("
        SELECT
            kategorija_id,
            naziv,
            opis
        FROM kategorije
        ORDER BY kategorija_id ASC
    ");


    $stmt->execute();


    $categories = $stmt->fetchAll();


    echo $this->twig->render(
        'Categories/categories.html.twig',
        [
            'categories'=>$categories
        ]
    );

}





}

?>