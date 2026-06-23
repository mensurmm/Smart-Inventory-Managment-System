let cart = [];
let isScanning = false;
const html5QrCode = new Html5Qrcode("reader");

const cartBody = document.getElementById("cart-body");
const grandTotalDisplay = document.getElementById("grand-total");
const subtotalDisplay = document.getElementById("subtotal");
const taxDisplay = document.getElementById("tax");
const manualInput = document.getElementById("manual_barcode");
const readerContainer = document.getElementById("reader-container");
const btnScan = document.getElementById("btn-scan");

// --- 1. SCANNER LOGIC ---
const scanConfig = { fps: 15, qrbox: { width: 250, height: 150 } };

btnScan.addEventListener("click", () => {
  if (!isScanning) {
    // Show container FIRST so the library can calculate dimensions
    readerContainer.style.display = "block";

    html5QrCode.start(
        { facingMode: "environment" },
        scanConfig,
        (text) => {
          fetchProduct(text);
          html5QrCode.pause();
          setTimeout(() => { if (isScanning) html5QrCode.resume(); }, 2000);
        }
      )
      .then(() => {
        isScanning = true;
        btnScan.innerHTML = '<i class="fa-solid fa-camera-slash"></i> Stop Camera';
        btnScan.style.background = "#e74c3c"; 
      })
      .catch((err) => {
        readerContainer.style.display = "none";
        console.error("Camera Error:", err);
        alert("Camera failed: " + err);
      });
  } else {
    html5QrCode.stop().then(() => {
      isScanning = false;
      readerContainer.style.display = "none";
      btnScan.innerHTML = '<i class="fa-solid fa-camera"></i> Toggle Camera';
      btnScan.style.background = ""; 
    });
  }
});

// --- 2. MANUAL ENTRY ---
document.getElementById("btn-manual-search").addEventListener("click", () => processManualEntry());
manualInput.addEventListener("keypress", (e) => { if (e.key === "Enter") processManualEntry(); });

function processManualEntry() {
  const code = manualInput.value.trim();
  if (code !== "") {
    fetchProduct(code);
    manualInput.value = "";
    manualInput.focus();
  }
}

// --- 3. DATABASE FETCHING ---
async function fetchProduct(barcode) {
  try {
    const response = await fetch(`../api/get_product.php?barcode=${barcode}&t=${Date.now()}`);
    const result = await response.json();
    if (result.success && result.data) {
      addItemToCart(result.data);
    } else {
      alert("Product not found!");
    }
  } catch (error) {
    alert("Connection Error: Check server status.");
  }
}

// --- 4. CART MANAGEMENT ---
function addItemToCart(product) {
  const existingIndex = cart.findIndex((item) => item.barcode === product.barcode);
  if (existingIndex !== -1) {
    cart[existingIndex].qty += 1;
  } else {
    cart.push({
      barcode: product.barcode,
      name: product.name,
      price: parseFloat(product.selling_price),
      qty: 1,
    });
  }
  renderCart();
}

function updateQty(index, newQty) {
  const qty = parseInt(newQty);
  if (isNaN(qty) || qty < 1) removeItem(index);
  else { cart[index].qty = qty; renderCart(); }
}

function removeItem(index) {
  cart.splice(index, 1);
  renderCart();
}

// --- 5. UI RENDERING ---
function renderCart() {
  cartBody.innerHTML = "";
  let subtotal = 0;
  cart.forEach((item, index) => {
    const lineTotal = item.qty * item.price;
    subtotal += lineTotal;
    cartBody.innerHTML += `
        <tr>
            <td>${item.name}</td>
            <td><input type="number" class="qty-input" value="${item.qty}" onchange="updateQty(${index}, this.value)"></td>
            <td>${item.price.toFixed(2)}</td>
            <td>${lineTotal.toFixed(2)}</td>
            <td><button onclick="removeItem(${index})" class="btn-del" style="border:none; background:none; color:red; cursor:pointer;"><i class="fa-solid fa-trash"></i></button></td>
        </tr>`;
  });
  const tax = subtotal * 0.15;
  subtotalDisplay.innerText = subtotal.toFixed(2);
  taxDisplay.innerText = tax.toFixed(2);
  grandTotalDisplay.innerText = (subtotal + tax).toFixed(2);
}

// --- 6. PAYMENT TOGGLE ---
document.querySelectorAll(".method-btn").forEach((btn) => {
  btn.addEventListener("click", function () {
    document.querySelector(".method-btn.active").classList.remove("active");
    this.classList.add("active");
  });
});

// --- 7. COMPLETE TRANSACTION ---
document.getElementById("btn-complete").addEventListener("click", async () => {
  if (cart.length === 0) return alert("Cart is empty!");
  const btn = document.getElementById("btn-complete");
  btn.disabled = true;
  btn.innerText = "Processing...";

  const checkoutData = {
    items: cart,
    total: grandTotalDisplay.innerText,
    method: document.querySelector(".method-btn.active").dataset.mode,
  };

  try {
    const response = await fetch("../api/complete_sale.php", {
      method: "POST",
      headers: { "Content-Type": "application/json" },
      body: JSON.stringify(checkoutData),
    });
    const result = await response.json();
    if (result.success) {
      alert("Sale Recorded Successfully!");
      cart = []; renderCart();
    } else { alert("Error: " + result.message); }
  } catch (error) { alert("Checkout System Error."); }
  finally { btn.disabled = false; btn.innerText = "Complete Transaction"; }
});