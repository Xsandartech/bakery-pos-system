<?php
include_once ($_SERVER['DOCUMENT_ROOT'].'/bread_factory/pos/dirs.php');
include_once (DB_PATH."connection.php");

class POS {
    private $pdo;

    public function __construct(){
        $db = new Database();
        $this->pdo = $db->db_connect();
    }

    public function get_pos_products() {
        $query = $this->pdo->prepare("SELECT * FROM pos_products WHERE visible = 1 ORDER BY description ASC");
        $query->execute();
        $result = $query->fetchAll(PDO::FETCH_ASSOC);
        return $result;
    }

    public function get_pos_promos() {
        $query = $this->pdo->prepare("SELECT * FROM pos_promos WHERE visible = 1 ORDER BY description ASC");
        $query->execute();
        $result = $query->fetchAll(PDO::FETCH_ASSOC);
        return $result;
    }

    public function get_product_data($id) {
        $query = $this->pdo->prepare("SELECT * from pos_products WHERE id = ?");
        $query->execute([$id]);
        $result = $query->fetch(PDO::FETCH_OBJ);
        return $result;
    }

    public function get_promo_data($id) {
        $query = $this->pdo->prepare("SELECT * from pos_promos WHERE id = ?");
        $query->execute([$id]);
        $result = $query->fetch(PDO::FETCH_OBJ);
        return $result;
    }

    public function finish_sale($cart, $payment_method) {
        date_default_timezone_set("America/Mexico_City");
        $now = date("Y-m-d H:i:s");

        //calc total sale
        $total = 0;
        foreach($cart as $item => $value) {
            //get product/promo data
            $id = $value['id'];
            $type = $value['type'];
            $quantity = $value['quantity'];
            $price = 0;
            $subtotal = 0;

            if($type === "product"){
                $product_data = $this->get_product_data($id);
                $price = $product_data->price;

                $subtotal = $price * $quantity;
            } else if ($type === "promo"){
                $promo_data = $this->get_promo_data($id);
                $pieces = $promo_data->pieces;
                $price = $promo_data->price;
                $real_price = $pieces * $price;

                $subtotal = $quantity * $real_price;
            }
            $total += $subtotal;
        }

        //register sale :)
        session_start();
        $user_id = $_SESSION['user_id'];

        $query = $this->pdo->prepare("INSERT INTO pos_sales (resp, datetime, payment_method, total) VALUES (?, ?, ?, ?)");
        $query->execute([$user_id, $now, $payment_method, $total]);

        //get sale id
        $query = $this->pdo->prepare("SELECT id FROM pos_sales ORDER BY id DESC LIMIT 1");
        $query->execute();
        $result = $query->fetch(PDO::FETCH_OBJ);
        $id_sale = $result === false ? 1 : $result->id;

        //register sold products/promos
        try {
            $this->pdo->beginTransaction();
            
            $query_product = $this->pdo->prepare("INSERT INTO pos_sold_products 
            (id_pos_sale, id_pos_product, quantity, cost, price) VALUES (?, ?, ?, ?, ?)");

            $query_promo = $this->pdo->prepare("INSERT INTO pos_sold_promos 
            (id_pos_sale, id_pos_promo, quantity, pieces, cost, price) VALUES (?, ?, ?, ?, ?, ?)");

            $query_stock = $this->pdo->prepare("UPDATE pos_products SET stock = stock - ? WHERE id = ?");

            foreach($cart as $item => $value) {
                //get product/promo data
                $id = $value['id'];
                $quantity = $value['quantity'];
                $type = $value['type'];
    
                if($type === "product"){
                    $product_data = $this->get_product_data($id);
                    $cost = $product_data->cost;
                    $price = $product_data->price;

                    $query_product->execute([$id_sale, $id, $quantity, $cost, $price]);
                    $query_stock->execute([$quantity, $id]);
                } else if ($type === "promo"){
                    $promo_data = $this->get_promo_data($id);
                    $pieces = $promo_data->pieces;
                    $price = $promo_data->price;

                    $product_id = $promo_data->id_pos_product;
                    $product_data = $this->get_product_data($product_id);
                    $cost = $product_data->cost;

                    $query_promo->execute([$id_sale, $id, $quantity, $pieces, $cost, $price]);
                    $query_stock->execute([$quantity, $product_id]);
                }
            }

            $result = $this->pdo->commit();
            if($result === true){
                return array('status' => "ok", "id_sale" => $id_sale);
            } else return array('status' => "error");

        } catch (\PDOException $e) {
	        $this->pdo->rollBack();
	        die($e->getMessage());
            return array('status' => "error");
        }
    }

    public function get_sale_data($id) {
        $query = $this->pdo->prepare("SELECT resp, DATE_FORMAT(datetime, '%d/%m/%Y %h:%i:%s%p') AS datetime, total FROM pos_sales WHERE id = ?");
        $query->execute([$id]);
        $result = $query->fetchAll(PDO::FETCH_OBJ);
        return $result;
    }

    public function get_sales($datetime) {
        //%d/%m/%Y %h:%i:%s%p
        $query = $this->pdo->prepare("SELECT pos_sales.id, users.display_name AS resp, pos_sales.total, pos_sales.payment_method, DATE_FORMAT(pos_sales.datetime, '%h:%i:%s%p') AS datetime FROM pos_sales 
        INNER JOIN users ON pos_sales.resp = users.id WHERE datetime >= ? ORDER BY pos_sales.id DESC");
        $result = $query->execute([$datetime]);
        $query->execute();
        $result = $query->fetchAll(PDO::FETCH_OBJ);    
        return $result;
    }

    public function get_sale_products($sale_id) {
        $query = $this->pdo->prepare("SELECT pos_sold_products.quantity, pos_sold_products.id_pos_product AS id, pos_sold_products.price, pos_sold_products.cost, pos_products.description
        FROM pos_sold_products INNER JOIN pos_products
        ON pos_sold_products.id_pos_product = pos_products.id
        WHERE pos_sold_products.id_pos_sale = ?");
        $query->execute([$sale_id]);
        $result = $query->fetchAll(PDO::FETCH_OBJ);
        return $result;
    }

    public function get_sale_promos($sale_id) {
        $query = $this->pdo->prepare("SELECT pos_sold_promos.quantity, pos_sold_promos.pieces, pos_sold_promos.id_pos_promo AS id, pos_sold_promos.price, pos_sold_promos.cost, pos_promos.description
        FROM pos_sold_promos INNER JOIN pos_promos
        ON pos_sold_promos.id_pos_promo = pos_promos.id
        WHERE pos_sold_promos.id_pos_sale = ?");
        $query->execute([$sale_id]);
        $result = $query->fetchAll(PDO::FETCH_OBJ);
        return $result;
    }

    public function get_sales_report() {
        $products = $this->get_sale_products();
        $promos = $this->get_sale_promos();
        return array("products" => $products, "promos" => $promos);
    }

    public function get_ticket_data($id) {
        $sale_data = $this->get_sale_data($id);
        $sale_products = $this->get_sale_products($id);
        $sale_promos = $this->get_sale_promos($id);

        $items = array();

        foreach ($sale_products as $sale_product) {
            $description = $sale_product->description;
            $quantity = $sale_product->quantity;
            $price = $sale_product->price;
            $subtotal = $quantity*$price;
            $items[] = array(
                "description"=>$description,
                "quantity"=>$quantity,
                "price" =>$price,
                "subtotal" => $subtotal);
        }

        foreach ($sale_promos as $sale_promo) {
            $description = $sale_promo->description;
            $quantity = $sale_promo->quantity;
            $pieces = $sale_promo->pieces;
            $price = $sale_promo->price;
            $subtotal = $quantity*$price*$pieces;
            $items[] = array(
                "description"=>$description,
                "quantity"=>$quantity,
                "price" =>$price,
                "subtotal" => $subtotal);
        }

        //$total = new item('Total', number_format($sale_total, 2), true);

        return array("sale_data" => $sale_data, "items" => $items);

    }

}

?>