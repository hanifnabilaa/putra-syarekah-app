import React, { useState } from 'react';
import { useCart } from '../contexts/CartContext';
import { useOrders } from '../hooks/useOrders';
import { formatRupiah } from '../utils/helpers';
import { Trash2, ShoppingBag } from 'lucide-react';
import { useNavigate } from 'react-router-dom';

const Cart = () => {
    const { cart, updateQuantity, removeFromCart, cartTotal, clearCart } = useCart();
    const { createOrder, loading } = useOrders();
    const navigate = useNavigate();

    const [shippingMethod, setShippingMethod] = useState('shipping');
    const [shippingAddress, setShippingAddress] = useState('');
    const [notes, setNotes] = useState('');
    const [error, setError] = useState('');

    const handleSubmit = async (e) => {
        e.preventDefault();
        setError('');

        if (cart.length === 0) {
            setError('Keranjang belanja kosong.');
            return;
        }

        const items = cart.map((item) => ({
            product_id: item.product.id,
            quantity: item.quantity,
        }));

        const payload = {
            shipping_method: shippingMethod,
            shipping_address: shippingMethod === 'shipping' ? shippingAddress : null,
            notes,
            items,
        };

        try {
            await createOrder(payload);
            clearCart();
            alert('Pesanan berhasil dibuat!');
            navigate('/orders');
        } catch (err) {
            setError('Gagal membuat pesanan. Pastikan semua data benar dan stok mencukupi.');
        }
    };

    if (cart.length === 0) {
        return (
            <div className="text-center py-20 bg-white rounded-xl shadow-sm border border-gray-100">
                <ShoppingBag className="mx-auto h-12 w-12 text-gray-300" />
                <h3 className="mt-2 text-sm font-medium text-gray-900">Keranjang Kosong</h3>
                <p className="mt-1 text-sm text-gray-500">Anda belum memilih kitab apapun.</p>
                <div className="mt-6">
                    <button
                        onClick={() => navigate('/catalog')}
                        className="inline-flex items-center px-4 py-2 border border-transparent shadow-sm text-sm font-medium rounded-md text-white bg-primary-600 hover:bg-primary-700 focus:outline-none"
                    >
                        Pilih Kitab
                    </button>
                </div>
            </div>
        );
    }

    return (
        <div className="space-y-6">
            <h2 className="text-2xl font-bold text-gray-900">Keranjang Belanja</h2>

            {error && (
                <div className="bg-red-50 text-red-700 p-4 rounded-md border border-red-200">
                    {error}
                </div>
            )}

            <div className="lg:grid lg:grid-cols-12 lg:gap-8">
                <div className="lg:col-span-8 space-y-4">
                    {/* Daftar Item */}
                    {cart.map((item) => (
                        <div key={item.product.id} className="flex gap-4 p-4 bg-white shadow-sm border border-gray-100 rounded-xl">
                            <div className="w-24 h-24 bg-gray-200 rounded-md overflow-hidden flex-shrink-0">
                                {item.product.image_url ? (
                                    <img src={item.product.image_url} alt={item.product.name} className="w-full h-full object-cover" />
                                ) : (
                                    <div className="w-full h-full flex items-center justify-center text-xs text-gray-400">No Image</div>
                                )}
                            </div>
                            <div className="flex-1 flex flex-col justify-between">
                                <div>
                                    <div className="flex justify-between">
                                        <h3 className="text-lg font-bold text-gray-900">{item.product.name}</h3>
                                        <p className="font-semibold text-primary-600">{formatRupiah(item.product.price * item.quantity)}</p>
                                    </div>
                                    <p className="text-sm text-gray-500 mt-1">{formatRupiah(item.product.price)} / eksemplar</p>
                                </div>
                                <div className="flex justify-between items-center mt-4">
                                    <div className="flex items-center border rounded-lg">
                                        <button 
                                            type="button"
                                            onClick={() => updateQuantity(item.product.id, Math.max(10, item.quantity - 10))}
                                            className="px-2 py-1 text-gray-600 hover:bg-gray-100 rounded-l-lg"
                                        >-</button>
                                        <input 
                                            type="number" 
                                            value={item.quantity}
                                            onChange={(e) => updateQuantity(item.product.id, parseInt(e.target.value) || 0)}
                                            className="w-12 text-center border-x-0 border-y-0 p-1 text-sm focus:ring-0"
                                            step="10"
                                            min="10"
                                        />
                                        <button 
                                            type="button"
                                            onClick={() => updateQuantity(item.product.id, item.quantity + 10)}
                                            className="px-2 py-1 text-gray-600 hover:bg-gray-100 rounded-r-lg"
                                        >+</button>
                                    </div>
                                    <button 
                                        onClick={() => removeFromCart(item.product.id)}
                                        className="text-red-500 hover:text-red-700"
                                    >
                                        <Trash2 className="h-5 w-5" />
                                    </button>
                                </div>
                            </div>
                        </div>
                    ))}
                </div>

                <div className="lg:col-span-4 mt-8 lg:mt-0">
                    <div className="bg-white p-6 shadow-sm border border-gray-100 rounded-xl sticky top-24">
                        <h3 className="text-lg font-bold text-gray-900 mb-4">Ringkasan Pesanan</h3>
                        
                        <div className="border-b border-gray-200 pb-4 mb-4">
                            <div className="flex justify-between text-base font-medium text-gray-900">
                                <p>Subtotal Tagihan</p>
                                <p>{formatRupiah(cartTotal)}</p>
                            </div>
                            <p className="mt-1 text-sm text-gray-500">Harga belum termasuk ongkos kirim jika ada.</p>
                        </div>

                        <form onSubmit={handleSubmit} className="space-y-4">
                            <div>
                                <label className="block text-sm font-medium text-gray-700 mb-1">Metode Pengiriman</label>
                                <select 
                                    className="w-full border-gray-300 rounded-md shadow-sm focus:ring-primary-500 focus:border-primary-500 sm:text-sm"
                                    value={shippingMethod}
                                    onChange={(e) => setShippingMethod(e.target.value)}
                                >
                                    <option value="shipping">Dikirim ke Alamat</option>
                                    <option value="pickup">Ambil Sendiri di Percetakan</option>
                                </select>
                            </div>

                            {shippingMethod === 'shipping' && (
                                <div>
                                    <label className="block text-sm font-medium text-gray-700 mb-1">Alamat Lengkap Pengiriman</label>
                                    <textarea 
                                        className="w-full border-gray-300 rounded-md shadow-sm focus:ring-primary-500 focus:border-primary-500 sm:text-sm"
                                        rows="3"
                                        required
                                        value={shippingAddress}
                                        onChange={(e) => setShippingAddress(e.target.value)}
                                        placeholder="Masukkan alamat lengkap tujuan pengiriman"
                                    />
                                </div>
                            )}

                            <div>
                                <label className="block text-sm font-medium text-gray-700 mb-1">Catatan (Opsional)</label>
                                <textarea 
                                    className="w-full border-gray-300 rounded-md shadow-sm focus:ring-primary-500 focus:border-primary-500 sm:text-sm"
                                    rows="2"
                                    value={notes}
                                    onChange={(e) => setNotes(e.target.value)}
                                />
                            </div>

                            <button
                                type="submit"
                                disabled={loading}
                                className="w-full bg-primary-600 text-white py-3 rounded-lg font-bold shadow-sm hover:bg-primary-700 transition-colors disabled:opacity-75"
                            >
                                {loading ? 'Memproses...' : 'Buat Pesanan Sekarang'}
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    );
};

export default Cart;
