<?php
namespace Ecommerce\Shop\Controllers;

use Ecommerce\Shop\Core\Controller;
Class HomeController extends Controller{


public function index()
{
    $limit = 20;
    $page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
    if($page < 1){

        $page = 4;

    }
    $offset = ($page - 1) * $limit;
    $stmt = $this->db->query("
        SELECT COUNT(*) 
        FROM proizvodi
    ");

    $totalProducts = $stmt->fetchColumn();

    $totalPages = ceil($totalProducts / $limit);

    $stmt = $this->db->prepare("

        SELECT
            p.*,
            b.naziv AS brend,
            k.naziv AS kategorija

        FROM proizvodi p

        LEFT JOIN brendovi b
            ON p.brend_id = b.brend_id

        LEFT JOIN kategorije k
            ON p.kategorija_id = k.kategorija_id

        ORDER BY p.proizvod_id DESC

        LIMIT :limit OFFSET :offset

    ");
    $stmt->bindValue(
        ':limit',
        $limit,
        \PDO::PARAM_INT
    );


    $stmt->bindValue(
        ':offset',
        $offset,
        \PDO::PARAM_INT
    );
    $stmt->execute();



    $products = $stmt->fetchAll(\PDO::FETCH_ASSOC);

    echo $this->twig->render(

        'products/home.html.twig',

        [

            'products' => $products,

            'page' => $page,

            'totalPages' => $totalPages

        ]

    );


}

public function about()
{
   echo $this->twig->render('account/about.html.twig');
}
}
?>