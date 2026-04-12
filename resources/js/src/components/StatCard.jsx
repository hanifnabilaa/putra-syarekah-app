import React from 'react';
import { classNames } from '../utils/helpers';

export const StatCard = ({ title, value, icon: Icon, colorClass }) => {
    return (
        <div className="bg-white overflow-hidden shadow-sm rounded-xl border border-gray-100">
            <div className="p-5">
                <div className="flex items-center">
                    <div className="flex-shrink-0">
                        <div className={classNames('p-3 rounded-md', colorClass.bg)}>
                            <Icon className={classNames('h-6 w-6', colorClass.text)} aria-hidden="true" />
                        </div>
                    </div>
                    <div className="ml-5 w-0 flex-1">
                        <dl>
                            <dt className="text-sm font-medium text-gray-500 truncate">{title}</dt>
                            <dd>
                                <div className="text-2xl font-bold text-gray-900">{value}</div>
                            </dd>
                        </dl>
                    </div>
                </div>
            </div>
        </div>
    );
};
