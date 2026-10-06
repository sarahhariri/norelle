function CategoryFilter({
  categories,
  selectedCategory,
  onSelect,
}) {
  return (
    <div className="category-filter">
      <button
        type="button"
        className={selectedCategory === null ? 'active' : ''}
        onClick={() => onSelect(null)}
      >
        All
      </button>

      {categories.map((category) => (
        <button
          type="button"
          key={category.id}
          className={
            selectedCategory === category.id ? 'active' : ''
          }
          onClick={() => onSelect(category.id)}
        >
          {category.name}
        </button>
      ))}
    </div>
  );
}

export default CategoryFilter;