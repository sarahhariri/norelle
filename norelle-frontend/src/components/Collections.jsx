import { API_URL } from "../config/api";

function Collections({ categories, products, onSelect }) {
  const collections = categories
  .map((category) => {
    const categoryProducts = products.filter(
      (product) =>
        Number(product.category_id ?? product.category?.id) ===
        Number(category.id)
    );

    return {
      ...category,
      count: categoryProducts.length,
    };
  })
  .filter((category) => category.count > 0);

  if (collections.length === 0) return null;

  return (
    <section id="collections" className="py-5">
      <div className="container">
        <div className="text-center mb-4">
          <p className="eyebrow mb-2">FIND YOUR STYLE</p>
          <h2 className="section-title display-5">
            Our Collections
          </h2>
        </div>

        <div className="row g-4 justify-content-center">
          {collections.map((category) => {
            const imageUrl = category.image
              ? category.image.startsWith("http")
                ? category.image
                : `${API_URL}/${category.image}`
              : null;

            return (
              <div
                key={category.id}
                className="col-12 col-sm-6 col-lg-4"
              >
                <button
  type="button"
  className="collection-card position-relative overflow-hidden w-100 p-0 text-start"
  onClick={() => onSelect(category.id)}
  aria-label={`Shop ${category.name}`}
>
  {imageUrl && (
    <img
      src={imageUrl}
      alt=""
      className="collection-image w-100"
      loading="lazy"
    />
  )}

  <div className="collection-overlay">
    <div>
      <h3 className="collection-name mb-2">
        {category.name}
      </h3>

      <span className="collection-caption">
        Explore the collection
      </span>
    </div>

    <i
      className="bi bi-arrow-right collection-arrow"
      aria-hidden="true"
    ></i>
  </div>
</button>
              </div>
            );
          })}
        </div>
      </div>
    </section>
  );
}

export default Collections;