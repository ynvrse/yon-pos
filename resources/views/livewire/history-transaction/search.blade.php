<div x-data="{
    search: '',
    searchProduct() {
        $dispatch('search-transaction', { search: this.search })
    },
    resetSearch() {
        this.search = ''
        this.searchTransaction()
    }
}" class="flex  w-full  gap-2 ">

    <x-input x-model="search" @input.debounce.500ms="searchTransaction()" type="text"
        placeholder="Cari transaksi Pembeli" />

    <template x-if="search">
        <x-button @click="resetSearch()" type="reset">
            <x-lucide-x class="size-4" />
        </x-button>
    </template>

    <template x-if="!search">
        <x-button @click="searchTransaction()">
            <x-lucide-search class="size-4" />
        </x-button>
    </template>

</div>
