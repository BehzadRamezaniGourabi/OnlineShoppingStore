<?php
// Database connection with mysqli
$conn = new mysqli('localhost', 'root', 'mysql', 'shopping_site');

if ($conn->connect_error) {
    die(json_encode(['error' => 'Database connection failed: ' . $conn->connect_error]));
}

// Check if a user exists
function checkUserExists($username) {
    global $conn;
    $stmt = $conn->prepare("SELECT * FROM users WHERE username = ?");
    $stmt->bind_param("s", $username);
    $stmt->execute();
    $result = $stmt->get_result();
    return $result->num_rows > 0;
}

// Validate user login
function validateUser($username, $password) {
    global $conn;
    $stmt = $conn->prepare("SELECT * FROM users WHERE username = ? AND password = ?");
    $stmt->bind_param("ss", $username, $password);
    $stmt->execute();
    $stmt->store_result();
    return $stmt->num_rows > 0;
}

// Admin login
function adminLogin() {
    global $conn;
    $username = $_POST['username'] ?? '';
    $password = $_POST['password'] ?? '';

    // Prepare the query to fetch the user by username
    $stmt = $conn->prepare("SELECT * FROM users WHERE username = ?");
    $stmt->bind_param("s", $username);
    $stmt->execute();
    $result = $stmt->get_result();

    // Check if the user exists
    if ($result->num_rows > 0) {
        $user = $result->fetch_assoc(); // Fetch the user record

        // Directly compare the entered password with the stored password
        if ($password === $user['password']) {
            $_SESSION['admin_logged_in'] = true; // Set the session variable for admin login
            echo json_encode(['success' => true, 'message' => 'Login successful']);
        } else {
            echo json_encode(['success' => false, 'message' => 'Invalid password']);
        }
    } else {
        echo json_encode(['success' => false, 'message' => 'Invalid username']);
    }
    exit(); // Ensure the script stops after sending a response
}

// Add a new product
function addProduct() {
    global $conn;

    // Get form input data
    $name = $_POST['names'] ?? '';
    $price = $_POST['price'] ?? 0;
    $stock = $_POST['stock'] ?? 0;

    // Validate input
    if (empty($name) || empty($price) || empty($stock)) {
        return ["success" => false, "message" => "All fields are required"];
    }

    // Check if an image is uploaded
    if (isset($_FILES['image']) && $_FILES['image']['error'] == 0) {
        // Define allowed file extensions
        $allowedExtensions = ['jpg', 'jpeg', 'png', 'gif'];

        // Get the uploaded file's extension
        $fileExtension = pathinfo($_FILES['image']['name'], PATHINFO_EXTENSION);

        // Check if the file extension is valid
        if (!in_array(strtolower($fileExtension), $allowedExtensions)) {
            return ["success" => false, "message" => "Invalid image format. Only JPG, JPEG, PNG, and GIF are allowed."];
        }

        // Define the upload directory
        $uploadDir = 'uploads/';
        // Generate a unique filename for the image to avoid conflicts
        $imageName = uniqid('product_', true) . '.' . $fileExtension;
        $imagePath = $uploadDir . $imageName;

        // Move the uploaded file to the server
        if (!move_uploaded_file($_FILES['image']['tmp_name'], $imagePath)) {
            return ["success" => false, "message" => "Failed to upload the image."];
        }
    } else {
        // If no image is uploaded, set the image to NULL
        $imagePath = NULL;
    }

    // Prepare the SQL query
    $stmt = $conn->prepare("INSERT INTO products (names, price, stock, image) VALUES (?, ?, ?, ?)");
    $stmt->bind_param("siis", $name, $price, $stock, $imagePath);

    // Execute the query and return the result
    return $stmt->execute() ? ["success" => true, "message" => "Product added successfully"] : ["error" => $stmt->error];
}

// Fetch all products
function getProducts() {
    global $conn;
    $result = $conn->query("SELECT * FROM products");
    return $result ? $result->fetch_all(MYSQLI_ASSOC) : [];
}

// Delete a product
function deleteProduct() {
    global $conn;
    $productId = $_POST['id'] ?? 0;

    $stmt = $conn->prepare("DELETE FROM products WHERE id = ?");
    $stmt->bind_param("i", $productId);
    return $stmt->execute() ? ["success" => true, "message" => "Product deleted successfully"] : ["error" => $stmt->error];
}

// Fetch product by name
function getProductByName($name) {
    global $conn;
    $stmt = $conn->prepare("SELECT * FROM products WHERE names = ?");
    $stmt->bind_param("s", $name);
    $stmt->execute();
    $result = $stmt->get_result();
    if ($result->num_rows > 0) {
        return ['success' => true, 'data' => $result->fetch_assoc()];
    } else {
        return ['success' => false, 'message' => 'Product not found'];
    }
}

// Modify product
function modifyProduct() {
    global $conn;

    $productId = $_POST['id'];
    $productName = $_POST['names'];
    $productPrice = $_POST['price'];
    $productStock = $_POST['stock'];

    // Update query
    $stmt = $conn->prepare("UPDATE products SET names = ?, price = ?, stock = ? WHERE id = ?");
    $stmt->bind_param("sdii", $productName, $productPrice, $productStock, $productId);

    return $stmt->execute() ? ["success" => true, "message" => "Product updated successfully"] : ["error" => $stmt->error];
}

// Search products by name
function search_product($term) {
    global $conn;
    $search_query = "%" . $conn->real_escape_string($term) . "%";
    $stmt = $conn->prepare("SELECT * FROM products WHERE name LIKE ?");
    $stmt->bind_param("s", $search_query);
    $stmt->execute();
    $result = $stmt->get_result();
    $products = $result->fetch_all(MYSQLI_ASSOC);
    return count($products) > 0 ? $products : ["message" => "Product not found"];
}


// Update product details
function updateProduct() {
    global $conn;
    $productId = $_POST['id'] ?? 0;
    $name = $_POST['names'] ?? '';
    $price = $_POST['price'] ?? '';
    $stock = $_POST['stock'] ?? '';
    $image = $_FILES['image']['name'] ?? '';

    if ($image) {
        $target_dir = "uploads/";
        $target_file = $target_dir . basename($image);
        move_uploaded_file($_FILES["image"]["tmp_name"], $target_file);
    } else {
        $image = $_POST['existing_image'];
    }

    $stmt = $conn->prepare("UPDATE products SET name = ?, price = ?, stock = ?, image = ? WHERE id = ?");
    $stmt->bind_param("siisi", $name, $price, $stock, $image, $productId);
    return $stmt->execute() ? ["success" => true, "message" => "Product updated successfully"] : ["error" => $stmt->error];
}

?>