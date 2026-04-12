import { useState, useCallback } from 'react';
import api from '../utils/api';

export const useOrders = () => {
    const [orders, setOrders] = useState([]);
    const [loading, setLoading] = useState(false);
    const [meta, setMeta] = useState(null);

    const fetchOrders = useCallback(async (params = {}) => {
        setLoading(true);
        try {
            const { data } = await api.get('/orders', { params });
            setOrders(data.data);
            setMeta(data.meta);
        } catch (error) {
            console.error('Failed to fetch orders', error);
        } finally {
            setLoading(false);
        }
    }, []);

    const fetchOrder = async (id) => {
        try {
            const { data } = await api.get(`/orders/${id}`);
            return data.data;
        } catch (error) {
            console.error('Failed to fetch order', error);
            throw error;
        }
    };

    const createOrder = async (payload) => {
        const { data } = await api.post('/orders', payload);
        return data.data;
    };

    const cancelOrder = async (id) => {
        const { data } = await api.delete(`/orders/${id}`);
        return data;
    };

    return { orders, loading, meta, fetchOrders, fetchOrder, createOrder, cancelOrder };
};
