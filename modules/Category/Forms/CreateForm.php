<?php

namespace Modules\Category\Forms;

use Livewire\Form;
use Modules\Category\Models\Category;
use Modules\Category\Services\CRUDService;

class CreateForm extends Form
{
    public string $name = '';

    public string $slug = '';

    public ?string $description = null;

    public ?int $parent_id = null;

    public int $sort_order = 0;

    public bool $is_active = true;

    public ?string $icon = null;

    public ?int $categoryId = null;

    public function rules(): array
    {
        $rules = [
            'name' => ['required', 'string', 'max:255'],
            'slug' => ['required', 'string', 'max:255', 'unique:categories,slug,'.($this->categoryId ?? 'NULL')],
            'description' => ['nullable', 'string', 'max:1000'],
            'parent_id' => ['nullable', 'exists:categories,id'],
            'sort_order' => ['required', 'integer', 'min:0', 'max:999'],
            'is_active' => ['required', 'boolean'],
        ];

        // Prevent selecting self as parent
        if ($this->categoryId) {
            $rules['parent_id'][] = function (string $attribute, mixed $value, \Closure $fail) {
                if ((int) $value === $this->categoryId) {
                    $fail(__('A category cannot be its own parent.'));
                }
            };
        }

        return $rules;
    }

    public function save(CRUDService $service): Category
    {
        $this->validate();

        $category = $service->create($this);

        return $category;
    }

    public function update(CRUDService $service, Category $category): Category
    {
        $this->validate();

        $category = $service->update($category, $this->except('categoryId'));

        return $category;
    }
}
