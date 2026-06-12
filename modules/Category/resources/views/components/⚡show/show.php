<?php

use Livewire\Component;
use Modules\Category\Models\Category;

new class extends Component
{
    public Category $category;

    public function mount(Category $category): void
    {
        $this->category = $category->loadCount('children');
    }
};
