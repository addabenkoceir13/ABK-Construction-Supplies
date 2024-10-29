<?php

namespace App\Http\Controllers\Category;

use App\Http\Controllers\Controller;
use App\Repositories\Category\CategoryRepository;
use App\Repositories\SubCategories\SubCategoryRepository;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class CategoryController extends Controller
{
    private $category;
    private $subcategory;

    public function __construct(CategoryRepository $category, SubCategoryRepository $subcategory)
    {
        $this->category = $category;
        $this->subcategory = $subcategory;
    }

    public function index()
    {
        $categories = $this->category->paginate(10);
        $subcategories = $this->subcategory->paginate(10);
        return view('content.category.index', compact('categories', 'subcategories'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create(): void
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request): RedirectResponse
    {
        $validator = Validator::make($request->all(),[
          'name' => ['required','string','max:255'],
        ]);

        if ($validator->fails()){
          toastr()->error($validator->errors()->first());
          return redirect()->back()->withErrors($validator)->withInput();
        }
        try {
          $this->category->create($request->all());
          toastr()->success(__('Building materials successfully created'));
          return redirect()->route('building-materals.index');
        }
        catch (\Exception $e) {
          toastr()->error($e->getMessage());
          return redirect()->back();
        }

    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id): void
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id): void
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id): RedirectResponse
    {
        $validator = Validator::make($request->all(),[
          'name' => ['required','string','max:255'],
        ]);

        if ($validator->fails()){
          toastr()->error($validator->errors()->first());
          return redirect()->back()->withErrors($validator)->withInput();;
        }

        try {
          $this->category->update($id, $request->all());
          toastr()->success(__('Building materials successfully Modified'));
          return redirect()->route('building-materals.index');

        }
        catch (\Exception $e) {
          toastr()->error($e->getMessage());
          return redirect()->back();
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        try {
          $this->category->delete($id);
          toastr()->success(__('Building materials successfully deleted'));
          return redirect()->route('building-materals.index');
        }
        catch (\Exception $e) {
          toastr()->error($e->getMessage());
          return redirect()->back();
        }
    }
}
