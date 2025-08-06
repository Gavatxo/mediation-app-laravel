// resources/js/Pages/Dashboard.jsx
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout';
import { Head, Link } from '@inertiajs/react';

export default function Dashboard({ auth, stats = {} }) {
    // Données de démonstration (à remplacer par vraies données plus tard)
    const defaultStats = {
        total_dossiers: 127,
        dossiers_actifs: 23,
        notifications: 5,
        actions_recentes: [
            { id: 1, action: 'Nouveau dossier créé', dossier: 'MED-2025-001', time: '2 min ago' },
            { id: 2, action: 'Rendez-vous planifié', dossier: 'MED-2025-002', time: '1h ago' },
            { id: 3, action: 'Document envoyé', dossier: 'MED-2024-999', time: '3h ago' },
        ]
    };

    const dashboardStats = { ...defaultStats, ...stats };

    return (
        <AuthenticatedLayout
            user={auth.user}
            header={
                <div className="flex justify-between items-center">
                    <h2 className="font-semibold text-xl text-gray-800 leading-tight">
                        Dashboard - Médiation
                    </h2>
                    <div className="text-sm text-gray-500">
                        Connecté en tant que {auth.user.name}
                    </div>
                </div>
            }
        >
            <Head title="Dashboard" />

            <div className="py-12">
                <div className="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">
                    
                    {/* Message de bienvenue */}
                    <div className="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                        <div className="p-6 text-gray-900 bg-gradient-to-r from-blue-50 to-indigo-50">
                            <div className="flex items-center">
                                <div className="flex-shrink-0">
                                    <div className="w-12 h-12 bg-blue-100 rounded-full flex items-center justify-center">
                                        <span className="text-2xl">👋</span>
                                    </div>
                                </div>
                                <div className="ml-4">
                                    <h3 className="text-lg font-medium text-gray-900">
                                        Bienvenue, {auth.user.name}!
                                    </h3>
                                    <p className="text-gray-600">
                                        Voici un aperçu de votre activité de médiation
                                    </p>
                                </div>
                            </div>
                        </div>
                    </div>

                    {/* Statistiques */}
                    <div className="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4">
                        
                        {/* Total Dossiers */}
                        <div className="bg-white overflow-hidden shadow-sm sm:rounded-lg hover:shadow-md transition-shadow">
                            <div className="p-6">
                                <div className="flex items-center">
                                    <div className="flex-shrink-0">
                                        <div className="w-12 h-12 bg-blue-100 rounded-lg flex items-center justify-center">
                                            <span className="text-2xl">📁</span>
                                        </div>
                                    </div>
                                    <div className="ml-4">
                                        <dt className="text-sm font-medium text-gray-500 truncate">
                                            Total Dossiers
                                        </dt>
                                        <dd className="text-3xl font-bold text-gray-900">
                                            {dashboardStats.total_dossiers}
                                        </dd>
                                    </div>
                                </div>
                            </div>
                        </div>

                        {/* Dossiers Actifs */}
                        <div className="bg-white overflow-hidden shadow-sm sm:rounded-lg hover:shadow-md transition-shadow">
                            <div className="p-6">
                                <div className="flex items-center">
                                    <div className="flex-shrink-0">
                                        <div className="w-12 h-12 bg-green-100 rounded-lg flex items-center justify-center">
                                            <span className="text-2xl">🔥</span>
                                        </div>
                                    </div>
                                    <div className="ml-4">
                                        <dt className="text-sm font-medium text-gray-500 truncate">
                                            En Cours
                                        </dt>
                                        <dd className="text-3xl font-bold text-green-600">
                                            {dashboardStats.dossiers_actifs}
                                        </dd>
                                    </div>
                                </div>
                            </div>
                        </div>

                        {/* Notifications */}
                        <div className="bg-white overflow-hidden shadow-sm sm:rounded-lg hover:shadow-md transition-shadow">
                            <div className="p-6">
                                <div className="flex items-center">
                                    <div className="flex-shrink-0">
                                        <div className="w-12 h-12 bg-yellow-100 rounded-lg flex items-center justify-center">
                                            <span className="text-2xl">🔔</span>
                                        </div>
                                    </div>
                                    <div className="ml-4">
                                        <dt className="text-sm font-medium text-gray-500 truncate">
                                            Notifications
                                        </dt>
                                        <dd className="flex items-center">
                                            <span className="text-3xl font-bold text-orange-600">
                                                {dashboardStats.notifications}
                                            </span>
                                            {dashboardStats.notifications > 0 && (
                                                <span className="ml-2 px-2 py-1 bg-red-100 text-red-800 text-xs font-medium rounded-full">
                                                    Nouveau
                                                </span>
                                            )}
                                        </dd>
                                    </div>
                                </div>
                            </div>
                        </div>

                        {/* Taux de Réussite */}
                        <div className="bg-white overflow-hidden shadow-sm sm:rounded-lg hover:shadow-md transition-shadow">
                            <div className="p-6">
                                <div className="flex items-center">
                                    <div className="flex-shrink-0">
                                        <div className="w-12 h-12 bg-purple-100 rounded-lg flex items-center justify-center">
                                            <span className="text-2xl">📊</span>
                                        </div>
                                    </div>
                                    <div className="ml-4">
                                        <dt className="text-sm font-medium text-gray-500 truncate">
                                            Taux Réussite
                                        </dt>
                                        <dd className="text-3xl font-bold text-purple-600">
                                            87%
                                        </dd>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    {/* Actions Rapides */}
                    <div className="grid grid-cols-1 lg:grid-cols-3 gap-6">
                        
                        {/* Liens Rapides */}
                        <div className="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                            <div className="p-6">
                                <h3 className="text-lg font-medium text-gray-900 mb-4">
                                    Actions Rapides
                                </h3>
                                <div className="space-y-3">
                                    <Link
                                        href="/dossiers/create"
                                        className="flex items-center p-3 rounded-lg bg-blue-50 hover:bg-blue-100 transition-colors"
                                    >
                                        <span className="text-2xl mr-3">➕</span>
                                        <span className="font-medium text-blue-700">Nouveau Dossier</span>
                                    </Link>
                                    
                                    <Link
                                        href="/dossiers"
                                        className="flex items-center p-3 rounded-lg bg-green-50 hover:bg-green-100 transition-colors"
                                    >
                                        <span className="text-2xl mr-3">📋</span>
                                        <span className="font-medium text-green-700">Voir Tous les Dossiers</span>
                                    </Link>
                                    
                                    <Link
                                        href="/dossiers/kanban"
                                        className="flex items-center p-3 rounded-lg bg-purple-50 hover:bg-purple-100 transition-colors"
                                    >
                                        <span className="text-2xl mr-3">🎯</span>
                                        <span className="font-medium text-purple-700">Vue Kanban</span>
                                    </Link>
                                </div>
                            </div>
                        </div>

                        {/* Activité Récente */}
                        <div className="lg:col-span-2 bg-white overflow-hidden shadow-sm sm:rounded-lg">
                            <div className="p-6">
                                <h3 className="text-lg font-medium text-gray-900 mb-4">
                                    Activité Récente
                                </h3>
                                <div className="space-y-3">
                                    {dashboardStats.actions_recentes.map((action) => (
                                        <div key={action.id} className="flex items-center justify-between py-3 border-b border-gray-100 last:border-b-0">
                                            <div className="flex items-center">
                                                <div className="flex-shrink-0 w-2 h-2 bg-green-400 rounded-full mr-3"></div>
                                                <div>
                                                    <p className="text-sm font-medium text-gray-900">
                                                        {action.action}
                                                    </p>
                                                    <p className="text-sm text-gray-500">
                                                        Dossier: {action.dossier}
                                                    </p>
                                                </div>
                                            </div>
                                            <span className="text-xs text-gray-400">
                                                {action.time}
                                            </span>
                                        </div>
                                    ))}
                                </div>
                                
                                <div className="mt-4">
                                    <Link
                                        href="/activity"
                                        className="text-sm text-blue-600 hover:text-blue-700 font-medium"
                                    >
                                        Voir toute l'activité →
                                    </Link>
                                </div>
                            </div>
                        </div>
                    </div>

                    {/* Dossiers Urgents */}
                    <div className="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                        <div className="p-6">
                            <div className="flex items-center justify-between mb-4">
                                <h3 className="text-lg font-medium text-gray-900">
                                    🚨 Dossiers Urgents
                                </h3>
                                <Link
                                    href="/dossiers?filter=urgent"
                                    className="text-sm text-blue-600 hover:text-blue-700 font-medium"
                                >
                                    Voir tous →
                                </Link>
                            </div>
                            
                            <div className="bg-red-50 border border-red-200 rounded-lg p-4">
                                <div className="flex items-center">
                                    <span className="text-2xl mr-3">⏰</span>
                                    <div>
                                        <p className="text-sm font-medium text-red-900">
                                            3 dossiers nécessitent votre attention immédiate
                                        </p>
                                        <p className="text-sm text-red-700">
                                            Délais d'échéance dans moins de 48h
                                        </p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                </div>
            </div>
        </AuthenticatedLayout>
    );
}