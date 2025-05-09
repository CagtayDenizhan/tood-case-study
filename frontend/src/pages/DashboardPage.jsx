import { useState, useEffect } from 'react';
import api from '../services/api';
import TodoList from '../components/TodoList';
import { toast } from 'react-toastify';

export default function DashboardPage() {
    const [stats, setStats] = useState(null);
    const [upcomingTodos, setUpcomingTodos] = useState([]);
    const [loading, setLoading] = useState(true);

    useEffect(() => {
        Promise.all([
            api.get('/stats/todos'),
            api.get('/todos?due_date=upcoming'),
        ])
            .then(([statsRes, todosRes]) => {
                console.log('Gelen stats:', statsRes.data.data); // burası önemli
                setStats(statsRes.data.data);
                setUpcomingTodos(todosRes.data.data);
                setLoading(false);
            })
            .catch((err) => {
                toast.error('Veriler yüklenemedi: ' + err.message);
                setLoading(false);
            });
    }, []);

    if (loading) return <div className="text-center mt-8">Yükleniyor...</div>;

    return (
        <div className="max-w-4xl mx-auto p-4">
            <h1 className="text-2xl font-bold mb-4">Dashboard</h1>
            {stats && (
                <div className="grid grid-cols-1 md:grid-cols-3 gap-4 mb-8">
                    <div className="bg-white dark:bg-gray-800 p-4 rounded-lg shadow-md">
                        <h3 className="text-lg font-semibold">Tamamlanan</h3>
                        <p className="text-2xl">{stats.completed ?? 0}</p>
                    </div>
                    <div className="bg-white dark:bg-gray-800 p-4 rounded-lg shadow-md">
                        <h3 className="text-lg font-semibold">Devam Eden</h3>
                        <p className="text-2xl">{stats.in_progress ?? 0}</p>
                    </div>
                    <div className="bg-white dark:bg-gray-800 p-4 rounded-lg shadow-md">
                        <h3 className="text-lg font-semibold">Geciken</h3>
                        <p className="text-2xl">{stats.overdue ?? 0}</p>
                    </div>
                </div>
            )}

            <h2 className="text-xl font-bold mb-4">Yaklaşan Todo'lar</h2>
            <TodoList todos={upcomingTodos} />
        </div>
    );
}