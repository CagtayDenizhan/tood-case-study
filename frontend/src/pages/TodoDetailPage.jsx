import { useState, useEffect } from 'react';
import { useParams, useNavigate } from 'react-router-dom';
import api from '../services/api';
import TodoForm from '../components/TodoForm';
import { toast } from 'react-toastify';

export default function TodoDetailPage() {
  const { id } = useParams();
  const navigate = useNavigate();
  const [todo, setTodo] = useState(null);
  const [loading, setLoading] = useState(true);

  useEffect(() => {
    if (id !== 'new') {
      api.get(`/todos/${id}`)
        .then((res) => {
          setTodo(res.data.data);
          setLoading(false);
        })
        .catch((err) => {
          toast.error('Todo yüklenemedi: ' + err.message);
          setLoading(false);
        });
    } else {
      setLoading(false);
    }
  }, [id]);

  const handleSubmit = (data) => {
    const request = id === 'new'
      ? api.post('/todos', data)
      : api.put(`/todos/${id}`, data);

    request
      .then(() => {
        toast.success(`Todo ${id === 'new' ? 'oluşturuldu' : 'güncellendi'}`);
        navigate('/todos');
      })
      .catch((err) => {
        toast.error('İşlem başarısız: ' + err.message);
      });
  };

  if (loading) return <div className="text-center mt-8">Yükleniyor...</div>;
  if (id !== 'new' && !todo) return <div className="text-center mt-8">Todo bulunamadı.</div>;

  return (
    <div className="max-w-4xl mx-auto p-4">
      <h1 className="text-2xl font-bold mb-4">
        {id === 'new' ? 'Yeni Todo Oluştur' : 'Todo Düzenle'}
      </h1>
      <TodoForm todo={todo} onSubmit={handleSubmit} />
    </div>
  );
}