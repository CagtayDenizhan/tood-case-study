export default function TodoFilter({ filters, onFilterChange }) {
  const handleChange = (e) => {
    const { name, value } = e.target;
    onFilterChange({ [name]: value });
  };

  return (
    <div className="bg-white dark:bg-gray-800 p-4 rounded-lg shadow-md mb-4">
      <div className="grid grid-cols-1 md:grid-cols-4 gap-4">
        <div>
          <label className="block text-sm font-medium">Arama</label>
          <input
            type="text"
            name="search"
            value={filters.search}
            onChange={handleChange}
            placeholder="Başlık ara..."
            className="w-full border rounded p-2 dark:bg-gray-700 dark:text-gray-100"
          />
        </div>
        <div>
          <label className="block text-sm font-medium">Durum</label>
          <select
            name="status"
            value={filters.status}
            onChange={handleChange}
            className="w-full border rounded p-2 dark:bg-gray-700 dark:text-gray-100"
          >
            <option value="">Tümü</option>
            <option value="pending">Bekliyor</option>
            <option value="in_progress">Devam Ediyor</option>
            <option value="completed">Tamamlandı</option>
          </select>
        </div>
        <div>
          <label className="block text-sm font-medium">Öncelik</label>
          <select
            name="priority"
            value={filters.priority}
            onChange={handleChange}
            className="w-full border rounded p-2 dark:bg-gray-700 dark:text-gray-100"
          >
            <option value="">Tümü</option>
            <option value="low">Düşük</option>
            <option value="medium">Orta</option>
            <option value="high">Yüksek</option>
          </select>
        </div>
        <div>
          <label className="block text-sm font-medium">Sıralama</label>
          <select
            name="sort"
            value={filters.sort}
            onChange={handleChange}
            className="w-full border rounded p-2 dark:bg-gray-700 dark:text-gray-100"
          >
            <option value="due_date">Bitiş Tarihi</option>
            <option value="priority">Öncelik</option>
            <option value="title">Başlık</option>
          </select>
        </div>
      </div>
    </div>
  );
}