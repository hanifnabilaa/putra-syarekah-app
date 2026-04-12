import { useState, useCallback } from 'react';
import api from '../utils/api';

export const useProducts = () => {
    const [products, setProducts] = useState([]);
    const [loading, setLoading] = useState(false);
    const [meta, setMeta] = useState(null);

    const fetchProducts = useCallback(async (params = {}) => {
        setLoading(true);
        try {
            const { data } = await api.get('/products', { params });
            setProducts(data.data);
            setMeta(data.meta);
        } catch (error) {
            console.error('Failed to fetch products', error);
        } finally {
            setLoading(false);
        }
    }, []);

    const fetchProduct = async (id) => {
        try {
            const { data } = await api.get(`/products/${id}`);
            return data.data;
        } catch (error) {
            console.error('Failed to fetch product', error);
            throw error;
        }
    };

    return { products, loading, meta, fetchProducts, fetchProduct };
};
