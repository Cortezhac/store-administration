<?php

use Illuminate\Support\Facades\Storage;
use Livewire\Component;
use Livewire\Features\SupportFileUploads\TemporaryUploadedFile;
use Livewire\WithFileUploads;
use Modules\Category\Forms\CreateForm;
use Modules\Category\Models\Category;
use Modules\Category\Services\CRUDService;

new class extends Component
{
    use WithFileUploads;

    public ?Category $category = null;

    public CreateForm $form;

    /** @var ?TemporaryUploadedFile */
    public $iconUpload = null;

    public bool $removeIcon = false;

    public function mount(?Category $category = null): void
    {
        $this->category = $category;
        $this->form->categoryId = $category?->id;

        if ($category) {
            $this->form->name = $category->name;
            $this->form->slug = $category->slug;
            $this->form->description = $category->description;
            $this->form->parent_id = $category->parent_id;
            $this->form->sort_order = $category->sort_order;
            $this->form->is_active = $category->is_active;
        }
    }

    public function save(CRUDService $crudService): void
    {
        $this->validate();

        if ($this->iconUpload) {
            // Delete old icon if it exists
            if ($this->category && $this->category->icon) {
                Storage::disk('local')->delete($this->category->icon);
            }

            $this->form->icon = $this->iconUpload->store('categories', 'local');
        }

        if ($this->removeIcon && $this->category && $this->category->icon) {
            Storage::disk('local')->delete($this->category->icon);
            $this->form->icon = null;
        }

        if ($this->category) {
            $this->category = $crudService->update($this->category, $this->form->except('categoryId'));
        } else {
            $this->category = $crudService->create($this->form);
        }

        $this->redirect(route('category.index'), navigate: true);
    }

    public function removeIconAction(): void
    {
        $this->removeIcon = true;
        $this->iconUpload = null;
    }

    /** @return array<string, list<mixed>> */
    protected function rules(): array
    {
        return [
            'iconUpload' => ['nullable', 'image', 'mimes:png,jpg,jpeg,svg,webp', 'max:1024'], // max 1 MB
        ];
    }

    /** @return array<int, array{id: int, name: string}> */
    public function getParentOptions(): array
    {
        return Category::query()
            ->where('is_active', true)
            ->when($this->category, fn ($q) => $q->where('id', '!=', $this->category->id))
            ->orderBy('name')
            ->get()
            ->map(fn (Category $cat) => [
                'id' => $cat->id,
                'name' => $cat->name,
            ])
            ->toArray();
    }
};
