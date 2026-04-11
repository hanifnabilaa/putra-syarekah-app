import React, { useEffect } from 'react';
import { useBills } from '../hooks/useBills';
import { StatusBadge } from '../components/StatusBadge';
import { formatRupiah, formatDate } from '../utils/helpers';
import { Link } from 'react-router-dom';
import { FileText, Calendar, CheckCircle } from 'lucide-react';

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

                                {bill.payments && bill.payments.length > 0 && (
                                    <div className="mt-5 border-t border-gray-100 pt-4">
                                        <h4 className="text-xs font-bold text-gray-500 uppercase tracking-wider mb-3">Histori Pembayaran</h4>
                                        <div className="space-y-3">
                                            {bill.payments.map((payment) => (
                                                <div key={payment.id} className="bg-gray-50 rounded-lg p-3 border border-gray-100/50">
                                                    <div className="flex justify-between items-start">
                                                        <div className="flex items-start space-x-2">
                                                            <CheckCircle className="h-4 w-4 text-green-500 bg-white rounded-full mt-0.5" />
                                                            <div>
                                                                <p className="text-sm font-semibold text-gray-900">{formatRupiah(payment.amount)}</p>
                                                                <div className="flex items-center text-xs text-gray-500 mt-1 space-x-2">
                                                                    <span className="flex items-center"><Calendar className="h-3 w-3 mr-1" /> {payment.payment_date}</span>
                                                                </div>
                                                                {payment.notes && (
                                                                    <p className="text-xs text-gray-600 mt-1.5 italic">"{payment.notes}"</p>
                                                                )}
                                                            </div>
                                                        </div>
                                                        <div className="text-right flex flex-col items-end shrink-0 pl-2">
                                                            <span className="text-[10px] bg-blue-100 text-blue-700 px-2 py-0.5 rounded-full font-medium mb-1.5 whitespace-nowrap">
                                                                Admin: {payment.confirmed_by}
                                                            </span>
                                                            {payment.proof_image_url && (
                                                                <a 
                                                                    href={payment.proof_image_url} 
                                                                    target="_blank" 
                                                                    rel="noreferrer"
                                                                    className="flex items-center text-xs text-primary-600 hover:text-primary-700 font-medium"
                                                                >
                                                                    <FileText className="h-3 w-3 mr-1" /> Lihat Bukti
                                                                </a>
                                                            )}
                                                        </div>
                                                    </div>
                                                </div>
                                            ))}
                                        </div>
                                    </div>
                                )}
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
