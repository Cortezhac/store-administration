<?php

use Illuminate\Support\Facades\Storage;
use Livewire\Component;
use Livewire\TemporaryUploadedFile;
use Livewire\WithFileUploads;
use Modules\Category\Models\Category;

new class extends Component
{
    use WithFileUploads;

    public ?Category $category = null;

    public string $name = '';

    public string $slug = '';

    public ?string $description = null;

    /** @var ?TemporaryUploadedFile */
    public $iconUpload = null;

    public ?int $parent_id = null;

    public int $sort_order = 0;

    public bool $is_active = true;

    public bool $removeIcon = false;

    public function mount(?Category $category = null): void
    {
        $this->category = $category;

        if ($category) {
            $this->name = $category->name;
            $this->slug = $category->slug;
            $this->description = $category->description;
            $this->parent_id = $category->parent_id;
            $this->sort_order = $category->sort_order;
            $this->is_active = $category->is_active;
        }
    }

    public function save(): void
    {
        $this->validate();

        $data = [
            'name' => $this->name,
            'slug' => $this->slug,
            'description' => $this->description,
            'parent_id' => $this->parent_id,
            'sort_order' => $this->sort_order,
            'is_active' => $this->is_active,
        ];

        if ($this->iconUpload) {
            // Delete old icon if it exists
            if ($this->category && $this->category->icon) {
                Storage::disk('local')->delete($this->category->icon);
            }

            $data['icon'] = $this->iconUpload->store('categories', 'local');
        }

        if ($this->removeIcon && $this->category && $this->category->icon) {
            Storage::disk('local')->delete($this->category->icon);
            $data['icon'] = null;
        }

        if ($this->category) {
            $this->category->update($data);
        } else {
            $this->category = Category::create($data);
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
        $rules = [
            'name' => ['required', 'string', 'max:255'],
            'slug' => ['required', 'string', 'max:255', 'unique:categories,slug,'.($this->category?->id ?? 'NULL')],
            'description' => ['nullable', 'string', 'max:1000'],
            'parent_id' => ['nullable', 'exists:categories,id'],
            'sort_order' => ['required', 'integer', 'min:0', 'max:999'],
            'is_active' => ['required', 'boolean'],
            'iconUpload' => ['nullable', 'image', 'mimes:png,jpg,jpeg,svg,webp', 'max:1024'], // max 1 MB
        ];

        // Prevent selecting self as parent
        if ($this->category) {
            $rules['parent_id'][] = function (string $attribute, mixed $value, Closure $fail) {
                if ((int) $value === $this->category->id) {
                    $fail(__('A category cannot be its own parent.'));
                }
            };
        }

        return $rules;
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
