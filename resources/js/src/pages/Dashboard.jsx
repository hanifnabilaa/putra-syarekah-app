import React, { useEffect, useState } from 'react';
import { useAuth } from '../contexts/AuthContext';
import { StatCard } from '../components/StatCard';
import { ClipboardList, FileText, ShoppingCart } from 'lucide-react';
import { Link } from 'react-router-dom';
import api from '../utils/api';

const Dashboard = () => {
    const { user } = useAuth();
    const [stats, setStats] = useState({ active_orders: 0, pending_bills: 0 });
    const [loading, setLoading] = useState(true);

    useEffect(() => {
        const fetchStats = async () => {
            try {
                // Since we don't have a dedicated stats endpoint, we fetch the first page of orders/bills to get the counts for specific statuses if needed
                // For simplicity, we just show 0 or fetch properly if an endpoint is added later.
                const [ordersRes, billsRes] = await Promise.all([
                    api.get('/orders', { params: { status: 'submitted' } }),
                    api.get('/bills', { params: { status: 'unpaid' } }),
                ]);

                setStats({
                    active_orders: ordersRes.data.meta?.total || 0,
                    pending_bills: billsRes.data.meta?.total || 0,
                });
            } catch (error) {
                console.error("Failed to load stats", error);
            } finally {
                setLoading(false);
            }
        };

        fetchStats();
    }, []);

    return (
        <div className="space-y-6">
            <div className="md:flex md:items-center md:justify-between">
                <div className="min-w-0 flex-1">
                    <h2 className="text-2xl font-bold leading-7 text-gray-900 sm:truncate sm:text-3xl sm:tracking-tight">
                        Selamat Datang, {user?.name}
                    </h2>
                    <p className="mt-1 text-sm text-gray-500">
                        {user?.daerah?.name} - {user?.daerah?.address}
                    </p>
                </div>
            </div>

            <div className="grid grid-cols-1 gap-5 sm:grid-cols-2 lg:grid-cols-3">
                <StatCard
                    title="Pesanan Menunggu Persetujuan"
                    value={loading ? '...' : stats.active_orders}
                    icon={ClipboardList}
                    colorClass={{ bg: 'bg-blue-100', text: 'text-blue-600' }}
                />
                <StatCard
                    title="Tagihan Belum Lunas"
                    value={loading ? '...' : stats.pending_bills}
                    icon={FileText}
                    colorClass={{ bg: 'bg-red-100', text: 'text-red-600' }}
                />
            </div>

            <div className="bg-white overflow-hidden shadow-sm rounded-xl border border-gray-100 p-6">
                <h3 className="text-lg font-medium text-gray-900 mb-4">Aksi Cepat</h3>
                <div className="grid grid-cols-1 sm:grid-cols-3 gap-4">
                    <Link to="/catalog" className="flex items-center p-4 border border-gray-200 rounded-lg hover:bg-gray-50 transition-colors">
                        <ShoppingCart className="h-6 w-6 text-primary-500 mr-3" />
                        <div>
                            <h4 className="font-medium text-gray-900">Buat Pesanan Baru</h4>
                            <p className="text-sm text-gray-500">Lihat katalog Produk</p>
                        </div>
                    </Link>
                    <Link to="/orders" className="flex items-center p-4 border border-gray-200 rounded-lg hover:bg-gray-50 transition-colors">
                        <ClipboardList className="h-6 w-6 text-primary-500 mr-3" />
                        <div>
                            <h4 className="font-medium text-gray-900">Riwayat Pesanan</h4>
                            <p className="text-sm text-gray-500">Lacak pengiriman pesanan</p>
                        </div>
                    </Link>
                    <Link to="/bills" className="flex items-center p-4 border border-gray-200 rounded-lg hover:bg-gray-50 transition-colors">
                        <FileText className="h-6 w-6 text-primary-500 mr-3" />
                        <div>
                            <h4 className="font-medium text-gray-900">Daftar Tagihan</h4>
                            <p className="text-sm text-gray-500">Cek status pembayaran</p>
                        </div>
                    </Link>
                </div>
            </div>
        </div>
    );
};

export default Dashboard;
