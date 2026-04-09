<?php

namespace Database\Seeders;

use App\Enums\UserRole;
use App\Models\Contract;
use App\Models\Project;
use App\Models\Ticket;
use App\Models\User;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        User::factory(100)->create();

        User::factory()->create([
            'first_name' => 'Admin',
            'last_name' => 'User',
            'email' => 'admin@example.com',
            'role' => UserRole::ADMIN,
        ]);
        $devUser = User::factory()->create([
            'first_name' => 'Dev',
            'last_name' => 'User',
            'email' => 'dev@example.com',
            'role' => UserRole::DEVELOPER,
        ]);
        $clientUser = User::factory()->create([
            'first_name' => 'Client',
            'last_name' => 'User',
            'email' => 'client@example.com',
            'role' => UserRole::CLIENT,
        ]);

        $projects = [
            ['name' => 'Site Vitrine Corporate',     'issue_prefix' => 'SVC'],
            ['name' => 'Application Mobile RH',      'issue_prefix' => 'AMR'],
            ['name' => 'Plateforme E-Commerce',      'issue_prefix' => 'PEC'],
            ['name' => 'Refonte SI Comptabilité',    'issue_prefix' => 'RSC'],
            ['name' => 'Dashboard Analytics',        'issue_prefix' => 'DAS'],
            ['name' => 'API Gateway Microservices',  'issue_prefix' => 'AGM'],
            ['name' => 'Portail Client B2B',         'issue_prefix' => 'PCB'],
            ['name' => 'Outil CRM Interne',          'issue_prefix' => 'CRM'],
            ['name' => 'Système de Ticketing',       'issue_prefix' => 'STK'],
            ['name' => 'Intégration ERP SAP',        'issue_prefix' => 'ERP'],
        ];

        $contracts = [
            ['included_hours' => 40,  'extra_hourly_rate' => 85.00],
            ['included_hours' => 80,  'extra_hourly_rate' => 90.00],
            ['included_hours' => 120, 'extra_hourly_rate' => 75.00],
            ['included_hours' => 60,  'extra_hourly_rate' => 95.00],
            ['included_hours' => 100, 'extra_hourly_rate' => 80.00],
            ['included_hours' => 200, 'extra_hourly_rate' => 70.00],
            ['included_hours' => 50,  'extra_hourly_rate' => 100.00],
            ['included_hours' => 150, 'extra_hourly_rate' => 85.00],
            ['included_hours' => 75,  'extra_hourly_rate' => 90.00],
            ['included_hours' => 300, 'extra_hourly_rate' => 65.00],
        ];

        foreach ($projects as $index => $projectData) {
            $contractData = $contracts[$index];

            $contractData['file'] = "test_pdf.pdf";

            $contract = Contract::create($contractData);

            Project::create([
                'name'           => $projectData['name'],
                'issue_prefix'   => $projectData['issue_prefix'],
                'contract_id'    => $contract->id,
                'next_ticket_id' => 1,
            ]);
        }

        $users = User::all();
        // Project members
        Project::all()->each(function (Project $project) use ($users) {
            $project->members()->attach($users->random(rand(4, 7))->pluck('id')->toArray());
        });

        $project = Project::find(1);
        if (!$project->members()->where('id', '=', $devUser->id)->exists())
            $project->members()->attach($devUser);
        $project = Project::find(2);
        if (!$project->members()->where('id', '=', $clientUser->id)->exists())
            $project->members()->attach($clientUser);


        Ticket::factory(70)->create();

        // Assign random people to tickets
        $users = $users->where('role', '!=', UserRole::CLIENT);
        Ticket::all()->each(function (Ticket $ticket) use ($users) {
            $ticket->assignedTo()->attach($users->random(rand(0, 2))->pluck('id')->toArray());
        });
    }
}
