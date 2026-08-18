<?php

namespace Ecommerce\Shop\Controllers;

use Ecommerce\Shop\Core\Controller;
use Ecommerce\Shop\Core\Database;
use PDO;

class AddProductController extends Controller
{

    public function __construct()
    {
        $db = Database::getInstance()->getConnection();
        parent::__construct($db);
    }


    public function index()
    {
        $kategorije = $this->db->query("SELECT * FROM kategorije ORDER BY naziv")
            ->fetchAll(PDO::FETCH_ASSOC);

        $brendovi = $this->db->query("SELECT * FROM brendovi ORDER BY naziv")
            ->fetchAll(PDO::FETCH_ASSOC);

        echo $this->twig->render('admin/AddProduct.html.twig', [
            'kategorije' => $kategorije,
            'brendovi' => $brendovi
        ]);
    }


    public function store()
    {
        $naziv = trim($_POST['naziv']);
        $opis = trim($_POST['opis']);
        $cena = $_POST['cena'];
        $stanje = $_POST['stanje'];
        $kategorija = $_POST['kategorija_id'];
        $brend = $_POST['brend_id'];

        $slika = '';

     


        if(isset($_FILES['slika']) && $_FILES['slika']['error'] == 0)
        {

            $folder = __DIR__.'/../../public/assets/images/products/';


            if(!is_dir($folder)){
                mkdir($folder,0777,true);
            }


            $ext = strtolower(
                pathinfo($_FILES['slika']['name'], PATHINFO_EXTENSION)
            );


            $slika = uniqid('product_').'.'.$ext;


            move_uploaded_file(
                $_FILES['slika']['tmp_name'],
                $folder.$slika
            );

        }



        $stmt = $this->db->prepare(
            "INSERT INTO proizvodi
            (
                naziv,
                opis,
                cena,
                stanje,
                slika,
                kategorija_id,
                brend_id
            )
            VALUES
            (
                ?,?,?,?,?,?,?
            )"
        );


        $stmt->execute([

            $naziv,
            $opis,
            $cena,
            $stanje,
            $slika,
            $kategorija,
            $brend

        ]);


        header("Location: /ecommerce-shop/public/");
        exit;

    }

}