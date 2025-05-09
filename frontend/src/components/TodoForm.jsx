import { useState, useEffect } from 'react';
import api from '../services/api';
import CategorySelector from './CategorySelector';
import DatePicker from 'react-datepicker';
import 'react-datepicker/dist/react-datepicker.css';

export default function TodoForm({ todo, onSubmit }) {
  const [formData, setFormData] = useState({
    title: '',
    description: '',
    status: 'pending',
    priority: 'medium',
    due_date: null,
    categories: [],
  });
  const [errors, setErrors] = useState({});

  useEffect(() => {
    if (todo) {
      setFormData({
        title: todo.title,
        description: todo.description || '',
        status: todo.status,
        priority: todo.priority,
        due_date: todo.due_date ? new Date(todo.due_date) : null,
        categories: todo.categories?.map((cat) => cat.id) || [],
      });
    }
  }, [todo]);

  const validate = () => {
    const newErrors = {};
    if (!formData.title) newErrors.title = 'Başlık zorunlu';
    if (!formData.due_date) newErrors.due_date = 'Bitiş tarihi zorunlu';
    setErrors(newErrors);
    return Object.keys(newErrors).length === 0;
  };

  const handleSubmit = (e) => {
    e.preventDefault();
    if (validate()) {
      onSubmit(formData);
    }
  };

  const handleChange = (e) => {
    const { name, value } = e.target;
    setFormData({ ...formData, [name]: value });
  };

  return (
    <form onSubmit={handleSubmit} className="bg-white dark:bg-gray-800 p-4 rounded-lg shadow-md">
      <div className="mb-4">
        <label className="block text-sm font-medium">Başlık</label>
        <input
          type="text"
          name="title"
          value={formData.title}
          onChange={handleChange}
          className="w-full border rounded p-2 dark:bg-gray-700 dark:text-gray-100"
        />
        {errors.title && <p className="text-red-500 text-sm">{errors.title}</p>}
      </div>
      <div className="mb-4">
        <label className="block text-sm font-medium">Açıklama</label>
        <textarea
          name="description"
          value={formData.description}
          onChange={handleChange}
          className="w-full border rounded p-2 dark:bg-gray-700 dark:text-gray-100"
        />
      </div>
      <div className="mb-4">
        <label className="block text-sm font-medium">Durum</label>
        <select
          name="status"
          value={formData.status}
          onChange={handleChange}
          className="w-full border rounded p-2 dark:bg-gray-700 dark:text-gray-100"
        >
          <option value="pending">Bekliyor</option>
          <option value="in_progress">Devam Ediyor</option>
          <option value="completed">Tamamlandı</option>
        </select>
      </div>
      <div className="mb-4">
        <label className="block text-sm font-medium">Öncelik</label>
        <select
          name="priority"
          value={formData.priority}
          onChange={handleChange}
          className="w-full border rounded p-2 dark:bg-gray-700 dark:text-gray-100"
        >
          <option value="low">Düşük</option>
          <option value="medium">Orta</option>
          <option value="high">Yüksek</option>
        </select>
      </div>
      <div className="mb-4">
        <label className="block text-sm font-medium">Bitiş Tarihi</label>
        <DatePicker
          selected={formData.due_date}
          onChange={(date) => setFormData({ ...formData, due_date: date })}
          className="w-full border rounded p-2 dark:bg-gray-700 dark:text-gray-100"
          dateFormat="dd/MM/yyyy"
        />
        {errors.due_date && <p className="text-red-500 text-sm">{errors.due_date}</p>}
      </div>
      <div className="mb-4">
        <label className="block text-sm font-medium">Kategoriler</label>
        <CategorySelector
          selectedCategories={formData.categories}
          onChange={(categories) => setFormData({ ...formData, categories })}
        />
      </div>
      <button
        type="submit"
        className="bg-blue-600 text-white px-4 py-2 rounded hover:bg-blue-700"
      >
        Kaydet
      </button>
    </form>
  );
}