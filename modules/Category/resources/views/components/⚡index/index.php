<?php

use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Livewire\Component;
use Livewire\WithPagination;
use Modules\Category\Models\Category;
use Modules\Category\Services\CRUDService;

new class extends Component
{
    use WithPagination;

    public string $search = '';

    public bool $activeOnly = false;

    public function delete(Category $category, CRUDService $crudService): void
    {
        $crudService->delete($category);
        $this->redirect(route('category.index'));
    }

    public function getCategories(): LengthAwarePaginator
    {
        return Category::query()
            ->when($this->search, fn ($q) => $q->where('name', 'like', '%'.$this->search.'%'))
            ->when($this->activeOnly, fn ($q) => $q->where('is_active', true))
            ->with('parent')
            ->orderBy('sort_order')
            ->orderBy('name')
            ->paginate(10);
    }
};
