<?php

namespace IO\Controllers;

use Plenty\Modules\Category\Contracts\CategoryRepositoryContract;
use Plenty\Plugin\Controller;

class CategoryByUrlController extends Controller
{
    /**
     * @var CategoryRepositoryContract
     */
    protected $categoryRepo;

    public function __construct()
    {
        parent::__construct();
        /** @var CategoryRepositoryContract categoryRepo */
        $this->categoryRepo = pluginApp(CategoryRepositoryContract::class);
    }
}