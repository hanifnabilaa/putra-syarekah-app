import React, { useEffect } from 'react';
import { useOrders } from '../hooks/useOrders';
import { StatusBadge } from '../components/StatusBadge';
import { formatRupiah, formatDate } from '../utils/helpers';
import { Link } from 'react-router-dom';
import { ChevronRight } from 'lucide-react';

const Orders = () => {
    const { orders, loading, fetchOrders } = useOrders();

    useEffect(() => {
        fetchOrders();
    }, [fetchOrders]);

    return (
        <div className="space-y-6">
            <div className="md:flex md:items-center md:justify-between">
                <div className="min-w-0 flex-1">
                    <h2 className="text-2xl font-bold leading-7 text-gray-900 sm:truncate sm:text-3xl sm:tracking-tight">
                        Riwayat Pesanan
                    </h2>
                </div>
            </div>

            {loading ? (
                <div className="text-center py-12">Loading...</div>
            ) : orders.length > 0 ? (
                <div className="bg-white shadow-sm ring-1 ring-black ring-opacity-5 rounded-lg overflow-hidden">
                    <ul role="list" className="divide-y divide-gray-200">
                        {orders.map((order) => (
                            <li key={order.id}>
                                <Link to={`/orders/${order.id}`} className="block hover:bg-gray-50 transition-colors">
                                    <div className="px-4 py-4 sm:px-6 flex items-center justify-between">
                                        <div className="flex-1 min-w-0">
                                            <div className="flex items-center justify-between">
                                                <p className="text-sm font-bold text-primary-600 truncate">{order.order_code}</p>
                                                <div className="ml-2 flex-shrink-0 flex">
                                                    <StatusBadge status={order.status} label={order.status_label} />
                                                </div>
                                            </div>
                                            <div className="mt-2 sm:flex sm:justify-between">
                                                <div className="sm:flex">
                                                    <p className="flex items-center text-sm text-gray-500">
                                                        {order.items?.length || 0} macam kitab
                                                    </p>
                                                </div>
                                                <div className="mt-2 flex items-center text-sm text-gray-500 sm:mt-0">
                                                    <p>
                                                        Total: <span className="font-semibold text-gray-900">{formatRupiah(order.total_bill)}</span>
                                                    </p>
                                                </div>
                                            </div>
                                            <div className="mt-2 text-xs text-gray-400">
                                                Dipesan: {formatDate(order.created_at)}
                                            </div>
                                        </div>
                                        <div className="ml-5 flex-shrink-0">
                                            <ChevronRight className="h-5 w-5 text-gray-400" />
                                        </div>
                                    </div>
                                </Link>
                            </li>
                        ))}
                    </ul>
                </div>
            ) : (
                <div className="text-center py-12 bg-white rounded-xl shadow-sm border border-gray-100">
                    <p className="text-gray-500">Belum ada riwayat pesanan.</p>
                </div>
            )}
        </div>
    );
};

export default Orders;
