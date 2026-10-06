import { useEffect, useState } from "react";
import ProductCard from "./components/ProductCard";
import Navbar from "./components/Navbar";
import CategoryFilter from "./components/CategoryFilter";
import heroImage from "./assets/images/norelle-hero.webp";
import { useLocation, useNavigate } from "react-router";
import Collections from "./components/Collections";
import aboutImage from "./assets/images/norelle-about.webp";
import Footer from "./components/Footer";
import { API_URL } from "./config/api";

function App() {
  const [products, setProducts] = useState([]);
  const [categories, setCategories] = useState([]);
  const [selectedCategory, setSelectedCategory] = useState(null);
  const [loading, setLoading] = useState(true);
  const [error, setError] = useState("");
  const { hash } = useLocation();
  const navigate = useNavigate();

  useEffect(() => {
    Promise.all([
  fetch(`${API_URL}/api/products`),
  fetch(`${API_URL}/api/categories`),
])
      .then(async ([productsResponse, categoriesResponse]) => {
        if (!productsResponse.ok || !categoriesResponse.ok) {
          throw new Error("Failed to load store data");
        }

        const productsResult = await productsResponse.json();
        const categoriesResult = await categoriesResponse.json();

        setProducts(productsResult.data);
        setCategories(categoriesResult.data);
      })
      .catch((error) => {
        setError(error.message);
      })
      .finally(() => {
        setLoading(false);
      });
  }, []);

  useEffect(() => {
  if (loading || error || !hash) return;

  const frame = requestAnimationFrame(() => {
    document.getElementById(hash.slice(1))?.scrollIntoView({
      behavior: "smooth",
      block: "start",
    });
  });

  return () => cancelAnimationFrame(frame);
}, [hash, loading, error]);

function handleCollectionSelect(categoryId) {
  setSelectedCategory(categoryId);

  if (hash === "#shop") {
    requestAnimationFrame(() => {
      document.getElementById("shop")?.scrollIntoView({
        behavior: "smooth",
        block: "start",
      });
    });
  } else {
    navigate("/#shop");
  }
}

  const filteredProducts =
    selectedCategory !== null
      ? products.filter(
          (product) =>
            Number(product.category_id) === Number(selectedCategory)
        )
      : products;

  if (loading) {
    return (
      <div className="min-vh-100 d-flex flex-column align-items-center justify-content-center">
        <div
          className="spinner-border norelle-spinner"
          role="status"
        >
          <span className="visually-hidden">Loading...</span>
        </div>

        <p className="mt-3 mb-0 text-secondary">
          Loading products...
        </p>
      </div>
    );
  }

  if (error) {
    return (
      <div className="container py-5">
        <div className="alert alert-danger text-center">
          {error}
        </div>
      </div>
    );
  }

  return (
    <>
      <Navbar />

      <main>
       <section className="hero" id="new">
  <div className="container-fluid px-0">
    <div className="row g-0">
      <div className="col-12 col-md-6 hero-copy d-flex align-items-center">
        <div className="px-4 px-lg-5 py-5">
          <p className="eyebrow mb-3">
            NEW SEASON • 2026
          </p>

          <h1 className="hero-title">
            Confidence looks good on you.
          </h1>

          <p className="hero-text my-4">
            Discover timeless pieces designed for effortless elegance.
          </p>

          <a
            href="#collections"
            className="btn hero-cta"
          >
            Shop the collection
            <i className="bi bi-arrow-down-right"></i>
          </a>
        </div>
      </div>

      <div className="col-12 col-md-6">
        <img
          src={heroImage}
          className="hero-image"
          alt="NORELLE new season collection"
        />
      </div>
    </div>
  </div>
</section>

<Collections
  categories={categories}
  products={products}
  onSelect={handleCollectionSelect}
/>

        <section className="py-5" id="shop">
          <div className="container py-lg-5">
            <div className="text-center mb-4">
              <p className="eyebrow mb-2">
                CURATED FOR YOU
              </p>

              <h2 className="section-title display-5">
                New Arrivals
              </h2>
            </div>

            <CategoryFilter
              categories={categories}
              selectedCategory={selectedCategory}
              onSelect={setSelectedCategory}
            />

            {filteredProducts.length === 0 ? (
              <div className="alert norelle-empty text-center">
                No products available.
              </div>
            ) : (
              <div className="row g-4 justify-content-center">
                {filteredProducts.map((product) => (
                  <div
                    className="col-12 col-sm-6 col-lg-3"
                    key={product.id}
                  >
                    <ProductCard product={product} />
                  </div>
                ))}
              </div>
            )}
          </div>
        </section>
<section className="py-5">
  <div className="container">
    <div className="text-center mb-5">
      <p className="eyebrow mb-2">
        THE NORELLE EXPERIENCE
      </p>

      <h2 className="section-title mb-0">
        Little details. Easier shopping.
      </h2>
    </div>

    <div className="row g-4 g-lg-5 text-center">
      <div className="col-12 col-md-4">
        <i
          className="bi bi-search experience-icon d-inline-block mb-3"
          aria-hidden="true"
        ></i>

        <h3 className="product-title fs-5 mb-2">
          Find your favourites
        </h3>

        <p className="product-description mb-0">
          Search for a piece or explore a category.
        </p>
      </div>

      <div className="col-12 col-md-4">
        <i
          className="bi bi-heart experience-icon d-inline-block mb-3"
          aria-hidden="true"
        ></i>

        <h3 className="product-title fs-5 mb-2">
          Save what you love
        </h3>

        <p className="product-description mb-0">
          Keep your favourites together for later.
        </p>
      </div>

      <div className="col-12 col-md-4">
        <i
          className="bi bi-bag experience-icon d-inline-block mb-3"
          aria-hidden="true"
        ></i>

        <h3 className="product-title fs-5 mb-2">
          Shop with ease
        </h3>

        <p className="product-description mb-0">
          Choose your size, add to your bag, and checkout.
        </p>
      </div>
    </div>
  </div>
</section>
        <section
  id="about"
  className="position-relative overflow-hidden"
>
  {/* خلفية للشاشات الكبيرة */}
  <div
    className="norelle-about position-absolute top-0 start-0 w-100 h-100 d-none d-lg-block"
    style={{ "--about-image": `url("${aboutImage}")` }}
    aria-hidden="true"
  ></div>

  {/* صورة كاملة للموبايل والتابلت */}
  <img
    src={aboutImage}
    alt="An elegant boutique interior in soft NORELLE colours"
    className="d-block d-lg-none w-100"
    loading="lazy"
  />

  <div className="container position-relative py-5">
    <div className="row justify-content-center py-lg-4">
      <div className="col-12 col-md-8 col-lg-6 text-center">
        <div className="about-content p-4 p-lg-5">
          <p className="eyebrow mb-3">
            THE NORELLE EDIT
          </p>

          <h2 className="section-title display-5 mb-4">
            A little elegance.
            <br />
            Every day.
          </h2>

          <p className="product-description mb-4">
            Timeless pieces, soft details, and a style
            that feels like you.
          </p>

          <a
            href="#shop"
            className="btn btn-about px-4 py-3"
          >
            Explore the collection
            <i
              className="bi bi-arrow-right ms-2"
              aria-hidden="true"
            ></i>
          </a>
        </div>
      </div>
    </div>
  </div>
</section>
      </main>
      <Footer />
    </>
  );
}
  

export default App;