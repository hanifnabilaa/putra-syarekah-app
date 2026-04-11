import React, { useState } from 'react';
import { useAuth } from '../contexts/AuthContext';
import { User, MapPin, Phone } from 'lucide-react';
import api from '../utils/api';

const Profile = () => {
    const { user, checkAuth } = useAuth();
    const [updating, setUpdating] = useState(false);
    const [message, setMessage] = useState('');
    
    const [formData, setFormData] = useState({
        address: user?.daerah?.address || '',
        phone: user?.daerah?.phone || '',
    });

    const handleChange = (e) => {
        setFormData({ ...formData, [e.target.name]: e.target.value });
    };

    const handleSubmit = async (e) => {
        e.preventDefault();
        setUpdating(true);
        setMessage('');

        try {
            await api.post('/profile', formData, {
                headers: { 'X-HTTP-Method-Override': 'PUT' } // Laravel PUT spoofing or direct put
            });
            setMessage('Profil berhasil diperbarui.');
            await checkAuth(); // Refresh user data
        } catch (error) {
            setMessage('Gagal memperbarui profil.');
        } finally {
            setUpdating(false);
        }
    };

    return (
        <div className="space-y-6">
            <div className="md:flex md:items-center md:justify-between">
                <div className="min-w-0 flex-1">
                    <h2 className="text-2xl font-bold leading-7 text-gray-900 sm:truncate sm:text-3xl sm:tracking-tight">
                        Profil Daerah
                    </h2>
                </div>
            </div>

            <div className="bg-white shadow-sm rounded-xl border border-gray-100 overflow-hidden">
                <div className="px-4 py-5 sm:px-6 bg-gray-50 border-b border-gray-200">
                    <h3 className="text-lg leading-6 font-medium text-gray-900">
                        Informasi Akun
                    </h3>
                    <p className="mt-1 max-w-2xl text-sm text-gray-500">
                        Detail informasi daerah dan penanggung jawab yang terdaftar.
                    </p>
                </div>
                <div className="px-4 py-5 sm:p-0">
                    <dl className="sm:divide-y sm:divide-gray-200">
                        <div className="py-4 sm:py-5 sm:grid sm:grid-cols-3 sm:gap-4 sm:px-6">
                            <dt className="text-sm font-medium text-gray-500">Nama Daerah</dt>
                            <dd className="mt-1 text-sm text-gray-900 sm:mt-0 sm:col-span-2 font-bold">{user?.daerah?.name}</dd>
                        </div>
                        <div className="py-4 sm:py-5 sm:grid sm:grid-cols-3 sm:gap-4 sm:px-6">
                            <dt className="text-sm font-medium text-gray-500">Nama Penanggung Jawab</dt>
                            <dd className="mt-1 text-sm text-gray-900 sm:mt-0 sm:col-span-2">{user?.daerah?.pic_name}</dd>
                        </div>
                        <div className="py-4 sm:py-5 sm:grid sm:grid-cols-3 sm:gap-4 sm:px-6">
                            <dt className="text-sm font-medium text-gray-500 flex items-center"><Phone className="mr-2 h-4 w-4" /> No HP PJ</dt>
                            <dd className="mt-1 text-sm text-gray-900 sm:mt-0 sm:col-span-2">{user?.daerah?.pic_phone}</dd>
                        </div>
                        <div className="py-4 sm:py-5 sm:grid sm:grid-cols-3 sm:gap-4 sm:px-6 bg-yellow-50">
                            <dt className="text-sm font-medium text-gray-500">Info Akun</dt>
                            <dd className="mt-1 text-sm text-gray-900 sm:mt-0 sm:col-span-2">
                                Login via: <span className="font-mono bg-yellow-100 px-1">{user?.username}</span> atau <span className="font-mono bg-yellow-100 px-1">{user?.email}</span>
                            </dd>
                        </div>
                    </dl>
                </div>
            </div>

            <div className="bg-white shadow-sm rounded-xl border border-gray-100 mt-6 overflow-hidden">
                <div className="px-4 py-5 sm:px-6 border-b border-gray-200">
                    <h3 className="text-lg leading-6 font-medium text-gray-900">
                        Edit Informasi Kontak
                    </h3>
                    <p className="mt-1 max-w-2xl text-sm text-gray-500">
                        Perbarui alamat pengiriman dan telepon umum daerah Anda.
                    </p>
                </div>
                
                {message && (
                    <div className="p-4 bg-green-50 text-green-700 text-sm border-b border-green-100">
                        {message}
                    </div>
                )}
                
                <form onSubmit={handleSubmit} className="px-4 py-5 sm:p-6 space-y-4">
                    <div>
                        <label className="block text-sm font-medium text-gray-700 mb-1 flex items-center">
                            <MapPin className="mr-1 h-4 w-4 text-gray-400" /> Alamat Lengkap
                        </label>
                        <textarea
                            name="address"
                            rows={3}
                            value={formData.address}
                            onChange={handleChange}
                            className="shadow-sm focus:ring-primary-500 focus:border-primary-500 block w-full sm:text-sm border-gray-300 rounded-md"
                            placeholder="Alamat lengkap tujuan pengiriman kitab"
                        />
                    </div>
                    <div>
                        <label className="block text-sm font-medium text-gray-700 mb-1 flex items-center">
                            <Phone className="mr-1 h-4 w-4 text-gray-400" /> Telepon (Opsional)
                        </label>
                        <input
                            type="text"
                            name="phone"
                            value={formData.phone}
                            onChange={handleChange}
                            className="shadow-sm focus:ring-primary-500 focus:border-primary-500 block w-full sm:text-sm border-gray-300 rounded-md"
                        />
                    </div>
                    <div className="pt-2">
                        <button
                            type="submit"
                            disabled={updating}
                            className="inline-flex justify-center py-2 px-4 border border-transparent shadow-sm text-sm font-medium rounded-md text-white bg-primary-600 hover:bg-primary-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-primary-500 disabled:opacity-75"
                        >
                            {updating ? 'Menyimpan...' : 'Simpan Perubahan'}
                        </button>
                    </div>
                </form>
            </div>
        </div>
    );
};

export default Profile;
