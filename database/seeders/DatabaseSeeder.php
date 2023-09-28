<?php

namespace Database\Seeders;

// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $this->call(TeamSeeder::class);
        $this->call(UserSeeder::class);
        $this->call(SportSectionSeeder::class);
        $this->call(TrainertypSeeder::class);
        $this->call(TrainertableSeeder::class);
        $this->call(InstructionSeeder::class);
        $this->call(CourseParticipantSeeder::class);
        $this->call(CourseSeeder::class);
        $this->call(CoursedateSeeder::class);
        $this->call(SportEquipmentSeeder::class);
        $this->call(SportEquipmentBookedSeeder::class);
    }
}
