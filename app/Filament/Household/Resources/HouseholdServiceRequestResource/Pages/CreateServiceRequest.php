<?php

namespace App\Filament\Household\Resources\HouseholdServiceRequestResource\Pages;

use App\Filament\Household\Resources\HouseholdServiceRequestResource;
use App\Models\Company;
use App\Models\ServiceRequest;
use App\Models\UserProfile;
use DB;
use Filament\Actions;
use Filament\Resources\Pages\CreateRecord;

class CreateServiceRequest extends CreateRecord
{
    protected static string $resource = HouseholdServiceRequestResource::class;
    protected function mutateFormDataBeforeCreate(array $data): array
    {
        $data['household_id'] = auth()->id();
          // Fetch the user's profile
          $userProfile = UserProfile::where('user_id', auth()->id())->first();

          if ($userProfile) {
              // Populate the address and phone from the profile
              $data['address'] = $userProfile->address;
              $data['client_number'] = $userProfile->phone;
          }
  
        return $data;
    }
    protected function afterCreate(): void
    {
        $record = $this->record; // The newly created service request

        // Find all verified and available companies
        $companies = Company::where('verification_status', 'verified')
            ->where('availability_status', 'open')
            ->get();

        if ($companies->isNotEmpty()) {
            foreach ($companies as $company) {
                // Associate the service request with the company
                DB::table('company_service_requests')->insert([
                    'service_request_id' => $record->id,
                    'company_id' => $company->id,
                    'company_user_id' => $company->user_id,
                    'status' => 'pending', // Initial status
                    'created_at' => now(),
                ]);
            }
        } else {
            // Throw an exception if no companies are available
            throw new \Exception('No verified and available companies found.');
        }
    }

    // protected function afterCreate(): void
    // {
    //     $record = $this->record;

    //     // Find a single verified and available company
    //     $company = Company::where('verification_status', 'verified')
    //         ->where('availability_status', 'open')
    //         ->first();

    //     if ($company) {
    //         $record->update([
    //             'company_id' => $company->id, 
    //             'company_user_id' => $company->user_id,
    //             'status' => 'assigned',
    //         ]);
    //     } else {
    //         // Throw an exception if no company is found
    //         throw new \Exception('No verified and available company found.');
    //     }
    // }

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }

}
