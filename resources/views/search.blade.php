<!-- resources/views/search.blade.php -->
<!DOCTYPE html>
<html lang="tr" data-theme="light">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Ürün Arama</title>
    <!-- Sıralama önemli, önce instantsearch.js sonra adapter yüklenmelidir -->
    <script src="https://cdn.jsdelivr.net/npm/algoliasearch@4.14.3/dist/algoliasearch-lite.umd.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/instantsearch.js@4.56.3/dist/instantsearch.production.min.js"></script>
    <script
        src="https://cdn.jsdelivr.net/npm/typesense-instantsearch-adapter@2.7.1/dist/typesense-instantsearch-adapter.min.js">
    </script>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/instantsearch.css@8.1.0/themes/reset-min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/instantsearch.css@8.1.0/themes/satellite-min.css">
    <!-- Tailwind ve DaisyUI -->
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://cdn.jsdelivr.net/npm/daisyui@4.4.19/dist/full.css" rel="stylesheet" type="text/css" />
</head>

<body class="min-h-screen bg-gray-50">
    <div class="container mx-auto p-4">
        <div class="text-center mb-8"> 
            <h1 class="text-3xl font-bold">Ürün Arama</h1>
        </div>

        <div class="max-w-4xl mx-auto">
            <div id="searchbox" class="form-control w-full mb-8"></div>
            <div id="stats" class="text-sm text-gray-600 mb-4"></div>
            <div id="hits" class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6"></div>
            <div id="pagination" class="mt-8"></div>
        </div>
    </div>

    <script>
        const typesenseInstantsearchAdapter = new TypesenseInstantSearchAdapter({
            server: {
                apiKey: '{{ config('scout.typesense.client-settings.api_key') }}',
                nodes: [{
                    host: '{{ config('scout.typesense.client-settings.nodes.0.host') }}',
                    port: '{{ config('scout.typesense.client-settings.nodes.0.port') }}',
                    protocol: '{{ config('scout.typesense.client-settings.nodes.0.protocol') }}'
                }]
            },
            additionalSearchParameters: {
                query_by: 'name,description,tags,meta_keywords,search_keywords'
            },
            collectionName: 'products_index'
        });

        const searchClient = typesenseInstantsearchAdapter.searchClient;

        const search = instantsearch({
            indexName: 'products_index',
            searchClient,
            routing: true
        });

        search.addWidgets([
            instantsearch.widgets.searchBox({
                container: '#searchbox',
                placeholder: 'Ürün ara...',
                showReset: true,
                showSubmit: true,
                cssClasses: {
                    root: 'relative',
                    form: 'relative',
                    input: 'input input-bordered w-full',
                    submit: 'btn btn-primary absolute right-0 top-0',
                    reset: 'btn btn-ghost absolute right-16 top-0'
                }
            }),

            instantsearch.widgets.stats({
                container: '#stats',
                templates: {
                    text: function(data) {
                        let text = '';
                        if (data.nbHits === 0) {
                            text = 'Sonuç bulunamadı';
                        } else if (data.nbHits === 1) {
                            text = '1 ürün bulundu';
                        } else {
                            text = `${data.nbHits} ürün bulundu`;
                        }
                        if (data.query) {
                            text += ` "${data.query}" için`;
                        }
                        return text;
                    }
                }
            }),

            instantsearch.widgets.hits({
                container: '#hits',
                templates: {
                    empty: `
                        <div class="col-span-full text-center p-8 bg-white rounded-lg shadow">
                            <h2 class="text-xl font-bold mb-2">Sonuç Bulunamadı</h2>
                            <p class="text-gray-600">Farklı anahtar kelimeler deneyebilirsiniz.</p>
                        </div>
                    `,
                    item: function(hit) {
                        return `
                            <div class="card bg-base-100 shadow-xl hover:shadow-2xl transition-shadow duration-200">
                                <figure class="px-4 pt-4">
                                    <img src="${hit.image || 'https://placehold.co/400x300'}" 
                                         alt="${hit.name}"
                                         class="rounded-xl h-48 w-full object-cover" 
                                         onerror="this.src='https://placehold.co/400x300'"
                                    />
                                </figure>
                                <div class="card-body">
                                    <h2 class="card-title">${instantsearch.highlight({ hit, attribute: 'name' })}</h2>
                                    <p class="text-sm text-gray-600 line-clamp-2">${instantsearch.highlight({ hit, attribute: 'description' })}</p>
                                    <div class="flex justify-between items-center mt-4">
                                        <div>
                                            <span class="text-xl font-bold text-primary">${Number(hit.price).toLocaleString('tr-TR')} TL</span>
                                            ${hit.old_price ? `<span class="text-sm line-through text-gray-500 ml-2">${Number(hit.old_price).toLocaleString('tr-TR')} TL</span>` : ''}
                                        </div>
                                    </div>
                                    <div class="flex flex-wrap gap-2 mt-2">
                                        ${hit.stock > 0 ? '<span class="badge badge-success">Stokta</span>' : '<span class="badge badge-error">Tükendi</span>'}
                                        ${hit.is_new ? '<span class="badge badge-primary">Yeni</span>' : ''}
                                        ${hit.is_featured ? '<span class="badge badge-secondary">Öne Çıkan</span>' : ''}
                                        ${hit.discount ? `<span class="badge badge-accent">%${hit.discount} İndirim</span>` : ''}
                                    </div>
                                    <div class="card-actions justify-end mt-4">
                                        <a href="/products/${hit.slug}" class="btn btn-primary btn-sm">Detay</a>
                                    </div>
                                </div>
                            </div>
                        `;
                    }
                }
            }),

            instantsearch.widgets.pagination({
                container: '#pagination',
                padding: 2,
                cssClasses: {
                    list: 'pagination',
                    item: 'join-item btn btn-sm',
                    selectedItem: 'btn-active'
                }
            })
        ]);

        search.start();
    </script>
</body>

</html>