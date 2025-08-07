// resources/js/Pages/Test.jsx
import { Head, Link, usePage } from '@inertiajs/react';
import { useState } from 'react';

export default function Test({ message, timestamp, env }) {
    const [counter, setCounter] = useState(0);
    const { auth } = usePage().props;

    return (
        <>
            <Head title="Test React + Inertia" />
            
            <div className="min-h-screen bg-gradient-to-br from-green-50 to-blue-50 p-8">
                <div className="max-w-4xl mx-auto">
                    
                    {/* Header */}
                    <div className="text-center mb-8">
                        <h1 className="text-3xl font-bold text-gray-900 mb-4">
                            🧪 Test Fonctionnel React + Inertia
                        </h1>
                        <div className="bg-white rounded-lg p-4 shadow-sm inline-block">
                            <p className="text-green-600 font-medium">{message}</p>
                            <p className="text-sm text-gray-500 mt-2">
                                Généré le : {timestamp} | Env: {env}
                            </p>
                        </div>
                    </div>

                    {/* Tests Interactifs */}
                    <div className="grid md:grid-cols-2 gap-8 mb-8">
                        
                        {/* Test State React */}
                        <div className="bg-white rounded-lg p-6 shadow-sm border">
                            <h2 className="text-xl font-semibold mb-4">
                                ⚛️ Test React State
                            </h2>
                            <div className="text-center">
                                <div className="text-4xl font-bold text-blue-600 mb-4">
                                    {counter}
                                </div>
                                <div className="space-x-2">
                                    <button 
                                        onClick={() => setCounter(counter - 1)}
                                        className="bg-red-500 text-white px-4 py-2 rounded hover:bg-red-600 transition-colors"
                                    >
                                        -1
                                    </button>
                                    <button 
                                        onClick={() => setCounter(0)}
                                        className="bg-gray-500 text-white px-4 py-2 rounded hover:bg-gray-600 transition-colors"
                                    >
                                        Reset
                                    </button>
                                    <button 
                                        onClick={() => setCounter(counter + 1)}
                                        className="bg-green-500 text-white px-4 py-2 rounded hover:bg-green-600 transition-colors"
                                    >
                                        +1
                                    </button>
                                </div>
                            </div>
                        </div>

                        {/* Test Inertia Links */}
                        <div className="bg-white rounded-lg p-6 shadow-sm border">
                            <h2 className="text-xl font-semibold mb-4">
                                🔗 Test Inertia Navigation
                            </h2>
                            <div className="space-y-3">
                                <Link 
                                    href="/"
                                    className="block w-full text-center bg-blue-600 text-white py-2 px-4 rounded hover:bg-blue-700 transition-colors"
                                >
                                    ← Accueil (SPA Navigation)
                                </Link>
                                
                                {auth.user ? (
                                    <Link 
                                        href="/dashboard"
                                        className="block w-full text-center bg-green-600 text-white py-2 px-4 rounded hover:bg-green-700 transition-colors"
                                    >
                                        Dashboard →
                                    </Link>
                                ) : (
                                    <Link 
                                        href="/login"
                                        className="block w-full text-center bg-purple-600 text-white py-2 px-4 rounded hover:bg-purple-700 transition-colors"
                                    >
                                        Se connecter →
                                    </Link>
                                )}

                                <button 
                                    onClick={() => window.location.reload()}
                                    className="block w-full text-center bg-orange-600 text-white py-2 px-4 rounded hover:bg-orange-700 transition-colors"
                                >
                                    🔄 Recharger (Test)
                                </button>
                            </div>
                        </div>
                    </div>

                    {/* Informations System */}
                    <div className="bg-white rounded-lg p-6 shadow-sm border">
                        <h2 className="text-xl font-semibold mb-4">
                            💻 Informations Système
                        </h2>
                        
                        <div className="grid md:grid-cols-3 gap-6">
                            <div>
                                <h3 className="font-medium text-gray-700 mb-2">Authentification</h3>
                                {auth.user ? (
                                    <div className="text-sm">
                                        <div className="text-green-600">✅ Connecté</div>
                                        <div className="font-medium">{auth.user.name}</div>
                                        <div className="text-gray-500">{auth.user.email}</div>
                                    </div>
                                ) : (
                                    <div className="text-sm">
                                        <div className="text-orange-600">⚠️ Non connecté</div>
                                        <div className="text-gray-500">Session publique</div>
                                    </div>
                                )}
                            </div>

                            <div>
                                <h3 className="font-medium text-gray-700 mb-2">Frontend</h3>
                                <div className="text-sm space-y-1">
                                    <div>React: ✅ Fonctionnel</div>
                                    <div>Inertia: ✅ Navigation SPA</div>
                                    <div>Tailwind: ✅ Styles appliqués</div>
                                    <div>Vite: ✅ Hot reload</div>
                                </div>
                            </div>

                            <div>
                                <h3 className="font-medium text-gray-700 mb-2">Backend</h3>
                                <div className="text-sm space-y-1">
                                    <div>Laravel: ✅ {env}</div>
                                    <div>Inertia Middleware: ✅ Actif</div>
                                    <div>Routes: ✅ Fonctionnelles</div>
                                    <div>Props: ✅ Transmises</div>
                                </div>
                            </div>
                        </div>
                    </div>

                    {/* Actions rapides */}
                    <div className="text-center mt-8">
                        <div className="space-x-4">
                            <a 
                                href="/" 
                                className="bg-gray-600 text-white px-6 py-2 rounded-lg hover:bg-gray-700 transition-colors inline-block"
                            >
                                ← Retour Accueil
                            </a>
                            <Link 
                                href="/" 
                                className="bg-blue-600 text-white px-6 py-2 rounded-lg hover:bg-blue-700 transition-colors inline-block"
                            >
                                ← Accueil (Inertia)
                            </Link>
                        </div>
                        
                        <p className="text-sm text-gray-500 mt-4">
                            Si vous voyez cette page avec interactions fonctionnelles, 
                            <strong>Laravel + React + Inertia fonctionnent parfaitement !</strong> 🎉
                        </p>
                    </div>

                </div>
            </div>
        </>
    );
}