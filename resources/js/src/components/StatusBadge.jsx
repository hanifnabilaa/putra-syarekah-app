import React from 'react';
import { classNames } from '../utils/helpers';

export const StatusBadge = ({ status, label }) => {
    // Assuming status matches the enum values
    const getColors = () => {
        switch (status) {
            case 'approved':
            case 'paid':
            case 'delivered':
                return 'bg-green-100 text-green-800 border-green-200';
            case 'rejected':
            case 'canceled':
                return 'bg-red-100 text-red-800 border-red-200';
            case 'shipped':
            case 'partial':
                return 'bg-blue-100 text-blue-800 border-blue-200';
            case 'submitted':
            case 'unpaid':
            case 'pending':
            default:
                return 'bg-yellow-100 text-yellow-800 border-yellow-200';
        }
    };

    return (
        <span className={classNames(
            'inline-flex items-center rounded-full px-2.5 py-0.5 text-xs font-medium border',
            getColors()
        )}>
            {label || status}
        </span>
    );
};
