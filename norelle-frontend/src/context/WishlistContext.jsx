import { createContext, useEffect, useState } from "react";

export const WishlistContext = createContext(null);

export function WishlistProvider({ children }) {
  const [wishlistIds, setWishlistIds] = useState(() => {
    try {
      const saved = JSON.parse(localStorage.getItem("norelle-wishlist"));
      return Array.isArray(saved) ? saved : [];
    } catch {
      return [];
    }
  });

  useEffect(() => {
    localStorage.setItem("norelle-wishlist", JSON.stringify(wishlistIds));
  }, [wishlistIds]);

 const isWishlisted = (id) =>
  wishlistIds.some((savedId) => String(savedId) === String(id));

const toggleWishlist = (id) => {
  setWishlistIds((current) => {
    const exists = current.some(
      (savedId) => String(savedId) === String(id)
    );

    return exists
      ? current.filter(
          (savedId) => String(savedId) !== String(id)
        )
      : [...current, id];
  });
};

  return (
    <WishlistContext.Provider
      value={{ wishlistIds, isWishlisted, toggleWishlist }}
    >
      {children}
    </WishlistContext.Provider>
  );
}