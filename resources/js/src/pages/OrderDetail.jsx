import React, { useEffect, useState } from 'react';
import { useParams, Link } from 'react-router-dom';
import { useOrders } from '../hooks/useOrders';
import { StatusBadge } from '../components/StatusBadge';
import { formatRupiah, formatDate } from '../utils/helpers';
import { ArrowLeft, Package } from 'lucide-react';

const OrderDetail = () => {
    const { id } = useParams();
    const { fetchOrder, cancelOrder } = useOrders();
    const [order, setOrder] = useState(null);
    const [loading, setLoading] = useState(true);

    useEffect(() => {
        const load = async () => {
            try {
                const data = await fetchOrder(id);
                setOrder(data);
            } catch (error) {
                // Handle error
            } finally {
                setLoading(false);
            }
        };
        load();
    }, [id, fetchOrder]);

    const handleCancel = async () => {
        if (window.confirm('Apakah Anda yakin ingin membatalkan pesanan ini?')) {
            try {
                await cancelOrder(id);
                alert('Pesanan dibatalkan.');
                setOrder(await fetchOrder(id));
            } catch (err) {
                alert('Gagal membatalkan pesanan. Mungkin sudah disetujui penjual.');
            }
        }
    };

    if (loading) return <div className="text-center py-12">Loading...</div>;
    if (!order) return <div className="text-center py-12">Pesanan tidak ditemukan.</div>;

    return (
        <div className="space-y-6">
            <div className="flex items-center space-x-4">
                <Link to="/orders" className="p-2 bg-white rounded-full shadow-sm hover:bg-gray-50">
                    <ArrowLeft className="h-5 w-5 text-gray-600" />
                </Link>
                <h2 className="text-2xl font-bold leading-7 text-gray-900 sm:truncate sm:text-3xl sm:tracking-tight">
                    Pesanan #{order.order_code}
                </h2>
                <StatusBadge status={order.status} label={order.status_label} />
            </div>

            <div className="grid grid-cols-1 lg:grid-cols-3 gap-6">
                <div className="lg:col-span-2 space-y-6">
                    <div className="bg-white shadow-sm border border-gray-100 rounded-xl overflow-hidden">
                        <div className="border-b border-gray-200 px-6 py-4">
                            <h3 className="text-lg font-bold text-gray-900">Daftar Item</h3>
                        </div>
                        <ul className="divide-y divide-gray-200">
                            {order.items.map((item) => (
                                <li key={item.id} className="p-6 flex items-start space-x-4">
                                    <div className="w-20 h-20 bg-gray-200 rounded-md flex-shrink-0">
                                        {item.product?.image_url ? (
                                            <img src={item.product.image_url} alt="" className="w-full h-full object-cover rounded-md" />
                                        ) : (
                                            <div className="w-full h-full flex justify-center items-center text-xs text-gray-400">No Img</div>
                                        )}
                                    </div>
                                    <div className="flex-1">
                                        <h4 className="font-bold text-gray-900">{item.product?.name}</h4>
                                        <p className="text-sm text-gray-500 mt-1">{formatRupiah(item.unit_price)} x {item.quantity}</p>
                                        <div className="mt-2 flex items-center text-xs space-x-4">
                                            <span className="text-blue-600 bg-blue-50 px-2 py-1 rounded">Terkirim: {item.shipped_quantity}</span>
                                            <span className="text-orange-600 bg-orange-50 px-2 py-1 rounded">Sisa: {item.remaining_quantity}</span>
                                        </div>
                                    </div>
                                    <div className="font-bold text-gray-900">
                                        {formatRupiah(item.subtotal)}
                                    </div>
                                </li>
                            ))}
                        </ul>
                    </div>

                    {order.shipments && order.shipments.length > 0 && (
                        <div className="bg-white shadow-sm border border-gray-100 rounded-xl overflow-hidden">
                            <div className="border-b border-gray-200 px-6 py-4">
                                <h3 className="text-lg font-bold text-gray-900">Riwayat Pengiriman</h3>
                            </div>
                            <div className="p-6">
                                <div className="flow-root">
                                    <ul className="-mb-8">
                                        {order.shipments.map((shipment, sIdx) => (
                                            <li key={shipment.id}>
                                                <div className="relative pb-8">
                                                    {sIdx !== order.shipments.length - 1 ? (
                                                        <span className="absolute top-4 left-4 -ml-px h-full w-0.5 bg-gray-200" aria-hidden="true" />
                                                    ) : null}
                                                    <div className="relative flex space-x-3">
                                                        <div>
                                                            <span className="h-8 w-8 rounded-full bg-blue-100 flex items-center justify-center ring-8 ring-white">
                                                                <Package className="h-4 w-4 text-blue-600" aria-hidden="true" />
                                                            </span>
                                                        </div>
                                                        <div className="flex min-w-0 flex-1 justify-between space-x-4 pt-1.5">
                                                            <div>
                                                                <p className="text-sm text-gray-500">
                                                                    Pengiriman via <span className="font-medium text-gray-900">{shipment.method_label}</span> <StatusBadge status={shipment.status} label={shipment.status_label} />
                                                                </p>
                                                                {shipment.notes && <p className="text-sm mt-1 text-gray-500 italic">Catatan: {shipment.notes}</p>}
                                                            </div>
                                                            <div className="whitespace-nowrap text-right text-sm text-gray-500">
                                                                <span>{shipment.shipping_date ? formatDate(shipment.shipping_date) : '-'}</span>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </li>
                                        ))}
                                    </ul>
                                </div>
                            </div>
                        </div>
                    )}
                </div>

                <div className="space-y-6">
                    <div className="bg-white shadow-sm border border-gray-100 rounded-xl overflow-hidden">
                        <div className="border-b border-gray-200 px-6 py-4">
                            <h3 className="font-bold text-gray-900">Rincian</h3>
                        </div>
                        <div className="p-6 space-y-4">
                            <div>
                                <p className="text-sm text-gray-500 font-medium">Tanggal Pemesanan</p>
                                <p className="text-sm font-semibold text-gray-900 mt-1">{order.created_at}</p>
                            </div>
                            <div>
                                <p className="text-sm text-gray-500 font-medium">Metode Pengiriman</p>
                                <p className="text-sm font-semibold text-gray-900 mt-1">{order.method_label}</p>
                            </div>
                            {order.shipping_method === 'shipping' && (
                                <div>
                                    <p className="text-sm text-gray-500 font-medium">Alamat Tujuan</p>
                                    <p className="text-sm font-semibold text-gray-900 mt-1">{order.shipping_address}</p>
                                </div>
                            )}
                            <div>
                                <p className="text-sm text-gray-500 font-medium">Total Tagihan</p>
                                <p className="text-xl font-black text-primary-600 mt-1">{formatRupiah(order.total_bill)}</p>
                            </div>
                            
                            {order.status === 'submitted' && (
                                <button
                                    onClick={handleCancel}
                                    className="w-full mt-4 bg-red-50 text-red-600 hover:bg-red-100 py-2 rounded-lg text-sm font-bold transition-colors"
                                >
                                    Batalkan Pesanan
                                </button>
                            )}

                            {order.rejection_reason && (
                                <div className="mt-4 p-3 bg-red-50 rounded border border-red-200">
                                    <p className="text-xs font-bold text-red-800">Alasan Penolakan:</p>
                                    <p className="text-sm text-red-700 mt-1">{order.rejection_reason}</p>
                                </div>
                            )}
                        </div>
                    </div>
                </div>
            </div>
        </div>
    );
};

export default OrderDetail;
