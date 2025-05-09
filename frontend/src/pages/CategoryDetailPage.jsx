import { useState, useEffect } from 'react';
import { useParams } from 'react-router-dom';
import api from '../services/api';

export default function CategoryDetailPage() {
    const { id } = useParams();
    const [category, setCategory] = useState(null);
    const [loading, setLoading] = useState(true);

    useEffect(() => {
        api.get(`/categories/${id}`)
            .then((res) => {
                setCategory(res.data.data);
                setLoading(false);
            })
            .catch((err) => {
                setLoading(false);
            });
    }, [id]);

    if (loading) return <div className="text-center mt-8">Yükleniyor...</div>;
    if (!category) return <div className="text-center mt-8">Kategori bulunamadı.</div>;

    return (
        <div className="bg-white dark:bg-gray-800 text-black dark:text-white p-4 rounded-lg shadow-md">
            <h3 className="text-lg font-semibold">İsim: {category.name}</h3>
            <div className="flex items-center mt-2">
                <span>Renk: </span>
                <div
                    className="w-10 h-5 rounded ml-2"
                    style={{ backgroundColor: category.color }}
                ></div>
            </div>
            <p className="mt-2">
                Oluşturulma: {new Date(category.created_at).toLocaleString()}
            </p>
            <p className="mt-2">
                Güncellenme: {new Date(category.updated_at).toLocaleString()}
            </p>
        </div>
    );
}