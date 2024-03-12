<?php

namespace Database\Seeders;

// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use App\Models\Organiser;
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
        $this->call(OrganiserSeeder::class);
        $this->call(CourseSeeder::class);
        $this->call(CoursedateSeeder::class);
        $this->call(SportEquipmentSeeder::class);
        $this->call(SportEquipmentBookedSeeder::class);
        $this->call(OrganiserSportSectionSeeder::class);
        $this->call(CourseSportSectionSeeder::class);
        $this->call(CoursedateUserSeeder::class);
        $this->call(CourseParticipantBookedSeeder::class);
        $this->call(OrganiserinformationSeeder::class);
    }
}
