<header class="fixed top-0 left-0 right-0 z-50 transition-all duration-300">
    <nav class="bg-white border border-gray-300 px-4 lg:px-6 py-2.5 dark:bg-gray-900 dark:border-gray-900">
        <div class="flex flex-wrap justify-between items-center mx-auto max-w-screen-xl">

            <x-sheet>
                <x-sheet.trigger variant="mobile">
                    <a class="flex items-center gap-2">
                        <x-avatar>
                            <x-avatar.image src="" />
                            <x-avatar.fallback>YON</x-avatar.fallback>
                        </x-avatar>
                        <span class="self-center text-xl font-bold whitespace-nowrap dark:text-white">
                            POS
                        </span>
                    </a>
                </x-sheet.trigger>
                <x-sheet.content side="left">
                    <x-sheet.header>
                        <x-sheet.title>Main Menu</x-sheet.title>
                    </x-sheet.header>
                    <ul class="mt-4 space-y-2 lg:space-y-0 lg:space-x-8 lg:flex lg:flex-row">
                        <li>
                            <x-link href="/">Home</x-link>
                        </li>

                        <li>
                            <x-link href="/history-transaction">Riwayat Transaksi</x-link>
                        </li>
                        <li>
                            <x-link href="/product">Produk</x-link>
                        </li>

                    </ul>
                </x-sheet.content>
            </x-sheet>

            <div class="flex items-center lg:order-2 gap-3">
                @use('App\Models\Product')

                {{-- <livewire:ui.combobox :model="Product::class" :searchable="['name']" display="name" placeholder="Search..." /> --}}

                <x-button variant="ghost" id="theme-toggle" type="button"
                    class="text-gray-500 dark:text-gray-400 hover:bg-gray-100 dark:hover:bg-gray-700 focus:outline-none focus:ring-4 focus:ring-gray-200 dark:focus:ring-gray-700 rounded-lg text-sm p-2.5">
                    <svg id="theme-toggle-dark-icon" class="hidden w-5 h-5" fill="currentColor" viewBox="0 0 20 20"
                        xmlns="http://www.w3.org/2000/svg">
                        <path d="M17.293 13.293A8 8 0 016.707 2.707a8.001 8.001 0 1010.586 10.586z"></path>
                    </svg>
                    <svg id="theme-toggle-light-icon" class="hidden w-5 h-5" fill="currentColor" viewBox="0 0 20 20"
                        xmlns="http://www.w3.org/2000/svg">
                        <path
                            d="M10 2a1 1 0 011 1v1a1 1 0 11-2 0V3a1 1 0 011-1zm4 8a4 4 0 11-8 0 4 4 0 018 0zm-.464 4.95l.707.707a1 1 0 001.414-1.414l-.707-.707a1 1 0 00-1.414 1.414zm2.12-10.607a1 1 0 010 1.414l-.706.707a1 1 0 11-1.414-1.414l.707-.707a1 1 0 011.414 0zM17 11a1 1 0 100-2h-1a1 1 0 100 2h1zm-7 4a1 1 0 011 1v1a1 1 0 11-2 0v-1a1 1 0 011-1zM5.05 6.464A1 1 0 106.465 5.05l-.708-.707a1 1 0 00-1.414 1.414l.707.707zm1.414 8.486l-.707.707a1 1 0 01-1.414-1.414l.707-.707a1 1 0 011.414 1.414zM4 11a1 1 0 100-2H3a1 1 0 000 2h1z"
                            fill-rule="evenodd" clip-rule="evenodd"></path>
                    </svg>
                </x-button>

                <x-sheet>
                    <x-sheet.trigger>
                        <x-avatar>
                            <x-avatar.image src="https://github.com/ynvrse.png" />
                            <x-avatar.fallback>YN</x-avatar.fallback>
                        </x-avatar>
                    </x-sheet.trigger>
                    <x-sheet.content>
                        <x-sheet.header>
                            <x-sheet.title>Edit profile</x-sheet.title>
                            <x-sheet.description>
                                Make changes to your profile here. Click save when you're done.
                            </x-sheet.description>
                        </x-sheet.header>
                        <div class="grid gap-4 py-4">
                            <div class="grid grid-cols-4 items-center gap-4">
                                <x-label htmlFor="name" class="text-right">
                                    Name
                                </x-label>
                                <x-input id="name" value="yoniverse" class="col-span-3" />
                            </div>
                            <div class="grid grid-cols-4 items-center gap-4">
                                <x-label htmlFor="username" class="text-right">
                                    Username
                                </x-label>
                                <x-input id="username" value="@ynvrse" class="col-span-3" />
                            </div>
                        </div>
                        <x-sheet.footer>
                            <x-sheet.close type="submit">
                                Save changes
                            </x-sheet.close>
                        </x-sheet.footer>
                    </x-sheet.content>
                </x-sheet>
            </div>

            <div class="hidden justify-between items-center w-full lg:flex lg:w-auto lg:order-1">
                <ul class="flex flex-col mt-4 font-medium lg:flex-row lg:space-x-8 lg:mt-0">
                    <li>
                        <x-link href="/">Home</x-link>
                    </li>

                    <li>
                        <x-link href="/history-transaction">Riwayat Transaksi</x-link>
                    </li>
                    <li>
                        <x-link href="/product">Produk</x-link>
                    </li>
                </ul>
            </div>

        </div>
    </nav>
</header>
