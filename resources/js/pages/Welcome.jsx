// resources/js/Pages/Welcome.jsx
import { Head, Link, usePage } from '@inertiajs/react';
import { useState, useEffect } from 'react';

export default function Welcome({ auth, laravelVersion, phpVersion }) {
    const [isLoading, setIsLoading] = useState(true);

    useEffect(() => {
        // Simuler un petit délai pour l'effet de chargement
        const timer = setTimeout(() => setIsLoading(false), 500);
        return () => clearTimeout(timer);
    }, []);

    if (isLoading) {
        return (
            <div className="min-h-screen bg-gray-50 flex items-center justify-center">
                <div className="text-center">
                    <div className="animate-spin rounded-full h-12 w-12 border-b-2 border-blue-600 mx-auto mb-4"></div>
                    <p className="text-gray-600">Chargement de l'application...</p>
                </div>
            </div>
        );
    }

    return (
        <>
            <Head title="TechMédiation - Modernisation Laravel + React" />
            
            <div className="min-h-screen bg-gradient-to-br from-blue-50 via-white to-blue-50">
                
                {/* Header */}
                <header className="bg-white shadow-sm border-b">
                    <div className="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                        <div className="flex justify-between h-16 items-center">
                            
                            {/* Logo */}
                            <div className="flex items-center">
                                <h1 className="text-2xl font-bold text-gray-900">
                                    🏛️ <span className="text-blue-600">TechMédiation</span>
                                </h1>
                            </div>

                            {/* Navigation */}
                            <nav className="flex items-center space-x-4">
                                {auth.user ? (
                                    <>
                                        <span className="text-sm text-gray-700">
                                            Bonjour, <strong>{auth.user.name}</strong>
                                        </span>
                                        <Link
                                            href="/dashboard"
                                            className="bg-blue-600 text-white px-4 py-2 rounded-md text-sm font-medium hover:bg-blue-700 transition-colors"
                                        >
                                            Dashboard
                                        </Link>
                                    </>
                                ) : (
                                    <>
                                        <Link
                                            href={route('login')}
                                            className="text-gray-700 hover:text-blue-600 px-3 py-2 text-sm font-medium transition-colors"
                                        >
                                            Se connecter
                                        </Link>
                                        <Link
                                            href={route('register')}
                                            className="bg-blue-600 text-white px-4 py-2 rounded-md text-sm font-medium hover:bg-blue-700 transition-colors"
                                        >
                                            S'inscrire
                                        </Link>
                                    </>
                                )}
                            </nav>
                        </div>
                    </div>
                </header>

                {/* Hero Section */}
                <main className="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-12">
                    
                    <div className="text-center mb-16">
                        <h2 className="text-4xl sm:text-5xl font-bold text-gray-900 mb-6">
                            Application de 
                            <span className="text-blue-600 block sm:inline sm:ml-2">Médiation</span>
                        </h2>
                        <p className="text-xl text-gray-600 max-w-3xl mx-auto leading-relaxed">
                            Migration et modernisation complète de l'application existante 
                            vers <strong>Laravel 12</strong> avec interface <strong>React 19</strong>
                        </p>
                    </div>

                    {/* Status Cards */}
                    <div className="grid md:grid-cols-2 lg:grid-cols-4 gap-6 mb-16">
                        
                        <div className="bg-white rounded-xl p-6 shadow-sm border hover:shadow-md transition-shadow">
                            <div className="flex items-center mb-4">
                                <div className="w-12 h-12 bg-red-100 rounded-lg flex items-center justify-center">
                                    <span className="text-2xl">🏗️</span>
                                </div>
                                <div className="ml-4">
                                    <h3 className="font-semibold text-gray-900">Laravel</h3>
                                    <p className="text-sm text-gray-500">Backend API</p>
                                </div>
                            </div>
                            <div className="text-sm">
                                <div className="flex justify-between mb-1">
                                    <span>Version:</span>
                                    <span className="font-mono text-green-600">{laravelVersion}</span>
                                </div>
                                <div className="flex justify-between">
                                    <span>Status:</span>
                                    <span className="text-green-600 font-medium">✅ Opérationnel</span>
                                </div>
                            </div>
                        </div>

                        <div className="bg-white rounded-xl p-6 shadow-sm border hover:shadow-md transition-shadow">
                            <div className="flex items-center mb-4">
                                <div className="w-12 h-12 bg-blue-100 rounded-lg flex items-center justify-center">
                                    <span className="text-2xl">⚛️</span>
                                </div>
                                <div className="ml-4">
                                    <h3 className="font-semibold text-gray-900">React</h3>
                                    <p className="text-sm text-gray-500">Interface utilisateur</p>
                                </div>
                            </div>
                            <div className="text-sm">
                                <div className="flex justify-between mb-1">
                                    <span>Version:</span>
                                    <span className="font-mono text-blue-600">19</span>
                                </div>
                                <div className="flex justify-between">
                                    <span>Status:</span>
                                    <span className="text-green-600 font-medium">✅ Fonctionnel</span>
                                </div>
                            </div>
                        </div>

                        <div className="bg-white rounded-xl p-6 shadow-sm border hover:shadow-md transition-shadow">
                            <div className="flex items-center mb-4">
                                <div className="w-12 h-12 bg-purple-100 rounded-lg flex items-center justify-center">
                                    <span className="text-2xl">🔗</span>
                                </div>
                                <div className="ml-4">
                                    <h3 className="font-semibold text-gray-900">Inertia.js</h3>
                                    <p className="text-sm text-gray-500">Bridge React-Laravel</p>
                                </div>
                            </div>
                            <div className="text-sm">
                                <div className="flex justify-between mb-1">
                                    <span>SPA:</span>
                                    <span className="font-mono text-purple-600">Enabled</span>
                                </div>
                                <div className="flex justify-between">
                                    <span>Status:</span>
                                    <span className="text-green-600 font-medium">✅ Configuré</span>
                                </div>
                            </div>
                        </div>

                        <div className="bg-white rounded-xl p-6 shadow-sm border hover:shadow-md transition-shadow">
                            <div className="flex items-center mb-4">
                                <div className="w-12 h-12 bg-green-100 rounded-lg flex items-center justify-center">
                                    <span className="text-2xl">🎨</span>
                                </div>
                                <div className="ml-4">
                                    <h3 className="font-semibold text-gray-900">Tailwind</h3>
                                    <p className="text-sm text-gray-500">Framework CSS</p>
                                </div>
                            </div>
                            <div className="text-sm">
                                <div className="flex justify-between mb-1">
                                    <span>Version:</span>
                                    <span className="font-mono text-green-600">4.x</span>
                                </div>
                                <div className="flex justify-between">
                                    <span>Status:</span>
                                    <span className="text-green-600 font-medium">✅ Actif</span>
                                </div>
                            </div>
                        </div>
                    </div>

                    {/* Features Preview */}
                    <div className="bg-white rounded-xl p-8 shadow-sm border mb-16">
                        <h3 className="text-2xl font-bold text-gray-900 mb-6 text-center">
                            🎯 Fonctionnalités en Développement
                        </h3>
                        
                        <div className="grid md:grid-cols-3 gap-8">
                            <div className="text-center">
                                <div className="w-16 h-16 bg-blue-100 rounded-full flex items-center justify-center mx-auto mb-4">
                                    <span className="text-2xl">👥</span>
                                </div>
                                <h4 className="font-semibold text-gray-900 mb-2">Gestion Utilisateurs</h4>
                                <p className="text-sm text-gray-600">
                                    Authentification, rôles et permissions avec interface React moderne
                                </p>
                            </div>

                            <div className="text-center">
                                <div className="w-16 h-16 bg-green-100 rounded-full flex items-center justify-center mx-auto mb-4">
                                    <span className="text-2xl">📁</span>
                                </div>
                                <h4 className="font-semibold text-gray-900 mb-2">Dossiers de Médiation</h4>
                                <p className="text-sm text-gray-600">
                                    CRUD complet, vue Kanban et système de recherche avancée
                                </p>
                            </div>

                            <div className="text-center">
                                <div className="w-16 h-16 bg-purple-100 rounded-full flex items-center justify-center mx-auto mb-4">
                                    <span className="text-2xl">🔔</span>
                                </div>
                                <h4 className="font-semibold text-gray-900 mb-2">Notifications</h4>
                                <p className="text-sm text-gray-600">
                                    Système d'alertes temps réel et historique des actions
                                </p>
                            </div>
                        </div>
                    </div>

                    {/* Call to Action */}
                    <div className="text-center">
                        {!auth.user && (
                            <div className="space-y-4">
                                <p className="text-gray-600">
                                    Prêt à découvrir la nouvelle application ?
                                </p>
                                <div className="space-x-4">
                                    <Link
                                        href={route('register')}
                                        className="bg-blue-600 text-white px-8 py-3 rounded-lg font-medium hover:bg-blue-700 transition-colors inline-block"
                                    >
                                        Créer un compte
                                    </Link>
                                    <Link
                                        href="/login"
                                        className="bg-gray-100 text-gray-700 px-8 py-3 rounded-lg font-medium hover:bg-gray-200 transition-colors inline-block"
                                    >
                                        Se connecter
                                    </Link>
                                </div>
                            </div>
                        )}
                    </div>

                </main>

                {/* Footer */}
                <footer className="border-t bg-gray-50 py-8 mt-16">
                    <div className="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                        <div className="text-center text-sm text-gray-500">
                            <p>
                                Application TechMédiation - Modernisation Laravel {laravelVersion} + React 19
                            </p>
                            {process.env.NODE_ENV === 'development' && (
                                <p className="mt-2">
                                    <strong>Mode Développement</strong> - PHP {phpVersion}
                                </p>
                            )}
                        </div>
                    </div>
                </footer>

            </div>
        </>
    );
}