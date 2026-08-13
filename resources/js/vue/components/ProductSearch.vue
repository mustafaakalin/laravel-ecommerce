<template>
    <div class="container mx-auto px-4 py-6">

        <!-- Breadcrumbs -->
        <div class="mb-8">
            <div class="text-sm breadcrumbs">
                <ul>
                    <li>
                        <a href="/" class="flex items-center gap-2">
                            <i class="fas fa-home h-4 w-4"></i>
                            Anasayfa
                        </a>
                    </li>
                    <li>
                        <a href="/products" class="flex items-center gap-2">
                            <i class="fas fa-store h-4 w-4"></i>
                            Ürünler
                        </a>
                    </li>
                </ul>
            </div>
        </div>


        <!-- Search Bar -->
        <div class="mb-6 relative">
            <span class="absolute left-4 top-1/2 -translate-y-1/2">
                <i class="fa-solid fa-search text-gray-400"></i>
            </span>
            <input type="text" v-model="searchQuery" @input="search" placeholder="Ürün ara..."
                class="w-full input input-bordered input-lg pl-12 bg-gradient-to-r from-primary/10 to-secondary/10 ">
        </div>

        <div class="flex flex-col lg:flex-row gap-6">
            <!-- Mobile Filter Toggle -->
            <div class="lg:hidden w-full">
                <button
                    class="btn btn-primary w-full flex justify-between items-center bg-gradient-to-r from-primary/10 to-secondary/10 bg-transparent"
                    onclick="document.getElementById('filter_modal').showModal()">
                    <span><i class="fa-solid fa-filter mr-2"></i>Filtreler</span>
                    <i class="fa-solid fa-chevron-down"></i>
                </button>
            </div>

            <!-- Filter Modal -->
            <dialog id="filter_modal" class="modal modal-bottom sm:modal-middle">
                <div
                    class="modal-box w-full max-w-3xl  bg-gradient-to-r from-primary/30 to-secondary/30 bg-transparent backdrop-blur-3xl">
                    <h3 class="text-lg font-bold mb-4">
                        <i class="fa-solid fa-sliders mr-2"></i>Filtreler
                    </h3>

                    <!-- Filter Content -->
                    <div class="space-y-4">
                        <!-- Sort -->
                        <div class="form-control">
                            <label class="label font-medium"><i class="fa-solid fa-sort mr-2"></i>Sıralama</label>
                            <select v-model="sortBy" @change="search" class="select select-bordered w-full">
                                <option value="created_at:desc">En Yeniler</option>
                                <option value="price:asc">Fiyat (Düşükten Yükseğe)</option>
                                <option value="price:desc">Fiyat (Yüksekten Düşüğe)</option>
                                <option value="rating:desc">En Çok Değerlendirilenler</option>
                                <option value="view_count:desc">En Çok Görüntülenenler</option>
                            </select>
                        </div>

                        <!-- Price Range -->
                        <div class="form-control">
                            <label class="label font-medium">
                                <i class="fa-solid fa-turkish-lira-sign mr-2"></i>Fiyat Aralığı
                            </label>
                            <div class="flex gap-2">
                                <input type="number" v-model="filters.minPrice" @change="search" placeholder="Min"
                                    class="input input-bordered w-full">
                                <input type="number" v-model="filters.maxPrice" @change="search" placeholder="Max"
                                    class="input input-bordered w-full">
                            </div>
                        </div>

                        <!-- Stock Status -->
                        <div class="form-control">
                            <label class="label font-medium"><i class="fa-solid fa-box mr-2"></i>Stok Durumu</label>
                            <select v-model="filters.inStock" @change="search" class="select select-bordered w-full">
                                <option value="">Tümü</option>
                                <option value="true">Stokta Var</option>
                                <option value="false">Tükendi</option>
                            </select>
                        </div>

                        <!-- Product Status -->
                        <div class="form-control">
                            <label class="label font-medium"><i class="fa-solid fa-tag mr-2"></i>Ürün Durumu</label>
                            <div class="space-y-2">
                                <label class="flex items-center hover:bg-base-200 p-2 rounded-lg cursor-pointer">
                                    <input type="checkbox" v-model="filters.isNew" @change="search"
                                        class="checkbox checkbox-primary">
                                    <span class="ml-2"><i class="fa-solid fa-sparkles mr-1"></i>Yeni Ürünler</span>
                                </label>
                                <label class="flex items-center hover:bg-base-200 p-2 rounded-lg cursor-pointer">
                                    <input type="checkbox" v-model="filters.isFeatured" @change="search"
                                        class="checkbox checkbox-primary">
                                    <span class="ml-2"><i class="fa-solid fa-star mr-1"></i>Öne Çıkanlar</span>
                                </label>
                                <label class="flex items-center hover:bg-base-200 p-2 rounded-lg cursor-pointer">
                                    <input type="checkbox" v-model="filters.hasDiscount" @change="search"
                                        class="checkbox checkbox-primary">
                                    <span class="ml-2"><i class="fa-solid fa-percent mr-1"></i>İndirimli Ürünler</span>
                                </label>
                            </div>
                        </div>

                        <!-- Brand Filter -->
                        <div class="form-control">
                            <label class="label font-medium"><i class="fa-solid fa-industry mr-2"></i>Markalar</label>
                            <select v-model="filters.brands" @change="search" class="select select-bordered w-full"
                                multiple>
                                <option v-for="brand in brands" :key="brand.id" :value="brand.id">{{ brand.name }}
                                </option>
                            </select>
                        </div>

                        <!-- Category Filter -->
                        <div class="form-control">
                            <label class="label font-medium"><i class="fa-solid fa-folder mr-2"></i>Kategoriler</label>
                            <select v-model="filters.categories" @change="search" class="select select-bordered w-full"
                                multiple>
                                <option v-for="category in categories" :key="category.id" :value="category.id">{{
                                    category.name }}</option>
                            </select>
                        </div>
                    </div>

                    <div class="modal-action">
                        <button class="btn btn-error" onclick="filter_modal.close()">İptal</button>
                        <button class="btn btn-primary" @click="search(); filter_modal.close()">Uygula</button>
                    </div>
                </div>
                <form method="dialog" class="modal-backdrop">
                    <button>close</button>
                </form>
            </dialog>

            <!-- Desktop Filters Sidebar -->
            <div class="hidden lg:block lg:w-80">
                <div
                    class="bg-gradient-to-r from-primary/10 to-secondary/10 p-4 rounded-lg shadow-lg sticky top-16 max-h-[calc(100vh-2rem)] overflow-y-auto">
                    <h3 class="text-lg font-bold mb-4"><i class="fa-solid fa-sliders mr-2"></i>Filtreler</h3>
                    <!-- Same filter content as in modal -->
                    <!-- Sort -->
                    <div class="mb-4">
                        <label class="label font-medium"><i class="fa-solid fa-sort mr-2"></i>Sıralama</label>
                        <select v-model="sortBy" @change="search" class="select select-bordered w-full">
                            <option value="created_at:desc">En Yeniler</option>
                            <option value="price:asc">Fiyat (Düşükten Yükseğe)</option>
                            <option value="price:desc">Fiyat (Yüksekten Düşüğe)</option>
                            <option value="rating:desc">En Çok Değerlendirilenler</option>
                            <option value="view_count:desc">En Çok Görüntülenenler</option>
                        </select>
                    </div>

                    <!-- Price Range -->
                    <div class="mb-4">
                        <label class="label font-medium"><i class="fa-solid fa-turkish-lira-sign mr-2"></i>Fiyat
                            Aralığı</label>
                        <div class="flex gap-2">
                            <input type="number" v-model="filters.minPrice" @change="search" placeholder="Min"
                                class="input input-bordered w-full">
                            <input type="number" v-model="filters.maxPrice" @change="search" placeholder="Max"
                                class="input input-bordered w-full">
                        </div>
                    </div>

                    <!-- Stock Status -->
                    <div class="mb-4">
                        <label class="label font-medium"><i class="fa-solid fa-box mr-2"></i>Stok Durumu</label>
                        <select v-model="filters.inStock" @change="search" class="select select-bordered w-full">
                            <option value="">Tümü</option>
                            <option value="true">Stokta Var</option>
                            <option value="false">Tükendi</option>
                        </select>
                    </div>

                    <!-- Product Status -->
                    <div class="mb-4">
                        <label class="label font-medium"><i class="fa-solid fa-tag mr-2"></i>Ürün Durumu</label>
                        <div class="space-y-2">
                            <label class="flex items-center hover:bg-base-200 p-2 rounded-lg cursor-pointer">
                                <input type="checkbox" v-model="filters.isNew" @change="search"
                                    class="checkbox checkbox-primary">
                                <span class="ml-2"><i class="fa-solid fa-sparkles mr-1"></i>Yeni Ürünler</span>
                            </label>
                            <label class="flex items-center hover:bg-base-200 p-2 rounded-lg cursor-pointer">
                                <input type="checkbox" v-model="filters.isFeatured" @change="search"
                                    class="checkbox checkbox-primary">
                                <span class="ml-2"><i class="fa-solid fa-star mr-1"></i>Öne Çıkanlar</span>
                            </label>
                            <label class="flex items-center hover:bg-base-200 p-2 rounded-lg cursor-pointer">
                                <input type="checkbox" v-model="filters.hasDiscount" @change="search"
                                    class="checkbox checkbox-primary">
                                <span class="ml-2"><i class="fa-solid fa-percent mr-1"></i>İndirimli Ürünler</span>
                            </label>
                        </div>
                    </div>

                    <!-- Brand Filter -->
                    <div class="mb-4">
                        <label class="label font-medium"><i class="fa-solid fa-industry mr-2"></i>Markalar</label>
                        <select v-model="filters.brands" @change="search" class="select select-bordered w-full"
                            multiple>
                            <option v-for="brand in brands" :key="brand.id" :value="brand.id">{{ brand.name }}</option>
                        </select>
                    </div>

                    <!-- Category Filter -->
                    <div class="mb-4">
                        <label class="label font-medium"><i class="fa-solid fa-folder mr-2"></i>Kategoriler</label>
                        <select v-model="filters.categories" @change="search" class="select select-bordered w-full"
                            multiple>
                            <option v-for="category in categories" :key="category.id" :value="category.id">{{
                                category.name }}</option>
                        </select>
                    </div>
                </div>
            </div>

            <!-- Results Section remains unchanged -->
            <div class="flex-1">
                <div v-if="loading" class="flex justify-center items-center h-64">
                    <div class="loading loading-spinner loading-lg text-primary"></div>
                </div>
                <div v-else>
                    <div v-if="results.length === 0" class="text-center py-8 bg-base-100 rounded-lg shadow-lg">
                        <p class="text-lg"><i class="fa-solid fa-box-open mr-2"></i>Ürün bulunamadı.</p>
                    </div>
                    <div v-else class="grid grid-cols-2 sm:grid-cols-2 lg:grid-cols-2 xl:grid-cols-3 gap-4">
                        <template v-for="product in results" :key="product.id">
                            <div v-html="product.component"></div>
                        </template>
                    </div>


                    <!-- Pagination Controls -->
                    <div class="flex justify-center mt-4">
                        <button @click="changePage(currentPage - 1)" :disabled="currentPage === 1"
                            class="btn btn-secondary mr-2">Önceki</button>
                        <span class="px-4 py-2">{{ currentPage }} / {{ totalPages }}</span>
                        <button @click="changePage(currentPage + 1)" :disabled="currentPage === totalPages"
                            class="btn btn-secondary ml-2">Sonraki</button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>

<script>
import { ref, watch, onMounted } from 'vue';
import debounce from 'lodash/debounce';

export default {
    props: {
        initialBrands: {
            type: Array,
            required: true
        },
        initialCategories: {
            type: Array,
            required: true
        },
        initialMinprice: {
            type: Array,
            required: true
        },
        initialMaxprice: {
            type: Array,
            required: true
        }
    },
    setup(props) {
        const searchQuery = ref('');
        const results = ref([]);
        const loading = ref(false);
        const sortBy = ref('created_at:desc');
        const filters = ref({
            minPrice: ref(props.initialMinprice),
            maxPrice: ref(props.initialMaxprice),
            inStock: '',
            isNew: false,
            isFeatured: false,
            hasDiscount: false,
            brands: [],
            categories: []
        });

        const brands = ref(props.initialBrands);
        const categories = ref(props.initialCategories);

        const currentPage = ref(1);
        const totalPages = ref(1);
        const perPage = 12;



        onMounted(() => {
            search(); 

        });

        const buildFilterString = () => {
            const filterParts = [];

            if (filters.value.minPrice) {
                filterParts.push(`price:>=${filters.value.minPrice}`);
            }
            if (filters.value.maxPrice) {
                filterParts.push(`price:<=${filters.value.maxPrice}`);
            }
            if (filters.value.inStock === 'true') {
                filterParts.push('stock:>0');
            }
            if (filters.value.inStock === 'false') {
                filterParts.push('stock:=0');
            }
            if (filters.value.isNew) {
                filterParts.push('is_new:=true');
            }
            if (filters.value.isFeatured) {
                filterParts.push('is_featured:=true');
            }
            if (filters.value.hasDiscount) {
                filterParts.push('discount:>0');
            }
            if (filters.value.brands.length) {
                filterParts.push(`brand_id:=[${filters.value.brands.join(',')}]`);
            }
            if (filters.value.categories.length) {
                filterParts.push(`category_id:=[${filters.value.categories.join(',')}]`);
            }

            return filterParts.join(' && ');
        };

        const search = debounce(async () => {
            loading.value = true;
            try {
                const params = new URLSearchParams({
                    query: searchQuery.value,
                    sort_by: sortBy.value,
                    filter_by: buildFilterString(),
                    page: currentPage.value,
                    per_page: perPage
                });

                const response = await fetch(`/search?${params}`);
                const data = await response.json();
                results.value = data.data;
                totalPages.value = data.pagination.total_pages;

            } catch (error) {
                console.error('Search error:', error);
            } finally {
                loading.value = false;
            }
        }, 300);

        const onSearchInput = () => {
            currentPage.value = 1;
            search();
        };

        watch([searchQuery, sortBy, filters], () => {
            currentPage.value = 1;
            search();
        }, { deep: true });

        const changePage = (page) => {
            if (page >= 1 && page <= totalPages.value) {
                currentPage.value = page;
                search();
            }
        };

        return {
            searchQuery,
            results,
            loading,
            sortBy,
            filters,
            search,
            brands,
            categories,
            currentPage,
            totalPages,
            changePage,
            onSearchInput
        };
    }
};


</script>

<style scoped>
/* Optional: Add smooth scrolling when filters are opened on mobile */
.transition-all {
    transition-property: all;
    transition-timing-function: cubic-bezier(0.4, 0, 0.2, 1);
    transition-duration: 300ms;
}
</style>
