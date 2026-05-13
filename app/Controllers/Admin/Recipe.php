<?php

namespace App\Controllers\Admin;

use App\Controllers\BaseController;
use App\Models\MenuModel;
use App\Models\RecipeModel;
use App\Models\IngredientModel;

class Recipe extends BaseController
{
    protected $menuModel;
    protected $recipeModel;
    protected $ingredientModel;
    protected $db;

    public function __construct()
    {
        $this->menuModel       = new MenuModel();
        $this->recipeModel     = new RecipeModel();
        $this->ingredientModel = new IngredientModel();
        $this->db              = \Config\Database::connect();
    }

    /**
     * Daftar menu untuk dikelola resepnya.
     */
    public function index()
    {
        $search         = $this->request->getGet('search');
        $filterKategori = $this->request->getGet('category_id');

        $builder = $this->menuModel->getMenuWithKategori()
            ->where('m.status', 'available');

        if ($search) {
            $builder->like('m.name', $search);
        }
        if ($filterKategori) {
            $builder->where('m.category_id', $filterKategori);
        }

        $menus = $builder->get()->getResultArray();

        return view('admin/recipes/index', [
            'title'          => 'Manajemen Resep Menu',
            'menus'          => $menus,
            'kategoris'      => model('App\Models\KategoriModel')->findAll(),
            'search'         => $search,
            'filterKategori' => $filterKategori,
        ]);
    }

    /**
     * Kelola resep untuk satu menu: tambah / hapus bahan.
     */
    public function manage($menuId)
    {
        $menu = $this->menuModel->find($menuId);
        if (! $menu) {
            return redirect()->to(base_url('admin/resep'))->with('error', 'Menu tidak ditemukan.');
        }

        $recipes = $this->db->table('recipes r')
            ->select('r.*, i.name as ingredient_name, i.stock_qty, i.unit as ingredient_unit')
            ->join('ingredients i', 'i.id = r.ingredient_id', 'left')
            ->where('r.menu_id', $menuId)
            ->orderBy('i.name', 'ASC')
            ->get()
            ->getResultArray();

        $ingredients = $this->ingredientModel->orderBy('name', 'ASC')->findAll();

        return view('admin/recipes/manage', [
            'title'       => 'Resep: ' . $menu['name'],
            'menu'        => $menu,
            'recipes'     => $recipes,
            'ingredients' => $ingredients,
            'errors'      => session('errors') ?? [],
        ]);
    }

    /**
     * Simpan bahan resep baru untuk menu tertentu.
     */
    public function store($menuId)
    {
        $menu = $this->menuModel->find($menuId);
        if (! $menu) {
            return redirect()->to(base_url('admin/resep'))->with('error', 'Menu tidak ditemukan.');
        }

        $rules = [
            'ingredient_id' => 'required|is_natural_no_zero',
            'qty_needed'    => 'required|numeric|greater_than[0]',
            'unit'          => 'required',
        ];

        if (! $this->validate($rules)) {
            return redirect()->back()->withInput()
                ->with('errors', $this->validator->getErrors());
        }

        $ingredientId = (int) $this->request->getPost('ingredient_id');
        $qtyNeeded    = (float) $this->request->getPost('qty_needed');
        $unit         = $this->request->getPost('unit');

        // Cek apakah kombinasi menu + ingredient sudah ada
        $exists = $this->recipeModel->where('menu_id', $menuId)
            ->where('ingredient_id', $ingredientId)
            ->first();

        if ($exists) {
            // Jika sudah ada, update qty-nya
            $this->recipeModel->update($exists['id'], [
                'qty_needed' => $qtyNeeded,
                'unit'       => $unit,
            ]);
        } else {
            $this->recipeModel->insert([
                'menu_id'       => $menuId,
                'ingredient_id' => $ingredientId,
                'qty_needed'    => $qtyNeeded,
                'unit'          => $unit,
            ]);
        }

        return redirect()->to(base_url('admin/resep/menu/' . $menuId))
            ->with('success', 'Resep berhasil disimpan.');
    }

    /**
     * Hapus satu baris resep.
     */
    public function delete($id)
    {
        $recipe = $this->recipeModel->find($id);
        if (! $recipe) {
            return redirect()->back()->with('error', 'Data resep tidak ditemukan.');
        }

        $menuId = $recipe['menu_id'];
        $this->recipeModel->delete($id);

        return redirect()->to(base_url('admin/resep/menu/' . $menuId))
            ->with('success', 'Bahan resep berhasil dihapus.');
    }
}

