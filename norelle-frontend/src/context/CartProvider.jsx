import { useEffect, useState } from "react";
import { CartContext } from "./CartContext";

export default function CartProvider({ children }) {
  const [items, setItems] = useState(() => {
    try {
      const saved = JSON.parse(localStorage.getItem("norelle-cart"));
      return Array.isArray(saved) ? saved : [];
    } catch {
      return [];
    }
  });

  useEffect(() => {
    localStorage.setItem("norelle-cart", JSON.stringify(items));
  }, [items]);

 
function addToCart(product, variant) {
  if (!product || !variant) return;

  const limit = Math.min(Number(variant.stock), 20);

  if (!Number.isFinite(limit) || limit < 1) return;

  setItems((current) => {
    const existing = current.find(
      (item) => Number(item.variantId) === Number(variant.id)
    );

    if (existing) {
      const quantity = Number(existing.quantity);

      if (!Number.isInteger(quantity) || quantity >= limit) {
        return current;
      }

      return current.map((item) =>
        Number(item.variantId) === Number(variant.id)
          ? { ...item, quantity: quantity + 1 }
          : item
      );
    }

    return [
      ...current,
      {
        productId: product.id,
        variantId: variant.id,
        name: product.name,
        size: variant.size,
        quantity: 1,
      },
    ];
  });
}

function updateQuantity(variantId, newQuantity, maxStock) {
  const quantity = Number(newQuantity);
  const limit = Math.min(Number(maxStock), 20);

  if (!Number.isInteger(quantity)) return;

  if (quantity < 1) {
    removeFromCart(variantId);
    return;
  }

  if (!Number.isFinite(limit) || quantity > limit) return;

  setItems((current) =>
    current.map((item) =>
      Number(item.variantId) === Number(variantId)
        ? { ...item, quantity }
        : item
    )
  );
}


  function removeFromCart(variantId) {
    setItems((current) =>
      current.filter((item) => item.variantId !== variantId)
    );
  }
 
  function clearCart() {
    setItems([]);
  }
    const cartCount = items.reduce(
    (total, item) => total + item.quantity,
    0
  );
  return (
    <CartContext.Provider value={{ items, addToCart, cartCount, removeFromCart, updateQuantity, clearCart }}>
      {children}
    </CartContext.Provider>
  );
}