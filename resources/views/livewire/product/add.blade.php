<?php
use App\Models\Product;
use App\Models\Category;
use Illuminate\Support\Facades\DB;
use function Livewire\Volt\{state, usesFileUploads, rules, on, computed};

usesFileUploads();
state(id: '', category_id: '', category_name: '', name: '', price: '', photo: '', isEdit: false);

$categories = computed(fn() => Category::latest()->get());
rules([
    'name' => 'required|string|max:255',
    'category_id' => 'required|exists:categories,id',
    'photo' => 'nullable|image|max:2048',
    'price' => 'required|numeric|min:0',
]);

on([
    'edit-product' => function ($product) {
        $this->isEdit = true;
        $this->id = $product['id'];
        $this->category_id = $product['category_id'];
        $this->name = $product['name'];
        $this->price = $product['price'];

        $category = Category::find($product['category_id']);
        if (!$category) {
            $this->category_id = null;
        }
    },
    'newCategory' => function ($categoryId) {
        $this->category_id = $categoryId;
    },
]);

$add = function () {
    DB::beginTransaction();
    try {
        $validate = $this->validate();

        if ($this->photo) {
            $validate['photo'] = $this->photo->store('photos', 'public');
        }

        Product::create($validate);
        $this->reset();

        $this->dispatch('toast', $message = 'Product berhasil ditambahkan');
        DB::commit();
    } catch (\Exception $e) {
        $this->dispatch('toast', $message = 'Product gagal ditambahkan', 'destructive');

        DB::rollBack();
        throw $e;
    }
};

$addCategory = function () {
    if ($this->category_name) {
        $newCategory = Category::create(['name' => $this->category_name]);
        $this->category_name = '';
        $this->dispatch('newCategory', $newCategory->id);
        $this->dispatch('toast', $message = 'Kategori berhasil ditambahkan');
    }
};

$deleteCategory = function () {
    $category = Category::find($this->category_id);
    $category->delete();
    $this->category_id = '';
    $this->dispatch('toast', $message = 'Kategori berhasil dihapus');
};

$update = function () {
    DB::beginTransaction();
    try {
        $validate = $this->validate();
        if ($this->photo) {
            $validate['photo'] = $this->photo->store('photos', 'public');
        }
        Product::find($this->id)->update($validate);
        $this->reset();

        $this->dispatch('toast', $message = 'Product berhasil diupdate');
        DB::commit();
    } catch (\Exception $e) {
        $this->dispatch('toast', $message = $e->getMessage(), 'destructive');
        // $this->dispatch('toast', $message = 'Product gagal diupdate', 'destructive');
        DB::rollBack();
        throw $e;
    }
};

?>

<div>

    <x-card.title>{{ $isEdit ? 'Edit Produk' : 'Tambah Produk Baru' }}</x-card.title>

    <x-card.content>
        <form wire:submit="{{ $isEdit ? 'update' : 'add' }}" enctype="multipart/form-data">

            <div class="mb-3">
                <x-label htmlFor="name">Nama Produk</x-label>
                <x-input wire:model.lazy="name" id="name" />
                @error('name')
                    <span class="text-red-500">{{ $message }}</span>
                @enderror
            </div>
            <div class="mb-3">
                <x-label>Gambar Produk</x-label>
                <div wire:ignore x-data x-init="pond = FilePond.create($refs.input);
                pond.setOptions({
                    server: {
                        process: (fieldName, file, metadata, load, error, progress, abort, transfer, options) => {
                            @this.upload('photo', file, load, error, progress)
                        },
                        revert: (filename, load) => {
                            @this.removeUpload('photo', filename, load)
                        }
                    }
                });
                Livewire.on('filepond:reset', () => {
                    pond.removeFiles();
                });
                Livewire.on('filepond:remove', () => {
                    pond.removeFiles();
                });
                Livewire.hook('message.processed', (message, component) => {
                    if (message.updateQueue && message.updateQueue.length > 0) {
                        pond.destroy();
                        pond = FilePond.create($refs.input);
                        pond.setOptions({
                            server: {
                                process: (fieldName, file, metadata, load, error, progress, abort, transfer, options) => {
                                    @this.upload('photo', file, load, error, progress)
                                },
                                revert: (filename, load) => {
                                    @this.removeUpload('photo', filename, load)
                                }
                            }
                        });
                    }
                });
                Livewire.on('operation:finished', () => {
                    pond.removeFiles();
                });">

                    <input type="file" id="photo" x-ref="input" wire:model="photo" />
                </div>
            </div>

            <div class="mb-3">

                <x-label>Kategori Produk</x-label>

                <div class="flex gap-2" x-data="{ addCategory: false }">
                    <template x-if="!addCategory">

                        <x-select wire:model.live="category_id" id="category_id">
                            <x-select.option value="">Pilih Kategori</x-select.option>
                            @foreach ($this->categories as $category)
                                <x-select.option value="{{ $category->id }}">
                                    {{ $category->name }}
                                </x-select.option>
                            @endforeach
                        </x-select>
                    </template>

                    <template x-if="addCategory">
                        <x-input wire:model.live.debounce.500ms="category_name" id="category_name"
                            placeholder="Nama Kategori Baru" />

                    </template>

                    <template x-if="addCategory && $wire.category_name">
                        <x-button wire:click="addCategory"
                            @click="addCategory = !addCategory; $wire.set('category_name', $refs.category_name.value)">
                            <x-lucide-check class="size-4" />
                        </x-button>
                    </template>

                    <x-button variant="secondary"
                        @click="addCategory = !addCategory; $wire.set('category_id', ''); $wire.set('category_name', '')">
                        <template x-if="!addCategory">
                            <x-lucide-plus class="size-4" />
                        </template>

                        <template x-if="addCategory">
                            <x-lucide-x class="size-4" />
                        </template>
                    </x-button>



                    @if ($category_id)
                        <x-button variant="destructive" wire:click="deleteCategory">
                            <x-lucide-trash class="size-4" />
                        </x-button>
                    @endif
                </div>
                @error('category_id')
                    <span class="text-red-500">{{ $message }}</span>
                @enderror
            </div>

            <div class="mb-3">
                <x-label>Harga Produk</x-label>
                <x-input type="number" wire:model.lazy="price" id="price" />
                @error('price')
                    <span class="text-red-500">{{ $message }}</span>
                @enderror
            </div>

            <div class="flex justify-end gap-2 items-end ">
                @if ($isEdit)
                    <x-button class="ml-2 " type="reset" variant="secondary" @click="$wire.set('isEdit', false)">

                        <x-lucide-loader-circle class="size-4 animate-spin" wire:loading wire:target="isEdit" />
                        <p wire:loading wire:target="isEdit">Membatalkan..</p>
                        <p wire:loading.remove wire:target="isEdit">Batal</p>

                    </x-button>
                @endif

                <x-button type="submit" wire:loading.attr="disabled" wire:loading.class="opacity-50 cursor-not-allowed"
                    wire:target="add,update,photo">
                    <x-lucide-loader-circle class="size-4 animate-spin" wire:loading wire:target="add,update,photo" />
                    <p wire:loading.remove wire:target="add,update,photo">{{ $isEdit ? 'Update' : 'Simpan' }}</p>
                    <p wire:loading wire:target="photo">Uploading..</p>
                </x-button>

            </div>



        </form>
    </x-card.content>



</div>
