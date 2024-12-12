<?php
session_start();
header('Content-Type: application/json');
require('modal.php');

$action = $_POST['action'] ?? $_GET['action'] ?? '';

switch ($action) {
    case 'login':
        echo json_encode(adminLogin());
        break;

    case 'addProduct':
        echo json_encode(addProduct());
        break;

    case 'getProducts':
        echo json_encode(getProducts());
        break;
    
    case 'getProductByName':
        echo json_encode(getProductByName($_GET['name']));
        break;

    case 'deleteProduct':
        echo json_encode(deleteProduct());
        break;

    case 'modifyProduct':
        echo json_encode(modifyProduct());
        break;
    
    case 'searchProduct':
        $term = $_POST['term'] ?? '';
        echo json_encode(search_product($term));
        break;
    
    case 'validateUser':
        $username = $_POST['username'] ?? '';
        $password = $_POST['password'] ?? '';
        echo json_encode(['valid' => validateUser($username, $password)]);
        break;
    
    case 'checkUserExists':
        $username = $_POST['username'] ?? '';
        echo json_encode(['exists' => checkUserExists($username)]);
        break;

    default:
        echo json_encode(['error' => 'Invalid action']);
}
?>
