<?php

namespace App\Livewire;

use App\Models\Contact;
use App\Models\Setting;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;

#[Layout('components.layouts.app')]
#[Title('Contact Us - PakGold Rates')]
class ContactPage extends Component
{
    /**
     * Contact information from settings.
     */
    public string $contactPhone = '';
    public string $contactWhatsapp = '';
    public string $contactEmail = '';
    public string $contactAddress = '';

    /**
     * Opening hours from settings.
     */
    public string $hoursMonThu = '';
    public string $hoursFri = '';
    public string $hoursSat = '';
    public string $hoursSun = '';

    /**
     * Contact form fields.
     */
    public string $formName = '';
    public string $formEmail = '';
    public string $formPhone = '';
    public string $formSubject = '';
    public string $formMessage = '';

    /**
     * Success notification flag.
     */
    public bool $formSubmitted = false;

    public function mount(): void
    {
        $this->contactPhone = Setting::get('contact_phone', '+92 300 1234567');
        $this->contactWhatsapp = Setting::get('contact_whatsapp', '+92 300 1234567');
        $this->contactEmail = Setting::get('contact_email', 'info@pakgoldrates.com');
        $this->contactAddress = Setting::get('contact_address', 'Sarafa Bazaar, Lahore, Pakistan');

        $this->hoursMonThu = Setting::get('hours_mon_thu', '10:00 AM - 8:00 PM');
        $this->hoursFri = Setting::get('hours_fri', '2:30 PM - 8:00 PM');
        $this->hoursSat = Setting::get('hours_sat', '10:00 AM - 8:00 PM');
        $this->hoursSun = Setting::get('hours_sun', 'Closed');
    }

    /**
     * Submit the contact form.
     */
    public function submitForm(): void
    {
        $validated = $this->validate([
            'formName' => 'required|string|max:255',
            'formEmail' => 'required|email|max:255',
            'formPhone' => 'nullable|string|max:50',
            'formSubject' => 'nullable|string|max:255',
            'formMessage' => 'required|string|max:5000',
        ]);

        Contact::create([
            'name' => $validated['formName'],
            'email' => $validated['formEmail'],
            'phone' => $validated['formPhone'] ?? null,
            'subject' => $validated['formSubject'] ?? null,
            'message' => $validated['formMessage'],
        ]);

        $this->reset(['formName', 'formEmail', 'formPhone', 'formSubject', 'formMessage']);
        $this->formSubmitted = true;
    }

    public function render()
    {
        return view('livewire.contact-page');
    }
}
