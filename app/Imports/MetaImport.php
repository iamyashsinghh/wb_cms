<?php

    namespace App\Imports;

    use App\Models\City;
    use App\Models\Location;
    use App\Models\VendorCategory;
    use App\Models\VenueCategory;
    use App\Models\VenueListingMeta;
    use App\Models\VendorListingMeta;
    use Maatwebsite\Excel\Row;
    use Maatwebsite\Excel\Concerns\OnEachRow;
    use Maatwebsite\Excel\Concerns\WithHeadingRow;

    class MetaImport implements OnEachRow, WithHeadingRow
    {
        public $data = [];

        public function onRow(Row $row)
    {
        $row = $row->toArray();

        if (!empty($row['url'])) {

            // ✅ Parse URL to extract slug parts
            $urlParts = parse_url($row['url']);
            $path = $urlParts['path'] ?? '';
            $segments = explode('/', trim($path, '/')); // remove starting/ending slashes

            // Example: ['wedding-photographers', 'delhi', 'netaji-subhash-place']
            $categorySlug = $segments[0] ?? null;
            $citySlug = $segments[1] ?? null;
            $localitySlug = $segments[2] ?? null;

            // ✅ Fetch IDs from DB using slug (instead of name)
            $category = VendorCategory::where('slug', $categorySlug)->first();
            $is_venue = false;
            if(!$category){
                $category = VenueCategory::where('slug', $categorySlug)->first();
                $is_venue = true;
            }
            $city = City::where('slug', $citySlug)->first();
            $locality = Location::where('slug', $localitySlug)->first();
            $slug = $categorySlug . '/' . $citySlug . '/' . $localitySlug;
            if($is_venue){
                $venuelocationmeta = VenueListingMeta::where('slug', $slug)->first();
            if($venuelocationmeta){
                $venuelocationmeta->meta_title = $row['title'] ?? '';
                $venuelocationmeta->meta_description = $row['description'] ?? '';
                $venuelocationmeta->save();
            }else{
                $venuelocationmeta = new VenueListingMeta();
                $new_slug = $categorySlug . '/' . $citySlug . '/' . $localitySlug;
                $venuelocationmeta->slug = $new_slug;
                $venuelocationmeta->meta_title = $row['title'] ?? '';
                $venuelocationmeta->meta_description = $row['description'] ?? '';
                $venuelocationmeta->category_id = $category ? $category->id : null;
                $venuelocationmeta->city_id = $city ? $city->id : null;
                $venuelocationmeta->location_id = $locality ? $locality->id : null;
                $venuelocationmeta->save();
            }
            }else{
                $Vendorlocationmeta = VendorListingMeta::where('slug', $slug)->first();
                if($Vendorlocationmeta){
                    $Vendorlocationmeta->meta_title = $row['title'] ?? '';
                    $Vendorlocationmeta->meta_description = $row['description'] ?? '';
                    $Vendorlocationmeta->save();
                }else{
                    $Vendorlocationmeta = new VendorListingMeta();
                    $new_slug = $categorySlug . '/' . $citySlug . '/' . $localitySlug;
                    $Vendorlocationmeta->slug = $new_slug;
                    $Vendorlocationmeta->meta_title = $row['title'] ?? '';
                    $Vendorlocationmeta->meta_description = $row['description'] ?? '';
                    $Vendorlocationmeta->category_id = $category ? $category->id : null;
                    $Vendorlocationmeta->city_id = $city ? $city->id : null;
                    $Vendorlocationmeta->location_id = $locality ? $locality->id : null;
                    $Vendorlocationmeta->save();
                }
            }


            $this->data[] = [
                'url' => $row['url'],
                'category_id' => $category ? $category->id : null,
                'category_slug' => $categorySlug,
                'city_id' => $city ? $city->id : null,
                'city_slug' => $citySlug,
                'locality_id' => $locality ? $locality->id : null,
                'locality_slug' => $localitySlug,
                'meta_title' => $row['title'] ?? '',
                'meta_description' => $row['description'] ?? '',
            ];
        }
    }
}
