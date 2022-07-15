<?php

namespace App\Http\Controllers;

use App\Models\Equipment;
use App\Helpers\AppHelper;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;

class EquipmentController extends Controller
{
    /**
     * Return all equipments sorted by their categories.
     *
     * @return \Illuminate\Http\Response
     */
    public function index($asked_category = null)
    {
        $available_equipments = Equipment::available();
        $unavailable_equipments = Equipment::unavailable();
        $equipments = [];
        foreach($available_equipments as $eq){
            $eq["availability"] = true;
            $eq["reservation"] = null;
            $eq["borrow"] = null;
            array_push($equipments, $eq);
        }
        foreach($unavailable_equipments as $eq){
            $eq["availability"] = false;
            $eq["reservation"] = Equipment::findOrFail($eq["id"])->getCurrentReservation();
            $eq["borrow"] = Equipment::findOrFail($eq["id"])->getCurrentBorrow();
            array_push($equipments, $eq);
        }
        if($asked_category != null){
        $equipments = array_filter($equipments,function ($eq) use ($asked_category) {
                return $eq["category"] == $asked_category;
            });
        }
        return view('equipments_view')->with('data', $equipments);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
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
    public function update(Request $request, $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
    }

    public function getCategories()
    {
        $categories_collection = Equipment::categories();
        $categories_with_image = ["laptop", "tablet", "camera", "earphone"];
        $categories = [];
        foreach ($categories_collection as $std) {
            $category = ["name" => $std->category, "image_url" => ""];
            if (in_array($std->category, $categories_with_image)) {
                $category["image_url"] = "/equipments/categories/" . $std->category . ".jpg";
            } else {
                $category["image_url"] = "/equipments/categories/undefined.jpg";
            }
            array_push($categories, $category);
        }
        return view('categories_view')->with("data", $categories);
    }
}
