import React, { useState } from 'react';
import { Outlet, NavLink, useNavigate } from 'react-router-dom';
import { Menu, X, Home, BookOpen, ShoppingCart, ClipboardList, FileText, User as UserIcon, LogOut } from 'lucide-react';
import { useAuth } from '../contexts/AuthContext';
import { useCart } from '../contexts/CartContext';
import { classNames } from '../utils/helpers';

const navigation = [
    { name: 'Dashboard', href: '/', icon: Home },
    { name: 'Katalog Kitab', href: '/catalog', icon: BookOpen },
    { name: 'Pesanan Saya', href: '/orders', icon: ClipboardList },
    { name: 'Tagihan', href: '/bills', icon: FileText },
    { name: 'Profil Daerah', href: '/profile', icon: UserIcon },
];

export const Layout = () => {
    const { user, logout } = useAuth();
    const { cartCount } = useCart();
    const navigate = useNavigate();
    const [sidebarOpen, setSidebarOpen] = useState(false);

    return (
        <div className="min-h-screen bg-gray-50 flex">
            {/* Mobile sidebar visibility toggle */}
            <div className={`fixed inset-0 z-40 lg:hidden ${sidebarOpen ? 'block' : 'hidden'}`} role="dialog" aria-modal="true">
                <div className="fixed inset-0 bg-gray-600 bg-opacity-75" onClick={() => setSidebarOpen(false)}></div>
                <div className="fixed inset-y-0 left-0 flex w-64 max-w-xs flex-col bg-white pt-5 pb-4">
                    <div className="flex items-center justify-between px-4">
                        <div className="text-xl font-bold text-primary-600">Putra Syarekah</div>
                        <button type="button" className="-mr-2 flex h-10 w-10 items-center justify-center rounded-md text-gray-500 hover:text-gray-900" onClick={() => setSidebarOpen(false)}>
                            <X className="h-6 w-6" aria-hidden="true" />
                        </button>
                    </div>
                    <div className="mt-5 h-0 flex-1 overflow-y-auto">
                        <nav className="space-y-1 px-2">
                            {navigation.map((item) => (
                                <NavLink
                                    key={item.name}
                                    to={item.href}
                                    onClick={() => setSidebarOpen(false)}
                                    className={({ isActive }) => classNames(
                                        isActive ? 'bg-primary-50 text-primary-600' : 'text-gray-700 hover:bg-gray-50 hover:text-gray-900',
                                        'group flex items-center rounded-md px-2 py-2 text-base font-medium'
                                    )}
                                >
                                    <item.icon className="mr-4 h-6 w-6 flex-shrink-0" aria-hidden="true" />
                                    {item.name}
                                </NavLink>
                            ))}
                        </nav>
                    </div>
                </div>
            </div>

            {/* Desktop sidebar */}
            <div className="hidden lg:fixed lg:inset-y-0 lg:flex lg:w-64 lg:flex-col">
                <div className="flex flex-grow flex-col overflow-y-auto border-r border-gray-200 bg-white pt-5 pb-4">
                    <div className="flex items-center flex-shrink-0 px-4">
                        <div className="text-2xl font-black text-primary-600 tracking-tight">Putra Syarekah</div>
                    </div>
                    <div className="mt-5 flex flex-1 flex-col">
                        <nav className="flex-1 space-y-1 px-2 pb-4">
                            {navigation.map((item) => (
                                <NavLink
                                    key={item.name}
                                    to={item.href}
                                    className={({ isActive }) => classNames(
                                        isActive ? 'bg-primary-50 text-primary-600' : 'text-gray-700 hover:bg-gray-50 hover:text-gray-900',
                                        'group flex items-center rounded-md px-2 py-2 text-sm font-medium transition-colors'
                                    )}
                                >
                                    <item.icon className="mr-3 h-5 w-5 flex-shrink-0" aria-hidden="true" />
                                    {item.name}
                                </NavLink>
                            ))}
                        </nav>
                    </div>
                    <div className="border-t border-gray-200 pt-4 px-4">
                        <div className="flex items-center">
                            <div>
                                <p className="text-sm font-medium text-gray-700 truncate">{user?.daerah?.name || user?.name}</p>
                                <button onClick={logout} className="mt-1 text-xs font-medium text-red-600 hover:text-red-500 flex items-center">
                                    <LogOut className="mr-1 h-3 w-3" /> Keluar
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            {/* Main column */}
            <div className="flex flex-1 flex-col lg:pl-64">
                <div className="sticky top-0 z-10 flex h-16 flex-shrink-0 bg-white shadow-sm border-b border-gray-200">
                    <button type="button" className="border-r border-gray-200 px-4 text-gray-500 focus:outline-none focus:ring-2 focus:ring-inset focus:ring-primary-500 lg:hidden" onClick={() => setSidebarOpen(true)}>
                        <Menu className="h-6 w-6" aria-hidden="true" />
                    </button>
                    <div className="flex flex-1 justify-between px-4 sm:px-6 lg:px-8">
                        <div className="flex flex-1 items-center">
                            {/* Additional header content can go here */}
                        </div>
                        <div className="ml-4 flex items-center md:ml-6 gap-4">
                            <button 
                                onClick={() => navigate('/cart')}
                                className="relative rounded-full bg-white p-1 text-gray-400 hover:text-primary-600 focus:outline-none focus:ring-2 focus:ring-primary-500 focus:ring-offset-2 transition-colors"
                            >
                                <ShoppingCart className="h-6 w-6" aria-hidden="true" />
                                {cartCount > 0 && (
                                    <span className="absolute -top-1 -right-1 flex h-5 w-5 items-center justify-center rounded-full bg-red-500 text-[10px] font-bold text-white ring-2 ring-white">
                                        {cartCount}
                                    </span>
                                )}
                            </button>
                        </div>
                    </div>
                </div>

                <main className="flex-1 pb-8">
                    <div className="w-full max-w-7xl mx-auto py-6 px-4 sm:px-6 lg:px-8">
                        <Outlet />
                    </div>
                </main>
            </div>
        </div>
    );
};
