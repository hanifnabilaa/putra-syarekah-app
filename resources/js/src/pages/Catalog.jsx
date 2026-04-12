import React, { useEffect, useState } from 'react';
import { useProducts } from '../hooks/useProducts';
import { ProductCard } from '../components/ProductCard';
import { Search } from 'lucide-react';

const Catalog = () => {
    const { products, loading, fetchProducts } = useProducts();
    const [search, setSearch] = useState('');

    useEffect(() => {
        const timer = setTimeout(() => {
            fetchProducts({ search });
        }, 500);

        return () => clearTimeout(timer);
    }, [search, fetchProducts]);

    return (
        <div className="space-y-6">
            <div className="md:flex md:items-center md:justify-between">
                <div className="min-w-0 flex-1">
                    <h2 className="text-2xl font-bold leading-7 text-gray-900 sm:truncate sm:text-3xl sm:tracking-tight">
                        Katalog Produk
                    </h2>
                </div>
            </div>

            <div className="bg-white p-4 rounded-xl shadow-sm border border-gray-100 flex items-center">
                <Search className="h-5 w-5 text-gray-400 mr-2" />
                <input
                    type="text"
                    placeholder="Cari Produk..."
                    className="w-full border-none focus:ring-0 p-0 text-gray-900 sm:text-sm"
                    value={search}
                    onChange={(e) => setSearch(e.target.value)}
                />
            </div>

            <div className="bg-blue-50 border border-blue-100 rounded-xl p-4 flex items-start">
                <div className="flex-shrink-0">
                    <svg className="h-5 w-5 text-blue-400" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true">
                        <path fillRule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a.75.75 0 000 1.5h.253a.25.25 0 01.244.304l-.459 2.066A1.75 1.75 0 0010.747 15H11a.75.75 0 000-1.5h-.253a.25.25 0 01-.244-.304l.459-2.066A1.75 1.75 0 009.253 9H9z" clipRule="evenodd" />
                    </svg>
                </div>
                <div className="ml-3">
                    <h3 className="text-sm font-medium text-blue-800">Catatan Pemesanan</h3>
                    <div className="mt-1 text-sm text-blue-700">
                        <p>Pembelian produk pada katalog ini wajib dalam <strong>kelipatan 10</strong> (contoh: 10, 20, 30, dst).</p>
                    </div>
                </div>
            </div>


            {loading ? (
                <div className="text-center py-12">Loading...</div>
            ) : products.length > 0 ? (
                <div className="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-6">
                    {products.map((product) => (
                        <ProductCard key={product.id} product={product} />
                    ))}
                </div>
            ) : (
                <div className="text-center py-12 bg-white rounded-xl shadow-sm border border-gray-100">
                    <p className="text-gray-500">Tidak ada Produk yang ditemukan.</p>
                </div>
            )}
        </div>
    );
};

export default Catalog;
