import { useState, useEffect } from 'react';
import { Link } from 'react-router-dom';
import api from '../services/api';

export default function CategoryListPage() {
    const [categories, setCategories] = useState([]);
    const [loading, setLoading] = useState(true);

    useEffect(() => {
        api.get('/categories')
            .then((res) => {
                setCategories(res.data.data);
                setLoading(false);
            })
            .catch((err) => {
                setLoading(false);
            });
    }, []);

    if (loading) return <div className="text-center mt-8">Yükleniyor...</div>;

    return (
        <div className="max-w-4xl mx-auto p-4">
            <h1 className="text-2xl font-bold mb-4">Kategoriler</h1>
            <div className="space-y-4">
                {categories.length === 0 ? (
                    <p>Henüz kategori yok.</p>
                ) : (
                    categories.map((category) => (
                        <div
                            key={category.id}
                            className="bg-white dark:bg-gray-800 text-black dark:text-white p-4 rounded-lg shadow-md flex justify-between items-center"
                        >
                            <div>
                                <h3 className="text-lg font-semibold">{category.name}</h3>
                                <div
                                    className="w-10 h-5 rounded mt-1"
                                    style={{ backgroundColor: category.color }}
                                ></div>
                            </div>
                            <Link
                                to={`/categories/${category.id}`}
                                className="text-blue-500 hover:underline dark:text-blue-400"
                            >
                                Detay
                            </Link>
                        </div>
                    ))
                )}
            </div>
        </div>
    );
}