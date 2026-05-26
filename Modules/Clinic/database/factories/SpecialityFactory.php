<?php

namespace Modules\Clinic\database\factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Modules\Clinic\Models\Speciality;

class SpecialityFactory extends Factory
{
    protected $model = Speciality::class;

    public function definition(): array
    {
        return [
            'name' => $this->faker->randomElement([
                'طب القلب',
                'طب الأطفال',
                'الجراحة العامة',
                'طب النساء والولادة',
                'طب العيون',
                'طب الأسنان',
                'طب الجلدية',
                'طب الأنف والأذن والحنجرة',
                'طب العظام',
                'الطب النفسي',
                'طب الأعصاب',
                'طب المسالك البولية'
            ]),
        ];
    }
}
