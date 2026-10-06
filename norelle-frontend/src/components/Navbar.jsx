import { useContext, useEffect, useRef, useState } from "react";
import { Link, useLocation } from "react-router";
import { CartContext } from "../context/CartContext";
import { WishlistContext } from "../context/WishlistContext";
import { Collapse } from "bootstrap";
import { API_URL } from "../config/api";

function Navbar() {
  const { pathname, hash } = useLocation();
  const { cartCount } = useContext(CartContext);

  const [searchOpen, setSearchOpen] = useState(false);
  const [searchTerm, setSearchTerm] = useState("");
  const [products, setProducts] = useState([]);
  const [searchLoaded, setSearchLoaded] = useState(false);
  const [searchLoading, setSearchLoading] = useState(false);
  const [searchError, setSearchError] = useState("");

  const searchPanelRef = useRef(null);
  const searchButtonRef = useRef(null);
  const searchInputRef = useRef(null);

  const { wishlistIds } = useContext(WishlistContext);
  const [activeSection, setActiveSection] = useState("");
  const activeLink =
    pathname === "/" ? activeSection : "";

  const linkClass = (section) =>
    `nav-link ${activeLink === section ? "active" : ""}`;

  async function loadSearchProducts() {
    if (searchLoaded || searchLoading) return;

    setSearchLoading(true);
    setSearchError("");

    try {
      const response = await fetch(`${API_URL}/api/products`);

      if (!response.ok) {
        throw new Error("Could not load products");
      }

      const result = await response.json();
      setProducts(result.data ?? []);
      setSearchLoaded(true);
    } catch {
      setSearchError("Could not load products. Please try again.");
    } finally {
      setSearchLoading(false);
    }
  }

  function toggleSearch() {
    if (!searchOpen) {
      loadSearchProducts();
    }

    setSearchOpen((current) => !current);
  }

function closeSearch() {
  setSearchOpen(false);
  setSearchTerm("");
}

function handleSectionClick(section) {
  closeSearch();

  function scrollToSection() {
    if (window.location.pathname !== "/") return;

    document.getElementById(section)?.scrollIntoView({
      behavior: "smooth",
      block: "start",
    });
  }

  const menuWasOpen = closeMobileMenu(scrollToSection);

  if (
    !menuWasOpen &&
    pathname === "/" &&
    hash === `#${section}`
  ) {
    scrollToSection();
  }
}

function closeNavigation() {
  closeSearch();
  closeMobileMenu();
}

  useEffect(() => {
    if (!searchOpen) return;

    searchInputRef.current?.focus();

    function handleKeyDown(event) {
      if (event.key === "Escape") {
        closeSearch();
      }
    }

    function handlePointerDown(event) {
      if (
        !searchPanelRef.current?.contains(event.target) &&
        !searchButtonRef.current?.contains(event.target)
      ) {
        closeSearch();
      }
    }

    

    document.addEventListener("keydown", handleKeyDown);
    document.addEventListener("pointerdown", handlePointerDown);

    return () => {
      document.removeEventListener("keydown", handleKeyDown);
      document.removeEventListener("pointerdown", handlePointerDown);
    };
  }, [searchOpen]);

  const normalizedSearch = searchTerm.trim().toLowerCase();

  const searchResults = normalizedSearch
    ? products.filter((product) => {
        const name = product.name?.toLowerCase() ?? "";
        const category =
          product.category?.name?.toLowerCase() ?? "";

        return (
          name.includes(normalizedSearch) ||
          category.includes(normalizedSearch)
        );
      })
    : [];
    useEffect(() => {
  if (pathname !== "/") return;

  const sectionIds = ["new", "collections", "shop", "about"];

  function updateActiveSection() {
    let currentSection = "new";

    for (const id of sectionIds) {
      const section = document.getElementById(id);

      if (section && section.getBoundingClientRect().top <= 100) {
        currentSection = id;
      }
    }

    setActiveSection(currentSection);
  }

  const frame = requestAnimationFrame(updateActiveSection);

  window.addEventListener("scroll", updateActiveSection, {
    passive: true,
  });

  window.addEventListener("resize", updateActiveSection);

  return () => {
    cancelAnimationFrame(frame);
    window.removeEventListener("scroll", updateActiveSection);
    window.removeEventListener("resize", updateActiveSection);
  };
}, [pathname, hash]);

function closeMobileMenu(afterClose) {
  const menu = document.getElementById("norelleNavbar");

  if (!menu?.classList.contains("show")) return false;

  if (afterClose) {
    menu.addEventListener("hidden.bs.collapse", afterClose, {
      once: true,
    });
  }

  Collapse.getOrCreateInstance(menu, { toggle: false }).hide();

  return true;
}

  return (
    <header className="navbar navbar-expand-lg sticky-top">
      <div className="container">
        <Link className="navbar-brand" to="/#new" onClick={() => handleSectionClick("new")}>
          NORELLE
        </Link>

        <button
          className="navbar-toggler"
          type="button"
          data-bs-toggle="collapse"
          data-bs-target="#norelleNavbar"
          aria-controls="norelleNavbar"
          aria-expanded="false"
          aria-label="Toggle navigation"
        >
          <i className="bi bi-list" aria-hidden="true"></i>
        </button>

        <div className="collapse navbar-collapse" id="norelleNavbar">
          <ul className="navbar-nav mx-auto gap-lg-3">
  <li className="nav-item">
    <Link
      className={linkClass("new")}
      to="/#new"
      onClick={() => handleSectionClick("new")}
    >
      New In
    </Link>
  </li>

  <li className="nav-item">
    <Link
      className={linkClass("collections")}
      to="/#collections"
      onClick={() => handleSectionClick("collections")}
    >
      Collections
    </Link>
  </li>

  <li className="nav-item">
    <Link
      className={linkClass("shop")}
      to="/#shop"
      onClick={() => handleSectionClick("shop")}
    >
      Shop
    </Link>
  </li>

  

  <li className="nav-item">
    <Link
      className={linkClass("about")}
      to="/#about"
      onClick={() => handleSectionClick("about")}
    >
      About
    </Link>
  </li>
</ul>

          <div className="nav-actions">
            <button
              ref={searchButtonRef}
              type="button"
              aria-label={searchOpen ? "Close search" : "Open search"}
              aria-expanded={searchOpen}
              aria-controls="storeSearchPanel"
              onClick={toggleSearch}
            >
              <i className="bi bi-search" aria-hidden="true"></i>
            </button>

            <Link
  to="/wishlist"
  className="wishlist-nav-link"
  aria-label={`Wishlist, ${wishlistIds.length} items`}
  onClick={closeNavigation}
>
  <span className="wishlist-icon-wrap">
    <i className="bi bi-heart" aria-hidden="true"></i>

    {wishlistIds.length > 0 && (
      <span className="wishlist-count">{wishlistIds.length}</span>
    )}
  </span>
</Link>

            <Link
              to="/bag"
              className="bag-link"
              aria-label={`Shopping bag, ${cartCount} items`}
              onClick={closeNavigation}
            >
              <span className="bag-icon-wrap">
                <i className="bi bi-bag" aria-hidden="true"></i>

                {cartCount > 0 && (
                  <span className="bag-count">{cartCount}</span>
                )}
              </span>
            </Link>
          </div>
        </div>
      </div>

      {searchOpen && (
        <div
          id="storeSearchPanel"
          className="store-search-panel"
          ref={searchPanelRef}
        >
          <div className="container py-4">
            <div className="d-flex align-items-center justify-content-between mb-3">
              <h2 className="section-title fs-4 mb-0">
                Find your piece
              </h2>

              <button
                type="button"
                className="btn-close"
                aria-label="Close search"
                onClick={closeSearch}
              ></button>
            </div>

            <label
              htmlFor="storeSearchInput"
              className="visually-hidden"
            >
              Search products
            </label>

            <input
              ref={searchInputRef}
              id="storeSearchInput"
              type="search"
              className="form-control form-control-lg"
              placeholder="Search dresses, sets, tops..."
              value={searchTerm}
              onChange={(event) =>
                setSearchTerm(event.target.value)
              }
            />

            {searchLoading && (
              <p className="small text-secondary mt-3 mb-0">
                Loading products...
              </p>
            )}

            {searchError && (
              <div className="alert alert-danger mt-3 mb-0">
                {searchError}
                <button
                  type="button"
                  className="btn btn-link"
                  onClick={loadSearchProducts}
                >
                  Retry
                </button>
              </div>
            )}

            {!searchLoading &&
              !searchError &&
              normalizedSearch &&
              searchResults.length === 0 && (
                <p className="text-secondary mt-3 mb-0">
                  No products found.
                </p>
              )}

            {searchResults.length > 0 && (
              <div className="store-search-results mt-3">
                {searchResults.map((product) => {
                  const price = Number(
                    product.sale_price ?? product.price
                  );

                  return (
                    <Link
                      key={product.id}
                      to={`/products/${product.slug}`}
                      className="d-flex align-items-center gap-3 py-2 text-decoration-none border-bottom"
                      onClick={closeSearch}
                    >
                      {product.main_image && (
                        <img
                          src={
                               product.main_image.startsWith("http")
                               ? product.main_image
                               : `${API_URL}/${product.main_image}`
                               }
                          alt=""
                          className="store-search-image"
                        />
                      )}

                      <span className="flex-grow-1">
                        <span className="d-block store-search-name">
                          {product.name}
                        </span>
                        <span className="small text-secondary">
                          {product.category?.name}
                        </span>
                      </span>

                      <span className="store-search-price text-nowrap">
                        ${price.toFixed(2)}
                      </span>
                    </Link>
                  );
                })}
              </div>
            )}
          </div>
        </div>
      )}
    </header>
  );
}

export default Navbar;