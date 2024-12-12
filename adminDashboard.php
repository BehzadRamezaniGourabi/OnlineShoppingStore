<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard</title>
    <link rel="stylesheet" href="style.css">
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
</head>
<body>
    <?php include('navbar.php'); ?>
    <section>
    <div>
        <h1>Admin Dashboard</h1>
        <h2>Add Product</h2>
        <form id="add-product-form" enctype="multipart/form-data">
            <input type="text" id="product-names" name="product-names" placeholder="Product Name" required>
            <input type="number" id="product-price" name="product-price" placeholder="Price" required step="0.01">
            <input type="number" id="product-stock" name="product-stock" placeholder="Stock" required>
            <input type="file" id="product-image" name="product-image" required>
            <button type="button" onclick="addProduct()">Add New Product</button>
        </form>
        <p id="add-product-message"></p>
    </div>

    </section>

    <section>
    <div id="modify-modal" style="display: none;">
        <div class="modal-content">
            <h2>Modify Product</h2>
            <form id="modify-product-form">
                <input type="hidden" id="modify-product-id">
                <input type="text" id="modify-product-name" placeholder="Product Name" required>
                <input type="number" id="modify-product-price" placeholder="Price" required step="0.01">
                <input type="number" id="modify-product-stock" placeholder="Stock" required>
                <button type="submit">Save Changes</button>
            </form>
            <button type="button" id="cancel-modify-btn">Cancel</button>
        </div>
    </div>
    </section>

    <div>
        <h2>Product List</h2>
        <div id="product-list"></div>
    </div>

<script>
$(document).ready(function () {
    loadProducts();

    // Function to add a new product
    window.addProduct = function () {
        let formData = new FormData();
        formData.append('action', 'addProduct');
        formData.append('names', $('#product-names').val());
        formData.append('price', $('#product-price').val());
        formData.append('stock', $('#product-stock').val());
        formData.append('image', $('#product-image')[0].files[0]);

        $.ajax({
            url: 'controller.php',
            type: 'POST',
            data: formData,
            contentType: false,
            processData: false,
            success: function(response) {
                $('#add-product-message').text(response.message);
                if (response.success) {
                    loadProducts(); // Refresh the product list
                }
            },
            dataType: 'json'
        });
    }

    // Load product list
    function loadProducts() {
        $.get('controller.php', { action: 'getProducts' }, function (response) {
            if (response.error) {
                $('#product-list').html(response.error);
            } else {
                let productHtml = '<ul>';
                response.forEach(function (product) {
                    productHtml += `
                        <li>
                            ${product.names} - $${product.price} - Stock: ${product.stock}
                            <button onclick="deleteProduct(${product.id})">Delete</button>
                            <button onclick="showModifyModal('${product.names}')">Modify</button>
                        </li>`;
                });
                productHtml += '</ul>';
                $('#product-list').html(productHtml);
            }
        }, 'json');
    }

    // Function to delete product
    window.deleteProduct = function(productId) {
        if (confirm('Are you sure you want to delete this product?')) {
            $.post('controller.php', {
                action: 'deleteProduct',
                id: productId
            }, function(response) {
                alert(response.message);
                if (response.success) {
                    loadProducts(); // Refresh the product list
                }
            }, 'json');
        }
    };

    // Function to show the modify modal with data fetched from the server
    window.showModifyModal = function(productName) {
        // Fetch product details using AJAX
        $.get('controller.php', { action: 'getProductByName', name: productName }, function (response) {
            if (response.success) {
                // Populate modal fields with product data
                $('#modify-product-id').val(response.data.id);
                $('#modify-product-name').val(response.data.names);
                $('#modify-product-price').val(response.data.price);
                $('#modify-product-stock').val(response.data.stock);
                $('#modify-modal').show();
            } else {
                alert(response.message || 'Failed to fetch product details.');
            }
        }, 'json').fail(function (xhr, status, error) {
            console.error('Error:', error);
            alert('Failed to communicate with the server.');
        });
    }

    // Close modal function
    window.closeModifyModal = function() {
        $('#modify-modal').hide();
    }

    // Cancel button event listener
    $('#cancel-modify-btn').on('click', function() {
        closeModifyModal();
    });

    // Event listener for form submission
    $('#modify-product-form').on('submit', function (e) {
        e.preventDefault();

        const productId = $('#modify-product-id').val();
        const productName = $('#modify-product-name').val();
        const productPrice = $('#modify-product-price').val();
        const productStock = $('#modify-product-stock').val();

        // Send updated data to the server
        $.post('controller.php', {
            action: 'modifyProduct',
            id: productId,
            names: productName,
            price: productPrice,
            stock: productStock
        }, function (response) {
            alert(response.message);
            if (response.success) {
                closeModifyModal();
                loadProducts(); // Refresh the product list
            }
        }, 'json');
    });

});

</script>
</body>
</html>
