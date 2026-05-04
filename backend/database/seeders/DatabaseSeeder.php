<?php

namespace Database\Seeders;

use App\Models\Area;
use App\Models\Donation;
use App\Models\Need;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{

    public function run(): void
    {
        // Admin user
        $admin = User::firstOrCreate(
            ['email' => 'admin@humanitarian.local'],
            [
                'name'                => 'Admin',
                'password'            => Hash::make('admin1234'),
                'role'                => 'admin',
                'status'              => 'approved',
                'can_review_accounts' => true,
                'reviewed_at'         => now(),
            ]
        );

        // Demo volunteer user
        $volunteer = User::firstOrCreate(
            ['email' => 'volunteer@humanitarian.local'],
            [
                'name'     => 'متطوع تجريبي',
                'password' => Hash::make('volunteer1234'),
                'role'     => 'volunteer',
                'status'   => 'approved',
            ]
        );

        // Areas with translatable names
        $areasData = [
            [
                'name'          => ['ar' => 'منطقة الشمال', 'en' => 'North Zone', 'fr' => 'Zone Nord'],
                'description'   => ['ar' => 'المنطقة الشمالية تحتاج إمدادات غذائية', 'en' => 'Northern area needs food supplies', 'fr' => 'La zone nord a besoin de vivres'],
                'latitude'      => 30.0626,
                'longitude'     => 31.2497,
                'priority_level'=> 'high',
                'status'        => 'active',
            ],
            [
                'name'          => ['ar' => 'منطقة الجنوب', 'en' => 'South Zone', 'fr' => 'Zone Sud'],
                'description'   => ['ar' => 'المنطقة الجنوبية تحتاج رعاية طبية', 'en' => 'Southern area needs medical care', 'fr' => 'La zone sud a besoin de soins médicaux'],
                'latitude'      => 29.9737,
                'longitude'     => 32.5311,
                'priority_level'=> 'critical',
                'status'        => 'active',
            ],
            [
                'name'          => ['ar' => 'منطقة الشرق', 'en' => 'East Zone', 'fr' => 'Zone Est'],
                'description'   => ['ar' => 'المنطقة الشرقية تعاني نقص الملبس', 'en' => 'Eastern zone lacks clothing', 'fr' => 'La zone est manque de vêtements'],
                'latitude'      => 30.5852,
                'longitude'     => 32.2654,
                'priority_level'=> 'medium',
                'status'        => 'active',
            ],
            [
                'name'          => ['ar' => 'منطقة الغرب', 'en' => 'West Zone', 'fr' => 'Zone Ouest'],
                'description'   => ['ar' => 'المنطقة الغربية بحاجة ماء نظيف', 'en' => 'Western area needs clean water', 'fr' => "La zone ouest a besoin d'eau potable"],
                'latitude'      => 30.8168,
                'longitude'     => 30.9944,
                'priority_level'=> 'high',
                'status'        => 'active',
            ],
        ];

        $areas = [];
        foreach ($areasData as $data) {
            $area = Area::withoutGlobalScopes()->where('latitude', $data['latitude'])->first();
            if (! $area) {
                $area = new Area();
                $area->setTranslations('name', $data['name']);
                $area->setTranslations('description', $data['description']);
                $area->latitude       = $data['latitude'];
                $area->longitude      = $data['longitude'];
                $area->priority_level = $data['priority_level'];
                $area->status         = $data['status'];
                $area->save();
            }
            $areas[] = $area;
        }

        // Needs for each area
        $needTypes = ['food', 'water', 'medicine', 'shelter', 'other'];
        $statuses  = ['pending', 'in_progress', 'fulfilled'];

        foreach ($areas as $i => $area) {
            for ($j = 0; $j < 3; $j++) {
                $existing = Need::withoutGlobalScopes()
                    ->where('area_id', $area->id)
                    ->where('type', $needTypes[($i + $j) % count($needTypes)])
                    ->first();
                if (! $existing) {
                    $need = new Need();
                    $need->area_id  = $area->id;
                    $need->type     = $needTypes[($i + $j) % count($needTypes)];
                    $need->quantity = rand(50, 500);
                    $need->status   = $statuses[$j % count($statuses)];
                    $need->setTranslations('notes', [
                        'ar' => 'ملاحظات بالعربية للاحتياج رقم ' . ($j + 1),
                        'en' => 'English notes for need ' . ($j + 1),
                        'fr' => 'Notes en français pour le besoin ' . ($j + 1),
                    ]);
                    $need->save();
                }
            }
        }

        // Donations with blockchain hash chain (use create() to trigger model events for hash)
        if (Donation::withoutGlobalScopes()->count() === 0) {
            $donationAmounts = [500, 1200, 750, 2000, 300];
            $causes          = ['food_aid', 'medical', 'shelter', 'water', 'education'];
            foreach ($donationAmounts as $k => $amount) {
                Donation::create([
                    'donor_id' => $admin->id,
                    'amount'   => $amount,
                    'cause'    => $causes[$k],
                ]);
            }
        }
    }
}
