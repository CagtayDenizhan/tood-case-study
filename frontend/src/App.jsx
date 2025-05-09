import { BrowserRouter as Router, Routes, Route, Link } from 'react-router-dom';
import { ThemeProvider, useTheme } from './context/ThemeContext';
import CategoryListPage from './pages/CategoryListPage';
import CategoryDetailPage from './pages/CategoryDetailPage';
import DashboardPage from './pages/DashboardPage';
import TodoListPage from './pages/TodoListPage';
import TodoDetailPage from './pages/TodoDetailPage';
import { ToastContainer } from 'react-toastify';
import 'react-toastify/dist/ReactToastify.css';

function ThemeToggle() {
  const { theme, toggleTheme } = useTheme();
  return (
    <button
      onClick={toggleTheme}
      className="p-2 rounded-full bg-gray-200 dark:bg-gray-700"
    >
      {theme === 'light' ? '🌙 Koyu Tema' : '☀️ Açık Tema'}
    </button>
  );
}

function App() {
  return (
    <ThemeProvider>
      <Router>
        <div className="min-h-screen bg-gray-100 dark:bg-gray-900">
          <nav className="bg-blue-600 text-white p-4">
            <div className="max-w-4xl mx-auto flex justify-between items-center">
              <ul className="flex space-x-4">
                <li>
                  <Link to="/" className="hover:underline">Dashboard</Link>
                </li>
                <li>
                  <Link to="/todos" className="hover:underline">Todo Listesi</Link>
                </li>
                <li>
                  <Link to="/categories" className="hover:underline">Kategoriler</Link>
                </li>
              </ul>
              <ThemeToggle />
            </div>
          </nav>
          <Routes>
            <Route path="/" element={<DashboardPage />} />
            <Route path="/todos" element={<TodoListPage />} />
            <Route path="/todos/:id" element={<TodoDetailPage />} />
            <Route path="/categories" element={<CategoryListPage />} />
            <Route path="/categories/:id" element={<CategoryDetailPage />} />
          </Routes>
          <ToastContainer position="top-right" autoClose={3000} />
        </div>
      </Router>
    </ThemeProvider>
  );
}

export default App;