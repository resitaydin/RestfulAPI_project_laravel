<?php

namespace Database\Seeders;

use App\Models\Author;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\File;

class AuthorSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $json = File::get('database/data/authors.json');
        $authors = json_decode($json);

        foreach ($authors as $key => $value) {
            Author::create([
                'author_name'=> $value->author_name,
                'is_local' => $value->author_type
            ]);
        }
    }
}
