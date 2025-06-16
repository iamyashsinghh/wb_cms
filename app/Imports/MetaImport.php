<?php

namespace App\Imports;

use App\Models\City;
use App\Models\Location;
use App\Models\VendorCategory;
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
        $city = City::where('slug', $citySlug)->first();
        $locality = Location::where('slug', $localitySlug)->first();
        $Vendorlocationmeta = new VendorListingMeta();

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
