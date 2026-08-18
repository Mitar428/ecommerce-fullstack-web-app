<?php

namespace Ecommerce\Shop\Controllers;

use Ecommerce\Shop\Core\Controller;
use Ecommerce\Shop\Core\Database;

class CategoryController extends Controller
{

    public function categories()
    {
        $db = Database::getInstance()->getConnection();

        $stmt = $db->query("
            SELECT * 
            FROM kategorije 
            ORDER BY naziv
        ");

        $categories = $stmt->fetchAll();

        echo $this->twig->render('categories/Categories.html.twig', [
            'categories' => $categories
        ]);
    }


    public function show($id)
    {

        $db = Database::getInstance()->getConnection();


        $limit = 3;

        $page = $_GET['page'] ?? 1;

        $page = max(1,(int)$page);


        $offset = ($page - 1) * $limit;



        // broj proizvoda

        $stmt = $db->prepare("
            SELECT COUNT(*) 
            FROM proizvodi 
            WHERE kategorija_id=?
        ");

        $stmt->execute([$id]);


        $total = $stmt->fetchColumn();

        $totalPages = ceil($total / $limit);




        $stmt = $db->prepare("
            SELECT 
                p.*,
                b.naziv AS brend

            FROM proizvodi p

            LEFT JOIN brendovi b
            ON p.brend_id = b.brend_id

            WHERE p.kategorija_id = ?

            LIMIT $limit OFFSET $offset
        ");


        $stmt->execute([$id]);


        $products = $stmt->fetchAll();



        echo $this->twig->render('Categories/CategoriesShow.html.twig', [

            'products' => $products,

            'page' => $page,

            'totalPages' => $totalPages,

            'categoryId' => $id

        ]);

    }

}