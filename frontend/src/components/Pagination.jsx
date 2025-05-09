export default function Pagination({ currentPage, totalPages, onPageChange }) {
  // totalPages değeri güvenli değilse varsayılan olarak 1 yap
  const safeTotalPages = Number.isFinite(totalPages) && totalPages > 0 ? totalPages : 1;

  const pages = [...Array(safeTotalPages).keys()].map((i) => i + 1);

  return (
    <div className="flex justify-center space-x-2 mt-4">
      {pages.map((page) => (
        <button
          key={page}
          onClick={() => onPageChange(page)}
          className={`px-3 py-1 rounded ${
            page === currentPage
              ? 'bg-blue-600 text-white'
              : 'bg-gray-200 dark:bg-gray-700'
          }`}
        >
          {page}
        </button>
      ))}
    </div>
  );
}
