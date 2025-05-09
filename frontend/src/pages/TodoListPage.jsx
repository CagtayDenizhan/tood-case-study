import { useState, useEffect } from 'react';
import { Link } from 'react-router-dom';
import api from '../services/api';
import TodoList from '../components/TodoList';
import TodoFilter from '../components/TodoFilter';
import Pagination from '../components/Pagination';
import { toast } from 'react-toastify';

export default function TodoListPage() {
  const [todos, setTodos] = useState([]);
  const [filters, setFilters] = useState({
    search: '',
    status: '',
    priority: '',
    sort: 'due_date',
    page: 1,
    perPage: 10,
  });
  const [totalPages, setTotalPages] = useState(1);
  const [loading, setLoading] = useState(true);

  useEffect(() => {
    const query = new URLSearchParams({
      search: filters.search,
      status: filters.status,
      priority: filters.priority,
      sort: filters.sort,
      page: filters.page,
      per_page: filters.perPage,
    }).toString();

    setLoading(true);
    api.get(`/todos?${query}`)
      .then((res) => {
        setTodos(res.data.data);
        setTotalPages(Math.ceil(res.data.total / filters.perPage));
        setLoading(false);
      })
      .catch((err) => {
        toast.error("Todo'lar yüklenemedi: " + err.message);
        setLoading(false);
      });
  }, [filters]);

  const handleFilterChange = (newFilters) => {
    setFilters({ ...filters, ...newFilters, page: 1 });
  };

  const handlePageChange = (page) => {
    setFilters({ ...filters, page });
  };

  return (
    <div className="max-w-4xl mx-auto p-4">
      <div className="flex justify-between items-center mb-4">
        <h1 className="text-2xl font-bold">Todo Listesi</h1>
        <Link
          to="/todos/new"
          className="bg-blue-600 text-white px-4 py-2 rounded hover:bg-blue-700"
        >
          Yeni Todo
        </Link>
      </div>
      <TodoFilter filters={filters} onFilterChange={handleFilterChange} />
      {loading ? (
        <div className="text-center mt-8">Yükleniyor...</div>
      ) : todos.length === 0 ? (
        <div className="text-center mt-8">Todo bulunamadı.</div>
      ) : (
        <>
          <TodoList todos={todos} />
          <Pagination
            currentPage={filters.page}
            totalPages={totalPages}
            onPageChange={handlePageChange}
          />
        </>
      )}
    </div>
  );
}