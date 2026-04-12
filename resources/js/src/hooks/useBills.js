import { useState, useCallback } from 'react';
import api from '../utils/api';

export const useBills = () => {
    const [bills, setBills] = useState([]);
    const [loading, setLoading] = useState(false);
    const [meta, setMeta] = useState(null);

    const fetchBills = useCallback(async (params = {}) => {
        setLoading(true);
        try {
            const { data } = await api.get('/bills', { params });
            setBills(data.data);
            setMeta(data.meta);
        } catch (error) {
            console.error('Failed to fetch bills', error);
        } finally {
            setLoading(false);
        }
    }, []);

    const fetchBill = async (id) => {
        try {
            const { data } = await api.get(`/bills/${id}`);
            return data.data;
        } catch (error) {
            console.error('Failed to fetch bill', error);
            throw error;
        }
    };

    return { bills, loading, meta, fetchBills, fetchBill };
};
