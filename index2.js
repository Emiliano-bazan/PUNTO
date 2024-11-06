document.addEventListener('DOMContentLoaded', (event) => {
// Loader animation
document.addEventListener('DOMContentLoaded', () => {
    setTimeout(() => {
        document.getElementById('loader').style.display = 'none';
    }, 1500); // Tiempo de espera antes de que desaparezca el loader
});

// Animación de la pelota de tenis
window.addEventListener('load', () => {
    const tennisBall = document.querySelector('#tennis-ball-loader');
    if (tennisBall) {
        const animate = () => {
            const x = Math.random() * (window.innerWidth - 50); // Ajusta el tamaño de la pelota
            const y = Math.random() * (window.innerHeight - 50);
            tennisBall.style.transform = `translate(${x}px, ${y}px)`;
            setTimeout(animate, Math.random() * 2000 + 1000);
        };
        animate();
    }
});

// Menú desplegable
document.querySelector('.menu-icon').addEventListener('click', () => {
    document.querySelector('.menu').classList.toggle('active');
});

// Simulación de datos de productos
const products = [
    { id: 1, title: 'Camiseta Deportiva', price: 29.99, stock: 50, image: 'https://ejemplo.com/camiseta.jpg' },
    { id: 2, title: 'Shorts de Running', price: 24.99, stock: 30, image: 'https://ejemplo.com/shorts.jpg' },
    { id: 3, title: 'Zapatillas de Tenis', price: 89.99, stock: 20, image: 'https://ejemplo.com/zapatillas.jpg' },
    { id: 4, title: 'Raqueta de Tenis', price: 129.99, stock: 15, image: 'https://ejemplo.com/raqueta.jpg' },
];

// Cargar productos
const productsContainer = document.querySelector('.menu-items');
if (productsContainer) {
    products.forEach(product => {
        const productCard = document.createElement('div');
        productCard.className = 'menu-item';
        productCard.innerHTML = `
            <img src="${product.image}" alt="${product.title}" width="300" height="250">
            <div class="menu-info">
                <h3>${product.title}</h3>
                <p>Apurate que solo quedan ${product.stock}</p>
                <span class="price">$${product.price.toFixed(2)}</span>
            </div>
            <button onclick="addToCart(${product.id})">Agregar al carrito</button>
            <span class="favorite" onclick="toggleFavorite(this, ${product.id})">♡</span>
        `;
        productsContainer.appendChild(productCard);
    });
}

// Funciones para manejar favoritos y carrito
let cart = [];

function addToCart(productId) {
    const product = products.find(p => p.id === productId);
    const existingItem = cart.find(item => item.id === productId);

    if (existingItem) {
        existingItem.quantity++;
    } else {
        cart.push({ ...product, quantity: 1 });
    }

    updateCartCount();
    console.log(`Producto ${productId} añadido al carrito`);
}

function updateCartCount() {
    const cartCount = document.querySelector('.cart-count');
    const totalItems = cart.reduce((sum, item) => sum + item.quantity, 0);
    if (totalItems > 0) {
        cartCount.style.display = 'flex';
        cartCount.textContent = totalItems;
    } else {
        cartCount.style.display = 'none';
    }
}

function toggleFavorite(element, productId) {
    element.classList.toggle('active');
    if (element.classList.contains('active')) {
        element.textContent = '♥';
        console.log(`Producto ${productId} añadido a favoritos`);
    } else {
        element.textContent = '♡';
        console.log(`Producto ${productId} eliminado de favoritos`);
    }
}

function openCart() {
    const cartItems = cart.map(item => `
        <li>
            ${item.title} - Cantidad: ${item.quantity} - Precio: $${(item.price * item.quantity).toFixed(2)}
        </li>
    `).join('');

    const total = cart.reduce((sum, item) => sum + item.price * item.quantity, 0);

    const cartWindow = window.open('', 'Cart', 'width=400,height=400');
    cartWindow.document.write(`
        <html>
        <head>
            <title>Carrito de Compras</title>
            <style>
                body { font-family: Arial, sans-serif; padding: 20px; }
                h1 { color: #424242; }
                ul { list-style-type: none; padding: 0; }
                li { margin-bottom: 10px; }
                .total { font-weight: bold; margin-top: 20px; }
            </style>
        </head>
        <body>
            <h1>Carrito de Compras</h1>
            <ul>${cartItems}</ul>
            <p class="total">Total: $${total.toFixed(2)}</p>
        </body>
        </html>
    `);
}

// Asigna el evento de abrir carrito al ícono del carrito
document.querySelector('.cart-icon').addEventListener('click', openCart));
