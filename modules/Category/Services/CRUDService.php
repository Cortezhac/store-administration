<?php

namespace Modules\Category\Services;

use Modules\Category\Forms\CreateForm;
use Modules\Category\Models\Category;

class CRUDService
{
    public function create(CreateForm $form): Category
    {
        $category = Category::create($form->all());

        return $category;
    }

    public function update(Category $category, array $data): Category
    {
        $category->update($data);

        return $category;
    }

    public function delete(Category $category): bool
    {
        return $category->delete();
    }
}
