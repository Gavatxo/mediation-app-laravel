// resources/js/Pages/Dashboard.jsx
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout';
import DashboardCard from '@/Components/DashboardCard';
import { Head, Link } from '@inertiajs/react';
import { Button } from "@/components/ui/button"
import { Badge } from "@/components/ui/badge"
import { 
    FolderOpen, 
    Activity, 
    Bell, 
    TrendingUp, 
    Plus,
    Eye,
    LayoutGrid,
    AlertCircle,
    Clock
} from "lucide-react"

export default function Dashboard({ auth, stats = {} }) {
    // Données de démonstration améliorées
    const dashboardStats = {
        total_dossiers: 127,
        dossiers_actifs: 23,
        notifications: 5,
        taux_reussite: 87,
        actions_recentes: [
            { 
                id: 1, 
                action: 'Nouveau dossier créé', 
                dossier: 'MED-2025-001', 
                time: '2 min ago',
                type: 'success'
            },
            { 
                id: 2, 
                action: 'Rendez-vous planifié', 
                dossier: 'MED-2025-002', 
                time: '1h ago',
                type: 'info'
            },
            { 
                id: 3, 
                action: 'Document envoyé', 
                dossier: 'MED-2024-999', 
                time: '3h ago',
                type: 'info'
            },
        ],
        ...stats
    };

    return (
        <AuthenticatedLayout
            user={auth.user}
            header={
                <div className="flex justify-between items-center">
                    <h2 className="font-semibold text-xl text-gray-800 leading-tight">
                        Dashboard Médiation
                    </h2>
                    <Badge variant="outline" className="text-sm">
                        {new Date().toLocaleDateString('fr-FR')}
                    </Badge>
                </div>
            }
        >
            <Head title="Dashboard" />

            <div className="py-8">
                <div className="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-8">
                    
                    {/* Message de bienvenue amélioré */}
                    <div className="bg-gradient-to-r from-blue-50 to-indigo-100 rounded-lg p-6 border border-blue-200">
                        <div className="flex items-center">
                            <div className="flex-shrink-0">
                                <div className="w-12 h-12 bg-blue-500 rounded-full flex items-center justify-center">
                                    <span className="text-2xl">👋</span>
                                </div>
                            </div>
                            <div className="ml-4">
                                <h3 className="text-lg font-medium text-gray-900">
                                    Bonjour {auth.user.name} !
                                </h3>
                                <p className="text-gray-600">
                                    Voici votre aperçu d'activité de médiation pour aujourd'hui
                                </p>
                            </div>
                        </div>
                    </div>

                    {/* Statistiques avec shadcn/ui Cards */}
                    <div className="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
                        <DashboardCard
                            title="Total Dossiers"
                            value={dashboardStats.total_dossiers}
                            description="Tous dossiers confondus"
                            trend={12}
                            icon={FolderOpen}
                        />

                        <DashboardCard
                            title="Dossiers Actifs"
                            value={dashboardStats.dossiers_actifs}
                            description="En cours de traitement"
                            trend={8}
                            icon={Activity}
                        />

                        <DashboardCard
                            title="Notifications"
                            value={dashboardStats.notifications}
                            description="À traiter"
                            trend={-2}
                            icon={Bell}
                        />

                        <DashboardCard
                            title="Taux de Réussite"
                            value={`${dashboardStats.taux_reussite}%`}
                            description="Médiations réussies"
                            trend={3}
                            icon={TrendingUp}
                        />
                    </div>

                    {/* Actions Rapides avec shadcn/ui Buttons */}
                    <div className="grid grid-cols-1 lg:grid-cols-3 gap-6">
                        
                        {/* Actions rapides */}
                        <div className="bg-white rounded-lg border shadow-sm p-6">
                            <h3 className="text-lg font-semibold mb-4 flex items-center">
                                <Plus className="mr-2 h-5 w-5 text-blue-500" />
                                Actions Rapides
                            </h3>
                            <div className="space-y-3">
                                <Button asChild className="w-full justify-start" variant="outline">
                                    <Link href="/dossiers/create">
                                        <Plus className="mr-2 h-4 w-4" />
                                        Nouveau Dossier
                                    </Link>
                                </Button>
                                
                                <Button asChild className="w-full justify-start" variant="outline">
                                    <Link href="/dossiers">
                                        <Eye className="mr-2 h-4 w-4" />
                                        Voir Tous les Dossiers
                                    </Link>
                                </Button>
                                
                                <Button asChild className="w-full justify-start" variant="outline">
                                    <Link href="/dossiers/kanban">
                                        <LayoutGrid className="mr-2 h-4 w-4" />
                                        Vue Kanban
                                    </Link>
                                </Button>
                            </div>
                        </div>

                        {/* Activité récente */}
                        <div className="lg:col-span-2 bg-white rounded-lg border shadow-sm p-6">
                            <h3 className="text-lg font-semibold mb-4 flex items-center">
                                <Clock className="mr-2 h-5 w-5 text-green-500" />
                                Activité Récente
                            </h3>
                            <div className="space-y-4">
                                {dashboardStats.actions_recentes.map((action) => (
                                    <div key={action.id} className="flex items-center justify-between py-2 border-b border-gray-100 last:border-b-0">
                                        <div className="flex items-center">
                                            <div className={`flex-shrink-0 w-2 h-2 rounded-full mr-3 ${
                                                action.type === 'success' ? 'bg-green-500' : 'bg-blue-500'
                                            }`}></div>
                                            <div>
                                                <p className="text-sm font-medium text-gray-900">
                                                    {action.action}
                                                </p>
                                                <p className="text-xs text-gray-500">
                                                    Dossier: {action.dossier}
                                                </p>
                                            </div>
                                        </div>
                                        <Badge variant="outline" className="text-xs">
                                            {action.time}
                                        </Badge>
                                    </div>
                                ))}
                            </div>
                            
                            <div className="mt-4">
                                <Button variant="link" asChild className="p-0 h-auto">
                                    <Link href="/activity">
                                        Voir toute l'activité →
                                    </Link>
                                </Button>
                            </div>
                        </div>
                    </div>

                    {/* Dossiers urgents avec Alert */}
                    <div className="bg-gradient-to-r from-orange-50 to-red-50 rounded-lg border border-orange-200 p-6">
                        <div className="flex items-center justify-between mb-4">
                            <h3 className="text-lg font-semibold flex items-center text-orange-900">
                                <AlertCircle className="mr-2 h-5 w-5 text-orange-600" />
                                Dossiers Urgents
                            </h3>
                            <Button variant="outline" size="sm" asChild>
                                <Link href="/dossiers?filter=urgent">
                                    Voir tous
                                </Link>
                            </Button>
                        </div>
                        
                        <div className="flex items-center p-4 bg-white rounded-lg border border-orange-200">
                            <AlertCircle className="mr-3 h-6 w-6 text-orange-500 flex-shrink-0" />
                            <div>
                                <p className="font-medium text-orange-900">
                                    3 dossiers nécessitent votre attention immédiate
                                </p>
                                <p className="text-sm text-orange-700">
                                    Délais d'échéance dans moins de 48h
                                </p>
                            </div>
                            <Badge variant="destructive" className="ml-auto">
                                Urgent
                            </Badge>
                        </div>
                    </div>

                </div>
            </div>
        </AuthenticatedLayout>
    );
}