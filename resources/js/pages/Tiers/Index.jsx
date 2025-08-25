import { useState } from 'react';
import { Head, Link, router } from '@inertiajs/react';
import AuthenticatedLayout from '@/layouts/AuthenticatedLayout';

export default function Index({ tiers, stats, filters, types }) {
  const [searchTerm, setSearchTerm] = useState(filters.search || '');
  const [typeFilter, setTypeFilter] = useState(filters.type || 'all');

  const handleSearch = () => {
    router.get('/tiers', {
      search: searchTerm,
      type: typeFilter,
    }, {
      preserveState: true,
      replace: true,
    });
  };

  const handleReset = () => {
    setSearchTerm('');
    setTypeFilter('all');
    router.get('/tiers');
  };

  const getTypeBadgeClass = (tier) => {
    if (tier.is_personne) return 'bg-blue-100 text-blue-800';
    if (tier.is_tribunal) return 'bg-red-100 text-red-800';
    return 'bg-gray-100 text-gray-800';
  };

  return (
    <AuthenticatedLayout
      header={
        <div className="flex justify-between items-center">
          <h2 className="font-semibold text-xl text-gray-800 leading-tight">
            Tiers
          </h2>
          <Link
            href="/tiers/create"
            className="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded"
          >
            Nouveau Tiers
          </Link>
        </div>
      }
    >
      <Head title="Tiers" />

      <div className="py-6">
        <div className="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">
          {/* Statistiques */}
          <div className="grid grid-cols-1 md:grid-cols-5 gap-4">
            <div className="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6">
              <div className="text-gray-900">
                <div className="text-2xl font-bold text-blue-600">{stats.total}</div>
                <div className="text-sm text-gray-500">Total</div>
              </div>
            </div>
            
            <div className="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6">
              <div className="text-gray-900">
                <div className="text-2xl font-bold text-green-600">{stats.personnes}</div>
                <div className="text-sm text-gray-500">Personnes</div>
              </div>
            </div>
            
            <div className="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6">
              <div className="text-gray-900">
                <div className="text-2xl font-bold text-purple-600">{stats.juridictions}</div>
                <div className="text-sm text-gray-500">Juridictions</div>
              </div>
            </div>
            
            <div className="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6">
              <div className="text-gray-900">
                <div className="text-2xl font-bold text-red-600">{stats.tribunaux}</div>
                <div className="text-sm text-gray-500">Tribunaux</div>
              </div>
            </div>
            
            <div className="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6">
              <div className="text-gray-900">
                <div className="text-2xl font-bold text-orange-600">{stats.recent_access}</div>
                <div className="text-sm text-gray-500">Accès 24h</div>
              </div>
            </div>
          </div>

          {/* Filtres */}
          <div className="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6">
            <div className="flex items-center space-x-4">
              <div className="flex-1">
                <input
                  type="text"
                  placeholder="Rechercher par nom, prénom, dénomination..."
                  value={searchTerm}
                  onChange={(e) => setSearchTerm(e.target.value)}
                  onKeyPress={(e) => e.key === 'Enter' && handleSearch()}
                  className="w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50"
                />
              </div>
              
              <select
                value={typeFilter}
                onChange={(e) => setTypeFilter(e.target.value)}
                className="rounded-md border-gray-300 shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50"
              >
                {types.map((type) => (
                  <option key={type.value} value={type.value}>
                    {type.label}
                  </option>
                ))}
              </select>
              
              <button
                onClick={handleSearch}
                className="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded"
              >
                Rechercher
              </button>
              
              <button
                onClick={handleReset}
                className="bg-gray-500 hover:bg-gray-700 text-white font-bold py-2 px-4 rounded"
              >
                Réinitialiser
              </button>
            </div>
          </div>

          {/* Tableau */}
          <div className="bg-white overflow-hidden shadow-sm sm:rounded-lg">
            <div className="overflow-x-auto">
              <table className="min-w-full divide-y divide-gray-200">
                <thead className="bg-gray-50">
                  <tr>
                    <th className="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                      Type
                    </th>
                    <th className="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                      Nom/Dénomination
                    </th>
                    <th className="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                      Référence
                    </th>
                    <th className="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                      Hiérarchie
                    </th>
                    <th className="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                      Dernier accès
                    </th>
                    <th className="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">
                      Actions
                    </th>
                  </tr>
                </thead>
                <tbody className="bg-white divide-y divide-gray-200">
                  {tiers.data.map((tier) => (
                    <tr key={tier.id} className="hover:bg-gray-50">
                      <td className="px-6 py-4 whitespace-nowrap">
                        <span className={`inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium ${getTypeBadgeClass(tier)}`}>
                          {tier.type_entity}
                        </span>
                      </td>
                      
                      <td className="px-6 py-4 whitespace-nowrap">
                        <div>
                          <div className="text-sm font-medium text-gray-900">
                            {tier.full_name}
                          </div>
                          {tier.identifiant && (
                            <div className="text-sm text-gray-500">
                              ID: {tier.identifiant}
                            </div>
                          )}
                        </div>
                      </td>
                      
                      <td className="px-6 py-4 whitespace-nowrap">
                        {tier.reference && (
                          <span className="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-gray-100 text-gray-800">
                            {tier.reference}
                          </span>
                        )}
                      </td>
                      
                      <td className="px-6 py-4 whitespace-nowrap">
                        {tier.parent && (
                          <div className="text-sm text-gray-500">
                            → {tier.parent.full_name}
                          </div>
                        )}
                        {tier.children && tier.children.length > 0 && (
                          <div className="text-sm text-blue-600">
                            {tier.children.length} enfant(s)
                          </div>
                        )}
                      </td>
                      
                      <td className="px-6 py-4 whitespace-nowrap">
                        {tier.last_access ? (
                          <div className="text-sm">
                            <div className="text-gray-900">{tier.last_access}</div>
                            {tier.is_recently_accessed && (
                              <span className="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">
                                Récent
                              </span>
                            )}
                          </div>
                        ) : (
                          <span className="text-gray-500 text-sm">
                            Jamais
                          </span>
                        )}
                      </td>
                      
                      <td className="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                        <Link
                          href={`/tiers/${tier.id}`}
                          className="text-indigo-600 hover:text-indigo-900"
                        >
                          Voir
                        </Link>
                      </td>
                    </tr>
                  ))}
                </tbody>
              </table>
            </div>

            {/* Pagination */}
            {tiers.links && (
              <div className="bg-white px-4 py-3 flex items-center justify-between border-t border-gray-200 sm:px-6">
                <div className="flex-1 flex justify-center">
                  <nav className="relative z-0 inline-flex rounded-md shadow-sm -space-x-px">
                    {tiers.links.map((link, index) => (
                      <button
                        key={index}
                        onClick={() => link.url && router.visit(link.url)}
                        disabled={!link.url}
                        className={`relative inline-flex items-center px-4 py-2 border text-sm font-medium ${
                          link.active
                            ? 'z-10 bg-indigo-50 border-indigo-500 text-indigo-600'
                            : 'bg-white border-gray-300 text-gray-500 hover:bg-gray-50'
                        } ${!link.url ? 'cursor-not-allowed' : 'hover:bg-gray-50'}`}
                        dangerouslySetInnerHTML={{ __html: link.label }}
                      />
                    ))}
                  </nav>
                </div>
              </div>
            )}
          </div>
        </div>
      </div>
    </AuthenticatedLayout>
  );
}