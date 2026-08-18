<?php

namespace Ecommerce\Shop\Controllers;

use Ecommerce\Shop\Core\Database;
use Ecommerce\Shop\Core\Controller;

class SettingsController extends Controller
{

    public function index()
    {
        $db = Database::getInstance()->getConnection();


        $stmt = $db->prepare(
            "SELECT * FROM shop_settings WHERE id = 1"
        );

        $stmt->execute();


        $settings = $stmt->fetch();


        echo $this->twig->render(
            'admin/Settings.html.twig',
            [
                'settings' => $settings
            ]
        );
    }



    public function update()
    {
        $db = Database::getInstance()->getConnection();


        $sql = "UPDATE shop_settings SET
                show_sales_chart = ?,
                show_latest_orders = ?,
                order_notification = ?,
                stock_notification = ?,
                login_attempts = ?
                WHERE id = 1";


        $stmt = $db->prepare($sql);


        $stmt->execute([

            isset($_POST['show_sales_chart']) ? 1 : 0,

            isset($_POST['show_latest_orders']) ? 1 : 0,

            isset($_POST['order_notification']) ? 1 : 0,

            isset($_POST['stock_notification']) ? 1 : 0,

            $_POST['login_attempts']

        ]);



        header(
            "Location: /ecommerce-shop/public/admin/settings"
        );

        exit;
    }

}