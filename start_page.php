<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Your Website - Shop</title>
    <link rel="stylesheet" href="style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
</head>
<body>
    <?php include('navbar.php'); ?>

    <section id="shop">
        <!-- Filters Sidebar -->
        <aside id="filters">
            <h2>Filters</h2>
            <div>
                <label for="category">Category</label>
                <select id="category">
                    <option value="all">All</option>
                    <option value="electronics">Electronics</option>
                    <option value="clothing">Clothing</option>
                    <option value="accessories">Accessories</option>
                </select>
            </div>
            <div>
                <label for="price">Price Range</label>
                <input type="range" id="price" min="0" max="1000" step="0">
                <span id="price-value">0</span>$
            </div>
        </aside>

        <!-- Main Product Section -->
        <main id="product-list"></div>

        </main>

        <!-- Shopping Cart -->
        <div id="cart">
            <button id="cart-btn">
                <i class="fas fa-shopping-cart"></i> 
                Cart (<span id="cart-count">0</span>)
            </button>
        </div>
    </section>

    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.6.0/jquery.min.js"></script>
    
    <script>
        $(document).ready(function() {
            // Load products with AJAX
            $.get('controller.php', { action: 'getProducts' }, function (data) {
                data.forEach(product => {
                    $('#product-list').append(`
                        <div class="product-card">
                            <h3 class="product-name">${product.names}</h3>
                            <img class="product-image" src="${product.image}" alt="${product.name}">
                            <p class="product-price">$${product.price}</p>
                            <p class="product-stock">In stock: ${product.stock}</p>
                            <button class="add-to-cart" data-id="${product.id}">Add to Cart</button>
                        </div>
                    `);
                });
            });

            // Dropdown menu navigation
            $('#menu').on('change', function () {
                const selectedPage = $(this).val();
            
                if (selectedPage === 'admin') {
                    window.location.href = 'admin.php';
                } else if (selectedPage === 'about') {
                    window.location.href = 'about.php';
                } else if (selectedPage === 'products') {
                    window.location.href = 'start_page.php';
                }
            });

            // Cart functionality
            let cart = [];
            const cartCount = $('#cart-count');

            // Update cart count
            function updateCart() {
                cartCount.text(cart.length);
            }

            // Add product to cart
            $(document).on('click', '.add-to-cart', function () {
                const productId = $(this).data('id');
                cart.push(productId);
                updateCart();
            });

            $('#cart-btn').on('click', function () {
                alert('Your cart has ' + cart.length + ' items');
            });

            // Function to load products
            function loadProducts() {
                $.ajax({
                    url: 'controller.php',
                    type: 'GET',
                    data: { action: 'getProducts' },
                    success: function(response) {
                        let productHtml = '';
                        response.forEach(product => {
                            productHtml += `
                                <div class="product-card">
                                    <img src="uploads/${product.image}" alt="${product.name}">
                                    <div class="product-info">
                                        <h3>${product.name}</h3>
                                        <p>Price: $${product.price}</p>
                                        <button class="add-to-cart" data-id="${product.id}">Add to Cart</button>
                                    </div>
                                </div>
                            `;
                        });
                        $('#products-container').html(productHtml);
                    },
                    error: function() {
                        $('#products-container').html('<p>Error loading products.</p>');
                    }
                });
            }
        });
    </script>
</body>
</html>
