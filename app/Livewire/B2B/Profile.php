<?php

namespace App\Livewire\B2B;

use Livewire\Component;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class Profile extends Component
{
    public $activeTab = 'company';

    // Company data
    public $company_name;
    public $nip;
    public $regon;
    public $address;
    public $postal_code;
    public $city;
    public $phone;
    public $website;

    // Contact data
    public $email;
    public $contact_person;
    public $contact_phone;
    public $contact_email;

    // Business data
    public $business_type;
    public $business_description;

    // Delivery preferences
    public $delivery_addresses = [];
    public $preferred_delivery_time = 'morning';
    public $delivery_days = [];

    // Notification preferences
    public $email_notifications = true;
    public $sms_notifications = false;

    // Password change
    public $current_password;
    public $password;
    public $password_confirmation;

    protected function rules()
    {
        return [
            'company_name' => 'required|string|max:255',
            'nip' => 'nullable|string|max:20',
            'regon' => 'nullable|string|max:14',
            'address' => 'required|string|max:255',
            'postal_code' => 'required|string|max:10',
            'city' => 'required|string|max:255',
            'phone' => 'nullable|string|max:20',
            'website' => 'nullable|url|max:255',
            'email' => 'required|email|max:255|unique:b2_b_clients,email,' . Auth::guard('b2b')->id(),
            'contact_person' => 'nullable|string|max:255',
            'contact_phone' => 'nullable|string|max:20',
            'contact_email' => 'nullable|email|max:255',
            'business_type' => 'required|in:hotel,restaurant,cafe,shop,catering,other',
            'business_description' => 'nullable|string|max:500',
            'preferred_delivery_time' => 'required|in:morning,afternoon,evening,flexible',
            'delivery_days' => 'array',
            'delivery_days.*' => 'in:monday,tuesday,wednesday,thursday,friday,saturday,sunday',
            'email_notifications' => 'boolean',
            'sms_notifications' => 'boolean',
        ];
    }

    protected function passwordRules()
    {
        return [
            'current_password' => 'required',
            'password' => 'required|min:8|confirmed',
            'password_confirmation' => 'required',
        ];
    }

    public function mount()
    {
        if (!Auth::guard('b2b')->check()) {
            return redirect()->route('b2b.login');
        }

        $client = Auth::guard('b2b')->user();

        // Load client data
        $this->company_name = $client->company_name;
        $this->nip = $client->nip;
        $this->regon = $client->regon;
        $this->address = $client->address;
        $this->postal_code = $client->postal_code;
        $this->city = $client->city;
        $this->phone = $client->phone;
        $this->website = $client->website;
        $this->email = $client->email;
        $this->contact_person = $client->contact_person;
        $this->contact_phone = $client->contact_phone;
        $this->contact_email = $client->contact_email;
        $this->business_type = $client->business_type;
        $this->business_description = $client->business_description;
        $this->delivery_addresses = $client->delivery_addresses ?? [];
        $this->preferred_delivery_time = $client->preferred_delivery_time ?? 'morning';
        $this->delivery_days = $client->delivery_days ?? [];
        $this->email_notifications = $client->email_notifications ?? true;
        $this->sms_notifications = $client->sms_notifications ?? false;
    }

    public function setActiveTab($tab)
    {
        $this->activeTab = $tab;
    }

    public function updateProfile()
    {
        $this->validate();

        $client = Auth::guard('b2b')->user();

        $client->update([
            'company_name' => $this->company_name,
            'nip' => $this->nip,
            'regon' => $this->regon,
            'address' => $this->address,
            'postal_code' => $this->postal_code,
            'city' => $this->city,
            'phone' => $this->phone,
            'website' => $this->website,
            'email' => $this->email,
            'contact_person' => $this->contact_person,
            'contact_phone' => $this->contact_phone,
            'contact_email' => $this->contact_email,
            'business_type' => $this->business_type,
            'business_description' => $this->business_description,
            'delivery_addresses' => $this->delivery_addresses,
            'preferred_delivery_time' => $this->preferred_delivery_time,
            'delivery_days' => $this->delivery_days,
            'email_notifications' => $this->email_notifications,
            'sms_notifications' => $this->sms_notifications,
        ]);

        session()->flash('success', 'Profil został zaktualizowany.');
    }

    public function changePassword()
    {
        $this->validate($this->passwordRules());

        $client = Auth::guard('b2b')->user();

        if (!Hash::check($this->current_password, $client->password)) {
            $this->addError('current_password', 'Aktualne hasło jest nieprawidłowe.');
            return;
        }

        $client->update([
            'password' => Hash::make($this->password),
        ]);

        $this->reset(['current_password', 'password', 'password_confirmation']);
        session()->flash('success', 'Hasło zostało zmienione.');
    }

    public function addDeliveryAddress()
    {
        $this->delivery_addresses[] = [
            'name' => '',
            'address' => '',
            'contact' => '',
            'phone' => '',
            'notes' => '',
        ];
    }

    public function removeDeliveryAddress($index)
    {
        unset($this->delivery_addresses[$index]);
        $this->delivery_addresses = array_values($this->delivery_addresses);
    }

    public function getBusinessTypes()
    {
        return [
            'hotel' => 'Hotel',
            'restaurant' => 'Restauracja',
            'cafe' => 'Kawiarnia',
            'shop' => 'Sklep',
            'catering' => 'Catering',
            'other' => 'Inne',
        ];
    }

    public function getDeliveryTimes()
    {
        return [
            'morning' => 'Rano (6:00-12:00)',
            'afternoon' => 'Popołudnie (12:00-18:00)',
            'evening' => 'Wieczór (18:00-22:00)',
            'flexible' => 'Elastycznie',
        ];
    }

    public function getDaysOfWeek()
    {
        return [
            'monday' => 'Poniedziałek',
            'tuesday' => 'Wtorek',
            'wednesday' => 'Środa',
            'thursday' => 'Czwartek',
            'friday' => 'Piątek',
            'saturday' => 'Sobota',
            'sunday' => 'Niedziela',
        ];
    }

    public function render()
    {
        return view('livewire.b2-b.profile');
    }
}
