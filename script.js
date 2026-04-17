let cart = [];

function loadProducts(){
    fetch("get_products.php")
    .then(res => res.json())
    .then(data => {
        if(!data || data.length === 0){
            document.getElementById("productTable").innerHTML = `
            <div class="empty-state">
                <span class="empty-state-icon"></span>
                <h3>No Products Available</h3>
                <p>Add your first product from the Products section to get started!</p>
            </div>`;
            return;
        }
        
        let html = '<div class="product-grid">';

        data.forEach(p => {
            const statusClass = p.status === 'LOW' ? 'low' : 'ok';
            const statusText = p.status === 'LOW' ? 'Low Stock' : 'In Stock';
            
            const imageSrc = p.image && p.image.includes('http') ? p.image : 'uploads/' + (p.image || 'default.png');
            html += `
            <div class="product-card">
                <div class="product-card-image">
                    <img src="${imageSrc}" alt="${p.name}">
                </div>
                <div class="product-card-content">
                    <h3>${p.name}</h3>
                    <p class="category">${p.category}</p>
                    <div class="price">₱${parseFloat(p.price).toFixed(2)}</div>
                    <span class="stock ${statusClass}">${statusText}</span>
                    <button onclick="addToCart(${p.id}, '${p.name}', ${p.price})">
                        Add to Cart
                    </button>
                </div>
            </div>`;
        });

        html += '</div>';
        document.getElementById("productTable").innerHTML = html;
    });
}

function addToCart(id, name, price){
    let item = cart.find(p => p.id === id);

    if(item){
        item.qty++;
    } else {
        cart.push({id, name, price, qty:1});
    }

    renderCart();
    showToast(`\u2705 ${name} added to cart!`);
}

function renderCart(){
    let html = `
    <tr>
        <th>Product</th>
        <th>Qty</th>
        <th>Price</th>
        <th>Action</th>
    </tr>`;

    let total = 0;

    cart.forEach((p, index) => {
        const subtotal = p.price * p.qty;
        total += subtotal;

        html += `
        <tr>
            <td>${p.name}</td>
            <td>
                <button class="qty-btn" onclick="updateQty(${index}, -1)">−</button>
                <span style="margin: 0 8px;">${p.qty}</span>
                <button class="qty-btn" onclick="updateQty(${index}, 1)">+</button>
            </td>
            <td>₱${subtotal.toFixed(2)}</td>
            <td><button class="remove-btn" onclick="removeFromCart(${index})">Remove</button></td>
        </tr>`;
    });

    document.getElementById("cartTable").innerHTML = html;
    document.getElementById("total").innerText = "₱" + total.toFixed(2);
}

function updateQty(index, change){
    cart[index].qty += change;
    if(cart[index].qty <= 0){
        cart.splice(index, 1);
    }
    renderCart();
}

function removeFromCart(index){
    cart.splice(index, 1);
    renderCart();
}

function checkout(){
    if(cart.length === 0){
        showToast("Cart is empty!", true);
        return;
    }

    fetch("checkout.php", {
        method: "POST",
        headers: {"Content-Type": "application/json"},
        body: JSON.stringify(cart)
    })
    .then(res => res.text())
    .then(msg => {
        showToast("Checkout successful! " + msg);
        cart = [];
        renderCart();
        loadProducts();
    })
    .catch(err => {
        showToast("Error during checkout", true);
    });
}

function showToast(message, isError = false){
    const toast = document.createElement('div');
    toast.className = isError ? 'toast error' : 'toast';
    toast.textContent = message;
    document.body.appendChild(toast);
    
    setTimeout(() => {
        toast.classList.add('removing');
        setTimeout(() => {
            toast.remove();
        }, 300);
    }, 3000);
}

loadProducts();