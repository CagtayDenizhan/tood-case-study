import { useState } from 'react';
import { Link } from 'react-router-dom';
import api from '../services/api';
import StatusBadge from './StatusBadge';
import PriorityIndicator from './PriorityIndicator';
import ConfirmationModal from './ConfirmationModal';
import { toast } from 'react-toastify';
import { motion } from 'framer-motion';

export default function TodoItem({ todo }) {
  const [isModalOpen, setIsModalOpen] = useState(false);

  const handleStatusChange = (status) => {
    api.patch(`/todos/${todo.id}/status`, { status })
      .then(() => {
        toast.success('Durum güncellendi');
      })
      .catch((err) => {
        toast.error('Durum güncellenemedi: ' + err.message);
      });
  };

  const handleDelete = () => {
    api.delete(`/todos/${todo.id}`)
      .then(() => {
        toast.success('Todo silindi');
        setIsModalOpen(false);
      })
      .catch((err) => {
        toast.error('Todo silinemedi: ' + err.message);
      });
  };

  return (
    <motion.div
      initial={{ opacity: 0, y: 20 }}
      animate={{ opacity: 1, y: 0 }}
      className="bg-white dark:bg-gray-800 p-4 rounded-lg shadow-md flex justify-between items-center"
    >
      <div>
        <Link to={`/todos/${todo.id}`} className="text-lg font-semibold hover:underline">
          {todo.title}
        </Link>
        <div className="flex items-center space-x-2 mt-2">
          <StatusBadge status={todo.status} />
          <PriorityIndicator priority={todo.priority} />
        </div>
        <p className="text-sm text-gray-600 dark:text-gray-400">
          Bitiş: {new Date(todo.due_date).toLocaleDateString()}
        </p>
      </div>
      <div className="flex space-x-2">
        <select
          value={todo.status}
          onChange={(e) => handleStatusChange(e.target.value)}
          className="border rounded p-1 dark:bg-gray-700 dark:text-gray-100"
        >
          <option value="pending">Bekliyor</option>
          <option value="in_progress">Devam Ediyor</option>
          <option value="completed">Tamamlandı</option>
        </select>
        <button
          onClick={() => setIsModalOpen(true)}
          className="text-red-500 hover:text-red-700"
        >
          Sil
        </button>
      </div>
      <ConfirmationModal
        isOpen={isModalOpen}
        onClose={() => setIsModalOpen(false)}
        onConfirm={handleDelete}
        message="Bu todo'yu silmek istediğinizden emin misiniz?"
      />
    </motion.div>
  );
}