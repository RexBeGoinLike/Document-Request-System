<?php

namespace App\Providers;

use Filament\Forms\Components\TextInput;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        $this->loadViewsFrom(__DIR__.'/../resources/views', 'your-package');

        TextInput::configureUsing(function (TextInput $component) {
            $name = $component->getName();

            // 1. Run your custom replacement string logic
            $cleanName = str_replace('_count', '', $name);
            $cleanName = str_replace('_', ' ', $cleanName);
            $cleanName = str_replace('cert', 'Certification', $cleanName);
            $cleanLabel = ucwords($cleanName);

            // 2. Handle your hardcoded manual exceptions
            $exceptions = [
                'cert_cadet_count' => 'Certification As Cadet',
                'cert_graduate_count' => 'Certification As Graduate',
                'cert_authenticity_count' => 'Certification and Authenticity Verification',
                'cert_course_desc_count' => 'Course Description Certification',
                'cert_latin_honors_count' => 'Cum Laude (Latin Honors) Certification',
                'cert_ranking_gpa_count' => 'Ranking/GPA Certification',
                'cert_instruction_medium_count' => 'Medium of Instruction Certification',
                'cert_service_record_count' => 'Service Record/Statement of Service Certification',
                'photocopy_count' => 'Photocopying and Authentication',
                'signature_image' => 'Signature',
            ];

            if (array_key_exists($name, $exceptions)) {
                $cleanLabel = $exceptions[$name];
            }

            // 3. Set the Filament label automatically
            $component->label($cleanLabel);
        });
    }
}
