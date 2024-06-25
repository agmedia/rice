<?php


namespace App\Helpers;


use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;

class Country
{

    /**
     * @return Collection
     * @throws \Illuminate\Contracts\Filesystem\FileNotFoundException
     */
    public static function list(): Collection
    {
        $countries = Storage::disk('assets')->get('country.json');
        $countries = json_decode($countries, true);
        $response = [];

        foreach ($countries as $key => $country) {
            if ($country['iso_code_2'] == 'HR') {
                array_push($response, $country);
                unset($countries[$key]);
            }
        }

        $response = $response + $countries;

        return collect($response);
    }

    /**
     * @param null $id
     *
     * @return Collection
     * @throws \Illuminate\Contracts\Filesystem\FileNotFoundException
     */
    public static function zones($id = null): Collection
    {
        $zones = Storage::disk('assets')->get('zone.json');

        if ($id) {
            return collect(json_decode($zones))->where('country_id', $id);
        }

        return collect(json_decode($zones));
    }


    /**
     * @param string $city
     * @param string $zip
     *
     * @return bool
     */
    public static function checkIfZagreb(string $city, string $zip): bool
    {
        if (in_array($city, ['Zagreb', 'zagreb']) || in_array($zip, ['10000', '10 000', '10010', '10020', '10040', '10090', '10104', '10105', '10109', '10110', '10123', '10135', '10172', '10250', '10360', '10408', '10410', '10412' ])) {
            return true;
        }

        return false;
    }
}
