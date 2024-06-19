<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Slider;
use Illuminate\Support\Str;
use Carbon\Carbon;



class SliderController extends Controller
{
    public function getSlider() {
        $sliders = Slider::all()->map(function ($slider) {
            $slider->created_at = $slider->created_at_formatted;
            return $slider;
        });
        return response()->json(['sliders' => $sliders]);
    }   

    public function createSlider(Request $request) {
        $request->validate([
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
            'link' => 'required|string|max:255'
        ]);
        
        $file = null;
        if ($request->has('image')) {
            $fileName = Str::random(32).".".$request->file('image')->getClientOriginalExtension();
            $request->file('image')->move(public_path('uploads/slider/'), $fileName);
            $file = 'uploads/slider/' . $fileName;
        }
        $slider = Slider::create([
            'image' => $file,
            'link' => $request->input('link')
        ]);

        // Sử dụng accessor để định dạng created_at
        $slider->created_at = $slider->created_at_formatted;

        return response()->json([
            'status' => 200,
            'message' => 'Tạo slider thành công !',
            'slider' => $slider,
        ]);
    }

    public function updateSlider(Request $request, $id) {
       
        $slider = Slider::find($id);
        if ($slider) {  
            if ($request->has('link') && $request->input('link') !== $slider->link) {
                $slider->link = $request->input('link');
            }
            $oldImagePath = $slider->image;
    
            if ($request->hasFile('image')) {
                if (file_exists(public_path($oldImagePath))) {
                    unlink(public_path($oldImagePath));
                }
                $fileName = Str::random(32).".".$request->file('image')->getClientOriginalExtension();
                $request->file('image')->move(public_path('uploads/slider/'), $fileName);
                $slider->image = 'uploads/slider/' . $fileName;
            }
    
            $slider->update();

            // Sử dụng accessor để định dạng created_at
            $slider->created_at = $slider->created_at_formatted;
        }
    
        return response()->json([
            'status' => 200,
            'message' => 'Slider được cập nhật thành công !',
            'slider' => $slider
        ]);
    }

    public function deleteSlider($id) {
        Slider::find($id)->delete();
        return response()->json([
            'message' => 'Xóa thành công slider !'
        ]);
    }
}

