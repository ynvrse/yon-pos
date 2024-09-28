<div x-data="{
    search: '',
    searchProduct() {
        $dispatch('search-product', { search: this.search })
    },
    resetSearch() {
        this.search = ''
        this.searchProduct()
    }
}" class="flex  w-full md:w-48  gap-2 ">

    <x-input x-model="search" @input.debounce.500ms="searchProduct()" type="text" placeholder="Cari produk..." />

    <template x-if="search">
        <x-button @click="resetSearch()" type="reset">
            <x-lucide-x class="size-4" />
        </x-button>
    </template>

    <template x-if="!search">
        <x-button @click="searchProduct()">
            <x-lucide-search class="size-4" />
        </x-button>
    </template>

</div>
