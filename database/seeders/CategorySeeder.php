<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class CategorySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::table('categories')->insert([
            [
                'name' => 'Hư cấu',
                'parentID' => null
            ],
            [
                'name' => 'Phi hư cấu',
                'parentID' => null
            ],
            [
                'name' => 'Thiếu nhi',
                'parentID' => null
            ],
            [
                'name' => 'Phân loại khác',
                'parentID' => null
            ],[
                'name' => 'Văn học hiện đại',
                'parentID' => 1
            ],[
                'name' => 'Văn học kinh điển',
                'parentID' => 1
            ],
            [
                'name' => 'Văn học lãng mạn',
                'parentID' => 1
            ],[
                'name' => 'Văn học kỳ ảo',
                'parentID' => 1
            ],[
                'name' => 'Trinh thám',
                'parentID' => 1
            ],
            [
                'name' => 'Triết học',
                'parentID' => 2
            ],
            [
                'name' => 'Sử học',
                'parentID' => 2
            ],
            [
                'name' => 'Khoa học',
                'parentID' => 2
            ],
            [
                'name' => 'Công nghệ',
                'parentID' => 2
            ],
            [
                'name' => 'Văn học',
                'parentID' => 2
            ],
            [
                'name' => 'Truyện tranh',
                'parentID' => 3
            ],
            [
                'name' => 'Truyện tranh thiếu nhi',
                'parentID' => 3
            ], 
            [
                'name' => '0-5 tuổi',
                'parentID' => 3
            ],  [
                'name' => '12-16 tuổi',
                'parentID' => 3
            ], 
            [
                'name' => 'Sách tô màu',
                'parentID' => 4
            ], [
                'name' => 'Dụng cụ học tập',
                'parentID' => 4
            ],


        ]);
    }
    
}
