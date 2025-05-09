import { useState, useEffect } from 'react';
import Select from 'react-select';
import api from '../services/api';

export default function CategorySelector({ selectedCategories, onChange }) {
  const [categories, setCategories] = useState([]);

  useEffect(() => {
    api.get('/categories')
      .then((res) => {
        setCategories(
          res.data.data.map((cat) => ({
            value: cat.id,
            label: cat.name,
          }))
        );
      })
      .catch((err) => {
        console.error('Kategoriler yüklenemedi:', err);
      });
  }, []);

  return (
    <Select
      isMulti
      options={categories}
      value={categories.filter((cat) => selectedCategories.includes(cat.value))}
      onChange={(selected) => onChange(selected.map((opt) => opt.value))}
      classNamePrefix="react-select"
      className="dark:text-gray-100"
    />
  );
}