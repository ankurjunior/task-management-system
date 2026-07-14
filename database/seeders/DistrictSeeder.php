<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class DistrictSeeder extends Seeder
{
    public function run(): void
    {
        $timestamp = now();
        $districts = [
            [1, 'Agra Division', 'Agra', 118, 'AGR'], [28, 'Agra Division', 'Firozabad', 143, 'FIR'], [52, 'Agra Division', 'Mainpuri', 166, 'MAI'], [53, 'Agra Division', 'Mathura', 167, 'MAT'],
            [2, 'Aligarh Division', 'Aligarh', 119, 'ALI'], [24, 'Aligarh Division', 'Etah', 138, 'ETA'], [37, 'Aligarh Division', 'Hathras', 163, 'HAT'], [44, 'Aligarh Division', 'Kasganj', 633, 'KAS'],
            [3, 'Ayodhya Division', 'Ambedkar Nagar', 121, 'AMB'], [4, 'Ayodhya Division', 'Amethi', 640, 'AME'], [7, 'Ayodhya Division', 'Ayodhya', 140, 'AYO'], [14, 'Ayodhya Division', 'Barabanki', 129, 'BAR'], [73, 'Ayodhya Division', 'Sultanpur', 185, 'SUL'],
            [8, 'Azamgarh Division', 'Azamgarh', 123, 'AZA'], [11, 'Azamgarh Division', 'Ballia', 126, 'BAL'], [54, 'Azamgarh Division', 'Maunathbhanjan', 168, 'MAU'],
            [15, 'Bareilly Division', 'Bareilly', 130, 'BLY'], [19, 'Bareilly Division', 'Budaun', 133, 'BAD'], [59, 'Bareilly Division', 'Pilibhit', 173, 'PIL'], [67, 'Bareilly Division', 'Shahjahanpur', 180, 'SHA'],
            [16, 'Basti Division', 'Basti', 131, 'BAS'], [66, 'Basti Division', 'Sant Kabir Nagar', 178, 'SAN'], [70, 'Basti Division', 'Siddharth Nagar', 182, 'SID'],
            [13, 'Chitrakoot Division', 'Banda', 128, 'BAN'], [22, 'Chitrakoot Division', 'Chitrakoot', 136, 'CHI'], [34, 'Chitrakoot Division', 'Hamirpur', 149, 'HAM'], [51, 'Chitrakoot Division', 'Mahoba', 165, 'MHB'],
            [10, 'Gonda Division', 'Bahraich', 125, 'BEH'], [12, 'Gonda Division', 'Balrampur', 127, 'BRM'], [32, 'Gonda Division', 'Gonda', 147, 'GON'], [69, 'Gonda Division', 'Shravasti', 181, 'SHR'],
            [23, 'Gorakhpur Division', 'Deoria', 137, 'DEO'], [33, 'Gorakhpur Division', 'Gorakhpur', 148, 'GOR'], [46, 'Gorakhpur Division', 'Kushinagar', 160, 'KUS'], [50, 'Gorakhpur Division', 'Maharajganj', 164, 'MAH'],
            [38, 'Jhansi Division', 'Jalaun', 151, 'JAL'], [40, 'Jhansi Division', 'Jhansi', 153, 'JHA'], [48, 'Jhansi Division', 'Lalitpur', 161, 'LAL'],
            [6, 'Kanpur Nagar Division', 'Auraiya', 122, 'AUR'], [25, 'Kanpur Nagar Division', 'Etawah', 139, 'EWH'], [26, 'Kanpur Nagar Division', 'Farrukhabad', 141, 'FAR'], [41, 'Kanpur Nagar Division', 'Kannauj', 155, 'KAN'], [42, 'Kanpur Nagar Division', 'Kanpur Dehat', 156, 'KND'], [43, 'Kanpur Nagar Division', 'Kanpur Nagar', 157, 'KNN'],
            [36, 'Lucknow Division', 'Hardoi', 150, 'HAR'], [47, 'Lucknow Division', 'Lakhimpur Kheri', 159, 'LAK'], [49, 'Lucknow Division', 'Lucknow', 162, 'LUC'], [62, 'Lucknow Division', 'Rae Bareli', 175, 'RAE'], [71, 'Lucknow Division', 'Sitapur', 183, 'SIT'], [74, 'Lucknow Division', 'Unnao', 186, 'UNN'],
            [9, 'Meerut Division', 'Baghpat', 124, 'BAG'], [20, 'Meerut Division', 'Bulandshahar', 134, 'BUL'], [29, 'Meerut Division', 'Gautam Buddha Nagar', 144, 'GAU'], [30, 'Meerut Division', 'Ghaziabad', 145, 'GHA'], [35, 'Meerut Division', 'Hapur', 661, 'HAP'], [55, 'Meerut Division', 'Meerut', 169, 'MEE'],
            [17, 'Mirzapur Division', 'Bhadohi', 179, 'BHA'], [56, 'Mirzapur Division', 'Mirzapur', 170, 'MIR'], [72, 'Mirzapur Division', 'Sonbhadra', 184, 'SON'],
            [5, 'Moradabad Division', 'Amroha', 154, 'AMR'], [18, 'Moradabad Division', 'Bijnor', 132, 'BIJ'], [57, 'Moradabad Division', 'Moradabad', 171, 'MOR'], [63, 'Moradabad Division', 'Rampur', 176, 'RAM'], [65, 'Moradabad Division', 'Sambhal', 659, 'SAM'],
            [27, 'Prayagraj Division', 'Fatehpur', 142, 'FAT'], [45, 'Prayagraj Division', 'Kaushambi', 158, 'KAU'], [60, 'Prayagraj Division', 'Pratapgarh', 174, 'PRA'], [61, 'Prayagraj Division', 'Prayagraj', 120, 'PYJ'],
            [58, 'Saharanpur Division', 'Muzaffarnagar', 172, 'MUZ'], [64, 'Saharanpur Division', 'Saharanpur', 177, 'SAH'], [68, 'Saharanpur Division', 'Shamli', 660, 'SML'],
            [21, 'Varanasi Division', 'Chandauli', 135, 'CHA'], [31, 'Varanasi Division', 'Ghazipur', 146, 'GZR'], [39, 'Varanasi Division', 'Jaunpur', 152, 'JAU'], [75, 'Varanasi Division', 'Varanasi', 187, 'VAR'],
        ];

        foreach ($districts as [$id, $divisionName, $districtName, $lgdCode, $districtCode]) {
            DB::table('master_districts')->updateOrInsert(
                ['id' => $id],
                [
                    'division_name' => $divisionName,
                    'district_name' => $districtName,
                    'district_lgd_code' => $lgdCode,
                    'district_code' => $districtCode,
                    'created_at' => $timestamp,
                    'updated_at' => $timestamp,
                ]
            );
        }
    }
}
