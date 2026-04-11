import React, { useState } from 'react';
import { formatRupiah } from '../utils/helpers';
import { useCart } from '../contexts/CartContext';

export const ProductCard = ({ product }) => {
    const { addToCart } = useCart();
    const [quantity, setQuantity] = useState(10);
    const [added, setAdded] = useState(false);

    const handleAdd = () => {
        if (quantity < 10 || quantity % 10 !== 0) {
            alert('Jumlah pesanan harus kelipatan 10 (10, 20, 30, dst).');
            return;
        }
        if (quantity > product.stock) {
            alert('Jumlah pesanan melebihi stok yang tersedia.');
            return;
        }

        addToCart(product, quantity);
        setAdded(true);
        setTimeout(() => setAdded(false), 2000);
    };

    return (
        <div className="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden hover:shadow-md transition-shadow">
            <div className="h-48 bg-gray-200 relative overflow-x-auto flex snap-x snap-mandatory scrollbar-hide">
                {product.image_urls && product.image_urls.length > 0 ? (
                    product.image_urls.map((url, idx) => (
                        <div key={idx} className="flex-none w-full h-full snap-center relative">
                            <img src={url} alt={`${product.name} ${idx + 1}`} className="w-full h-full object-cover" />
                            {product.image_urls.length > 1 && (
                                <div className="absolute bottom-2 right-2 bg-black/50 text-white text-[10px] px-2 py-1 rounded">
                                    {idx + 1}/{product.image_urls.length}
                                </div>
                            )}
                        </div>
                    ))
                ) : product.image_url ? (
                    <div className="flex-none w-full h-full snap-center relative">
                        <img src={product.image_url} alt={product.name} className="w-full h-full object-cover" />
                    </div>
                ) : (
                    <div className="w-full h-full flex items-center justify-center text-gray-400">
                        <span className="text-sm">Tidak ada foto</span>
                    </div>
                )}
                {product.stock <= 0 && (
                    <div className="absolute top-2 right-2 bg-red-500 text-white text-xs font-bold px-2 py-1 rounded">
                        Stok Habis
                    </div>
                )}
            </div>
            <div className="p-4">
                <h3 className="font-bold text-gray-900 text-lg line-clamp-1">{product.name}</h3>
                <p className="text-primary-600 font-semibold mt-1">{formatRupiah(product.price)}</p>
                <p className="text-gray-500 text-sm mt-2 line-clamp-2 h-10">{product.description}</p>
                
                <div className="mt-4 pt-4 border-t border-gray-100">
                    <div className="flex items-center justify-between mb-3">
                        <span className="text-sm text-gray-500">Stok: {product.stock}</span>
                        <div className="flex items-center border rounded-lg">
                            <button 
                                type="button"
                                onClick={() => setQuantity(Math.max(10, quantity - 10))}
                                className="px-2 py-1 text-gray-600 hover:bg-gray-100 rounded-l-lg"
                            >-</button>
                            <input 
                                type="number" 
                                value={quantity}
                                onChange={(e) => setQuantity(parseInt(e.target.value) || 0)}
                                className="w-20 text-center border-x-0 border-y-0 p-1 text-sm focus:ring-0"
                                step="10"
                                min="10"
                                max={product.stock}
                            />
                            <button 
                                type="button"
                                onClick={() => setQuantity(Math.min(product.stock, quantity + 10))}
                                disabled={quantity + 10 > product.stock}
                                className="px-2 py-1 text-gray-600 hover:bg-gray-100 rounded-r-lg disabled:opacity-50 disabled:cursor-not-allowed"
                            >+</button>
                        </div>
                    </div>
                    
                    <button 
                        onClick={handleAdd}
                        disabled={product.stock <= 0 || added || quantity > product.stock || quantity % 10 !== 0}
                        className={`w-full py-2 rounded-lg font-medium text-sm transition-colors ${
                            added 
                                ? 'bg-green-500 text-white'
                                : (product.stock > 0 && quantity <= product.stock && quantity % 10 === 0)
                                    ? 'bg-primary-600 hover:bg-primary-700 text-white' 
                                    : 'bg-gray-200 text-gray-500 cursor-not-allowed'
                        }`}
                    >
                        {added ? 'Ditambahkan ke Keranjang' : (quantity > product.stock ? 'Stok Tidak Cukup' : 'Tambah ke Keranjang')}
                    </button>
                </div>
            </div>
        </div>
    );
};
