import React, { useEffect } from 'react';
import { useBills } from '../hooks/useBills';
import { StatusBadge } from '../components/StatusBadge';
import { formatRupiah, formatDate } from '../utils/helpers';
import { Link } from 'react-router-dom';

const Bills = () => {
    const { bills, loading, fetchBills } = useBills();

    useEffect(() => {
        fetchBills();
    }, [fetchBills]);

    return (
        <div className="space-y-6">
            <div className="md:flex md:items-center md:justify-between">
                <div className="min-w-0 flex-1">
                    <h2 className="text-2xl font-bold leading-7 text-gray-900 sm:truncate sm:text-3xl sm:tracking-tight">
                        Daftar Tagihan
                    </h2>
                </div>
            </div>

            {loading ? (
                <div className="text-center py-12">Loading...</div>
            ) : bills.length > 0 ? (
                <div className="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                    {bills.map((bill) => (
                        <div key={bill.id} className="bg-white rounded-xl border border-gray-200 shadow-sm overflow-hidden flex flex-col">
                            <div className="p-5 flex-1">
                                <div className="flex justify-between items-start mb-4">
                                    <div>
                                        <p className="text-xs text-gray-500 font-medium tracking-wide uppercase">Tagihan Pesanan</p>
                                        <p className="font-bold text-gray-900 text-lg mt-1"><Link to={`/orders/${bill.order?.id}`} className="hover:underline">{bill.order?.order_code}</Link></p>
                                    </div>
                                    <StatusBadge status={bill.status} label={bill.status_label} />
                                </div>
                                <div className="space-y-3">
                                    <div className="flex justify-between text-sm">
                                        <span className="text-gray-500">Total Tagihan</span>
                                        <span className="font-medium text-gray-900">{formatRupiah(bill.total_bill)}</span>
                                    </div>
                                    <div className="flex justify-between text-sm">
                                        <span className="text-gray-500">Total Dibayar</span>
                                        <span className="font-medium text-gray-900">{formatRupiah(bill.total_paid)}</span>
                                    </div>
                                    <div className="flex justify-between text-sm font-bold border-t border-gray-100 pt-2 pb-1">
                                        <span className="text-gray-900">Sisa Tagihan</span>
                                        <span className={bill.remaining_bill > 0 ? 'text-red-600' : 'text-green-600'}>
                                            {formatRupiah(bill.remaining_bill)}
                                        </span>
                                    </div>
                                    
                                    <div className="pt-2">
                                        <div className="flex justify-between text-xs mb-1">
                                            <span className="text-gray-500">Progress</span>
                                            <span className="font-medium text-primary-600">{bill.payment_percent}%</span>
                                        </div>
                                        <div className="w-full bg-gray-200 rounded-full h-2">
                                            <div className="bg-primary-600 h-2 rounded-full" style={{ width: `${Math.min(100, bill.payment_percent)}%` }}></div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div className="bg-gray-50 px-5 py-3 border-t border-gray-200 mt-auto">
                                <p className="text-xs text-gray-500 text-center">
                                    Transfer/Setor ke Admin Keuangan
                                </p>
                            </div>
                        </div>
                    ))}
                </div>
            ) : (
                <div className="text-center py-12 bg-white rounded-xl shadow-sm border border-gray-100">
                    <p className="text-gray-500">Belum ada tagihan pesanan.</p>
                </div>
            )}
        </div>
    );
};

export default Bills;
