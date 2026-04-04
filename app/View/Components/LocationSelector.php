<?php

namespace App\View\Components;

use App\Models\Division;
use Illuminate\View\Component;

class LocationSelector extends Component
{
    public $divisions;
    public $selectedDivision;
    public $selectedDistrict;
    public $selectedUpazila;

    public function __construct($selectedDivision = null, $selectedDistrict = null, $selectedUpazila = null)
    {
        $this->divisions = Division::orderBy('name', 'asc')->get();
        $this->selectedDivision = $selectedDivision;
        $this->selectedDistrict = $selectedDistrict;
        $this->selectedUpazila = $selectedUpazila;
    }

    public function render()
    {
        return view('components.location-selector');
    }
}
